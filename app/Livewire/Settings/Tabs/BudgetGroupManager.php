<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\BudgetGroup;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class BudgetGroupManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?string $description = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $percentage = null;

    public ?int $editingId = null;

    public string $modalTitle = 'Kelompok Anggaran';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.budget-group-manager', [
            'budgetGroups' => BudgetGroup::with(['components'])->latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['code', 'name', 'description', 'percentage', 'editingId']);
        $this->modalTitle = 'Tambah Kelompok Anggaran';
    }

    public function save(): void
    {
        $this->validate();

        // Check if total percentages exceed 100%
        $totalPercentage = $this->calculateTotalPercentage();

        if ($totalPercentage > 100) {
            $this->addError('percentage', 'Total persentase semua kelompok anggaran tidak boleh melebihi 100%. Saat ini: '.number_format($totalPercentage, 2).'%');

            return;
        }

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'percentage' => $this->percentage ? (float) $this->percentage : null,
        ];

        if ($this->editingId) {
            BudgetGroup::findOrFail($this->editingId)->update($data);
        } else {
            BudgetGroup::create($data);
        }

        $message = $this->editingId ? 'Kelompok Anggaran berhasil diubah' : 'Kelompok Anggaran berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-budget-group');
        $this->reset(['code', 'name', 'description', 'percentage', 'editingId']);
    }

    public function edit(BudgetGroup $budgetGroup): void
    {
        $this->editingId = $budgetGroup->id;
        $this->code = $budgetGroup->code;
        $this->name = $budgetGroup->name;
        $this->description = $budgetGroup->description;
        $this->percentage = $budgetGroup->percentage ? (string) $budgetGroup->percentage : null;
        $this->modalTitle = 'Edit Kelompok Anggaran';
    }

    public function delete(BudgetGroup $budgetGroup): void
    {
        $budgetGroup->delete();

        $this->resetForm();
        $message = 'Kelompok Anggaran berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['code', 'name', 'description', 'percentage', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            BudgetGroup::findOrFail($this->deleteItemId)->delete();

            $message = 'Kelompok Anggaran berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deleteItemId = $id;
        $this->deleteItemName = $name;
    }

    /**
     * Calculate total percentage across all budget groups.
     * Excludes the currently editing group to allow percentage updates.
     */
    private function calculateTotalPercentage(): float
    {
        $total = BudgetGroup::when($this->editingId, function ($query) {
            $query->where('id', '!=', $this->editingId);
        })
            ->whereNotNull('percentage')
            ->sum('percentage');

        // Add current percentage being set
        if ($this->percentage) {
            $total += (float) $this->percentage;
        }

        return (float) $total;
    }
}
