<?php

namespace App\Livewire\Dashboard;

use App\Models\Proposal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class KepalaLppmDashboard extends Component
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

        // OPTIMIZED: Single aggregated query for all stats
        $this->loadStats($yearFilter);

        // Load recent proposals
        $this->loadRecentProposals($yearFilter);
    }

    /**
     * Load all stats in a single aggregated query.
     * Replaces 9+ separate count queries with 1 grouped query.
     */
    private function loadStats(string $yearFilter): void
    {
        $statsRaw = Proposal::query()
            ->whereYear('created_at', $yearFilter)
            ->select([
                'detailable_type',
                'status',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('detailable_type', 'status')
            ->get();

        $this->stats = $this->transformStats($statsRaw);
    }

    /**
     * Transform raw stats query result into stats array.
     */
    private function transformStats(Collection $raw): array
    {
        $research = $raw->filter(fn ($r) => str_contains($r->detailable_type, 'Research'));
        $communityService = $raw->filter(fn ($r) => str_contains($r->detailable_type, 'CommunityService'));

        $researchPending = $research->where('status', 'reviewed')->sum('count');
        $communityServicePending = $communityService->where('status', 'reviewed')->sum('count');

        return [
            'total_research' => $research->sum('count'),
            'total_community_service' => $communityService->sum('count'),
            'research_pending' => $researchPending,
            'community_service_pending' => $communityServicePending,
            'research_approved' => $research->where('status', 'approved')->sum('count'),
            'community_service_approved' => $communityService->where('status', 'approved')->sum('count'),
            'research_completed' => $research->where('status', 'completed')->sum('count'),
            'community_service_completed' => $communityService->where('status', 'completed')->sum('count'),
            'pending_initial_approval' => $raw->where('status', 'approved')->sum('count'),
            'pending_final_decision' => $researchPending + $communityServicePending,
        ];
    }

    /**
     * Load recent proposals in a single query.
     */
    private function loadRecentProposals(string $yearFilter): void
    {
        $relevantStatuses = ['reviewed', 'approved', 'rejected', 'completed'];

        $recentProposals = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->whereIn('status', $relevantStatuses)
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
        return view('livewire.dashboard.kepala-lppm-dashboard');
    }
}
