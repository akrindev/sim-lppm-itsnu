<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\DB;

trait WithProposalWizard
{
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
        $isPkm = $this->getProposalTypeForValidation() === 'community-service';

        $this->validate([
            'form.new_partner.name' => 'required|string|max:255',
            'form.new_partner.email' => 'nullable|email|max:255',
            'form.new_partner.institution' => 'required|string|max:255',
            'form.new_partner.country' => 'required|string|max:255',
            'form.new_partner.type' => 'required|string|max:255',
            'form.new_partner.address' => 'nullable|string',
            'form.new_partner_commitment_file' => ($isPkm ? 'required' : 'nullable').'|file|mimes:pdf|max:5120',
        ]);

        DB::transaction(function () {
            $partner = \App\Models\Partner::create([
                'name' => $this->form->new_partner['name'],
                'email' => $this->form->new_partner['email'],
                'institution' => $this->form->new_partner['institution'],
                'country' => $this->form->new_partner['country'],
                'type' => $this->form->new_partner['type'],
                'address' => $this->form->new_partner['address'],
            ]);

            if ($this->form->new_partner_commitment_file) {
                $partner
                    ->addMedia($this->form->new_partner_commitment_file->getRealPath())
                    ->usingName($this->form->new_partner_commitment_file->getClientOriginalName())
                    ->usingFileName($this->form->new_partner_commitment_file->hashName())
                    ->toMediaCollection('commitment_letter');
            }

            $this->form->partner_ids[] = $partner->id;

            $this->form->new_partner = [
                'name' => '',
                'email' => '',
                'institution' => '',
                'country' => '',
                'type' => '',
                'address' => '',
            ];

            $this->form->new_partner_commitment_file = null;
        });

        $this->dispatch('partner-created');
        $this->dispatch('modal-close', id: 'modal-partner');
    }

    public function validateBudgetRealtime(): void
    {
        try {
            if (! empty($this->form->budget_items)) {
                app(\App\Services\BudgetValidationService::class)->validateBudgetGroupPercentages(
                    $this->form->budget_items,
                    $this->getProposalTypeForValidation(),
                    (int) date('Y')
                );

                app(\App\Services\BudgetValidationService::class)->validateBudgetCap(
                    $this->form->budget_items,
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
