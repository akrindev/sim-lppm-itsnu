<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CommunityService extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'partner_issue_summary',
        'solution_offered',
    ];

    /**
     * Get the partner for the community service.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the proposal that owns the community service details.
     */
    public function proposal(): MorphOne
    {
        return $this->morphOne(Proposal::class, 'detailable');
    }
}
