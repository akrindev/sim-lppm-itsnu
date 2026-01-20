<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\ScienceCluster;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Science Cluster Manager Component - Master-Detail Split Panel
 * Manages Science Cluster hierarchy (Level 1, 2, 3) with tree navigation
 */
class ScienceClusterManager extends Component
{
    use HasToast;

    // Navigation state
    #[Url(as: 'cluster')]
    public ?int $selectedClusterId = null;

    public string $search = '';

    // Expanded clusters in tree view
    public array $expandedLevel1 = [];

    public array $expandedLevel2 = [];

    // Inline editing state
    public ?int $editingClusterId = null;

    public string $clusterNameInput = '';

    // Adding new cluster
    public bool $addingCluster = false;

    public string $newClusterName = '';

    public ?int $newClusterParentId = null;

    public function mount(): void
    {
        // Auto-select first level 1 cluster if none selected
        if ($this->selectedClusterId === null) {
            $firstCluster = ScienceCluster::where('level', 1)->orderBy('name')->first();
            if ($firstCluster) {
                $this->selectedClusterId = $firstCluster->id;
                $this->expandedLevel1 = [$firstCluster->id];
            }
        } else {
            // Ensure selected cluster's parents are expanded
            $cluster = ScienceCluster::find($this->selectedClusterId);
            if ($cluster) {
                $this->expandParentsOfCluster($cluster);
            }
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.science-cluster-manager');
    }

    // =====================
    // COMPUTED PROPERTIES
    // =====================

    #[Computed]
    public function level1Clusters(): Collection
    {
        $query = ScienceCluster::where('level', 1)
            ->withCount('children')
            ->with(['children' => function ($q) {
                $q->withCount('children')->orderBy('name');
            }])
            ->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return $query->get();
    }

    #[Computed]
    public function selectedCluster(): ?ScienceCluster
    {
        if (! $this->selectedClusterId) {
            return null;
        }

        return ScienceCluster::with(['parent', 'children'])->find($this->selectedClusterId);
    }

    #[Computed]
    public function allClustersForSelect(): Collection
    {
        return ScienceCluster::orderBy('level')
            ->orderBy('name')
            ->get();
    }

    // =====================
    // TREE VIEW NAVIGATION
    // =====================

    public function toggleLevel1(int $clusterId): void
    {
        if (in_array($clusterId, $this->expandedLevel1)) {
            $this->expandedLevel1 = array_values(array_diff($this->expandedLevel1, [$clusterId]));
        } else {
            $this->expandedLevel1[] = $clusterId;
        }
    }

    public function toggleLevel2(int $clusterId): void
    {
        if (in_array($clusterId, $this->expandedLevel2)) {
            $this->expandedLevel2 = array_values(array_diff($this->expandedLevel2, [$clusterId]));
        } else {
            $this->expandedLevel2[] = $clusterId;
        }
    }

    public function selectCluster(int $clusterId): void
    {
        $cluster = ScienceCluster::find($clusterId);
        if (! $cluster) {
            return;
        }

        $this->selectedClusterId = $clusterId;

        // Ensure parents are expanded
        $this->expandParentsOfCluster($cluster);

        // Reset editing states
        $this->cancelAllEditing();
    }

    public function isLevel1Expanded(int $clusterId): bool
    {
        return in_array($clusterId, $this->expandedLevel1);
    }

    public function isLevel2Expanded(int $clusterId): bool
    {
        return in_array($clusterId, $this->expandedLevel2);
    }

    private function expandParentsOfCluster(ScienceCluster $cluster): void
    {
        if ($cluster->level === 3 && $cluster->parent) {
            // Level 3: expand level 2 parent and level 1 grandparent
            $this->expandedLevel2[] = $cluster->parent_id;
            if ($cluster->parent->parent) {
                $this->expandedLevel1[] = $cluster->parent->parent_id;
            }
        } elseif ($cluster->level === 2 && $cluster->parent) {
            // Level 2: expand level 1 parent
            $this->expandedLevel1[] = $cluster->parent_id;
        } elseif ($cluster->level === 1) {
            // Level 1: just expand itself
            $this->expandedLevel1[] = $cluster->id;
        }

        // Remove duplicates
        $this->expandedLevel1 = array_unique($this->expandedLevel1);
        $this->expandedLevel2 = array_unique($this->expandedLevel2);
    }

    // =====================
    // CLUSTER CRUD
    // =====================

    public function startAddCluster(?int $parentId = null): void
    {
        $this->addingCluster = true;
        $this->newClusterName = '';
        $this->newClusterParentId = $parentId;
        $this->editingClusterId = null;
    }

    public function cancelAddCluster(): void
    {
        $this->addingCluster = false;
        $this->newClusterName = '';
        $this->newClusterParentId = null;
    }

    public function saveNewCluster(): void
    {
        $this->validate([
            'newClusterName' => 'required|string|min:3|max:255',
        ], [], [
            'newClusterName' => 'Nama Cluster',
        ]);

        $data = [
            'name' => $this->newClusterName,
            'parent_id' => $this->newClusterParentId,
        ];

        if ($this->newClusterParentId) {
            $parent = ScienceCluster::findOrFail($this->newClusterParentId);
            if ($parent->level >= 3) {
                $this->addError('newClusterName', 'Tidak dapat menambah cluster di bawah level 3.');

                return;
            }
            $data['level'] = $parent->level + 1;
        } else {
            $data['level'] = 1;
        }

        $cluster = ScienceCluster::create($data);

        $this->toastSuccess('Cluster Sains berhasil ditambahkan');
        $this->addingCluster = false;
        $this->newClusterName = '';
        $this->newClusterParentId = null;

        // Auto-select the new cluster
        $this->selectedClusterId = $cluster->id;
        $this->expandParentsOfCluster($cluster);

        unset($this->level1Clusters, $this->selectedCluster, $this->allClustersForSelect);
    }

    public function startEditCluster(int $clusterId): void
    {
        $cluster = ScienceCluster::find($clusterId);
        if (! $cluster) {
            return;
        }

        $this->editingClusterId = $clusterId;
        $this->clusterNameInput = $cluster->name;
        $this->addingCluster = false;
    }

    public function cancelEditCluster(): void
    {
        $this->editingClusterId = null;
        $this->clusterNameInput = '';
    }

    public function saveCluster(): void
    {
        if (! $this->editingClusterId) {
            return;
        }

        $this->validate([
            'clusterNameInput' => 'required|string|min:3|max:255',
        ], [], [
            'clusterNameInput' => 'Nama Cluster',
        ]);

        $cluster = ScienceCluster::find($this->editingClusterId);
        if ($cluster) {
            $cluster->update(['name' => $this->clusterNameInput]);
        }

        $this->editingClusterId = null;
        $this->clusterNameInput = '';

        $this->toastSuccess('Cluster Sains berhasil disimpan');

        unset($this->level1Clusters, $this->selectedCluster, $this->allClustersForSelect);
    }

    public function deleteCluster(int $clusterId): void
    {
        $cluster = ScienceCluster::find($clusterId);
        if (! $cluster) {
            return;
        }

        // Check if has children
        if ($cluster->children()->count() > 0) {
            $this->toastError('Tidak dapat menghapus cluster yang masih memiliki sub-cluster.');

            return;
        }

        $cluster->delete();

        // Reset selection if deleted cluster was selected
        if ($this->selectedClusterId === $clusterId) {
            $this->selectedClusterId = null;

            // Select first available cluster
            $firstCluster = ScienceCluster::where('level', 1)->orderBy('name')->first();
            if ($firstCluster) {
                $this->selectedClusterId = $firstCluster->id;
                $this->expandedLevel1 = [$firstCluster->id];
            }
        }

        $this->toastSuccess('Cluster Sains berhasil dihapus');

        unset($this->level1Clusters, $this->selectedCluster, $this->allClustersForSelect);
    }

    // =====================
    // HELPERS
    // =====================

    public function cancelAllEditing(): void
    {
        $this->editingClusterId = null;
        $this->clusterNameInput = '';
        $this->addingCluster = false;
        $this->newClusterName = '';
        $this->newClusterParentId = null;
    }

    public function updatedSearch(): void
    {
        unset($this->level1Clusters);
    }
}
