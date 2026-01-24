<?php

namespace App\Livewire\Dashboard;

use App\Models\Proposal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ExecDashboard extends Component
{
    public $user;

    public $roleName;

    public $stats = [];

    public $recentResearch = [];

    public $recentCommunityService = [];

    public $selectedYear;

    public $selectedSemester = 'all';

    public $availableYears = [];

    public $periodicSummary = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->roleName = active_role();
        $this->selectedYear = (int) date('Y');
        $this->availableYears = $this->getAvailableYears();

        $this->loadAnalytics();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedSemester(): void
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
        $semesterFilter = $this->selectedSemester;
        $facultyId = $this->roleName === 'dekan' ? $this->user->identity?->faculty_id : null;

        // OPTIMIZED: Single aggregated query for all stats
        $this->loadStats($yearFilter, $semesterFilter, $facultyId);

        // Load recent proposals
        $this->loadRecentProposals($yearFilter, $semesterFilter, $facultyId);

        // Load periodic summary with optimized queries
        $this->periodicSummary = $this->getPeriodicSummary($facultyId);
    }

    /**
     * Build base query with filters applied.
     */
    private function buildBaseQuery(int $yearFilter, string $semesterFilter, ?int $facultyId)
    {
        $query = Proposal::whereYear('created_at', $yearFilter);

        if ($semesterFilter !== 'all') {
            if ($semesterFilter === '1') {
                $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
            } else {
                $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
            }
        }

        if ($facultyId) {
            $query->whereHas('submitter.identity', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        return $query;
    }

    /**
     * Load all stats in a single aggregated query.
     */
    private function loadStats(int $yearFilter, string $semesterFilter, ?int $facultyId): void
    {
        $query = $this->buildBaseQuery($yearFilter, $semesterFilter, $facultyId);

        $statsRaw = (clone $query)
            ->select([
                'detailable_type',
                'status',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('detailable_type', 'status')
            ->get();

        $this->stats = $this->transformStats($statsRaw, $facultyId);
    }

    /**
     * Transform raw stats query result into stats array.
     */
    private function transformStats(Collection $raw, ?int $facultyId): array
    {
        $research = $raw->filter(fn ($r) => str_contains($r->detailable_type, 'Research'));
        $communityService = $raw->filter(fn ($r) => str_contains($r->detailable_type, 'CommunityService'));

        return [
            'total_research' => $research->sum('count'),
            'total_community_service' => $communityService->sum('count'),
            'research_approved' => $research->where('status', 'approved')->sum('count'),
            'community_service_approved' => $communityService->where('status', 'approved')->sum('count'),
            'faculty_name' => $facultyId ? $this->user->identity?->faculty?->name : null,
        ];
    }

    /**
     * Load recent proposals in a single query.
     */
    private function loadRecentProposals(int $yearFilter, string $semesterFilter, ?int $facultyId): void
    {
        $query = $this->buildBaseQuery($yearFilter, $semesterFilter, $facultyId);

        $recentProposals = (clone $query)
            ->with(['submitter'])
            ->whereIn('status', ['approved', 'completed'])
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

    /**
     * Get periodic summary with optimized queries.
     * Uses single query per period instead of multiple.
     */
    private function getPeriodicSummary(?int $facultyId): array
    {
        $currentYear = (int) date('Y');
        $summary = [];

        for ($year = $currentYear; $year >= $currentYear - 4; $year--) {
            foreach ([2, 1] as $semester) {
                $query = Proposal::whereYear('created_at', $year);

                if ($semester === 1) {
                    $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                } else {
                    $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                }

                if ($facultyId) {
                    $query->whereHas('submitter.identity', function ($q) use ($facultyId) {
                        $q->where('faculty_id', $facultyId);
                    });
                }

                $data = (clone $query)->select('detailable_type', 'status', DB::raw('count(*) as count'))
                    ->groupBy('detailable_type', 'status')
                    ->get();

                $researchTotal = $data->filter(fn ($d) => str_contains($d->detailable_type, 'Research'))->sum('count');
                $researchApproved = $data->filter(fn ($d) => str_contains($d->detailable_type, 'Research'))
                    ->where('status', 'approved')->sum('count');

                $pkmTotal = $data->filter(fn ($d) => str_contains($d->detailable_type, 'CommunityService'))->sum('count');
                $pkmApproved = $data->filter(fn ($d) => str_contains($d->detailable_type, 'CommunityService'))
                    ->where('status', 'approved')->sum('count');

                if ($researchTotal > 0 || $pkmTotal > 0) {
                    $summary[] = [
                        'year' => $year,
                        'semester' => $semester,
                        'research_total' => $researchTotal,
                        'research_approved' => $researchApproved,
                        'pkm_total' => $pkmTotal,
                        'pkm_approved' => $pkmApproved,
                    ];
                }
            }
        }

        return $summary;
    }

    public function render()
    {
        return view('livewire.dashboard.exec-dashboard');
    }
}
