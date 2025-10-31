<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class SettingsIndex extends Component
{
    public string $activeTab = 'profile';

    /**
     * Set the active tab.
     */
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.index');
    }
}
