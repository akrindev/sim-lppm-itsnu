<?php

namespace App\Livewire;

use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\Research;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public $roleName;

    public $stats = [];

    public $recentProposals = [];

    public $reviewerStats = [];

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
        match ($this->roleName) {
            'superadmin', 'admin lppm', 'admin lppm saintek', 'admin lppm dekabita' => $this->loadAdminAnalytics(),
            'kepala lppm' => $this->loadKepalaLppmAnalytics(),
            'dosen' => $this->loadDosenAnalytics(),
            'reviewer' => $this->loadReviewerAnalytics(),
            'rektor', 'dekan' => $this->loadExecAnalytics(),
            default => $this->loadDefaultAnalytics(),
        };
    }

    private function loadAdminAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->stats = [
            'total_proposals' => Proposal::whereYear('created_at', $yearFilter)->count(),
            'total_research' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', Research::class)->count(),
            'total_community_service' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', CommunityService::class)->count(),
            'total_dosen' => User::role('dosen')->count(),
            'pending_review' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'submitted')->count(),
            'approved_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'approved')->count(),
            'rejected_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'rejected')->count(),
        ];

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereYear('created_at', $yearFilter)
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadKepalaLppmAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->stats = [
            'total_proposals' => Proposal::whereYear('created_at', $yearFilter)->count(),
            'pending_review' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'reviewed')->count(),
            'approved_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'approved')->count(),
            'rejected_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'rejected')->count(),
            'completed_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'completed')->count(),
        ];

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereYear('created_at', $yearFilter)
            ->whereIn('status', ['reviewed', 'approved', 'rejected'])
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadDosenAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->stats = [
            'my_proposals' => Proposal::where('submitter_id', $this->user->id)
                ->whereYear('created_at', $yearFilter)->count(),
            'as_team_member' => $this->user->proposals()->whereYear('proposals.created_at', $yearFilter)->count(),
            'pending_review' => Proposal::where('submitter_id', $this->user->id)
                ->whereYear('created_at', $yearFilter)
                ->where('status', 'submitted')->count(),
            'approved' => Proposal::where('submitter_id', $this->user->id)
                ->whereYear('created_at', $yearFilter)
                ->where('status', 'approved')->count(),
        ];

        $this->recentProposals = Proposal::with(['detailable'])
            ->where(function ($query) use ($yearFilter) {
                $query->where('submitter_id', $this->user->id)
                    ->whereYear('created_at', $yearFilter)
                    ->orWhereHas('teamMembers', function ($q) use ($yearFilter) {
                        $q->where('user_id', $this->user->id)
                            ->whereYear('proposals.created_at', $yearFilter);
                    });
            })
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadReviewerAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->reviewerStats = ProposalReviewer::with('proposal')
            ->where('user_id', $this->user->id)
            ->whereHas('proposal', function ($query) use ($yearFilter) {
                $query->whereYear('created_at', $yearFilter);
            })
            ->get();

        $this->stats = [
            'assigned_to_review' => ProposalReviewer::where('user_id', $this->user->id)
                ->whereHas('proposal', function ($query) use ($yearFilter) {
                    $query->whereYear('created_at', $yearFilter);
                })->count(),
            'completed_reviews' => ProposalReviewer::where('user_id', $this->user->id)
                ->whereHas('proposal', function ($query) use ($yearFilter) {
                    $query->whereYear('created_at', $yearFilter);
                })
                ->where('status', 'completed')->count(),
            'pending_reviews' => ProposalReviewer::where('user_id', $this->user->id)
                ->whereHas('proposal', function ($query) use ($yearFilter) {
                    $query->whereYear('created_at', $yearFilter);
                })
                ->where('status', 'pending')->count(),
        ];

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereHas('reviewers', function ($query) {
                $query->where('user_id', $this->user->id);
            })
            ->whereYear('created_at', $yearFilter)
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadExecAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->stats = [
            'total_proposals' => Proposal::whereYear('created_at', $yearFilter)->count(),
            'total_research' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', Research::class)->count(),
            'total_community_service' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', CommunityService::class)->count(),
            'approved_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'approved')->count(),
            'completed_proposals' => Proposal::whereYear('created_at', $yearFilter)
                ->where('status', 'completed')->count(),
        ];

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereYear('created_at', $yearFilter)
            ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadDefaultAnalytics()
    {
        $yearFilter = $this->selectedYear;

        $this->stats = [
            'total_proposals' => Proposal::whereYear('created_at', $yearFilter)->count(),
        ];

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereYear('created_at', $yearFilter)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
