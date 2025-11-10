<?php

declare(strict_types=1);

namespace App\Livewire\Abstracts;

use App\Enums\ProposalStatus;
use App\Livewire\Traits\ReportFilters;
use App\Livewire\Traits\ReportAuthorization;
use App\Livewire\Traits\ReportData;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

abstract class ReportIndex extends Component
{
    use ReportFilters;
    use ReportAuthorization;
    use ReportData;

    abstract protected function getDetailableType(): string;

    abstract protected function getStatusFilter(): array;

    abstract protected function getViewName(): string;

    #[Computed]
    public function proposals(): Collection
    {
        $query = $this->buildProposalQuery(
            $this->getDetailableType(),
            $this->getStatusFilter()
        );

        $query = $this->eagerLoadRelations($query, $this->getRelations());

        $query = $this->applySearchFilter($query, $this->search);

        $query = $this->applyYearFilter($query, $this->selectedYear);

        return $query->latest()->get();
    }

    #[Computed]
    public function availableYears()
    {
        return $this->getAvailableYears(
            $this->getDetailableType(),
            $this->getStatusFilter()
        );
    }

    protected function getRelations(): array
    {
        $baseRelations = [
            'submitter.identity',
            'focusArea',
            'progressReports' => function ($q) {
                if ($this->isFinalReport()) {
                    $q->finalReports()->latest();
                } else {
                    $q->latest();
                }
            },
        ];

        return $baseRelations;
    }

    protected function isFinalReport(): bool
    {
        return str_contains($this->getViewName(), 'final');
    }

    public function render()
    {
        return view($this->getViewName());
    }
}
