<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CommunityService extends Model
{
    /** @use HasFactory<\Database\Factories\CommunityServiceFactory> */
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
