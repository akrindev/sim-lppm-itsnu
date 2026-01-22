<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReviewLog extends Model
{
    protected $table = 'review_logs';

    protected $fillable = [
        'proposal_reviewer_id',
        'proposal_id',
        'user_id',
        'round',
        'review_notes',
        'recommendation',
        'total_score',
        'started_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'round' => 'integer',
            'total_score' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the proposal reviewer assignment.
     */
    public function proposalReviewer(): BelongsTo
    {
        return $this->belongsTo(ProposalReviewer::class);
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
     * Get scores for this log round.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(ReviewScore::class, 'proposal_reviewer_id', 'proposal_reviewer_id')
            ->where('round', $this->round);
    }

    /**
     * Get the recommendation label in Indonesian.
     */
    public function getRecommendationLabelAttribute(): string
    {
        return match ($this->recommendation) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_needed' => 'Perlu Revisi',
            default => '-',
        };
    }

    /**
     * Get the recommendation color for badges.
     */
    public function getRecommendationColorAttribute(): string
    {
        return match ($this->recommendation) {
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_needed' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Scope for specific proposal.
     */
    public function scopeForProposal($query, string $proposalId)
    {
        return $query->where('proposal_id', $proposalId);
    }

    /**
     * Scope for specific reviewer.
     */
    public function scopeForReviewer($query, string $userId)
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
     * Scope for completed reviews.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope ordered by round descending (latest first).
     */
    public function scopeLatestRound($query)
    {
        return $query->orderBy('round', 'desc');
    }
}
