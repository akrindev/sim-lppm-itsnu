<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetComponent extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetComponentFactory> */
    use HasFactory;

    protected $fillable = [
        'budget_group_id',
        'code',
        'name',
        'description',
    ];

    /**
     * Get the budget group that owns the component.
     */
    public function budgetGroup(): BelongsTo
    {
        return $this->belongsTo(BudgetGroup::class);
    }

    /**
     * Get all budget items for this component.
     */
    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }
}
