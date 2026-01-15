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
    }

    public function updatedFormResearchSchemeId(): void
    {
        // Only run logic if scheme ID is present
        if (! $this->form->research_scheme_id) {
            return;
        }

        // PREVENT OVERWRITE: Only fill if outputs are empty
        // This ensures editing existing proposals or user-modified lists are safe
        if (! empty($this->form->outputs)) {
            return;
        }

        $scheme = \App\Models\ResearchScheme::find($this->form->research_scheme_id);
        if (! $scheme) {
            return;
        }

        $output = [];
        $schemeName = strtolower($scheme->name);
        $schemeStrata = strtolower($scheme->strata);

        // Auto-fill logic based on Scheme Name/Strata (BIMA 2026 approximation)
        if (str_contains($schemeName, 'pemula') || str_contains($schemeName, 'internal')) {
            // Pemula / Internal -> Sinta 1-2 (Safe default, or user changes to 3-6)
            $output = [
                'year' => 1,
                'category' => 'Wajib',
                'group' => 'jurnal',
                'type' => 'Jurnal Nas. Terakreditasi (Sinta 1-2)',
                'status' => 'Published',
                'description' => 'Target publikasi jurnal nasional',
            ];
        } elseif (str_contains($schemeStrata, 'terapan') || str_contains($schemeName, 'terapan')) {
            // Terapan -> Produk / Prototipe
            $output = [
                'year' => 1,
                'category' => 'Wajib',
                'group' => 'produk',
                'type' => 'Purwarupa/Prototipe TRL 4-6',
                'status' => 'Draft',
                'description' => 'Target prototipe produk',
            ];
        } else {
            // Default (Dasar, Fundamental, Pascasarjana) -> Jurnal Internasional Bereputasi
            $output = [
                'year' => 1,
                'category' => 'Wajib',
                'group' => 'jurnal',
                'type' => 'Jurnal Int. Bereputasi (Q1-Q2)',
                'status' => 'Submitted',
                'description' => 'Target publikasi jurnal internasional bereputasi',
            ];
        }

        if (! empty($output)) {
            $this->form->outputs[] = $output;
        }
    }
}
