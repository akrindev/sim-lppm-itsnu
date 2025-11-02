<?php

namespace App\Livewire\CommunityService;

use App\Models\CommunityService;
use App\Models\Proposal;
use Illuminate\View\View;
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
    public string $sortBy = 'created_at';

    #[Reactive]
    public string $sortDirection = 'desc';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
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
        return view('livewire.community-service.index', [
            'proposals' => $this->getProposals(),
            'statusStats' => $this->getStatusStats(),
            'sortBy' => $this->sortBy,
            'sortDirection' => $this->sortDirection,
        ]);
    }

    protected function getProposals()
    {
        return Proposal::query()
            ->where('detailable_type', CommunityService::class)
            ->with(['submitter', 'focusArea'])
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

    protected function getStatusStats(): array
    {
        $stats = [
            'all' => 0,
            'draft' => 0,
            'submitted' => 0,
            'under_review' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
        ];

        Proposal::where('detailable_type', CommunityService::class)
            ->get(['status'])
            ->each(function ($proposal) use (&$stats) {
                $stats['all']++;
                if (isset($stats[$proposal->status->value])) {
                    $stats[$proposal->status->value]++;
                }
            });

        return $stats;
    }

    public function deleteProposal(int $id): void
    {
        Proposal::find($id)?->delete();
    }
}
