<?php

namespace App\Livewire\Review;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Research extends Component
{
    public function mount(): void
    {
        if (!Auth::user()->hasRole('reviewer')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }

    public function getProposalsProperty(): Collection
    {
        return Proposal::where('detailable_type', 'App\Models\Research')
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->with([
                'submitter',
                'detailable',
                'reviewers' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ])
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.review.research');
    }
}
