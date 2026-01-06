<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Abstracts\ProposalCreate;

class Create extends ProposalCreate
{
    protected function getProposalType(): string
    {
        return 'community-service';
    }

    protected function getIndexRoute(): string
    {
        return 'community-service.proposal.index';
    }

    protected function getShowRoute(string $proposalId): string
    {
        return route('community-service.proposal.show', $proposalId);
    }

    protected function getStep2Rules(): array
    {
        return [
            'form.background' => 'required|string|min:50',
            'form.methodology' => 'required|string|min:50',
            'form.partner_id' => 'nullable|exists:partners,id',
            'form.partner_issue_summary' => 'nullable|string|min:50',
            'form.solution_offered' => 'nullable|string|min:50',
            'form.author_tasks' => 'required|string',
        ];
    }
}
