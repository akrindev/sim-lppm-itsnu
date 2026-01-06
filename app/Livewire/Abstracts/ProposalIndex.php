<?php

namespace App\Livewire\Abstracts;

use App\Livewire\Traits\WithFilters;
use App\Services\ProposalService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

abstract class ProposalIndex extends Component
{
    use WithFilters;
    use WithPagination;

    protected ProposalService $proposalService;

    public function mount(): void
    {
        $this->proposalService = app(ProposalService::class);
    }

    abstract protected function getProposalType(): string;

    abstract protected function getViewName(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getShowRoute(string $proposalId): string;

    #[Computed]
    public function proposals()
    {
        return $this->proposalService->getProposalsWithFilters([
            'search' => $this->search,
            'status' => $this->statusFilter,
            'year' => $this->yearFilter,
            'role' => $this->roleFilter,
            'type' => $this->getProposalType(),
        ]);
    }

    #[Computed]
    public function statusStats()
    {
        return $this->proposalService->getProposalStatistics([
            'type' => $this->getProposalType(),
        ]);
    }

    #[Computed]
    public function typeStats()
    {
        return [];
    }

    #[Computed]
    public function availableYears()
    {
        return $this->proposalService->getAvailableYears(
            $this->getProposalType()
        );
    }

    public function render()
    {
        return view($this->getViewName());
    }
}
