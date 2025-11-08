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

        // Statistik Penelitian
        $totalResearch = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', Research::class)->count();

        $researchApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', Research::class)
            ->where('status', 'approved')->count();

        // Statistik PKM
        $totalCommunityService = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)->count();

        $communityServiceApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->where('status', 'approved')->count();

        $this->stats = [
            'total_research' => $totalResearch,
            'total_community_service' => $totalCommunityService,
            'research_approved' => $researchApproved,
            'community_service_approved' => $communityServiceApproved,
        ];

        // Data penelitian terbaru
        $this->recentResearch = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', Research::class)
            ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->limit(10)
            ->get();

        // Data PKM terbaru
        $this->recentCommunityService = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->whereIn('status', ['approved', 'completed'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.exec-dashboard');
    }
}
