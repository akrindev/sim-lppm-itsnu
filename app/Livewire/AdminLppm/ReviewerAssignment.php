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

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = 'all';

    #[Url]
    public string $yearFilter = '';

    #[Url]
    public string $assignmentFilter = 'all'; // all, assigned, unassigned

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
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('summary', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $detailableType = $this->typeFilter === 'research'
                    ? \App\Models\Research::class
                    : \App\Models\CommunityService::class;
                $query->where('detailable_type', $detailableType);
            })
            ->when($this->yearFilter, function ($query) {
                $query->whereYear('created_at', $this->yearFilter);
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
