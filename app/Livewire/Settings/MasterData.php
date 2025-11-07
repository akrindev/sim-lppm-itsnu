<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MasterData extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'focus-areas';

    public function setActiveTab(string $tab): void
    {
        $this->resetPage();
        $this->activeTab = $tab;
    }

    #[On('close-modal')]
    public function refresh(): void
    {
        // Just to trigger re-render
    }

    public function render()
    {
        return view('livewire.settings.master-data');
    }
}
