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
        $researchCount = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->count();

        $researchAsMember = $this->user->proposals()
            ->whereYear('proposals.created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->count();

        $researchPending = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->where('status', 'submitted')->count();

        $researchApproved = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->where('status', 'approved')->count();

        // Statistik PKM
        $communityServiceCount = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->count();

        $communityServiceAsMember = $this->user->proposals()
            ->whereYear('proposals.created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->count();

        $communityServicePending = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('status', 'submitted')->count();

        $communityServiceApproved = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('status', 'approved')->count();

        $this->stats = [
            'my_research' => $researchCount,
            'my_community_service' => $communityServiceCount,
            'research_as_member' => $researchAsMember,
            'community_service_as_member' => $communityServiceAsMember,
            'community_service_pending' => $communityServicePending,
            'research_approved' => $researchApproved,
            'community_service_approved' => $communityServiceApproved,
        ];

        // Data penelitian terbaru
        $this->recentResearch = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->latest()
            ->limit(10)
            ->get();

        // Data PKM terbaru
        $this->recentCommunityService = Proposal::where('submitter_id', $this->user->id)
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.dosen-dashboard');
    }
}
