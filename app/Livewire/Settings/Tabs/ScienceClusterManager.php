<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\ScienceCluster;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ScienceClusterManager extends Component
{
    use HasToast, WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $parentId = null;

    public ?int $editingId = null;

    public int $selectedLevel = 1;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        $allClusters = ScienceCluster::with(['parent', 'children'])->latest()->get();

        $level1Clusters = $allClusters->where('level', 1)->values();
        $level2Clusters = $allClusters->where('level', 2)->values();
        $level3Clusters = $allClusters->where('level', 3)->values();

        return view('livewire.settings.tabs.science-cluster-manager', [
            'level1Clusters' => $level1Clusters,
            'level2Clusters' => $level2Clusters,
            'level3Clusters' => $level3Clusters,
            'allClusters' => $allClusters,
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'parentId', 'editingId']);
        $this->modalTitle = 'Tambah Klaster Sains';
    }

    public function setSelectedLevel(int $level): void
    {
        $this->selectedLevel = $level;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'parent_id' => $this->parentId,
        ];

        if ($this->editingId) {
            ScienceCluster::findOrFail($this->editingId)->update($data);
        } else {
            if ($this->parentId) {
                $parent = ScienceCluster::findOrFail($this->parentId);
                $data['level'] = $parent->level + 1;
            } else {
                $data['level'] = 1;
            }
            ScienceCluster::create($data);
        }

        $message = $this->editingId ? 'Klaster Sains berhasil diubah' : 'Klaster Sains berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // close modal
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-science-cluster']);
        $this->reset(['name', 'parentId', 'editingId']);
    }

    public function edit(ScienceCluster $scienceCluster): void
    {
        $this->editingId = $scienceCluster->id;
        $this->name = $scienceCluster->name;
        $this->parentId = $scienceCluster->parent_id;
        $this->modalTitle = 'Edit Klaster Sains';
    }

    public function delete(ScienceCluster $scienceCluster): void
    {
        $scienceCluster->delete();

        $this->resetForm();
            $message = 'Klaster Sains berhasil dihapus';
            session()->flash('success', $message);
            $this->toastSuccess($message);
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'parentId', 'editingId']);
    }

    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            ScienceCluster::findOrFail($this->deleteItemId)->delete();

        $message = 'Klaster Sains berhasil dihapus';
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
