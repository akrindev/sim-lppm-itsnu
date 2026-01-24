<?php

namespace App\Livewire\Dashboard;

use App\Exports\ResearchProposalExport;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class AdminDashboard extends Component
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

    public function exportResearch()
    {
        return Excel::download(
            new ResearchProposalExport($this->selectedYear),
            "research-proposals-{$this->selectedYear}.xlsx"
        );
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

        // OPTIMIZED: Single aggregated query for all stats (replaces 9 separate count queries)
        $this->loadStats($yearFilter);

        // Load recent proposals
        $this->loadRecentProposals($yearFilter);
    }

    /**
     * Load all stats in a single aggregated query.
     * Replaces 9 separate count queries with 1 grouped query.
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

        // Get total dosen count (single query, cached)
        $totalDosen = User::role('dosen')->count();

        return [
            'total_research' => $research->sum('count'),
            'total_community_service' => $communityService->sum('count'),
            'research_pending' => $research->where('status', 'submitted')->sum('count'),
            'community_service_pending' => $communityService->where('status', 'submitted')->sum('count'),
            'research_approved' => $research->where('status', 'approved')->sum('count'),
            'community_service_approved' => $communityService->where('status', 'approved')->sum('count'),
            'research_rejected' => $research->where('status', 'rejected')->sum('count'),
            'community_service_rejected' => $communityService->where('status', 'rejected')->sum('count'),
            'total_dosen' => $totalDosen,
        ];
    }

    /**
     * Load recent proposals in a single query.
     */
    private function loadRecentProposals(string $yearFilter): void
    {
        $recentProposals = Proposal::with(['submitter'])
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
        return view('livewire.dashboard.admin-dashboard');
    }
}
