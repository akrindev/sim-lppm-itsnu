<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\Theme;
use App\Models\Topic;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class TopicManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|exists:themes,id')]
    public ?int $themeId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.topic-manager', [
            'topics' => Topic::with(['theme'])->latest()->paginate(10),
            'themes' => Theme::all(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'themeId', 'editingId']);
        $this->modalTitle = 'Tambah Topik';
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'theme_id' => $this->themeId,
        ];

        if ($this->editingId) {
            Topic::findOrFail($this->editingId)->update($data);
        } else {
            Topic::create($data);
        }

        $message = $this->editingId ? 'Topik berhasil diubah' : 'Topik berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-topic']);
        $this->reset(['name', 'themeId', 'editingId']);
    }

    public function edit(Topic $topic): void
    {
        $this->editingId = $topic->id;
        $this->name = $topic->name;
        $this->themeId = $topic->theme_id;
        $this->modalTitle = 'Edit Topik';
    }

    public function delete(Topic $topic): void
    {
        $topic->delete();

        $this->resetForm();
            $message = 'Topik berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'themeId', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            Topic::findOrFail($this->deleteItemId)->delete();

        $message = 'Topik berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
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
