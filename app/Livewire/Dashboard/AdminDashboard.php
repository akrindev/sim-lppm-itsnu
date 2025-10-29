<?php

namespace App\Livewire\Dashboard;

use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminDashboard extends Component
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

        // Statistik Penelitian
        $totalResearch = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', \App\Models\Research::class)->count();

        $researchPending = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', \App\Models\Research::class)
            ->where('status', 'submitted')->count();

        $researchApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', \App\Models\Research::class)
            ->where('status', 'approved')->count();

        $researchRejected = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', \App\Models\Research::class)
            ->where('status', 'rejected')->count();

        // Statistik Pengmas
        $totalCommunityService = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)->count();

        $communityServicePending = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->where('status', 'submitted')->count();

        $communityServiceApproved = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->where('status', 'approved')->count();

        $communityServiceRejected = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->where('status', 'rejected')->count();

        // Statistik Pengguna
        $totalDosen = User::role('dosen')->count();

        $this->stats = [
            'total_research' => $totalResearch,
            'total_community_service' => $totalCommunityService,
            'research_pending' => $researchPending,
            'community_service_pending' => $communityServicePending,
            'research_approved' => $researchApproved,
            'community_service_approved' => $communityServiceApproved,
            'research_rejected' => $researchRejected,
            'community_service_rejected' => $communityServiceRejected,
            'total_dosen' => $totalDosen,
        ];

        // Data penelitian terbaru
        $this->recentResearch = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', \App\Models\Research::class)
            ->latest()
            ->limit(10)
            ->get();

        // Data pengmas terbaru
        $this->recentCommunityService = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', CommunityService::class)
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard');
    }
}
