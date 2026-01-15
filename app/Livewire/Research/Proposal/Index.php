<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Abstracts\ProposalIndex;
use Illuminate\Support\Facades\Auth;

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

    protected function canDeleteProposal(\App\Models\Proposal $proposal): bool
    {
        if ($proposal->status !== \App\Enums\ProposalStatus::DRAFT) {
            return false;
        }

        if (Auth::user()->hasRole('admin lppm')) {
            return true;
        }

        return $proposal->submitter_id === Auth::id();
    }
}
