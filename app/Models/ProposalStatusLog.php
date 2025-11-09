<?php

namespace App\Models;

use App\Enums\ProposalStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalStatusLog extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalStatusLogFactory> */
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
        'user_id',
        'status_before',
        'status_after',
        'body',
        'notes',
        'at',
    ];

    protected $casts = [
        'status_before' => ProposalStatus::class,
        'status_after' => ProposalStatus::class,
        'at' => 'datetime',
    ];

    /**
     * Get the proposal that this log belongs to.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the user who performed this status change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get logs for a specific proposal, ordered by date.
     */
    public function scopeForProposal($query, $proposalId)
    {
        return $query->where('proposal_id', $proposalId)
            ->orderBy('at', 'desc');
    }

    /**
     * Scope to get logs by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get logs within a date range.
     */
    public function scopeWithinDateRange($query, $startDate, $endDate = null)
    {
        $query->where('at', '>=', $startDate);

        if ($endDate) {
            $query->where('at', '<=', $endDate);
        }

        return $query;
    }
}
