<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\Faculty;
use App\Models\Institution;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FacultyManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|exists:institutions,id')]
    public ?int $institutionId = null;

    public ?int $editingId = null;

    public string $modalTitle = 'Fakultas';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.faculty-manager', [
            'faculties' => Faculty::with(['institution'])->latest()->paginate(10),
            'institutions' => Institution::all(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'code', 'institutionId', 'editingId']);
        $this->modalTitle = 'Tambah Fakultas';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'institution_id' => $this->institutionId,
        ];

        if ($this->editingId) {
            Faculty::findOrFail($this->editingId)->update($data);
        } else {
            Faculty::create($data);
        }

        $message = $this->editingId ? 'Fakultas berhasil diubah' : 'Fakultas berhasil ditambahkan';

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-faculty');
        $this->reset(['name', 'code', 'institutionId', 'editingId']);

        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function edit(Faculty $faculty): void
    {
        $this->editingId = $faculty->id;
        $this->name = $faculty->name;
        $this->code = $faculty->code;
        $this->institutionId = $faculty->institution_id;
        $this->modalTitle = 'Edit Fakultas';
    }

    public function delete(Faculty $faculty): void
    {
        $faculty->delete();

        $this->resetForm();
        $message = 'Fakultas berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'code', 'institutionId', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Faculty::findOrFail($this->deleteItemId)->delete();

            $message = 'Fakultas berhasil dihapus';
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
        $this->deleteItemName = \App\Models\Faculty::find($id)?->name ?? '';
    }
}