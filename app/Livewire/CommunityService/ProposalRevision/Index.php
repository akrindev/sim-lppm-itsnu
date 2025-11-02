<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProposalRevision;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $selectedYear = '';

    #[On('resetFilters')]
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedYear']);
    }

    #[Computed]
    public function proposals(): Collection
    {
        $user = Auth::user();

        $query = Proposal::where('detailable_type', 'App\\Models\\CommunityService');

        // Filter berdasarkan role user
        if ($user->hasRole('dosen')) {
            // Dosen: hanya proposal milik sendiri yang perlu revisi
            $query->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->where('status', ProposalStatus::REVISION_NEEDED);
            });
        } elseif ($user->hasRole('reviewer')) {
            // Reviewer: proposal yang ditugaskan ke dia dengan review completed
            $query->whereHas('reviewers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('status', 'completed');
            });
        } elseif ($user->hasAnyRole(['kepala lppm', 'admin lppm', 'rektor'])) {
            // Kepala LPPM/Admin/Rektor: semua proposal yang sudah ada review completed
            $query->whereHas('reviewers', function ($q) {
                $q->where('status', 'completed');
            });
        }

        // Eager load relationships
        $query->with([
            'submitter.identity',
            'detailable',
            'focusArea',
            'reviewers' => function ($q) {
                $q->where('status', 'completed')
                    ->with('user');
            }
        ]);

        // Search filter
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                    ->orWhereHas('submitter', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'LIKE', $searchTerm);
                    });
            });
        }

        // Year filter
        if (!empty($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        return $query->latest()->get();
    }

    #[Computed]
    public function availableYears()
    {
        $user = Auth::user();

        $query = Proposal::where('detailable_type', 'App\\Models\\CommunityService');

        if ($user->hasRole('dosen')) {
            $query->where('submitter_id', $user->id)
                ->where('status', ProposalStatus::REVISION_NEEDED);
        } elseif ($user->hasRole('reviewer')) {
            $query->whereHas('reviewers', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('status', 'completed');
            });
        } elseif ($user->hasAnyRole(['kepala lppm', 'admin lppm', 'rektor'])) {
            $query->whereHas('reviewers', function ($q) {
                $q->where('status', 'completed');
            });
        }

        return $query->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year');
    }

    public function render()
    {
        return view('livewire.community-service.proposal-revision.index');
    }
}
