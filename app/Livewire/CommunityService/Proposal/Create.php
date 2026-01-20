<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Abstracts\ProposalCreate;

class Create extends ProposalCreate
{
    public function mount(?string $proposalId = null, ?\App\Models\Proposal $proposal = null): void
    {
        parent::mount($proposalId, $proposal);

        // If new proposal, add one default mandatory output
        if (! ($proposal ?? ($proposalId ? \App\Models\Proposal::find($proposalId) : null))) {
            if (empty($this->form->outputs)) {
                $this->form->outputs[] = [
                    'year' => 1,
                    'category' => 'Wajib',
                    'group' => 'jurnal',
                    'type' => 'Jurnal PKM (Sinta 1-6)',
                    'status' => 'Published',
                    'description' => 'Target publikasi jurnal PKM',
                ];
            }
        }
    }

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

                // Validate each row has required fields
                foreach ($value as $index => $item) {
                    $rowNum = $index + 1;
                    $errors = [];

                    if (empty($item['group'])) {
                        $errors[] = 'Kategori Luaran';
                    }
                    if (empty($item['type'])) {
                        $errors[] = 'Luaran';
                    }
                    if (empty($item['status'])) {
                        $errors[] = 'Status';
                    }
                    if (empty($item['description'])) {
                        $errors[] = 'Keterangan (URL)';
                    }

                    if (! empty($errors)) {
                        $fail("Baris {$rowNum}: ".implode(', ', $errors).' wajib diisi.');
                    }
                }
            }],
        ];
    }
}
