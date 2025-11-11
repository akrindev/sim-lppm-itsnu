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

    #[Url]
    public string $roleFilter = 'ketua';

    #[On('resetFilters')]
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedYear', 'roleFilter']);
    }
}
