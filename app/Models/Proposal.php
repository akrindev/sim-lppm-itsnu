<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalFactory> */
    use HasFactory, HasUuids;

    /**
     * The type of the auto-incrementing ID's primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    protected $fillable = [
        'title',
        'submitter_id',
        'detailable_id',
        'detailable_type',
        'research_scheme_id',
        'focus_area_id',
        'theme_id',
        'topic_id',
        'national_priority_id',
        'cluster_level1_id',
        'cluster_level2_id',
        'cluster_level3_id',
        'sbk_value',
        'duration_in_years',
        'start_year',
        'summary',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sbk_value' => 'decimal:2',
            'duration_in_years' => 'integer',
            'status' => ProposalStatus::class,
        ];
    }

    /**
     * Get the user who submitted the proposal.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    /**
     * Alias for submitter relationship.
     */
    public function user(): BelongsTo
    {
        return $this->submitter();
    }

    /**
     * Get the detailable model (Research or CommunityService).
     */
    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the research scheme for the proposal.
     */
    public function researchScheme(): BelongsTo
    {
        return $this->belongsTo(ResearchScheme::class);
    }

    /**
     * Get the focus area for the proposal.
     */
    public function focusArea(): BelongsTo
    {
        return $this->belongsTo(FocusArea::class);
    }

    /**
     * Get the theme for the proposal.
     */
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    /**
     * Get the topic for the proposal.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Get the national priority for the proposal.
     */
    public function nationalPriority(): BelongsTo
    {
        return $this->belongsTo(NationalPriority::class);
    }

    /**
     * Get the level 1 science cluster for the proposal.
     */
    public function clusterLevel1(): BelongsTo
    {
        return $this->belongsTo(ScienceCluster::class, 'cluster_level1_id');
    }

    /**
     * Get the level 2 science cluster for the proposal.
     */
    public function clusterLevel2(): BelongsTo
    {
        return $this->belongsTo(ScienceCluster::class, 'cluster_level2_id');
    }

    /**
     * Get the level 3 science cluster for the proposal.
     */
    public function clusterLevel3(): BelongsTo
    {
        return $this->belongsTo(ScienceCluster::class, 'cluster_level3_id');
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'proposal_user')
            ->orderByPivot('created_at', 'desc')
            ->withPivot('role', 'tasks', 'status')
            ->withTimestamps();
    }

    /**
     * Get all keywords for the proposal.
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'proposal_keyword')
            ->withTimestamps();
    }

    /**
     * Get all outputs for the proposal.
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(ProposalOutput::class);
    }

    /**
     * Get all budget items for the proposal.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    /**
     * Get all partners for the proposal.
     */
    public function partners(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'proposal_partner')
            ->withTimestamps();
    }

    /**
     * Get all activity schedules for the proposal.
     */
    public function activitySchedules(): HasMany
    {
        return $this->hasMany(ActivitySchedule::class);
    }

    /**
     * Get all research stages for the proposal.
     */
    public function researchStages(): HasMany
    {
        return $this->hasMany(ResearchStage::class);
    }

    /**
     * Get all reviewers for the proposal.
     */
    public function reviewers(): HasMany
    {
        return $this->hasMany(ProposalReviewer::class)->orderBy('round', 'desc')->orderBy('assigned_at', 'desc');
    }

    /**
     * Get all progress reports for the proposal.
     */
    public function progressReports(): HasMany
    {
        return $this->hasMany(ProgressReport::class);
    }

    /**
     * Get all daily notes for the proposal.
     */
    public function dailyNotes(): HasMany
    {
        return $this->hasMany(DailyNote::class);
    }

    /**
     * Get all status change logs for the proposal.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(ProposalStatusLog::class)->orderBy('at', 'desc');
    }

    /**
     * Check if all team members have accepted the invitation.
     */
    public function allTeamMembersAccepted(): bool
    {
        $totalMembers = $this->teamMembers()->count();
        if ($totalMembers === 0) {
            return true;
        }

        $acceptedMembers = $this->teamMembers()
            ->wherePivot('status', 'accepted')
            ->count();

        return $totalMembers === $acceptedMembers;
    }

    /**
     * Check if all reviewers have completed their reviews.
     */
    public function allReviewsCompleted(): bool
    {
        $totalReviewers = $this->reviewers()->count();
        if ($totalReviewers === 0) {
            return false;
        }

        $completedReviews = $this->reviewers()
            ->where('status', \App\Enums\ReviewStatus::COMPLETED)
            ->count();

        return $totalReviewers === $completedReviews;
    }

    /**
     * Get pending team member invitations.
     */
    public function pendingTeamMembers()
    {
        return $this->teamMembers()
            ->wherePivot('status', 'pending');
    }

    /**
     * Get pending reviewer assignments.
     */
    public function pendingReviewers()
    {
        return $this->reviewers()
            ->whereIn('status', [
                \App\Enums\ReviewStatus::PENDING,
                \App\Enums\ReviewStatus::RE_REVIEW_REQUESTED,
            ]);
    }

    /**
     * Get all pending team members (anggota who haven't accepted).
     */
    public function getPendingTeamMembers()
    {
        return $this->teamMembers()
            ->wherePivot('status', '!=', 'accepted')
            ->get();
    }

    /**
     * Get all pending reviewers (who haven't completed their review).
     */
    public function getPendingReviewers()
    {
        return $this->reviewers()
            ->where('status', '!=', \App\Enums\ReviewStatus::COMPLETED)
            ->get();
    }

    /**
     * Check if all reviewers have completed their reviews.
     */
    public function allReviewersCompleted(): bool
    {
        $totalReviewers = $this->reviewers()->count();
        $completedReviewers = $this->reviewers()
            ->where('status', \App\Enums\ReviewStatus::COMPLETED)
            ->count();

        return $totalReviewers > 0 && $totalReviewers === $completedReviewers;
    }

    /**
     * Check if proposal can be approved (all reviewers completed).
     */
    public function canBeApproved(): bool
    {
        return $this->allReviewersCompleted();
    }
}
