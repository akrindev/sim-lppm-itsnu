<?php

namespace App\Livewire\Dashboard;

use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DefaultDashboard extends Component
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
            ->where('detailable_type', 'App\Models\Research')->count();

        // Statistik Pengmas
        $totalCommunityService = Proposal::whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')->count();

        $this->stats = [
            'total_research' => $totalResearch,
            'total_community_service' => $totalCommunityService,
        ];

        // Data penelitian terbaru
        $this->recentResearch = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\Research')
            ->latest()
            ->limit(5)
            ->get();

        // Data pengmas terbaru
        $this->recentCommunityService = Proposal::with(['submitter'])
            ->whereYear('created_at', $yearFilter)
            ->where('detailable_type', 'App\Models\CommunityService')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.default-dashboard');
    }
}
