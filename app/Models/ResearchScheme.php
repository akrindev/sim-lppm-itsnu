<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchScheme extends Model
{
    /** @use HasFactory<\Database\Factories\ResearchSchemeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'strata',
    ];

    /**
     * Get all proposals using this research scheme.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
