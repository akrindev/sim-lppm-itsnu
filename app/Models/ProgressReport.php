<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgressReport extends Model
{
    /** @use HasFactory<\Database\Factories\ProgressReportFactory> */
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
        'proposal_id',
        'summary_update',
        'reporting_year',
        'reporting_period',
        'status',
        'submitted_by',
        'submitted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Get the proposal that owns the progress report.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the user who submitted the report.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get all keywords for the progress report.
     */
    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(Keyword::class, 'progress_report_keyword')
            ->withTimestamps();
    }

    /**
     * Get all mandatory outputs for the progress report.
     */
    public function mandatoryOutputs(): HasMany
    {
        return $this->hasMany(MandatoryOutput::class);
    }

    /**
     * Get all additional outputs for the progress report.
     */
    public function additionalOutputs(): HasMany
    {
        return $this->hasMany(AdditionalOutput::class);
    }

    /**
     * Check if this is a final report.
     */
    public function isFinalReport(): bool
    {
        return $this->reporting_period === 'final';
    }

    /**
     * Scope a query to only include final reports.
     */
    public function scopeFinalReports($query)
    {
        return $query->where('reporting_period', 'final');
    }

    /**
     * Scope a query to only include progress reports (exclude final).
     */
    public function scopeProgressReports($query)
    {
        return $query->whereIn('reporting_period', ['semester_1', 'semester_2', 'annual']);
    }
}
