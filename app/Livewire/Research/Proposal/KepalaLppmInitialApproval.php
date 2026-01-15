<?php

namespace App\Livewire\Research\Proposal;

use App\Enums\ProposalStatus;
use App\Livewire\Actions\RequestReReviewAction;
use App\Livewire\Concerns\HasToast;
use App\Models\Proposal;
use App\Notifications\ReviewerAssignment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class KepalaLppmInitialApproval extends Component
{
    use HasToast;

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
        $isKepalaLppm = $user->hasRole(['kepala lppm']);

        if (! $isKepalaLppm) {
            $message = 'Anda tidak memiliki akses untuk menyetujui proposal';
            session()->flash('error', $message);
            $this->toastError($message);

            return;
        }

        $proposal = $this->proposal;

        if ($proposal->status !== ProposalStatus::APPROVED) {
            $message = 'Proposal tidak dalam status yang dapat disetujui';
            session()->flash('error', $message);
            $this->toastError($message);

            return;
        }

        try {
            $notificationService = app(NotificationService::class);

            // Check if this proposal has existing reviewers (resubmission after revision)
            $hasExistingReviewers = $proposal->reviewers()->exists();

            if ($hasExistingReviewers) {
                // This is a resubmission - trigger re-review workflow
                $reReviewAction = app(RequestReReviewAction::class);
                $result = $reReviewAction->execute($proposal);

                if (! $result['success']) {
                    session()->flash('error', $result['message']);
                    $this->toastError($result['message']);

                    return;
                }

                // Transition directly to UNDER_REVIEW since reviewers are already assigned
                $proposal->update([
                    'status' => ProposalStatus::UNDER_REVIEW,
                ]);

                Log::info('Kepala LPPM initial approval - Re-review triggered', [
                    'proposal_id' => $proposal->id,
                    'user_id' => $user->id,
                    'new_status' => ProposalStatus::UNDER_REVIEW->value,
                    'has_existing_reviewers' => true,
                ]);

                $message = 'Proposal berhasil disetujui dan permintaan review ulang telah dikirim ke reviewer sebelumnya.';
                session()->flash('success', $message);
                $this->toastSuccess($message);
            } else {
                // First submission - transition to WAITING_REVIEWER status
                $proposal->update([
                    'status' => ProposalStatus::WAITING_REVIEWER,
                ]);

                Log::info('Kepala LPPM initial approval', [
                    'proposal_id' => $proposal->id,
                    'user_id' => $user->id,
                    'new_status' => ProposalStatus::WAITING_REVIEWER->value,
                ]);

                // Send notification to Admin LPPM to assign reviewers
                $adminLppmUsers = $notificationService->getUsersByRole(['admin lppm']);

                if ($adminLppmUsers->isNotEmpty()) {
                    $notificationService->sendToMany($adminLppmUsers, new ReviewerAssignment($proposal, $user));
                }

                $message = 'Proposal berhasil disetujui dan siap untuk ditugaskan reviewer oleh Admin LPPM';
                session()->flash('success', $message);
                $this->toastSuccess($message);
            }

            $this->dispatch('close-modal', detail: ['modalId' => 'initialApprovalModal']);
            $this->dispatch('proposal-initial-approved', proposalId: $proposal->id);
            $this->showModal = false;
            // Refresh page to show updated status
            $this->dispatch('refresh-page');
        } catch (\Exception $e) {
            Log::error('Kepala LPPM initial approval failed', [
                'proposal_id' => $proposal->id,
                'error' => $e->getMessage(),
            ]);

            $message = 'Terjadi kesalahan saat menyetujui proposal: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toastError($message);
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.kepala-lppm-initial-approval');
    }
}
