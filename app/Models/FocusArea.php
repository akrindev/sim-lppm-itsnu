<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FocusArea extends Model
{
    /** @use HasFactory<\Database\Factories\FocusAreaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all themes in this focus area.
     */
    public function themes(): HasMany
    {
        return $this->hasMany(Theme::class);
    }

    /**
     * Get all proposals in this focus area.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
