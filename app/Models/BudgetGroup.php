<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetGroup extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    /**
     * Get all components for this budget group.
     */
    public function components(): HasMany
    {
        return $this->hasMany(BudgetComponent::class);
    }

    /**
     * Get all budget items for this group.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }
}
