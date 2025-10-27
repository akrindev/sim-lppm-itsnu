<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';

    #[Url]
    public string $yearFilter = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->yearFilter = '';
        $this->resetPage();
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
            ->when($this->yearFilter, function ($query) {
                $query->whereYear('created_at', $this->yearFilter);
            })
            ->orderBy('created_at', 'desc')
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
        $query = Proposal::where('detifiable_type', Research::class)
            ->where('submitter_id', Auth::user()->id);

        return [
            'all' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', ['submitted', 'under_review', 'approved'])->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        $years = Proposal::where('detailable_type', Research::class)
            ->where('submitter_id', Auth::user()->id)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return $years;
    }

    public function deleteProposal(int $id): void
    {
        Proposal::find($id)?->delete();
    }
}
