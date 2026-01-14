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
            'form.macro_research_group_id' => 'nullable|exists:macro_research_groups,id',
            'form.substance_file' => 'nullable|file|mimes:pdf|max:10240',
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

                    if (! empty($errors)) {
                        $fail("Baris {$rowNum}: " . implode(', ', $errors) . ' wajib diisi.');
                    }
                }
            }],
        ];
    }
}
