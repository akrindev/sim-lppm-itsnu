<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NationalPriority extends Model
{
    /** @use HasFactory<\Database\Factories\NationalPriorityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all proposals with this national priority.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
