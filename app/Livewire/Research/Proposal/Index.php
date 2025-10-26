<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[Reactive]
    public string $statusFilter = 'all';

    #[Reactive]
    public string $typeFilter = 'all';

    #[Reactive]
    public string $sortBy = 'created_at';

    #[Reactive]
    public string $sortDirection = 'desc';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->typeFilter = 'all';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal.index');
    }

    #[Computed]
    public function proposals()
    {
        return Proposal::query()
            ->where('detailable_type', Research::class)
            ->where('submitter_id', Auth::user()->id)
            ->with(['submitter', 'focusArea', 'researchScheme'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('summary', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    #[Computed]
    public function statusStats(): array
    {
        $stats = [
            'all' => 0,
            'draft' => 0,
            'submitted' => 0,
            'reviewed' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
        ];

        Proposal::where('detailable_type', Research::class)
            ->where('submitter_id', Auth::user()->id)
            ->get(['status'])
            ->each(function ($proposal) use (&$stats) {
                $stats['all']++;
                if (isset($stats[$proposal->status])) {
                    $stats[$proposal->status]++;
                }
            });

        return $stats;
    }

    #[Computed]
    public function typeStats(): array
    {
        $query = Proposal::where('detailable_type', Research::class)
            ->where('submitter_id', Auth::user()->id);

        return [
            'all' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', ['submitted', 'under_review', 'approved'])->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
    }

    public function deleteProposal(int $id): void
    {
        Proposal::find($id)?->delete();
    }
}
