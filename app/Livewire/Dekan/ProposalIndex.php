<?php

namespace App\Livewire\Dekan;

use App\Enums\ProposalStatus;
use App\Livewire\Actions\DekanApprovalAction;
use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProposalIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = 'all';

    #[Url]
    public string $yearFilter = '';

    public string $selectedProposalId = '';

    public string $approvalDecision = '';

    public string $approvalNotes = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = 'all';
        $this->yearFilter = '';
        $this->resetPage();
    }

    public function openApprovalModal(string $proposalId, string $decision): void
    {
        $this->selectedProposalId = $proposalId;
        $this->approvalDecision = $decision;
        $this->approvalNotes = '';

        $this->dispatch('open-approval-modal');
    }

    public function cancelApproval(): void
    {
        $this->selectedProposalId = '';
        $this->approvalDecision = '';
        $this->approvalNotes = '';
    }

    #[On('confirm-approval')]
    public function processApproval(): void
    {
        if (! $this->selectedProposalId || ! $this->approvalDecision) {
            session()->flash('error', 'Data tidak valid');

            return;
        }

        $proposal = Proposal::find($this->selectedProposalId);

        if (! $proposal) {
            session()->flash('error', 'Proposal tidak ditemukan');
            $this->cancelApproval();

            return;
        }

        // Execute approval action
        $action = new DekanApprovalAction;
        $result = $action->execute($proposal, $this->approvalDecision, $this->approvalNotes);

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->dispatch('close-approval-modal');
            $this->cancelApproval();
            $this->resetPage();
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render(): View
    {
        return view('livewire.dekan.proposal-index');
    }

    #[Computed]
    public function proposals()
    {
        $query = Proposal::query()
            ->where('status', ProposalStatus::SUBMITTED);

        // TODO: Filter by faculty/prodi if needed based on Dekan's assignment
        // For now, show all submitted proposals

        return $query
            ->with(['submitter', 'detailable', 'focusArea', 'researchScheme'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('summary', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $detailableType = $this->typeFilter === 'research'
                    ? \App\Models\Research::class
                    : \App\Models\CommunityService::class;
                $query->where('detailable_type', $detailableType);
            })
            ->when($this->yearFilter, function ($query) {
                $query->whereYear('created_at', $this->yearFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    #[Computed]
    public function statusStats(): array
    {
        return [
            'all' => Proposal::where('status', ProposalStatus::SUBMITTED)->count(),
            'research' => Proposal::where('status', ProposalStatus::SUBMITTED)
                ->where('detailable_type', \App\Models\Research::class)
                ->count(),
            'community_service' => Proposal::where('status', ProposalStatus::SUBMITTED)
                ->where('detailable_type', \App\Models\CommunityService::class)
                ->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        $years = Proposal::where('status', ProposalStatus::SUBMITTED)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return $years;
    }
}
