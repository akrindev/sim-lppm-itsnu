<?php

namespace App\Livewire\Research\Proposal;

use App\Constants\ProposalConstants;
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
            'form.substance_file' => $hasFile ? 'nullable|file|mimes:pdf,doc,docx|max:10240' : 'required|file|mimes:pdf,doc,docx|max:10240',
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
                'category' => ProposalConstants::OUTPUT_CATEGORIES[0],
                'group' => 'jurnal',
                'type' => ProposalConstants::RESEARCH_OUTPUT_TYPES['jurnal'][3],
                'status' => 'Published',
                'description' => 'Target publikasi jurnal nasional',
            ];
        } elseif (str_contains($schemeStrata, 'terapan') || str_contains($schemeName, 'terapan')) {
            // Terapan -> Produk / Prototipe
            $output = [
                'year' => 1,
                'category' => ProposalConstants::OUTPUT_CATEGORIES[0],
                'group' => 'produk',
                'type' => ProposalConstants::RESEARCH_OUTPUT_TYPES['produk'][0] ?? 'Purwarupa/Prototipe TRL 4-6',
                'status' => 'Draft',
                'description' => 'Target prototipe produk',
            ];
        } else {
            // Default (Dasar, Fundamental, Pascasarjana) -> Jurnal Internasional Bereputasi
            $output = [
                'year' => 1,
                'category' => ProposalConstants::OUTPUT_CATEGORIES[0],
                'group' => 'jurnal',
                'type' => ProposalConstants::RESEARCH_OUTPUT_TYPES['jurnal'][0],
                'status' => 'Submitted',
                'description' => 'Target publikasi jurnal internasional bereputasi',
            ];
        }

        if (! empty($output)) {
            $this->form->outputs[] = $output;
        }
    }
}
