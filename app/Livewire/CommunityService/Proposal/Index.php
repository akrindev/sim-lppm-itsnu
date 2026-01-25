<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Abstracts\ProposalIndex;

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
}
