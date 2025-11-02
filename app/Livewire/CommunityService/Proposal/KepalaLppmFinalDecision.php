<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class KepalaLppmFinalDecision extends Component
{
    public string $proposalId = '';

    public string $decision = '';

    public string $notes = '';

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
    public function canDecide(): bool
    {
        $user = Auth::user();
        $isKepalaLppm = $user->hasRole(['kepala lppm', 'rektor']);
        $proposal = $this->proposal;

        return $isKepalaLppm && $proposal->status === ProposalStatus::REVIEWED && $proposal->allReviewsCompleted();
    }

    #[Computed]
    public function pendingReviewers()
    {
        return $this->proposal->pendingReviewers()->get();
    }

    #[Computed]
    public function reviewSummary()
    {
        return $this->proposal->reviewers()
            ->select('recommendation')
            ->get()
            ->groupBy('recommendation')
            ->map->count();
    }

    public function openDecisionModal(string $decision): void
    {
        $this->decision = $decision;
        $this->notes = '';
        $this->dispatch('open-final-decision-modal');
    }

    public function cancelDecision(): void
    {
        $this->decision = '';
        $this->notes = '';
    }

    #[On('confirm-final-decision')]
    public function processDecision(): void
    {
        $user = Auth::user();
        $isKepalaLppm = $user->hasRole(['kepala lppm', 'rektor']);

        if (! $isKepalaLppm) {
            session()->flash('error', 'Anda tidak memiliki akses untuk membuat keputusan');

            return;
        }

        $proposal = $this->proposal;

        if ($proposal->status !== ProposalStatus::REVIEWED) {
            session()->flash('error', 'Proposal tidak dalam status yang dapat diputuskan');

            return;
        }

        if (! $proposal->allReviewsCompleted()) {
            session()->flash('error', 'Semua reviewer harus menyelesaikan review terlebih dahulu');

            return;
        }

        if (! in_array($this->decision, ['completed', 'revision_needed'])) {
            session()->flash('error', 'Keputusan tidak valid');

            return;
        }

        try {
            $newStatus = $this->decision === 'completed'
                ? ProposalStatus::COMPLETED
                : ProposalStatus::REVISION_NEEDED;

            // Validate transition
            if (! $proposal->status->canTransitionTo($newStatus)) {
                session()->flash('error', 'Transisi status tidak diizinkan');

                return;
            }

            // Update proposal status
            $proposal->update([
                'status' => $newStatus,
            ]);

            Log::info('Kepala LPPM final decision', [
                'proposal_id' => $proposal->id,
                'user_id' => $user->id,
                'decision' => $this->decision,
                'new_status' => $newStatus->value,
                'notes' => $this->notes,
            ]);

            // TODO: Send notification to submitter

            $message = $this->decision === 'completed'
                ? 'Proposal berhasil disetujui dan selesai.'
                : 'Proposal memerlukan perbaikan dan dikembalikan ke pengusul.';

            session()->flash('success', $message);
            $this->dispatch('close-final-decision-modal');
            $this->dispatch('proposal-final-decided', proposalId: $proposal->id, decision: $this->decision);
            $this->cancelDecision();
        } catch (\Exception $e) {
            Log::error('Kepala LPPM final decision failed', [
                'proposal_id' => $proposal->id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Terjadi kesalahan saat membuat keputusan: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.kepala-lppm-final-decision');
    }
}
