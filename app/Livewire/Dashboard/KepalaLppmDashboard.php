<?php

namespace App\Livewire\Dashboard;

use App\Models\Proposal;
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
            ->where('detailable_type', 'App\Models\Research')->count();

        $researchPending = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->where('status', 'reviewed')->count();

        $researchApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->where('status', 'approved')->count();

        $researchCompleted = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->where('status', 'completed')->count();

        // Statistik Pengmas
        $totalCommunityService = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')->count();

        $communityServicePending = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('status', 'reviewed')->count();

        $communityServiceApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('status', 'approved')->count();

        $communityServiceCompleted = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('status', 'completed')->count();

        $this->stats = [
            'total_research' => $totalResearch,
            'total_community_service' => $totalCommunityService,
            'research_pending' => $researchPending,
            'community_service_pending' => $communityServicePending,
            'research_approved' => $researchApproved,
            'community_service_approved' => $communityServiceApproved,
            'research_completed' => $researchCompleted,
            'community_service_completed' => $communityServiceCompleted,
        ];

        // Data penelitian terbaru
        $this->recentResearch = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->whereIn('status', ['reviewed', 'approved', 'rejected', 'completed'])
            ->latest()
            ->limit(10)
            ->get();

        // Data pengmas terbaru
        $this->recentCommunityService = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->whereIn('status', ['reviewed', 'approved', 'rejected', 'completed'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.kepala-lppm-dashboard');
    }
}
