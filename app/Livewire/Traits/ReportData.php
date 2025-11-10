<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ReportData
{
    protected function buildProposalQuery(string $detailableType, array $statuses): Builder
    {
        $query = \App\Models\Proposal::where('detailable_type', $detailableType)
            ->whereIn('status', $statuses);

        $query = $this->filterByUserAccess($query);

        $user = \Illuminate\Support\Facades\Auth::user();
        $roleFilter = property_exists($this, 'roleFilter') ? $this->roleFilter : '';

        if ($roleFilter && ! $user->activeHasAnyRole(['admin lppm', 'kepala lppm', 'rektor'])) {
            $query = $this->applyRoleFilter($query, $user, $roleFilter);
        }

        return $query;
    }

    protected function applySearchFilter(Builder $query, string $searchTerm): Builder
    {
        if (empty($searchTerm)) {
            return $query;
        }

        $searchPattern = '%' . $searchTerm . '%';

        return $query->where(function ($q) use ($searchPattern) {
            $q->where('title', 'LIKE', $searchPattern)
                ->orWhereHas('submitter', function ($sq) use ($searchPattern) {
                    $sq->where('name', 'LIKE', $searchPattern);
                });
        });
    }

    protected function applyRoleFilter(Builder $query, $user, string $role): Builder
    {
        if ($role === 'ketua') {
            return $query->where('submitter_id', $user->id);
        } elseif ($role === 'anggota') {
            return $query->whereHas('teamMembers', function ($teamQuery) use ($user) {
                $teamQuery->where('user_id', $user->id)
                    ->where('role', 'anggota')
                    ->where('status', 'accepted');
            });
        }

        return $query;
    }

    protected function applyYearFilter(Builder $query, string $year): Builder
    {
        if (empty($year)) {
            return $query;
        }

        return $query->whereYear('created_at', $year);
    }

    protected function eagerLoadRelations(Builder $query, array $relations = []): Builder
    {
        if (empty($relations)) {
            $relations = [
                'submitter.identity',
                'focusArea',
                'progressReports' => fn($q) => $q->latest(),
            ];
        }

        return $query->with($relations);
    }

    protected function getAvailableYears(string $detailableType, array $statuses): \Illuminate\Support\Collection
    {
        $query = $this->buildProposalQuery($detailableType, $statuses);

        return $query->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year');
    }
}
