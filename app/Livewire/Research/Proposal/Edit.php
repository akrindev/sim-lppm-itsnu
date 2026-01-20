<?php

namespace App\Livewire\Research\Proposal;

use App\Constants\ProposalConstants;
use App\Livewire\Abstracts\ProposalCreate;

class Edit extends ProposalCreate
{
    public string $componentId = '';

    public function mount(?string $proposalId = null, ?\App\Models\Proposal $proposal = null): void
    {
        if ($proposalId === null && $proposal === null) {
            abort(404);
        }

        parent::mount($proposalId, $proposal);
        $this->componentId = $proposal ? $proposal->id : $proposalId;
    }

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
            // 'form.background' => 'required|string|min:50',
            // 'form.methodology' => 'required|string|min:50',
            // 'form.state_of_the_art' => 'required|string|min:50',
            'form.author_tasks' => 'required|string',
            'form.macro_research_group_id' => 'required|exists:macro_research_groups,id',
            'form.substance_file' => 'nullable|file|mimes:pdf|max:10240',
            'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                $wajibCount = collect($value)->where('category', 'Wajib')->count();
                if ($wajibCount < 1) {
                    $fail('Minimal harus ada 1 luaran wajib untuk proposal penelitian.');
                }
            }],
            'form.outputs.*.year' => 'required|integer|min:1|max:10',
            'form.outputs.*.category' => ['required', \Illuminate\Validation\Rule::in(ProposalConstants::OUTPUT_CATEGORIES)],
            'form.outputs.*.group' => ['required', \Illuminate\Validation\Rule::in(ProposalConstants::RESEARCH_OUTPUT_GROUPS)],
            'form.outputs.*.type' => ['required', 'string', function ($attribute, $value, $fail) {
                // Validate type matches group
                $index = explode('.', $attribute)[2];
                $group = $this->form->outputs[$index]['group'] ?? null;
                if ($group && isset(ProposalConstants::RESEARCH_OUTPUT_TYPES[$group])) {
                    if (! in_array($value, ProposalConstants::RESEARCH_OUTPUT_TYPES[$group])) {
                        $fail('Luaran baris '.($index + 1).' tidak valid untuk kategori yang dipilih.');
                    }
                }
            }],
            'form.outputs.*.status' => 'required|string|max:255',
            'form.outputs.*.description' => 'required|string|max:2000',
        ];

        $rules['form.tkt_type'] = 'nullable|string';
        $rules['form.tkt_results'] = 'nullable|array';
        $rules['form.roadmap_data'] = 'nullable|array';

        return $rules;
    }
}
