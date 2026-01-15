<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class BudgetComponentManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|exists:budget_groups,id')]
    public ?int $budgetGroupId = null;

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:2|max:20')]
    public string $unit = '';

    public ?string $description = null;

    public ?int $editingId = null;

    public string $modalTitle = 'Komponen Anggaran';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.budget-component-manager', [
            'budgetComponents' => BudgetComponent::with(['budgetGroup'])->latest()->paginate(10),
            'budgetGroups' => BudgetGroup::all(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['budgetGroupId', 'code', 'name', 'unit', 'description', 'editingId']);
        $this->modalTitle = 'Tambah Komponen Anggaran';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'budget_group_id' => $this->budgetGroupId,
            'code' => $this->code,
            'name' => $this->name,
            'unit' => $this->unit,
            'description' => $this->description,
        ];

        if ($this->editingId) {
            BudgetComponent::findOrFail($this->editingId)->update($data);
        } else {
            BudgetComponent::create($data);
        }

        $message = $this->editingId ? 'Komponen Anggaran berhasil diubah' : 'Komponen Anggaran berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-budget-component');
        $this->reset(['budgetGroupId', 'code', 'name', 'unit', 'description', 'editingId']);
    }

    public function edit(BudgetComponent $budgetComponent): void
    {
        $this->editingId = $budgetComponent->id;
        $this->budgetGroupId = $budgetComponent->budget_group_id;
        $this->code = $budgetComponent->code;
        $this->name = $budgetComponent->name;
        $this->unit = $budgetComponent->unit;
        $this->description = $budgetComponent->description;
        $this->modalTitle = 'Edit Komponen Anggaran';
    }

    public function delete(BudgetComponent $budgetComponent): void
    {
        $budgetComponent->delete();

        $this->resetForm();
        $message = 'Komponen Anggaran berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['budgetGroupId', 'code', 'name', 'unit', 'description', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            BudgetComponent::findOrFail($this->deleteItemId)->delete();

            $message = 'Komponen Anggaran berhasil dihapus';
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
}
