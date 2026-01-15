<?php

namespace App\Livewire\AdminLppm;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReviewerWorkload extends Component
{
    public function mount()
    {
        if (! Auth::user()->hasRole('admin lppm')) {
            abort(403);
        }
    }

    #[Computed]
    public function reviewers()
    {
        return User::role('reviewer')
            ->withCount([
                'reviews as total_assigned',
                'reviews as pending_count' => function ($query) {
                    $query->where('status', 'pending');
                },
                'reviews as completed_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->with(['identity.faculty'])
            ->get();
    }

    public function render()
    {
        return view('livewire.admin-lppm.reviewer-workload');
    }
}
