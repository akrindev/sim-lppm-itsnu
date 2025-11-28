<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BudgetCap model represents year-based budget limits for proposals.
 *
 * Each year can have different budget caps for research and community service.
 * Used to enforce maximum budget constraints when creating/editing proposals.
 */
class BudgetCap extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetCapFactory> */
    use HasFactory;

    protected $fillable = [
        'year',
        'research_budget_cap',
        'community_service_budget_cap',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'research_budget_cap' => 'decimal:2',
            'community_service_budget_cap' => 'decimal:2',
        ];
    }

    /**
     * Get the budget cap for a specific year and proposal type.
     *
     * @param  string  $type  'research' or 'community_service'
     */
    public static function getCapForYear(int $year, string $type): ?float
    {
        $budgetCap = self::where('year', $year)->first();

        if (! $budgetCap) {
            return null;
        }

        return $type === 'research'
            ? (float) $budgetCap->research_budget_cap
            : (float) $budgetCap->community_service_budget_cap;
    }
}
