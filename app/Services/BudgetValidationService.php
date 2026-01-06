<?php

namespace App\Services;

use App\Models\BudgetCap;
use App\Models\BudgetGroup;
use Illuminate\Validation\ValidationException;

class BudgetValidationService
{
    public function validateBudgetGroupPercentages(
        array $budgetItems,
        string $proposalType,
        ?int $currentYear = null
    ): void {
        if (empty($budgetItems)) {
            return;
        }

        $currentYear ??= (int) date('Y');
        $budgetCap = BudgetCap::getCapForYear($currentYear, $proposalType);

        if ($budgetCap === null || $budgetCap <= 0) {
            throw ValidationException::withMessages([
                'budget_items' => [
                    sprintf(
                        'Batas anggaran untuk %s tahun %s belum diatur. Silakan hubungi Admin LPPM.',
                        $proposalType === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat',
                        $currentYear
                    ),
                ],
            ]);
        }

        $budgetGroups = BudgetGroup::whereNotNull('percentage')->get();
        $errors = [];

        foreach ($budgetGroups as $group) {
            $groupTotal = collect($budgetItems)
                ->where('budget_group_id', $group->id)
                ->sum(fn ($item) => (float) ($item['total'] ?? 0));

            $percentageUsed = ($groupTotal / $budgetCap) * 100;
            $allowedPercentage = (float) $group->percentage;

            if ($percentageUsed > $allowedPercentage) {
                $errors[] = sprintf(
                    'Kelompok anggaran "%s" melebihi batas %s%%. Saat ini: %s%% (Rp %s dari batas anggaran Rp %s)',
                    $group->name,
                    number_format($allowedPercentage, 2),
                    number_format($percentageUsed, 2),
                    number_format($groupTotal, 0, ',', '.'),
                    number_format($budgetCap, 0, ',', '.')
                );
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages([
                'budget_items' => $errors,
            ]);
        }
    }

    public function validateBudgetCap(
        array $budgetItems,
        string $proposalType,
        ?int $currentYear = null
    ): void {
        if (empty($budgetItems)) {
            return;
        }

        $totalBudget = $this->calculateTotalBudget($budgetItems);

        if ($totalBudget <= 0) {
            return;
        }

        $currentYear ??= (int) date('Y');
        $budgetCap = BudgetCap::getCapForYear($currentYear, $proposalType);

        if ($budgetCap === null) {
            return;
        }

        if ($totalBudget > $budgetCap) {
            $typeLabel = $proposalType === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';
            throw ValidationException::withMessages([
                'budget_items' => [
                    sprintf(
                        'Total anggaran melebihi batas maksimal untuk %s tahun %s. Batas: Rp %s, Total saat ini: Rp %s',
                        $typeLabel,
                        $currentYear,
                        number_format($budgetCap, 0, ',', '.'),
                        number_format($totalBudget, 0, ',', '.')
                    ),
                ],
            ]);
        }
    }

    public function calculateTotalBudget(array $budgetItems): float
    {
        return collect($budgetItems)->sum(fn ($item) => (float) ($item['total'] ?? 0));
    }

    public function getBudgetSummary(array $budgetItems, string $proposalType, ?int $currentYear = null): array
    {
        $currentYear ??= (int) date('Y');
        $budgetCap = BudgetCap::getCapForYear($currentYear, $proposalType) ?? 0;
        $totalBudget = $this->calculateTotalBudget($budgetItems);
        $remainingBudget = max(0, $budgetCap - $totalBudget);

        return [
            'total' => $totalBudget,
            'cap' => $budgetCap,
            'remaining' => $remainingBudget,
            'percentage_used' => $budgetCap > 0 ? ($totalBudget / $budgetCap) * 100 : 0,
        ];
    }
}
