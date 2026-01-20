<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MacroResearchGroup extends Model
{
    /** @use HasFactory<\Database\Factories\MacroResearchGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all research associated with this macro research group.
     */
    public function research(): HasMany
    {
        return $this->hasMany(Research::class);
    }

    /**
     * Get all community services associated with this macro research group.
     */
    public function communityServices(): HasMany
    {
        return $this->hasMany(CommunityService::class);
    }
}
