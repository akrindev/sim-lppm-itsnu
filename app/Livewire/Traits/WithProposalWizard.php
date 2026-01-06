<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\DB;

trait WithProposalWizard
{
    public array $outputs = [];

    public array $budget_items = [];

    public array $partner_ids = [];

    public array $new_partner = [
        'name' => '',
        'email' => '',
        'institution' => '',
        'country' => '',
        'address' => '',
    ];

    public $new_partner_commitment_file;

    public function addOutput(): void
    {
        $this->form->outputs[] = [
            'year' => 1,
            'category' => 'Wajib',
            'group' => '',
            'type' => '',
            'status' => '',
            'description' => '',
        ];
    }

    public function removeOutput(int $index): void
    {
        unset($this->form->outputs[$index]);
        $this->form->outputs = array_values($this->form->outputs);
    }

    public function addBudgetItem(): void
    {
        $this->form->budget_items[] = [
            'year' => 1,
            'budget_group_id' => '',
            'budget_component_id' => '',
            'group' => '',
            'component' => '',
            'item' => '',
            'unit' => '',
            'volume' => 1,
            'unit_price' => 0,
            'total' => 0,
        ];
    }

    public function removeBudgetItem(int $index): void
    {
        unset($this->form->budget_items[$index]);
        $this->form->budget_items = array_values($this->form->budget_items);
    }

    public function calculateTotal(int $index): void
    {
        $volume = (float) ($this->form->budget_items[$index]['volume'] ?? 0);
        $price = (float) ($this->form->budget_items[$index]['unit_price'] ?? 0);
        $this->form->budget_items[$index]['total'] = $volume * $price;
    }

    public function saveNewPartner(): void
    {
        $this->validate([
            'new_partner.name' => 'required|string|max:255',
            'new_partner.email' => 'required|email|max:255',
            'new_partner.institution' => 'required|string|max:255',
            'new_partner.country' => 'required|string|max:255',
            'new_partner.address' => 'required|string',
            'new_partner_commitment_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        DB::transaction(function () {
            $partner = \App\Models\Partner::create([
                'name' => $this->new_partner['name'],
                'email' => $this->new_partner['email'],
                'institution' => $this->new_partner['institution'],
                'country' => $this->new_partner['country'],
                'address' => $this->new_partner['address'],
            ]);

            if ($this->new_partner_commitment_file) {
                $partner
                    ->addMedia($this->new_partner_commitment_file->getRealPath())
                    ->usingName($this->new_partner_commitment_file->getClientOriginalName())
                    ->usingFileName($this->new_partner_commitment_file->hashName())
                    ->toMediaCollection('commitment_file');
            }

            $this->partner_ids[] = $partner->id;

            $this->new_partner = [
                'name' => '',
                'email' => '',
                'institution' => '',
                'country' => '',
                'address' => '',
            ];

            $this->new_partner_commitment_file = null;
        });

        $this->dispatch('partner-created');
    }

    public function validateBudgetRealtime(): void
    {
        try {
            if (! empty($this->budget_items)) {
                app(\App\Services\BudgetValidationService::class)->validateBudgetGroupPercentages(
                    $this->budget_items,
                    $this->getProposalTypeForValidation(),
                    (int) date('Y')
                );

                app(\App\Services\BudgetValidationService::class)->validateBudgetCap(
                    $this->budget_items,
                    $this->getProposalTypeForValidation(),
                    (int) date('Y')
                );
            }

            $this->budgetValidationErrors = [];
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->budgetValidationErrors = $e->errors()['budget_items'] ?? [];
        }
    }

    abstract protected function getProposalTypeForValidation(): string;
}
