<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Abstracts\ProposalIndex;

class Index extends ProposalIndex
{
    protected function getProposalType(): string
    {
        return 'research';
    }

    protected function getViewName(): string
    {
        return 'livewire.research.proposal.index';
    }

    protected function getIndexRoute(): string
    {
        return 'research.proposal.index';
    }

    protected function getShowRoute(string $proposalId): string
    {
        return route('research.proposal.show', $proposalId);
    }
}
