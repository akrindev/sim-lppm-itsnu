<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

/**
 * Trait ReportAccess
 *
 * Handles report access control and loading logic
 */
trait ReportAccess
{
    public Proposal $proposal;

    public ?ProgressReport $progressReport = null;

    public bool $canEdit = false;

    #[On('report-saved')]
    public function onReportSaved(): void
    {
        $this->dispatch('$refresh');
    }

    /**
     * Check if current user has access to view and edit the report
     */
    protected function checkAccess(): void
    {
        if (! $this->canView()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan ini.');
        }

        $this->canEdit = $this->canEditReport($this->proposal);
    }

    /**
     * Check if current user can view the report
     * User can view if they are submitter or accepted team member
     */
    protected function canView(): bool
    {
        $user = Auth::user();
        // dd($user->getRoleNames());

        if ($user->hasAnyRole(['admin lppm', 'kepala lppm', 'rektor', 'dekan'])) {
            return true;
        }

        return $this->proposal->submitter_id === $user->id
            || $this->proposal->teamMembers()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
    }

    /**
     * Load latest progress report for the proposal
     */
    protected function loadReport(): void
    {
        $this->progressReport = $this->proposal->progressReports()->latest()->first();
    }
}
