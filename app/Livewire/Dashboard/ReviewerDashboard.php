<?php

namespace App\Livewire\Dashboard;

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

    public $selectedYear;

    public $availableYears = [];

    public function mount()
    {
        $this->user = Auth::user();
        $this->roleName = $this->user->getRoleNames()->first();
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

        // Reviewer stats untuk pengmas
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
            ->where('status', 'completed')->count();

        $researchPending = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\Research');
            })
            ->where('status', 'pending')->count();

        // Statistik Pengmas
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
            ->where('status', 'completed')->count();

        $communityServicePending = ProposalReviewer::where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter)
                    ->where('detailable_type', 'App\Models\CommunityService');
            })
            ->where('status', 'pending')->count();

        $this->stats = [
            'research_assigned' => $researchAssigned,
            'research_completed' => $researchCompleted,
            'research_pending' => $researchPending,
            'community_service_assigned' => $communityServiceAssigned,
            'community_service_completed' => $communityServiceCompleted,
            'community_service_pending' => $communityServicePending,
        ];

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

        // Data pengmas terbaru
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
