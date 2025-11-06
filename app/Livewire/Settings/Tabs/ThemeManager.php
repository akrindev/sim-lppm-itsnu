<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\FocusArea;
use App\Models\Theme;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ThemeManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|exists:focus_areas,id')]
    public ?int $focusAreaId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.theme-manager', [
            'themes' => Theme::with(['focusArea'])->paginate(10),
            'focusAreas' => FocusArea::all(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'focusAreaId', 'editingId']);
        $this->modalTitle = 'Tambah Tema';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'focus_area_id' => $this->focusAreaId,
        ];

        if ($this->editingId) {
            Theme::findOrFail($this->editingId)->update($data);
        } else {
            Theme::create($data);
        }

        $this->dispatch('notify', message: $this->editingId ? 'Tema berhasil diubah' : 'Tema berhasil ditambahkan', type: 'success');
        $this->reset(['name', 'focusAreaId', 'editingId']);
    }

    public function edit(Theme $theme): void
    {
        $this->editingId = $theme->id;
        $this->name = $theme->name;
        $this->focusAreaId = $theme->focus_area_id;
        $this->modalTitle = 'Edit Tema';
    }

    public function delete(Theme $theme): void
    {
        $theme->delete();
        $this->dispatch('notify', message: 'Tema berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'focusAreaId', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Theme::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Tema berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
