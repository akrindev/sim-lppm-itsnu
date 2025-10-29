<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeamMemberInvitation extends Component
{
    public Proposal $proposal;

    #[Computed]
    public function pendingMembers()
    {
        return $this->proposal->teamMembers()
            ->wherePivot('status', 'pending')
            ->get();
    }

    #[Computed]
    public function acceptedMembers()
    {
        return $this->proposal->teamMembers()
            ->wherePivot('status', 'accepted')
            ->get();
    }

    #[Computed]
    public function rejectedMembers()
    {
        return $this->proposal->teamMembers()
            ->wherePivot('status', 'rejected')
            ->get();
    }

    #[Computed]
    public function allAccepted()
    {
        return $this->proposal->allTeamMembersAccepted();
    }

    public function acceptInvitation(): void
    {
        $user = Auth::user();

        // Find the team member record
        $teamMember = $this->proposal->teamMembers()
            ->where('user_id', $user->id)
            ->first();

        if ($teamMember) {
            $teamMember->pivot->update(['status' => 'accepted']);
            $this->dispatch('team-member-accepted', proposalId: $this->proposal->id);
            session()->flash('success', 'Anda telah menerima undangan menjadi anggota proposal ini.');
        }
    }

    public function rejectInvitation(): void
    {
        $user = Auth::user();

        $teamMember = $this->proposal->teamMembers()
            ->where('user_id', $user->id)
            ->first();

        if ($teamMember) {
            $teamMember->pivot->update(['status' => 'rejected']);
            $this->dispatch('team-member-rejected', proposalId: $this->proposal->id);
            session()->flash('warning', 'Anda telah menolak undangan untuk proposal ini.');
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.team-member-invitation');
    }
}
