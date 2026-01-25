<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\FocusArea;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FocusAreaManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Area Fokus';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.focus-area-manager', [
            'focusAreas' => FocusArea::latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'editingId']);
        $this->modalTitle = 'Tambah Area Fokus';
        $this->dispatch('open-modal', modalId: 'modal-focus-area');
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            FocusArea::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            FocusArea::create(['name' => $this->name]);
        }

        $message = $this->editingId ? 'Area Fokus berhasil diubah' : 'Area Fokus berhasil ditambahkan';

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-focus-area');
        $this->reset(['name', 'editingId']);

        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function edit(FocusArea $focusArea): void
    {
        $this->editingId = $focusArea->id;
        $this->name = $focusArea->name;
        $this->modalTitle = 'Edit Area Fokus';
        $this->dispatch('open-modal', modalId: 'modal-focus-area');
    }

    public function delete(FocusArea $focusArea): void
    {
        $focusArea->delete();

        $this->resetForm();
        $message = 'Area Fokus berhasil dihapus';
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
            FocusArea::findOrFail($this->deleteItemId)->delete();

            $message = 'Area Fokus berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteItemId = $id;
        $this->deleteItemName = \App\Models\FocusArea::find($id)?->name ?? '';
        $this->dispatch('open-modal', modalId: 'modal-confirm-delete-focus-area');
    }
}
