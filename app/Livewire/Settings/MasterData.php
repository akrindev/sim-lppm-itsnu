<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class MasterData extends Component
{
    use WithPagination;

    #[Url(as: 'group')]
    public string $group = 'academic-content';

    #[Url(as: 'tab')]
    public string $activeTab = '';

    public function mount(): void
    {
        if (empty($this->activeTab)) {
            $this->activeTab = match ($this->group) {
                'academic-structure' => 'study-programs',
                'budget' => 'budget-groups',
                'partnership' => 'partners',
                default => 'focus-areas',
            };
        }
    }

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
        $title = match ($this->group) {
            'academic-structure' => 'Struktur Akademik',
            'budget' => 'Anggaran & RAB',
            'partnership' => 'Kemitraan & Prioritas',
            default => 'Master Data',
        };

        return view('livewire.settings.master-data', [
            'pageTitle' => $title,
        ]);
    }
}
