<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Abstracts\ProposalCreate;

class Create extends ProposalCreate
{
    protected function getProposalType(): string
    {
        return 'research';
    }

    protected function getIndexRoute(): string
    {
        return 'research.proposal.index';
    }

    protected function getShowRoute(string $proposalId): string
    {
        return route('research.proposal.show', $proposalId);
    }

    protected function getStep2Rules(): array
    {
        $rules = [
            'form.background' => 'required|string|min:50',
            'form.methodology' => 'required|string|min:50',
            'form.state_of_the_art' => 'required|string|min:50',
            'form.author_tasks' => 'required|string',
            'form.macro_research_group_id' => 'required|exists:macro_research_groups,id',
            'form.substance_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];

        $rules['form.tkt_type'] = 'nullable|string';
        $rules['form.tkt_results'] = 'nullable|array';
        $rules['form.roadmap_data'] = 'nullable|array';

        return $rules;
    }
}
