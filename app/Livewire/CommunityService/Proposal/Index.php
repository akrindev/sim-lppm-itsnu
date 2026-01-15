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
