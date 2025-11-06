<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\Institution;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class InstitutionManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.institution-manager', [
            'institutions' => Institution::paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'editingId']);
        $this->modalTitle = 'Tambah Institusi';
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Institution::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            Institution::create(['name' => $this->name]);
        }

        $this->dispatch('notify', message: $this->editingId ? 'Institusi berhasil diubah' : 'Institusi berhasil ditambahkan', type: 'success');
        $this->reset(['name', 'editingId']);
    }

    public function edit(Institution $institution): void
    {
        $this->editingId = $institution->id;
        $this->name = $institution->name;
        $this->modalTitle = 'Edit Institusi';
    }

    public function delete(Institution $institution): void
    {
        $institution->delete();
        $this->dispatch('notify', message: 'Institusi berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Institution::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Institusi berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
