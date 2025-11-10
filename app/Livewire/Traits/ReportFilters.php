<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;

trait ReportFilters
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $selectedYear = '';

    #[On('resetFilters')]
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedYear']);
    }
}
