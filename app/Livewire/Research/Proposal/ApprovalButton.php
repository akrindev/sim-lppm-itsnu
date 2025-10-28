<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ApprovalButton extends Component
{
    public string $proposalId = '';

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
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);
        $proposal = $this->proposal;

        return $isAdmin && $proposal->status === 'reviewed' && $proposal->allReviewsCompleted();
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

    public function approve(): void
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        if (! $isAdmin) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk approve proposal');
            return;
        }

        $proposal = $this->proposal;

        if ($proposal->status !== 'reviewed') {
            $this->dispatch('error', message: 'Proposal harus dalam status reviewed untuk diapprove');
            return;
        }

        if (! $proposal->allReviewsCompleted()) {
            $this->dispatch('error', message: 'Semua reviewer harus menyelesaikan review terlebih dahulu');
            return;
        }

        try {
            $proposal->update(['status' => 'approved']);

            $this->dispatch('success', message: 'Proposal berhasil disetujui');
            $this->dispatch('proposal-approved', proposalId: $proposal->id);
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal approve proposal: ' . $e->getMessage());
        }
    }

    public function reject(): void
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        if (! $isAdmin) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk reject proposal');
            return;
        }

        $proposal = $this->proposal;

        if ($proposal->status !== 'reviewed' && $proposal->status !== 'under_review') {
            $this->dispatch('error', message: 'Hanya proposal yang belum diapprove yang bisa ditolak');
            return;
        }

        try {
            $proposal->update(['status' => 'rejected']);

            $this->dispatch('warning', message: 'Proposal ditolak');
            $this->dispatch('proposal-rejected', proposalId: $proposal->id);
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal reject proposal: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.approval-button');
    }
}
