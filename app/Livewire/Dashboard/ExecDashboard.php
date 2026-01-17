<?php

namespace App\Livewire\Dashboard;

use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\Research;
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

    public function mount()
    {
        $this->user = Auth::user();
        $this->roleName = active_role();
        $this->selectedYear = (int) date('Y');
        $this->availableYears = $this->getAvailableYears();

        $this->loadAnalytics();
    }

    public function updatedSelectedYear()
    {
        $this->loadAnalytics();
    }

    public function updatedSelectedSemester()
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
        $semesterFilter = $this->selectedSemester;
        $facultyId = $this->roleName === 'dekan' ? $this->user->identity?->faculty_id : null;

        // Base query for stats
        $baseQuery = Proposal::whereYear('created_at', $yearFilter);

        if ($semesterFilter !== 'all') {
            if ($semesterFilter === '1') {
                $baseQuery->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
            } else {
                $baseQuery->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
            }
        }

        if ($facultyId) {
            $baseQuery->whereHas('submitter.identity', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        // Statistik Penelitian
        $totalResearch = (clone $baseQuery)->where('detailable_type', Research::class)->count();

        $researchApproved = (clone $baseQuery)->where('detailable_type', Research::class)
            ->where('status', 'approved')->count();

        // Statistik PKM
        $totalCommunityService = (clone $baseQuery)->where('detailable_type', CommunityService::class)->count();

        $communityServiceApproved = (clone $baseQuery)->where('detailable_type', CommunityService::class)
            ->where('status', 'approved')->count();

        $this->stats = [
            'total_research' => $totalResearch,
            'total_community_service' => $totalCommunityService,
            'research_approved' => $researchApproved,
            'community_service_approved' => $communityServiceApproved,
            'faculty_name' => $facultyId ? $this->user->identity?->faculty?->name : null,
        ];

        // Data penelitian terbaru
        $recentResearchQuery = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', Research::class)
            ->whereIn('status', ['approved', 'completed']);

        if ($semesterFilter !== 'all') {
            if ($semesterFilter === '1') {
                $recentResearchQuery->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
            } else {
                $recentResearchQuery->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
            }
        }

        if ($facultyId) {
            $recentResearchQuery->whereHas('submitter.identity', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        $this->recentResearch = $recentResearchQuery->latest()->limit(10)->get();

        // Data PKM terbaru
        $recentPkmQuery = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->whereIn('status', ['approved', 'completed']);

        if ($semesterFilter !== 'all') {
            if ($semesterFilter === '1') {
                $recentPkmQuery->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
            } else {
                $recentPkmQuery->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
            }
        }

        if ($facultyId) {
            $recentPkmQuery->whereHas('submitter.identity', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        $this->recentCommunityService = $recentPkmQuery->latest()->limit(10)->get();

        $this->periodicSummary = $this->getPeriodicSummary();
    }

    private function getPeriodicSummary(): array
    {
        $facultyId = $this->roleName === 'dekan' ? $this->user->identity?->faculty_id : null;
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

                $researchTotal = $data->where('detailable_type', Research::class)->sum('count');
                $researchApproved = $data->where('detailable_type', Research::class)
                    ->where('status', 'approved')->sum('count');

                $pkmTotal = $data->where('detailable_type', CommunityService::class)->sum('count');
                $pkmApproved = $data->where('detailable_type', CommunityService::class)
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
