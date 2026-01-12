<?php

namespace App\Livewire\Review;

use App\Models\Proposal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class CommunityService extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $selectedYear = '';

    #[Url]
    public string $statusFilter = 'all'; // all, pending, completed

    public function mount(): void
    {
        if (! Auth::user()->hasRole('reviewer')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    #[On('resetFilters')]
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedYear', 'statusFilter']);
    }

    #[Computed]
    public function proposals(): Collection
    {
        $query = Proposal::where('detailable_type', 'App\Models\CommunityService')
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with([
                'submitter',
                'detailable',
                'reviewers' => function ($query) {
                    $query->where('user_id', Auth::id());
                },
            ]);

        if (! empty($this->search)) {
            $searchTerm = '%'.$this->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                    ->orWhereHas('submitter', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'LIKE', $searchTerm);
                    });
            });
        }

        if (! empty($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        if ($this->statusFilter !== 'all') {
            $query->whereHas('reviewers', function ($q) {
                $q->where('user_id', Auth::id())
                    ->where('status', $this->statusFilter);
            });
        }

        return $query->latest()->get();
    }

    #[Computed]
    public function availableYears()
    {
        return Proposal::where('detailable_type', 'App\Models\CommunityService')
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year');
    }

    #[Computed]
    public function statusStats(): array
    {
        $baseQuery = Proposal::where('detailable_type', 'App\Models\CommunityService')
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            });

        return [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->whereHas('reviewers', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'pending');
            })->count(),
            'completed' => (clone $baseQuery)->whereHas('reviewers', function ($q) {
                $q->where('user_id', Auth::id())->where('status', 'completed');
            })->count(),
        ];
    }

    public function render()
    {
        return view('livewire.review.community-service');
    }
}
