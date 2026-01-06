<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;

trait WithFilters
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $yearFilter = '';

    public string $roleFilter = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->yearFilter = '';
        $this->roleFilter = '';
        $this->resetPage();
    }
}
