<?php

namespace App\Livewire\Dashboard;

use App\Enums\ReviewStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReviewerDashboard extends Component
{
    public $user;

    public $roleName;

    public $stats = [];

    public $recentResearch = [];

    public $recentCommunityService = [];

    public $researchReviewerStats = [];

    public $communityServiceReviewerStats = [];

    public $overdueReviews = [];

    public $dueSoonReviews = [];

    public $reReviewNeeded = [];

    public $selectedYear;

    public $availableYears = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->roleName = active_role();
        $this->selectedYear = date('Y');
        $this->availableYears = $this->getAvailableYears();

        $this->loadAnalytics();
    }

    public function updatedSelectedYear()
    {
        $this->loadAnalytics();
    }

    private function getAvailableYears(): array
    {
        $years = Proposal::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        return $years;
    }

    public function loadAnalytics()
    {
        $yearFilter = $this->selectedYear;

        // Reviewer stats untuk penelitian
        $this->researchReviewerStats = ProposalReviewer::with('proposal')
            ->where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })
            ->get();

        // Reviewer stats untuk PKM
        $this->communityServiceReviewerStats = ProposalReviewer::with('proposal')
            ->where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })
            ->get();

        // Statistik Penelitian
        $researchAssigned = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })->count();

        $researchCompleted = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })
            ->where('status', ReviewStatus::COMPLETED)->count();

        $researchPending = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })
            ->where('status', ReviewStatus::PENDING)->count();

        $researchReReview = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })
            ->where('status', ReviewStatus::RE_REVIEW_REQUESTED)->count();

        // Statistik PKM
        $communityServiceAssigned = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })->count();

        $communityServiceCompleted = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })
            ->where('status', ReviewStatus::COMPLETED)->count();

        $communityServicePending = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })
            ->where('status', ReviewStatus::PENDING)->count();

        $communityServiceReReview = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })
            ->where('status', ReviewStatus::RE_REVIEW_REQUESTED)->count();

        $this->stats = [
            'research_assigned' => $researchAssigned,
            'research_completed' => $researchCompleted,
            'research_pending' => $researchPending,
            'research_re_review' => $researchReReview,
            'community_service_assigned' => $communityServiceAssigned,
            'community_service_completed' => $communityServiceCompleted,
            'community_service_pending' => $communityServicePending,
            'community_service_re_review' => $communityServiceReReview,
        ];

        // Overdue reviews (past deadline, not completed)
        $this->overdueReviews = ProposalReviewer::with(['proposal.submitter', 'proposal.detailable'])
            ->where('user_id', $this->user->id)
            ->overdue()
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter);
            })
            ->orderBy('deadline_at', 'asc')
            ->limit(10)
            ->get();

        // Due soon reviews (within 3 days)
        $this->dueSoonReviews = ProposalReviewer::with(['proposal.submitter', 'proposal.detailable'])
            ->where('user_id', $this->user->id)
            ->deadlineApproaching(3)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter);
            })
            ->orderBy('deadline_at', 'asc')
            ->limit(10)
            ->get();

        // Re-review needed
        $this->reReviewNeeded = ProposalReviewer::with(['proposal.submitter', 'proposal.detailable'])
            ->where('user_id', $this->user->id)
            ->where('status', ReviewStatus::RE_REVIEW_REQUESTED)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter);
            })
            ->orderBy('assigned_at', 'desc')
            ->limit(10)
            ->get();

        // Data penelitian terbaru
        $this->recentResearch = Proposal::with(['submitter'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', $this->user->id);
            })
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->latest()
            ->limit(10)
            ->get();

        // Data PKM terbaru
        $this->recentCommunityService = Proposal::with(['submitter'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', $this->user->id);
            })
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.reviewer-dashboard');
    }
}
