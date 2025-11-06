<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\FocusArea;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FocusAreaManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Area Fokus';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.focus-area-manager', [
            'focusAreas' => FocusArea::paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'editingId']);
        $this->modalTitle = 'Tambah Area Fokus';
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            FocusArea::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            FocusArea::create(['name' => $this->name]);
        }

        $this->dispatch('notify', message: $this->editingId ? 'Area Fokus berhasil diubah' : 'Area Fokus berhasil ditambahkan', type: 'success');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-focus-area']);
        $this->reset(['name', 'editingId']);
    }

    public function edit(FocusArea $focusArea): void
    {
        $this->editingId = $focusArea->id;
        $this->name = $focusArea->name;
        $this->modalTitle = 'Edit Area Fokus';
    }

    public function delete(FocusArea $focusArea): void
    {
        $focusArea->delete();
        $this->dispatch('notify', message: 'Area Fokus berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            FocusArea::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Area Fokus berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
