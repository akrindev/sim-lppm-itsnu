<?php

namespace App\Livewire\AdminLppm;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewerAssignment extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $typeFilter = 'all';

    #[Url(history: true)]
    public string $yearFilter = '';

    #[Url(history: true)]
    public string $assignmentFilter = 'all'; // all, assigned, unassigned

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function updatedAssignmentFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->typeFilter = 'all';
        $this->yearFilter = '';
        $this->assignmentFilter = 'all';
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin-lppm.reviewer-assignment');
    }

    #[Computed]
    public function proposals()
    {
        // Include both WAITING_REVIEWER (new) and UNDER_REVIEW (existing) statuses
        $query = Proposal::query()
            ->whereIn('status', [
                ProposalStatus::WAITING_REVIEWER,
                ProposalStatus::UNDER_REVIEW,
            ]);

        return $query
            ->with([
                'submitter.identity',
                'detailable',
                'focusArea',
                'researchScheme',
                'reviewers.user',
            ])
            ->when($this->search, function ($query) {
                $search = (string) $this->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%");
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $type = (string) $this->typeFilter;
                if (in_array($type, ['research', 'community_service'])) {
                    $detailableType = $type === 'research'
                        ? \App\Models\Research::class
                        : \App\Models\CommunityService::class;
                    $query->where('detailable_type', $detailableType);
                }
            })
            ->when($this->yearFilter, function ($query) {
                $year = (int) $this->yearFilter;
                if ($year > 2000 && $year < 2100) {
                    $query->whereYear('created_at', $year);
                }
            })
            ->when($this->assignmentFilter === 'assigned', function ($query) {
                $query->has('reviewers');
            })
            ->when($this->assignmentFilter === 'unassigned', function ($query) {
                $query->doesntHave('reviewers');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    #[Computed]
    public function statusStats(): array
    {
        $statuses = [ProposalStatus::WAITING_REVIEWER, ProposalStatus::UNDER_REVIEW];

        return [
            'all' => Proposal::whereIn('status', $statuses)->count(),
            'waiting_reviewer' => Proposal::where('status', ProposalStatus::WAITING_REVIEWER)->count(),
            'under_review' => Proposal::where('status', ProposalStatus::UNDER_REVIEW)->count(),
            'research' => Proposal::whereIn('status', $statuses)
                ->where('detailable_type', \App\Models\Research::class)
                ->count(),
            'community_service' => Proposal::whereIn('status', $statuses)
                ->where('detailable_type', \App\Models\CommunityService::class)
                ->count(),
            'assigned' => Proposal::whereIn('status', $statuses)
                ->has('reviewers')
                ->count(),
            'unassigned' => Proposal::whereIn('status', $statuses)
                ->doesntHave('reviewers')
                ->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        $statuses = [ProposalStatus::WAITING_REVIEWER, ProposalStatus::UNDER_REVIEW];

        $years = Proposal::whereIn('status', $statuses)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return $years;
    }
}
