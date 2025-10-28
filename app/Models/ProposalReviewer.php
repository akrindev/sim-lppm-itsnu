<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalReviewer extends Model
{
    protected $table = 'proposal_reviewer';

    protected $fillable = [
        'proposal_id',
        'user_id',
        'status',
        'review_notes',
        'recommendation',
    ];

    protected $casts = [
        'status' => 'string',
        'recommendation' => 'string',
    ];

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
     * Check if review is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark review as completed.
     */
    public function complete(string $reviewNotes, string $recommendation): void
    {
        $this->update([
            'status' => 'completed',
            'review_notes' => $reviewNotes,
            'recommendation' => $recommendation,
        ]);
    }
}
