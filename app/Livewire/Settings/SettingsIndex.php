<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SettingsIndex extends Component
{
    public string $activeTab = 'profile';

    /**
     * Set the active tab.
     */
    public function setActiveTab(string $tab): void
    {
        $adminOnlyTabs = ['appearance'];

        if (in_array($tab, $adminOnlyTabs) && ! Auth::user()->hasRole('admin lppm')) {
            return;
        }

        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
