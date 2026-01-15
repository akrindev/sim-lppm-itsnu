<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\BudgetCap;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * BudgetCapManager component for managing year-based budget caps.
 *
 * Only accessible by Admin LPPM users. Allows setting maximum budget
 * limits for research and community service proposals per year.
 */
class BudgetCapManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|integer|min:2000|max:2100')]
    public string $year = '';

    #[Validate('nullable|numeric|min:0')]
    public ?string $research_budget_cap = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $community_service_budget_cap = null;

    public ?int $editingId = null;

    public string $modalTitle = 'Pengaturan Anggaran';

    public ?int $deleteItemId = null;

    public string $deleteItemYear = '';

    /**
     * Authorization check - only Admin LPPM can access this component
     */
    public function mount(): void
    {
        if (! auth()->user()->hasRole('admin lppm')) {
            abort(403, 'Hanya Admin LPPM yang dapat mengakses pengaturan anggaran.');
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.budget-cap-manager', [
            'budgetCaps' => BudgetCap::latest('year')->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['year', 'research_budget_cap', 'community_service_budget_cap', 'editingId']);
        $this->modalTitle = 'Tambah Pengaturan Anggaran';
    }

    public function save(): void
    {
        $this->validate();

        // Check for duplicate year (except when editing)
        $exists = BudgetCap::where('year', $this->year)
            ->when($this->editingId, function ($query) {
                $query->where('id', '!=', $this->editingId);
            })
            ->exists();

        if ($exists) {
            $this->addError('year', 'Pengaturan anggaran untuk tahun '.$this->year.' sudah ada.');

            return;
        }

        $data = [
            'year' => (int) $this->year,
            'research_budget_cap' => $this->research_budget_cap ? (float) $this->research_budget_cap : null,
            'community_service_budget_cap' => $this->community_service_budget_cap ? (float) $this->community_service_budget_cap : null,
        ];

        if ($this->editingId) {
            BudgetCap::findOrFail($this->editingId)->update($data);
        } else {
            BudgetCap::create($data);
        }

        $message = $this->editingId ? 'Pengaturan Anggaran berhasil diubah' : 'Pengaturan Anggaran berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-budget-cap');
        $this->reset(['year', 'research_budget_cap', 'community_service_budget_cap', 'editingId']);
    }

    public function edit(BudgetCap $budgetCap): void
    {
        $this->editingId = $budgetCap->id;
        $this->year = (string) $budgetCap->year;
        $this->research_budget_cap = $budgetCap->research_budget_cap ? (string) $budgetCap->research_budget_cap : null;
        $this->community_service_budget_cap = $budgetCap->community_service_budget_cap ? (string) $budgetCap->community_service_budget_cap : null;
        $this->modalTitle = 'Edit Pengaturan Anggaran';
    }

    public function delete(BudgetCap $budgetCap): void
    {
        $budgetCap->delete();

        $this->resetForm();
        $message = 'Pengaturan Anggaran berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['year', 'research_budget_cap', 'community_service_budget_cap', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            BudgetCap::findOrFail($this->deleteItemId)->delete();

            $message = 'Pengaturan Anggaran berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemYear']);
    }

    public function confirmDelete(int $id, string $year): void
    {
        $this->deleteItemId = $id;
        $this->deleteItemYear = $year;
    }
}
