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

    public string $confirmingDeleteProposalId = '';

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

    public function confirmDeleteProposal(string $proposalId): void
    {
        $this->confirmingDeleteProposalId = $proposalId;
    }

    public function cancelDeleteProposal(): void
    {
        $this->confirmingDeleteProposalId = '';
    }

    public function deleteProposal(string $proposalId): void
    {
        $proposal = \App\Models\Proposal::findOrFail($proposalId);

        if ($proposal->detailable_type !== match ($this->getProposalType()) {
            'research' => \App\Models\Research::class,
            'community-service' => \App\Models\CommunityService::class,
        }) {
            abort(404);
        }

        if (! $this->canDeleteProposal($proposal)) {
            abort(403);
        }

        app(\App\Services\ProposalService::class)->deleteProposal($proposal);

        $this->dispatch('proposal-deleted');
        $this->cancelDeleteProposal();
    }

    abstract protected function canDeleteProposal(\App\Models\Proposal $proposal): bool;
}
