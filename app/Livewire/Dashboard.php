<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public $roleName;

    public $dashboardComponent;

    public function mount()
    {
        $this->user = Auth::user();
        $this->roleName = $this->user->getRoleNames()->first();

        $this->dashboardComponent = match ($this->roleName) {
            'superadmin', 'admin lppm', 'admin lppm saintek', 'admin lppm dekabita' => 'dashboard.admin-dashboard',
            'kepala lppm' => 'dashboard.kepala-lppm-dashboard',
            'dosen' => 'dashboard.dosen-dashboard',
            'reviewer' => 'dashboard.reviewer-dashboard',
            'rektor', 'dekan' => 'dashboard.exec-dashboard',
            default => 'dashboard.default-dashboard',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.main-dashboard');
    }
}
