<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySchedule extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'activity_name',
        'year',
        'start_month',
        'end_month',
    ];

    /**
     * Get the proposal that owns the activity schedule.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
