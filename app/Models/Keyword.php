<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    /** @use HasFactory<\Database\Factories\KeywordFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all proposals that have this keyword.
     */
    public function proposals(): BelongsToMany
    {
        return $this->belongsToMany(Proposal::class, 'proposal_keyword');
    }
}
