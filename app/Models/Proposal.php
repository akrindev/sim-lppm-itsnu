<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Proposal extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalFactory> */
    use HasFactory;

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

    /**
     * Get all team members for the proposal.
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'proposal_user')
            ->withPivot('role', 'tasks')
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
}
