<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Models\CommunityService;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pengabdian Masyarakat')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

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
        return view('livewire.community-service.proposal.index', [
            'proposals' => $this->proposals,
            'statusStats' => $this->statusStats,
            'typeStats' => $this->typeStats,
            'availableYears' => $this->availableYears,
        ]);
    }

    public function setSortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function proposals()
    {
        return Proposal::query()
            ->where('detailable_type', CommunityService::class)
            ->where(function ($query) {
                $query->where('submitter_id', Auth::user()->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) {
                        $teamQuery->where('user_id', Auth::user()->id);
                    });
            })
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
            ->when($this->yearFilter, function ($query) {
                $query->whereYear('created_at', $this->yearFilter);
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

        Proposal::where('detailable_type', CommunityService::class)
            ->where(function ($query) {
                $query->where('submitter_id', Auth::user()->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) {
                        $teamQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->get(['status'])
            ->each(function ($proposal) use (&$stats) {
                $stats['all']++;
                if (isset($stats[$proposal->status->value])) {
                    $stats[$proposal->status->value]++;
                }
            });

        return $stats;
    }

    #[Computed]
    public function typeStats(): array
    {
        $query = Proposal::where('detailable_type', CommunityService::class)
            ->where(function ($q) {
                $q->where('submitter_id', Auth::user()->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) {
                        $teamQuery->where('user_id', Auth::user()->id);
                    });
            });

        return [
            'all' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', ['submitted', 'under_review', 'approved'])->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        $years = Proposal::where('detailable_type', CommunityService::class)
            ->where(function ($query) {
                $query->where('submitter_id', Auth::user()->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) {
                        $teamQuery->where('user_id', Auth::user()->id);
                    });
            })
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return $years;
    }

    public function deleteProposal(int $id): void
    {
        $proposal = Proposal::find($id);

        if (! $proposal) {
            session()->flash('error', 'Proposal tidak ditemukan');

            return;
        }

        // Check authorization - user can only delete their own proposals
        if ($proposal->submitter_id !== Auth::user()->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus proposal ini');

            return;
        }

        try {
            $proposal->delete();
            session()->flash('success', 'Proposal berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus proposal: ' . $e->getMessage());
        }
    }
}
