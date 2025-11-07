<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\Institution;
use App\Models\StudyProgram;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class StudyProgramManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|exists:institutions,id')]
    public ?int $institutionId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.study-program-manager', [
            'studyPrograms' => StudyProgram::with(['institution'])->paginate(10),
            'institutions' => Institution::all(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'institutionId', 'editingId']);
        $this->modalTitle = 'Tambah Program Studi';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'institution_id' => $this->institutionId,
        ];

        if ($this->editingId) {
            StudyProgram::findOrFail($this->editingId)->update($data);
        } else {
            StudyProgram::create($data);
        }

        session()->flash('success', $this->editingId ? 'Program Studi berhasil diubah' : 'Program Studi berhasil ditambahkan');

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-study-program']);
        $this->reset(['name', 'institutionId', 'editingId']);
    }

    public function edit(StudyProgram $studyProgram): void
    {
        $this->editingId = $studyProgram->id;
        $this->name = $studyProgram->name;
        $this->institutionId = $studyProgram->institution_id;
        $this->modalTitle = 'Edit Program Studi';
    }

    public function delete(StudyProgram $studyProgram): void
    {
        $studyProgram->delete();

        $this->resetForm();
        session()->flash('success', 'Program Studi berhasil dihapus');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'institutionId', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            StudyProgram::findOrFail($this->deleteItemId)->delete();

            session()->flash('success', 'Program Studi berhasil dihapus');
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
