<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Abstracts\ProposalIndex;
use Illuminate\Support\Facades\Auth;

class Index extends ProposalIndex
{
    protected function getProposalType(): string
    {
        return 'community-service';
    }

    protected function getViewName(): string
    {
        return 'livewire.community-service.proposal.index';
    }

    protected function getIndexRoute(): string
    {
        return 'community-service.proposal.index';
    }

    protected function getShowRoute(string $proposalId): string
    {
        return route('community-service.proposal.show', $proposalId);
    }

    public function deleteProposal(string $proposalId): void
    {
        $proposal = \App\Models\Proposal::findOrFail($proposalId);

        if ($proposal->detailable_type !== \App\Models\CommunityService::class) {
            abort(404);
        }

        if (! $this->canDeleteProposal($proposal)) {
            abort(403);
        }

        app(\App\Services\ProposalService::class)->deleteProposal($proposal);

        $this->dispatch('proposal-deleted');
    }

    protected function canDeleteProposal(\App\Models\Proposal $proposal): bool
    {
        return $proposal->status === 'draft'
            && $proposal->submitter_id === Auth::id();
    }
}
