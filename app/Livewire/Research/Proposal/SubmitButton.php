<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitButton extends Component
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
    public function canSubmit(): bool
    {
        $proposal = $this->proposal;

        return $proposal->status === 'draft'
            && $proposal->allTeamMembersAccepted()
            && Auth::id() === $proposal->submitter_id;
    }

    #[Computed]
    public function pendingMembers()
    {
        return $this->proposal->pendingTeamMembers()->get();
    }

    #[Computed]
    public function rejectedMembers()
    {
        return $this->proposal->teamMembers()
            ->wherePivot('status', 'rejected')
            ->get();
    }

    public function submit(): void
    {
        $proposal = $this->proposal;

        if ($proposal->status !== 'draft') {
            $this->dispatch('error', message: 'Proposal harus dalam status draft untuk disubmit');
            return;
        }

        if (! $proposal->allTeamMembersAccepted()) {
            $this->dispatch('error', message: 'Semua anggota tim harus menerima undangan terlebih dahulu');
            return;
        }

        if (Auth::id() !== $proposal->submitter_id) {
            $this->dispatch('error', message: 'Hanya pengaju dapat mensubmit proposal');
            return;
        }

        try {
            $proposal->update(['status' => 'submitted']);

            $this->dispatch('success', message: 'Proposal berhasil disubmit');
            $this->dispatch('proposal-submitted', proposalId: $proposal->id);
            $this->redirect(route('research.proposal.show', $proposal->id));
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Gagal submit proposal: ' . $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.submit-button');
    }
}
