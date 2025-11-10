<?php

declare(strict_types=1);

namespace App\Livewire\Abstracts;

use App\Livewire\Traits\ReportAuthorization;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

abstract class ReportShow extends Component
{
    use WithFileUploads;
    use ReportAuthorization;

    public Proposal $proposal;
    public ?ProgressReport $progressReport = null;
    public bool $canEdit = false;

    #[On('report-saved')]
    public function onReportSaved(): void
    {
        $this->dispatch('$refresh');
    }

    protected function checkAccess(): void
    {
        if (!$this->canView()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan ini.');
        }

        $this->canEdit = $this->canEditReport($this->proposal);
    }

    protected function canView(): bool
    {
        $user = Auth::user();

        return $this->proposal->submitter_id === $user->id
            || $this->proposal->teamMembers()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
    }

    protected function loadReport(): void
    {
        $this->progressReport = $this->proposal->progressReports()->latest()->first();
    }
}
