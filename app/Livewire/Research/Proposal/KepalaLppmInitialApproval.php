<?php

namespace App\Livewire\Research\Proposal;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class KepalaLppmInitialApproval extends Component
{
    public string $proposalId = '';

    public bool $showModal = false;

    public function mount(string $proposalId): void
    {
        $this->proposalId = $proposalId;
    }

    #[Computed]
    public function proposal()
    {
        return Proposal::find($this->proposalId);
    }

    #[Computed]
    public function canApprove(): bool
    {
        $user = Auth::user();
        $isKepalaLppm = $user->hasRole(['kepala lppm', 'rektor']);
        $proposal = $this->proposal;

        return $isKepalaLppm && $proposal->status === ProposalStatus::APPROVED;
    }

    public function openApprovalModal(): void
    {
        $this->showModal = true;
        $this->dispatch('open-initial-approval-modal');
    }

    public function cancelApproval(): void
    {
        $this->showModal = false;
    }

    #[On('confirm-initial-approval')]
    public function approve(): void
    {
        $user = Auth::user();
        $isKepalaLppm = $user->hasRole(['kepala lppm', 'rektor']);

        if (! $isKepalaLppm) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menyetujui proposal');

            return;
        }

        $proposal = $this->proposal;

        if ($proposal->status !== ProposalStatus::APPROVED) {
            session()->flash('error', 'Proposal tidak dalam status yang dapat disetujui');

            return;
        }

        try {
            // Transition to UNDER_REVIEW status (ready for reviewer assignment)
            $proposal->update([
                'status' => ProposalStatus::UNDER_REVIEW,
            ]);

            Log::info('Kepala LPPM initial approval', [
                'proposal_id' => $proposal->id,
                'user_id' => $user->id,
                'new_status' => ProposalStatus::UNDER_REVIEW->value,
            ]);

            // TODO: Send notification to Admin LPPM to assign reviewers

            session()->flash('success', 'Proposal berhasil disetujui dan siap untuk ditugaskan reviewer');
            $this->dispatch('close-initial-approval-modal');
            $this->dispatch('proposal-initial-approved', proposalId: $proposal->id);
            $this->showModal = false;
        } catch (\Exception $e) {
            Log::error('Kepala LPPM initial approval failed', [
                'proposal_id' => $proposal->id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Terjadi kesalahan saat menyetujui proposal: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.kepala-lppm-initial-approval');
    }
}
