<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\ResearchScheme;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ResearchSchemeManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:3|max:255')]
    public string $strata = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Skema Penelitian';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.research-scheme-manager', [
            'researchSchemes' => ResearchScheme::paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'strata', 'editingId']);
        $this->modalTitle = 'Tambah Skema Penelitian';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'strata' => $this->strata,
        ];

        if ($this->editingId) {
            ResearchScheme::findOrFail($this->editingId)->update($data);
        } else {
            ResearchScheme::create($data);
        }

        session()->flash('success', $this->editingId ? 'Skema Penelitian berhasil diubah' : 'Skema Penelitian berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-research-scheme']);
        $this->reset(['name', 'strata', 'editingId']);
    }

    public function edit(ResearchScheme $researchScheme): void
    {
        $this->editingId = $researchScheme->id;
        $this->name = $researchScheme->name;
        $this->strata = $researchScheme->strata;
        $this->modalTitle = 'Edit Skema Penelitian';
    }

    public function delete(ResearchScheme $researchScheme): void
    {
        $researchScheme->delete();

        $this->resetForm();
        session()->flash('success', 'Skema Penelitian berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'strata', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            ResearchScheme::findOrFail($this->deleteItemId)->delete();

            session()->flash('success', 'Skema Penelitian berhasil dihapus');
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
