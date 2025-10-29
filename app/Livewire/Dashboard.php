<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\ProposalReviewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $user;
    public $roleName;
    public $stats = [];
    public $recentProposals = [];
    public $proposalsByStatus = [];
    public $proposalsByType = [];
    public $proposalsByMonth = [];
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

        $this->proposalsByStatus = Proposal::whereYear('created_at', $yearFilter)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $this->proposalsByType = [
            'research' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', Research::class)->count(),
            'community_service' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', CommunityService::class)->count(),
        ];

        $this->proposalsByMonth = Proposal::whereYear('created_at', $yearFilter)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

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

        $this->proposalsByStatus = Proposal::whereYear('created_at', $yearFilter)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $this->proposalsByMonth = Proposal::whereYear('created_at', $yearFilter)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

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

        $this->proposalsByStatus = Proposal::select('status', DB::raw('count(*) as count'))
            ->where(function ($query) use ($yearFilter) {
                $query->where('submitter_id', $this->user->id)
                    ->whereYear('created_at', $yearFilter)
                    ->orWhereHas('teamMembers', function ($q) use ($yearFilter) {
                        $q->where('user_id', $this->user->id)
                          ->whereYear('proposals.created_at', $yearFilter);
                    });
            })
            ->groupBy('status')
            ->get();

        $this->proposalsByMonth = Proposal::whereYear('created_at', $yearFilter)
            ->where(function ($query) {
                $query->where('submitter_id', $this->user->id)
                    ->orWhereHas('teamMembers', function ($q) {
                        $q->where('user_id', $this->user->id);
                    });
            })
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

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

        $this->proposalsByStatus = Proposal::select('p.status', DB::raw('count(*) as count'))
            ->from('proposals as p')
            ->join('proposal_reviewer as pr', 'p.id', '=', 'pr.proposal_id')
            ->where('pr.user_id', $this->user->id)
            ->whereYear('p.created_at', $yearFilter)
            ->groupBy('p.status')
            ->get();

        $this->proposalsByMonth = Proposal::from('proposals as p')
            ->join('proposal_reviewer as pr', 'p.id', '=', 'pr.proposal_id')
            ->where('pr.user_id', $this->user->id)
            ->whereYear('p.created_at', $yearFilter)
            ->select(
                DB::raw('DATE_FORMAT(p.created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

        $this->recentProposals = Proposal::with(['submitter', 'detailable'])
            ->whereHas('reviewers', function ($query) use ($yearFilter) {
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

        $this->proposalsByStatus = Proposal::whereYear('created_at', $yearFilter)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $this->proposalsByType = [
            'research' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', Research::class)->count(),
            'community_service' => Proposal::whereYear('created_at', $yearFilter)
                ->where('detailable_type', CommunityService::class)->count(),
        ];

        $this->proposalsByMonth = Proposal::whereYear('created_at', $yearFilter)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get();

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

    public function getChartDataProperty()
    {
        // Get proposal types chart data
        $proposalTypeData = [
            'series' => [
                $this->proposalsByType['research'] ?? 0,
                $this->proposalsByType['community_service'] ?? 0,
            ],
            'labels' => ['Penelitian', 'Pengmas'],
        ];

        // Get status distribution chart data
        $statusData = [
            'series' => [],
            'labels' => [],
        ];

        foreach ($this->proposalsByStatus as $item) {
            $statusData['series'][] = $item->count;
            $statusData['labels'][] = ucfirst($item->status);
        }

        // Get monthly trends chart data
        $monthlyData = [
            'series' => [
                [
                    'name' => 'Proposal',
                    'data' => [],
                ],
            ],
            'categories' => [],
        ];

        foreach ($this->proposalsByMonth as $item) {
            $monthlyData['categories'][] = date('M Y', strtotime($item->month . '-01'));
            $monthlyData['series'][0]['data'][] = $item->count;
        }

        return [
            'proposal_types' => $proposalTypeData,
            'status_distribution' => $statusData,
            'monthly_trends' => $monthlyData,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
