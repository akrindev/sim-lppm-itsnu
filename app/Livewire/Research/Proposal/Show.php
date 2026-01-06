<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Abstracts\ProposalShow;

class Show extends ProposalShow
{
    protected function getProposalType(): string
    {
        return 'research';
    }

    protected function getIndexRoute(): string
    {
        return 'research.proposal.index';
    }

    protected function getEditRoute(string $proposalId): string
    {
        return route('research.proposal.edit', $proposalId);
    }

    protected function getReviewRoute(string $proposalId): string
    {
        return route('research.proposal.review', $proposalId);
    }

    protected function getViewName(): string
    {
        return 'livewire.research.proposal.show';
    }
}
