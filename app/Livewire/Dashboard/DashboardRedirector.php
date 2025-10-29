<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DashboardRedirector extends Component
{
    public $user;

    public $roleName;

    public function mount()
    {
        $this->user = Auth::user();
        $this->roleName = $this->user->getRoleNames()->first();

        $dashboardComponent = match ($this->roleName) {
            'superadmin', 'admin lppm', 'admin lppm saintek', 'admin lppm dekabita' => 'dashboard.admin-dashboard',
            'kepala lppm' => 'dashboard.kepala-lppm-dashboard',
            'dosen' => 'dashboard.dosen-dashboard',
            'reviewer' => 'dashboard.reviewer-dashboard',
            'rektor', 'dekan' => 'dashboard.exec-dashboard',
            default => 'dashboard.default-dashboard',
        };

        Log::info("User {$this->user->name} dengan role {$this->roleName} diarahkan ke dashboard {$dashboardComponent}");

        // Emit event untuk redirect
        $this->dispatch('redirect-to-dashboard', component: $dashboardComponent);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-redirector');
    }
}
