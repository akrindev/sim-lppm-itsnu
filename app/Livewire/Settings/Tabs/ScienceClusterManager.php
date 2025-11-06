<?php

namespace App\Livewire\Settings\Tabs;

use App\Models\ScienceCluster;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class ScienceClusterManager extends Component
{
    use WithPagination;

    #[Validate('required|min:3|max:255')]
    public string $name = '';

    public ?int $parentId = null;

    public ?int $editingId = null;

    public string $modalTitle = '';

    public ?int $deleteItemId = null;

    public string $deleteItemName = '';

    public function render()
    {
        return view('livewire.settings.tabs.science-cluster-manager', [
            'scienceClusters' => ScienceCluster::with(['parent'])->paginate(10),
            'parentClusters' => ScienceCluster::whereNull('parent_id')->get(),
        ]);
    }

    public function create(): void
    {
        $this->reset(['name', 'parentId', 'editingId']);
        $this->modalTitle = 'Tambah Klaster Sains';
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
            ScienceCluster::create($data);
        }

        $this->dispatch('notify', message: $this->editingId ? 'Klaster Sains berhasil diubah' : 'Klaster Sains berhasil ditambahkan', type: 'success');
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
        $this->dispatch('notify', message: 'Klaster Sains berhasil dihapus', type: 'success');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'parentId', 'editingId']);
    }


    public function handleConfirmDeleteAction(): void
    {
        if ($this->deleteItemId) {
            ScienceCluster::findOrFail($this->deleteItemId)->delete();
            $this->dispatch('notify', message: 'Klaster Sains berhasil dihapus', type: 'success');
            $this->resetConfirmDelete();
        }
    }

    public function resetConfirmDelete(): void
    {
        $this->reset(['deleteItemId', 'deleteItemName']);
    }
}
