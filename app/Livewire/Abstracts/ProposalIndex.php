<?php

namespace App\Livewire\Abstracts;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Traits\WithFilters;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

abstract class ProposalIndex extends Component
{
    use HasToast;
    use WithFilters;
    use WithPagination;

    private ?ProposalService $proposalService = null;

    public function mount(): void
    {
        // If user is a regular 'dosen' (not an admin/leader role), default to 'ketua' view
        if (! Auth::user()->activeHasAnyRole(['admin lppm', 'kepala lppm', 'rektor', 'dekan'])) {
            $this->roleFilter = 'ketua';
        }
    }

    private function proposalService(): ProposalService
    {
        return $this->proposalService ??= app(ProposalService::class);
    }

    abstract protected function getProposalType(): string;

    abstract protected function getViewName(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getShowRoute(string $proposalId): string;

    #[Computed]
    public function proposals()
    {
        return $this->proposalService()->getProposalsWithFilters([
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
        return $this->proposalService()->getProposalStatistics([
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
        return $this->proposalService()->getAvailableYears(
            $this->getProposalType()
        );
    }

    public function render()
    {
        return view($this->getViewName());
    }
}
