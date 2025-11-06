<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\Keyword;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class KeywordManager extends Component
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
        return view('livewire.settings.tabs.keyword-manager', [
            'keywords' => Keyword::paginate(10),
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

        $this->dispatch('notify', message: $this->editingId ? 'Kata Kunci berhasil diubah' : 'Kata Kunci berhasil ditambahkan', type: 'success');
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
        $this->dispatch('notify', message: 'Kata Kunci berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Keyword::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Kata Kunci berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
