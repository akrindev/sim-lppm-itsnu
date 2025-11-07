<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\BudgetGroup;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class BudgetGroupManager extends Component
{
    use WithPagination;

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?string $description = null;

    public ?int $editingId = null;

    public string $modalTitle = 'Kelompok Anggaran';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.budget-group-manager', [
            'budgetGroups' => BudgetGroup::with(['components'])->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['code', 'name', 'description', 'editingId']);
        $this->modalTitle = 'Tambah Kelompok Anggaran';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->editingId) {
            BudgetGroup::findOrFail($this->editingId)->update($data);
        } else {
            BudgetGroup::create($data);
        }

        session()->flash('success', $this->editingId ? 'Kelompok Anggaran berhasil diubah' : 'Kelompok Anggaran berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-budget-group']);
        $this->reset(['code', 'name', 'description', 'editingId']);
    }

    public function edit(BudgetGroup $budgetGroup): void
    {
        $this->editingId = $budgetGroup->id;
        $this->code = $budgetGroup->code;
        $this->name = $budgetGroup->name;
        $this->description = $budgetGroup->description;
        $this->modalTitle = 'Edit Kelompok Anggaran';
    }

    public function delete(BudgetGroup $budgetGroup): void
    {
        $budgetGroup->delete();

        $this->resetForm();
        session()->flash('success', 'Kelompok Anggaran berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['code', 'name', 'description', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            BudgetGroup::findOrFail($this->deleteItemId)->delete();

            session()->flash('success', 'Kelompok Anggaran berhasil dihapus');
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
}
