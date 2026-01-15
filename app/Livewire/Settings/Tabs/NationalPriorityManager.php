<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\NationalPriority;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class NationalPriorityManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?string $editingId = null;

    public string $modalTitle = 'Prioritas Nasional';

    public ?string $deleteItemId = null;

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

        $message = $this->editingId ? 'Prioritas Nasional berhasil diubah' : 'Prioritas Nasional berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

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
            $message = 'Prioritas Nasional berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            NationalPriority::findOrFail($this->deleteItemId)->delete();

        $message = 'Prioritas Nasional berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }

    public function confirmDelete(string|int $id, string $name): void
    {
        $this->deleteItemId = $id;
        $this->deleteItemName = $name;
    }
}
