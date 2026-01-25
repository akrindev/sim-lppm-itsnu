<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\StudyProgram;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class StudyProgramManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|exists:institutions,id')]
    public ?int $institutionId = null;

    #[Validate('nullable|exists:faculties,id')]
    public ?int $facultyId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.study-program-manager', [
            'studyPrograms' => StudyProgram::with(['institution', 'faculty'])->latest()->paginate(10),
            'institutions' => Institution::all(),
            'faculties' => $this->institutionId ? Faculty::where('institution_id', $this->institutionId)->orderBy('name')->get() : [],
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'institutionId', 'facultyId', 'editingId']);
        $this->modalTitle = 'Tambah Program Studi';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'institution_id' => $this->institutionId,
            'faculty_id' => $this->facultyId,
        ];

        if ($this->editingId) {
            StudyProgram::findOrFail($this->editingId)->update($data);
        } else {
            StudyProgram::create($data);
        }

        $message = $this->editingId ? 'Program Studi berhasil diubah' : 'Program Studi berhasil ditambahkan';

        // close modal
        $this->dispatch('close-modal', modalId: 'modal-study-program');
        $this->reset(['name', 'institutionId', 'facultyId', 'editingId']);

        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function edit(StudyProgram $studyProgram): void
    {
        $this->editingId = $studyProgram->id;
        $this->name = $studyProgram->name;
        $this->institutionId = $studyProgram->institution_id;
        $this->facultyId = $studyProgram->faculty_id;
        $this->modalTitle = 'Edit Program Studi';
    }

    public function delete(StudyProgram $studyProgram): void
    {
        $studyProgram->delete();

        $this->resetForm();
        $message = 'Program Studi berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'institutionId', 'facultyId', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            StudyProgram::findOrFail($this->deleteItemId)->delete();

            $message = 'Program Studi berhasil dihapus';
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
        $this->deleteItemName = \App\Models\StudyProgram::find($id)?->name ?? '';
    }
}