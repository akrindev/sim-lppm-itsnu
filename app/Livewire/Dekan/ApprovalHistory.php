<?php

namespace App\Livewire\Dekan;

use App\Models\ProposalStatusLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalHistory extends Component
{
    use WithPagination;

    public function mount()
    {
        if (! Auth::user()->hasRole('dekan')) {
            abort(403);
        }
    }

    #[Computed]
    public function history()
    {
        return ProposalStatusLog::where('user_id', Auth::id())
            ->whereHas('proposal', function ($query) {
                // Optionally filter by faculty again just in case
                $facultyId = Auth::user()->identity?->faculty_id;
                if ($facultyId) {
                    $query->whereHas('submitter.identity', function ($q) use ($facultyId) {
                        $q->where('faculty_id', $facultyId);
                    });
                }
            })
            ->with(['proposal.submitter', 'proposal.detailable'])
            ->latest('at')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.dekan.approval-history');
    }
}
