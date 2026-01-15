<?php

namespace App\Livewire\Abstracts;

use App\Livewire\Traits\WithFilters;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

abstract class ProposalIndex extends Component
{
    use WithFilters;
    use WithPagination;

    public string $confirmingDeleteProposalId = '';

    private ?ProposalService $proposalService = null;

    public function mount(): void
    {
        // If user is a regular 'dosen' (not an admin/leader role), default to 'ketua' view
        if (! Auth::user()->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor', 'dekan'])) {
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

    public function confirmDeleteProposal(string $proposalId): void
    {
        $this->confirmingDeleteProposalId = $proposalId;
        $this->dispatch('open-modal', modalId: 'deleteProposalModal');
    }

    public function cancelDeleteProposal(): void
    {
        $this->confirmingDeleteProposalId = '';
    }

    public function prepareConfirmation(): void
    {
        // Required by modal-confirmation component
    }

    public function cleanupConfirmation(): void
    {
        $this->cancelDeleteProposal();
    }

    public function deleteProposal(?string $proposalId = null): void
    {
        $id = $proposalId ?: $this->confirmingDeleteProposalId;
        $proposal = \App\Models\Proposal::findOrFail($id);

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

        session()->flash('success', 'Proposal berhasil dihapus.');
        $this->dispatch('proposal-deleted');
        $this->cancelDeleteProposal();
    }

    abstract protected function canDeleteProposal(\App\Models\Proposal $proposal): bool;
}
