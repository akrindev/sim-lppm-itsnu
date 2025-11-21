<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\Keyword;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class KeywordManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Kata Kunci';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.keyword-manager', [
            'keywords' => Keyword::latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'editingId']);
        $this->modalTitle = 'Tambah Kata Kunci';
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Keyword::findOrFail($this->editingId)->update(['name' => $this->name]);
        } else {
            Keyword::create(['name' => $this->name]);
        }

        session()->flash('success', $this->editingId ? 'Kata Kunci berhasil diubah' : 'Kata Kunci berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-keyword']);
        $this->reset(['name', 'editingId']);
    }

    public function edit(Keyword $keyword): void
    {
        $this->editingId = $keyword->id;
        $this->name = $keyword->name;
        $this->modalTitle = 'Edit Kata Kunci';
    }

    public function delete(Keyword $keyword): void
    {
        $keyword->delete();

        $this->resetForm();
        session()->flash('success', 'Kata Kunci berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Keyword::findOrFail($this->deleteItemId)->delete();

            session()->flash('success', 'Kata Kunci berhasil dihapus');
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
