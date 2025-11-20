<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\NationalPriority;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class NationalPriorityManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Prioritas Nasional';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.national-priority-manager', [
            'nationalPriorities' => NationalPriority::latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'editingId']);
        $this->modalTitle = 'Tambah Prioritas Nasional';
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            NationalPriority::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            NationalPriority::create(['name' => $this->name]);
        }

        session()->flash('success', $this->editingId ? 'Prioritas Nasional berhasil diubah' : 'Prioritas Nasional berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-national-priority']);
        $this->reset(['name', 'editingId']);
    }

    public function edit(NationalPriority $nationalPriority): void
    {
        $this->editingId = $nationalPriority->id;
        $this->name = $nationalPriority->name;
        $this->modalTitle = 'Edit Prioritas Nasional';
    }

    public function delete(NationalPriority $nationalPriority): void
    {
        $nationalPriority->delete();

        $this->resetForm();
        session()->flash('success', 'Prioritas Nasional berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            NationalPriority::findOrFail($this->deleteItemId)->delete();

            session()->flash('success', 'Prioritas Nasional berhasil dihapus');
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
