<?php

namespace App\Livewire\Settings;

use App\Models\Partner;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class PartnerManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:3|max:255')]
    public string $type = '';

    #[Validate('required|min:3|max:255')]
    public string $address = '';

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.partner-manager', [
            'partners' => Partner::paginate(10),
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

        $this->dispatch('notify', message: $this->editingId ? 'Mitra berhasil diubah' : 'Mitra berhasil ditambahkan', type: 'success');
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
        $this->dispatch('notify', message: 'Mitra berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'type', 'address', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Partner::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Mitra berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
