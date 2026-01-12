<?php

namespace App\Livewire\Review;

use App\Models\ProposalReviewer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewHistory extends Component
{
    use WithPagination;

    public function mount()
    {
        if (! Auth::user()->hasRole('reviewer')) {
            abort(403);
        }
    }

    #[Computed]
    public function history()
    {
        return ProposalReviewer::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->with(['proposal.submitter', 'proposal.detailable'])
            ->latest('updated_at')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.review.review-history');
    }
}
