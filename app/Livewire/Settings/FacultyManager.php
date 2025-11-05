<?php

namespace App\Livewire\Settings;

use App\Models\Faculty;
use App\Models\Institution;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class FacultyManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|min:2|max:10')]
    public string $code = '';

    #[Validate('required|exists:institutions,id')]
    public ?int $institutionId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.faculty-manager', [
            'faculties' => Faculty::with(['institution'])->paginate(10),
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

        $this->dispatch('notify', message: $this->editingId ? 'Fakultas berhasil diubah' : 'Fakultas berhasil ditambahkan', type: 'success');
        $this->reset(['name', 'code', 'institutionId', 'editingId']);
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
        $this->dispatch('notify', message: 'Fakultas berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'code', 'institutionId', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Faculty::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Fakultas berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
