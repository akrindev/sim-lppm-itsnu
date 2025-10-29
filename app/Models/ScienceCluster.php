<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScienceCluster extends Model
{
    /** @use HasFactory<\Database\Factories\ScienceClusterFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'level',
        'name',
    ];

    /**
     * Get the parent science cluster.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ScienceCluster::class, 'parent_id');
    }

    /**
     * Get all child science clusters.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ScienceCluster::class, 'parent_id');
    }

    /**
     * Get all proposals using this as level 1 cluster.
     */
    public function proposalsLevel1(): HasMany
    {
        return $this->hasMany(Proposal::class, 'cluster_level1_id');
    }

    /**
     * Get all proposals using this as level 2 cluster.
     */
    public function proposalsLevel2(): HasMany
    {
        return $this->hasMany(Proposal::class, 'cluster_level2_id');
    }

    /**
     * Get all proposals using this as level 3 cluster.
     */
    public function proposalsLevel3(): HasMany
    {
        return $this->hasMany(Proposal::class, 'cluster_level3_id');
    }
}
