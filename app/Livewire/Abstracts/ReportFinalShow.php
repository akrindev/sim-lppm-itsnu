<?php

declare(strict_types=1);

namespace App\Livewire\Abstracts;

use App\Enums\ProposalStatus;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ManagesOutputs;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class ReportFinalShow extends ReportShow
{
    use HasFileUploads;
    use ManagesOutputs;

    // Form instance
    public $form;

    // Final Report document files (3 files instead of 1)
    public $substanceFile;
    public $realizationFile;
    public $presentationFile;

    /**
     * Get the Form class name - to be implemented by child classes
     */
    abstract protected function getFormClass(): string;

    /**
     * Get the route name for redirection - to be implemented by child classes
     */
    abstract protected function getRouteName(): string;

    /**
     * Mount the component
     */
    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;

        // Check if user can view this proposal
        if (! $this->canViewFinalReport()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan akhir proposal ini.');
        }

        // Check if user can edit (only submitter)
        $this->canEdit = $this->isSubmitter();

        // Load existing final report or initialize new
        $this->progressReport = $proposal->progressReports()->finalReports()->latest()->first();

        if ($this->progressReport) {
            $this->loadExistingReport($this->progressReport);
        } else {
            $this->initializeNewReport($proposal);
        }
    }

    protected function canViewFinalReport(): bool
    {
        $user = Auth::user();

        // Proposal must be COMPLETED
        if ($this->proposal->status !== ProposalStatus::COMPLETED) {
            return false;
        }

        // Proposal owner can view
        if ($this->proposal->submitter_id === $user->id) {
            return true;
        }

        // Accepted team members can view
        $isTeamMember = $this->proposal->teamMembers()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        return $isTeamMember;
    }

    protected function isSubmitter(): bool
    {
        return $this->proposal->submitter_id === Auth::id();
    }

    /**
     * Load existing report data
     */
    protected function loadExistingReport(ProgressReport $report): void
    {
        parent::loadReport();

        // Load outputs via trait
        $this->loadExistingReport($report);
    }

    /**
     * Initialize new report
     */
    protected function initializeNewReport(Proposal $proposal): void
    {
        parent::loadReport();

        // Initialize outputs via trait
        $this->initializeNewReport($proposal);
    }

    /**
     * Validate Final Report files (3 files)
     */
    public function validateFinalReportFiles(): void
    {
        $this->validate([
            'substanceFile' => 'nullable|file|mimes:pdf|max:10240',
            'realizationFile' => 'nullable|file|mimes:pdf,docx|max:10240',
            'presentationFile' => 'nullable|file|mimes:pdf,pptx|max:51200',
        ]);
    }

    /**
     * Save Final Report files
     */
    protected function saveFinalReportFiles(ProgressReport $report): void
    {
        // Upload substance file
        if ($this->substanceFile) {
            $report->clearMediaCollection('substance_file');
            $report
                ->addMedia($this->substanceFile->getRealPath())
                ->usingName($this->substanceFile->getClientOriginalName())
                ->usingFileName($this->substanceFile->hashName())
                ->withCustomProperties([
                    'uploaded_by' => auth()->id(),
                    'proposal_id' => $this->proposal->id,
                    'report_type' => 'final',
                ])
                ->toMediaCollection('substance_file');
        }

        // Upload realization file
        if ($this->realizationFile) {
            $report->clearMediaCollection('realization_file');
            $report
                ->addMedia($this->realizationFile->getRealPath())
                ->usingName($this->realizationFile->getClientOriginalName())
                ->usingFileName($this->realizationFile->hashName())
                ->withCustomProperties([
                    'uploaded_by' => auth()->id(),
                    'proposal_id' => $this->proposal->id,
                    'report_type' => 'final',
                ])
                ->toMediaCollection('realization_file');
        }

        // Upload presentation file
        if ($this->presentationFile) {
            $report->clearMediaCollection('presentation_file');
            $report
                ->addMedia($this->presentationFile->getRealPath())
                ->usingName($this->presentationFile->getClientOriginalName())
                ->usingFileName($this->presentationFile->hashName())
                ->withCustomProperties([
                    'uploaded_by' => auth()->id(),
                    'proposal_id' => $this->proposal->id,
                    'report_type' => 'final',
                ])
                ->toMediaCollection('presentation_file');
        }
    }

    /**
     * Get all keywords for the view
     */
    public function getAllKeywords(): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::orderBy('name')->get();
    }

    /**
     * Render the view
     */
    public function render()
    {
        return view($this->getViewName(), [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }

    /**
     * Get view name - to be implemented by child classes
     */
    abstract protected function getViewName(): string;
}
