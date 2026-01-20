<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Constants\ProposalConstants;
use App\Livewire\Abstracts\ProposalCreate;

class Edit extends ProposalCreate
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

    protected function getStepValidationRules(int $step): array
    {
        if ($step === 1) {
            $rules = parent::getStepValidationRules(1);
            $rules['form.partner_issue_summary'] = 'required|string|min:50';
            $rules['form.solution_offered'] = 'required|string|min:50';

            return $rules;
        }

        if ($step === 4) {
            return [
                'form.partner_ids' => 'required|array|min:1',
                'form.partner_ids.*' => 'exists:partners,id',
            ];
        }

        return parent::getStepValidationRules($step);
    }

    protected function getStep2Rules(): array
    {
        // Check if file already exists (edit mode)
        $hasFile = $this->form->proposal &&
                   $this->form->proposal->detailable &&
                   $this->form->proposal->detailable->hasMedia('substance_file');

        return [
            'form.macro_research_group_id' => 'required|exists:macro_research_groups,id',
            'form.substance_file' => $hasFile ? 'nullable|file|mimes:pdf|max:10240' : 'required|file|mimes:pdf|max:10240',
            'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                $wajibCount = collect($value)->where('category', 'Wajib')->count();
                if ($wajibCount < 1) {
                    $fail('Minimal harus ada 1 luaran wajib untuk proposal pengabdian masyarakat.');
                }
            }],
            'form.outputs.*.year' => 'required|integer|min:1|max:10',
            'form.outputs.*.category' => ['required', \Illuminate\Validation\Rule::in(ProposalConstants::OUTPUT_CATEGORIES)],
            'form.outputs.*.group' => ['required', \Illuminate\Validation\Rule::in(ProposalConstants::PKM_OUTPUT_GROUPS)],
            'form.outputs.*.type' => ['required', 'string', function ($attribute, $value, $fail) {
                // Validate type matches group
                $index = explode('.', $attribute)[2];
                $group = $this->form->outputs[$index]['group'] ?? null;
                if ($group && isset(ProposalConstants::PKM_OUTPUT_TYPES[$group])) {
                    if (! in_array($value, ProposalConstants::PKM_OUTPUT_TYPES[$group])) {
                        $fail('Luaran baris '.($index + 1).' tidak valid untuk kategori yang dipilih.');
                    }
                }
            }],
            'form.outputs.*.status' => 'required|string|max:255',
            'form.outputs.*.description' => 'required|string|max:255',
        ];
    }
}
