<?php

namespace App\Livewire\Review;

use App\Models\ReviewLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewHistory extends Component
{
    use WithPagination;

    #[Url]
    public string $roundFilter = '';

    #[Url]
    public string $recommendationFilter = '';

    public function mount(): void
    {
        if (! Auth::user()->hasRole('reviewer')) {
            abort(403);
        }
    }

    #[Computed]
    public function history()
    {
        $query = ReviewLog::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->with(['proposal.submitter', 'proposal.detailable']);

        if (! empty($this->roundFilter)) {
            $query->where('round', $this->roundFilter);
        }

        if (! empty($this->recommendationFilter)) {
            $query->where('recommendation', $this->recommendationFilter);
        }

        return $query->latest('completed_at')
            ->paginate(15);
    }

    #[Computed]
    public function historyByProposal()
    {
        return ReviewLog::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->with(['proposal.submitter', 'proposal.detailable'])
            ->latest('completed_at')
            ->get()
            ->groupBy('proposal_id');
    }

    #[Computed]
    public function availableRounds(): array
    {
        return ReviewLog::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->distinct()
            ->pluck('round')
            ->sort()
            ->values()
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        $baseQuery = ReviewLog::where('user_id', Auth::id())
            ->whereNotNull('completed_at');

        return [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('recommendation', 'approved')->count(),
            'revision_needed' => (clone $baseQuery)->where('recommendation', 'revision_needed')->count(),
            'rejected' => (clone $baseQuery)->where('recommendation', 'rejected')->count(),
        ];
    }

    public function resetFilters(): void
    {
        $this->reset(['roundFilter', 'recommendationFilter']);
    }

    public function render()
    {
        return view('livewire.review.review-history');
    }
}
