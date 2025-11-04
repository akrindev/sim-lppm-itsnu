<?php

namespace App\Livewire\CommunityService\Proposal;

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
        return Proposal::with([
            'teamMembers.identity',
        ])->find($this->proposalId);
    }

    #[Computed]
    public function teamMembers()
    {
        return $this->proposal->teamMembers
            ->sortByDesc('pivot.created_at')
            ->values();
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal.team-member-form');
    }
}
