<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeamMemberInvitations extends Component
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
    public function teamMembers()
    {
        return $this->proposal->teamMembers()
            ->orderByPivot('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function pendingInvitations()
    {
        return $this->teamMembers->filter(fn ($member) => $member->pivot->status === 'pending');
    }

    #[Computed]
    public function acceptedMembers()
    {
        return $this->teamMembers->filter(fn ($member) => $member->pivot->status === 'accepted');
    }

    #[Computed]
    public function rejectedMembers()
    {
        return $this->teamMembers->filter(fn ($member) => $member->pivot->status === 'rejected');
    }

    #[Computed]
    public function allAccepted(): bool
    {
        $total = $this->teamMembers->count();
        $accepted = $this->acceptedMembers->count();

        return $total > 0 && $total === $accepted;
    }

    public function acceptInvitation(): void
    {
        $user = Auth::user();
        $proposal = $this->proposal;

        $member = $proposal->teamMembers()
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            $this->dispatch('error', message: 'Anda bukan anggota proposal ini');

            return;
        }

        if ($member->pivot->status === 'accepted') {
            $this->dispatch('info', message: 'Anda sudah menerima undangan');

            return;
        }

        $proposal->teamMembers()
            ->updateExistingPivot($user->id, ['status' => 'accepted']);

        $this->dispatch('success', message: 'Undangan diterima');
        $this->dispatch('team-member-action');
    }

    public function rejectInvitation(): void
    {
        $user = Auth::user();
        $proposal = $this->proposal;

        $member = $proposal->teamMembers()
            ->where('user_id', $user->id)
            ->first();

        if (! $member) {
            $this->dispatch('error', message: 'Anda bukan anggota proposal ini');

            return;
        }

        $proposal->teamMembers()
            ->updateExistingPivot($user->id, ['status' => 'rejected']);

        $this->dispatch('success', message: 'Undangan ditolak');
        $this->dispatch('team-member-action');
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.team-member-invitations');
    }
}
