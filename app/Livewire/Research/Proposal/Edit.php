<?php

namespace App\Livewire\Research\Proposal;

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

                    if (! empty($errors)) {
                        $fail("Baris {$rowNum}: ".implode(', ', $errors).' wajib diisi.');
                    }
                }
            }],
        ];

        $rules['form.tkt_type'] = 'nullable|string';
        $rules['form.tkt_results'] = 'nullable|array';
        $rules['form.roadmap_data'] = 'nullable|array';

        return $rules;
    }
}
