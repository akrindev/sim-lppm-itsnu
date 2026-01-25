<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\Institution;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class InstitutionManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|min:5|max:500')]
    public string $address = '';

    public ?int $editingId = null;

    public string $modalTitle = 'Institusi';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.institution-manager', [
            'institutions' => Institution::latest()->paginate(10),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'code', 'address', 'editingId']);
        $this->modalTitle = 'Tambah Institusi';
    }

    public function edit(Institution $institution): void
    {
        $this->editingId = $institution->id;
        $this->name = $institution->name;
        $this->code = $institution->code;
        $this->address = $institution->address;
        $this->modalTitle = 'Edit Institusi';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
        ];

        if ($this->editingId) {
            Institution::findOrFail($this->editingId)->update($data);
        } else {
            Institution::create($data);
        }

        $message = $this->editingId ? 'Institusi berhasil diubah' : 'Institusi berhasil ditambahkan';

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-institution');
        $this->reset(['name', 'code', 'address', 'editingId']);

        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function delete(Institution $institution): void
    {
        $institution->delete();

        $this->resetForm();
        $message = 'Institusi berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'code', 'address', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Institution::findOrFail($this->deleteItemId)->delete();

            $message = 'Institusi berhasil dihapus';
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
        $this->deleteItemName = \App\Models\Institution::find($id)?->name ?? '';
        $this->dispatch('open-modal', modalId: 'modal-confirm-delete-institution');
    }
}
