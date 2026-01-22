<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewScore extends Model
{
    protected $fillable = [
        'proposal_reviewer_id',
        'review_criteria_id',
        'acuan',
        'score',
        'weight_snapshot',
        'value',
        'round',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'weight_snapshot' => 'integer',
            'value' => 'integer',
            'round' => 'integer',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(ProposalReviewer::class, 'proposal_reviewer_id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(ReviewCriteria::class, 'review_criteria_id');
    }
}
