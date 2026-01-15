<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\Partner;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:3|max:255')]
    public string $type = '';

    #[Validate('required|min:3|max:255')]
    public string $address = '';

    public ?string $editingId = null;

    public string $modalTitle = 'Mitra';

    public ?string $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.partner-manager', [
            'partners' => Partner::latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'type', 'address', 'editingId']);
        $this->modalTitle = 'Tambah Mitra';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'address' => $this->address,
        ];

        if ($this->editingId) {
            Partner::findOrFail($this->editingId)->update($data);
        } else {
            Partner::create($data);
        }

        $this->toastSuccess($this->editingId ? 'Mitra berhasil diubah' : 'Mitra berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-partner']);
        $this->reset(['name', 'type', 'address', 'editingId']);
    }

    public function edit(Partner $partner): void
    {
        $this->editingId = $partner->id;
        $this->name = $partner->name;
        $this->type = $partner->type;
        $this->address = $partner->address;
        $this->modalTitle = 'Edit Mitra';
    }

    public function delete(Partner $partner): void
    {
        $partner->delete();

        $this->resetForm();
        $this->toastSuccess('Mitra berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'type', 'address', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Partner::findOrFail($this->deleteItemId)->delete();

            $this->toastSuccess('Mitra berhasil dihapus');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }

    public function confirmDelete(string $id, string $name): void
    {
        $this->deleteItemId = $id;
        $this->deleteItemName = $name;
    }
}
