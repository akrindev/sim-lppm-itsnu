<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProposalReviewer extends Model
{
    protected $table = 'proposal_reviewer';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'status',
        'review_notes',
        'recommendation',
        'round',
        'assigned_at',
        'deadline_at',
        'started_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => ReviewStatus::class,
            'recommendation' => 'string',
            'round' => 'integer',
            'assigned_at' => 'datetime',
            'deadline_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the proposal being reviewed.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the reviewer user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all review logs (history) for this reviewer assignment.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ReviewLog::class)->orderBy('round', 'desc');
    }

    /**
     * Get all scores for this reviewer assignment.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(ReviewScore::class);
    }

    /**
     * Get scores for the current round.
     */
    public function currentScores(): HasMany
    {
        return $this->scores()->where('round', $this->round);
    }

    /**
     * Get the latest completed review log.
     */
    public function latestLog()
    {
        return $this->logs()->completed()->first();
    }

    /**
     * Get the review log for a specific round.
     */
    public function logForRound(int $round): ?ReviewLog
    {
        return $this->logs()->forRound($round)->first();
    }

    /**
     * Get the previous round's review log.
     */
    public function previousRoundLog(): ?ReviewLog
    {
        $currentRound = $this->round ?? 1;
        if ($currentRound <= 1) {
            return null;
        }

        return $this->logs()->forRound($currentRound - 1)->first();
    }

    /**
     * Check if review is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === ReviewStatus::COMPLETED;
    }

    /**
     * Check if review is pending (needs action).
     */
    public function isPending(): bool
    {
        return $this->status === ReviewStatus::PENDING;
    }

    /**
     * Check if review is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === ReviewStatus::IN_PROGRESS;
    }

    /**
     * Check if re-review is requested.
     */
    public function isReReviewRequested(): bool
    {
        return $this->status === ReviewStatus::RE_REVIEW_REQUESTED;
    }

    /**
     * Check if review requires action from reviewer.
     */
    public function requiresAction(): bool
    {
        return $this->status->requiresAction();
    }

    /**
     * Check if review is overdue.
     */
    public function isOverdue(): bool
    {
        if (! $this->deadline_at) {
            return false;
        }

        return ! $this->isCompleted() && $this->deadline_at->isPast();
    }

    /**
     * Check if deadline is approaching (within specified days).
     */
    public function isDeadlineApproaching(int $days = 3): bool
    {
        if (! $this->deadline_at) {
            return false;
        }

        if ($this->isCompleted()) {
            return false;
        }

        $daysUntilDeadline = now()->diffInDays($this->deadline_at, false);

        return $daysUntilDeadline >= 0 && $daysUntilDeadline <= $days;
    }

    /**
     * Get days remaining until deadline.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (! $this->deadline_at) {
            return null;
        }

        return (int) now()->diffInDays($this->deadline_at, false);
    }

    /**
     * Get days overdue (negative if not overdue).
     */
    public function getDaysOverdueAttribute(): ?int
    {
        if (! $this->deadline_at) {
            return null;
        }

        $days = now()->diffInDays($this->deadline_at, false);

        return $days < 0 ? abs($days) : 0;
    }

    /**
     * Mark review as started (in progress).
     */
    public function markAsStarted(): void
    {
        if ($this->started_at) {
            return; // Already started
        }

        $this->update([
            'status' => ReviewStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);
    }

    /**
     * Mark review as completed.
     */
    public function complete(string $reviewNotes, string $recommendation): void
    {
        $this->update([
            'status' => ReviewStatus::COMPLETED,
            'review_notes' => $reviewNotes,
            'recommendation' => $recommendation,
            'completed_at' => now(),
        ]);
    }

    /**
     * Request re-review (after proposal revision).
     */
    public function requestReReview(): void
    {
        $currentRound = $this->round ?? 1;

        $this->update([
            'status' => ReviewStatus::RE_REVIEW_REQUESTED,
            'round' => $currentRound + 1,
            'review_notes' => null,
            'recommendation' => null,
            'started_at' => null,
            'completed_at' => null,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Reset for new review round (keeps reviewer but resets review data).
     */
    public function resetForNewRound(int $daysToReview = 14): void
    {
        $currentRound = $this->round ?? 1;

        $this->update([
            'status' => ReviewStatus::PENDING,
            'round' => $currentRound + 1,
            'review_notes' => null,
            'recommendation' => null,
            'started_at' => null,
            'completed_at' => null,
            'assigned_at' => now(),
            'deadline_at' => now()->addDays($daysToReview),
        ]);
    }

    /**
     * Scope for pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', ReviewStatus::PENDING);
    }

    /**
     * Scope for completed reviews.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', ReviewStatus::COMPLETED);
    }

    /**
     * Scope for reviews requiring action.
     */
    public function scopeRequiresAction($query)
    {
        return $query->whereIn('status', [ReviewStatus::PENDING, ReviewStatus::RE_REVIEW_REQUESTED]);
    }

    /**
     * Scope for overdue reviews.
     */
    public function scopeOverdue($query)
    {
        return $query->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->where('status', '!=', ReviewStatus::COMPLETED);
    }

    /**
     * Scope for reviews with approaching deadline.
     */
    public function scopeDeadlineApproaching($query, int $days = 3)
    {
        return $query->whereNotNull('deadline_at')
            ->where('deadline_at', '>=', now())
            ->where('deadline_at', '<=', now()->addDays($days))
            ->where('status', '!=', ReviewStatus::COMPLETED);
    }

    /**
     * Scope for specific reviewer.
     */
    public function scopeForReviewer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific round.
     */
    public function scopeForRound($query, int $round)
    {
        return $query->where('round', $round);
    }

    /**
     * Scope for current (latest) round.
     */
    public function scopeCurrentRound($query)
    {
        return $query->orderBy('round', 'desc');
    }
}
