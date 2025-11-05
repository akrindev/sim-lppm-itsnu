<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use Livewire\Attributes\Url;
use Livewire\Component;

class MasterData extends Component
{
    #[Url(as: 'tab')]
    public string $activeTab = 'focus-areas';

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.master-data');
    }
}
