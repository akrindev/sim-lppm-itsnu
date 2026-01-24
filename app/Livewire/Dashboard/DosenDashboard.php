<?php

namespace App\Livewire\Dashboard;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DosenDashboard extends Component
{
    public $user;

    public $roleName;

    public $stats = [];

    public $recentResearch = [];

    public $recentCommunityService = [];

    public $selectedYear;

    public $availableYears = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->roleName = active_role();
        $this->selectedYear = date('Y');
        $this->availableYears = $this->getAvailableYears();

        $this->loadAnalytics();
    }

    public function updatedSelectedYear(): void
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

    public function loadAnalytics(): void
    {
        $yearFilter = $this->selectedYear;

        // OPTIMIZED: Single aggregated query for own proposals stats
        $this->loadStats($yearFilter);

        // Load as member counts (2 separate queries needed due to relationship)
        $this->loadMemberStats($yearFilter);

        // Load recent proposals
        $this->loadRecentProposals($yearFilter);
    }

    /**
     * Load all stats in a single aggregated query.
     * Replaces 6 separate count queries with 1 grouped query.
     */
    private function loadStats(string $yearFilter): void
    {
        $statsRaw = Proposal::query()
            ->where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->select([
                'detailable_type',
                'status',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('detailable_type', 'status')
            ->get();

        $research = $statsRaw->filter(fn ($r) => str_contains($r->detailable_type, 'Research'));
        $communityService = $statsRaw->filter(fn ($r) => str_contains($r->detailable_type, 'CommunityService'));

        $this->stats = [
            'my_research' => $research->sum('count'),
            'my_community_service' => $communityService->sum('count'),
            'research_pending' => $research->where('status', 'submitted')->sum('count'),
            'community_service_pending' => $communityService->where('status', 'submitted')->sum('count'),
            'research_approved' => $research->where('status', 'approved')->sum('count'),
            'community_service_approved' => $communityService->where('status', 'approved')->sum('count'),
        ];
    }

    /**
     * Load member stats in optimized queries.
     * Uses raw query to avoid pivot column conflicts with GROUP BY.
     */
    private function loadMemberStats(string $yearFilter): void
    {
        // Use raw query builder to avoid pivot columns being auto-included
        $memberStats = DB::table('proposals')
            ->join('proposal_user', 'proposals.id', '=', 'proposal_user.proposal_id')
            ->where('proposal_user.user_id', $this->user->id)
            ->whereYear('proposals.created_at', $yearFilter)
            ->select([
                'proposals.detailable_type',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('proposals.detailable_type')
            ->get();

        $this->stats['research_as_member'] = $memberStats
            ->filter(fn ($r) => str_contains($r->detailable_type, 'Research'))
            ->sum('count');

        $this->stats['community_service_as_member'] = $memberStats
            ->filter(fn ($r) => str_contains($r->detailable_type, 'CommunityService'))
            ->sum('count');
    }

    /**
     * Load recent proposals in a single query.
     */
    private function loadRecentProposals(string $yearFilter): void
    {
        $recentProposals = Proposal::query()
            ->where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->latest()
            ->limit(20)
            ->get();

        $this->recentResearch = $recentProposals
            ->filter(fn ($p) => str_contains($p->detailable_type, 'Research'))
            ->take(10)
            ->values();

        $this->recentCommunityService = $recentProposals
            ->filter(fn ($p) => str_contains($p->detailable_type, 'CommunityService'))
            ->take(10)
            ->values();
    }

    public function render()
    {
        return view('livewire.dashboard.dosen-dashboard');
    }
}
