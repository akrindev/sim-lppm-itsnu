<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeamMemberForm extends Component
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

    public function render(): View
    {
        return view('livewire.research.proposal.team-member-form');
    }
}
