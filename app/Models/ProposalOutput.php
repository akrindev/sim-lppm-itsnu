<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalOutput extends Model
{
    /** @use HasFactory<\Database\Factories\ProposalOutputFactory> */
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'output_year',
        'category',
        'group',
        'type',
        'target_status',
    ];

    /**
     * Get the proposal that owns the output.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
