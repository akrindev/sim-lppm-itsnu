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
        return [
            'form.macro_research_group_id' => 'required|exists:macro_research_groups,id',
            'form.substance_file' => 'nullable|file|mimes:pdf|max:10240',
            'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                $wajibCount = collect($value)->where('category', 'Wajib')->count();
                if ($wajibCount < 1) {
                    $fail('Minimal harus ada 1 luaran wajib untuk proposal penelitian.');
                }
            }],
        ];
    }
}
