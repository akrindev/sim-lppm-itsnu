<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Abstracts\ProposalShow;

class Show extends ProposalShow
{
    protected function getProposalType(): string
    {
        return 'community-service';
    }

    protected function getIndexRoute(): string
    {
        return 'community-service.proposal.index';
    }

    protected function getEditRoute(string $proposalId): string
    {
        return route('community-service.proposal.edit', $proposalId);
    }

    protected function getReviewRoute(string $proposalId): string
    {
        return route('community-service.proposal.review', $proposalId);
    }

    protected function getViewName(): string
    {
        return 'livewire.community-service.proposal.show';
    }
}
