<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\FinalReport;

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

        $query = Proposal::where('detailable_type', 'App\Models\CommunityService')
            ->where('status', ProposalStatus::COMPLETED);

        // Only show to proposal owner or team members
        $query->where(function ($q) use ($user) {
            $q->where('submitter_id', $user->id)
                ->orWhereHas('teamMembers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                        ->where('status', 'accepted');
                });
        });

        // Eager load relationships
        $query->with([
            'submitter.identity',
            'researchScheme',
            'focusArea',
            'progressReports' => fn ($q) => $q->finalReports()->latest(),
        ]);

        // Search filter
        if (! empty($this->search)) {
            $searchTerm = '%'.$this->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                    ->orWhereHas('submitter', function ($sq) use ($searchTerm) {
                        $sq->where('name', 'LIKE', $searchTerm);
                    });
            });
        }

        // Year filter
        if (! empty($this->selectedYear)) {
            $query->whereYear('created_at', $this->selectedYear);
        }

        return $query->latest()->get();
    }

    #[Computed]
    public function availableYears()
    {
        $user = Auth::user();

        $query = Proposal::where('detailable_type', 'App\Models\CommunityService')
            ->where('status', ProposalStatus::COMPLETED);

        // Only show to proposal owner or team members
        $query->where(function ($q) use ($user) {
            $q->where('submitter_id', $user->id)
                ->orWhereHas('teamMembers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                        ->where('status', 'accepted');
                });
        });

        return $query->selectRaw('DISTINCT YEAR(created_at) as year')
            ->orderByDesc('year')
            ->pluck('year');
    }

    public function render()
    {
        return view('livewire.community-service.final-report.index');
    }
}
