<?php

declare(strict_types=1);

namespace App\Livewire\Abstracts;

use App\Enums\ProposalStatus;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ManagesOutputs;
use App\Models\Keyword;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class ReportFinalShow extends ReportShow
{
    use HasFileUploads;
    use ManagesOutputs;

    // Final Report document files (3 files instead of 1)
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
        $this->loadExistingOutputsForReport($report);
    }

    /**
     * Initialize new report
     */
    protected function initializeNewReport(Proposal $proposal): void
    {
        parent::loadReport();

        // Initialize outputs via trait
        $this->initializeOutputsForNewReport($proposal);
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
        // Ensure proposal is loaded
        if (!$this->proposal) {
            throw new \Exception('Proposal not loaded in component');
        }

        // Ensure user has permission
        if (!$this->canEdit) {
            throw new \Exception('Unauthorized: User does not have permission to edit this report');
        }

        // Upload substance file
        if ($this->substanceFile) {
            $report->clearMediaCollection('substance_file');
            $report
                ->addMedia($this->substanceFile->getRealPath())
                ->usingName($this->substanceFile->getClientOriginalName())
                ->usingFileName($this->substanceFile->hashName())
                ->withCustomProperties([
                    'uploaded_by' => Auth::id(),
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
                    'uploaded_by' => Auth::id(),
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
                    'uploaded_by' => Auth::id(),
                    'proposal_id' => $this->proposal->id,
                    'report_type' => 'final',
                ])
                ->toMediaCollection('presentation_file');
        }
    }

    /**
     * Load existing outputs for report
     */
    protected function loadExistingOutputsForReport(ProgressReport $report): void
    {
        // Ensure proposal is loaded
        if (!$this->proposal) {
            throw new \Exception('Proposal not loaded in component');
        }

        // Load from the outputs data
        if ($this->progressReport) {
            foreach ($this->progressReport->mandatoryOutputs as $output) {
                if (empty($output->proposal_output_id)) {
                    continue;
                }

                $this->mandatoryOutputs[$output->proposal_output_id] = [
                    'id' => $output->id,
                    'status_type' => $output->status_type,
                    'author_status' => $output->author_status,
                    'journal_title' => $output->journal_title,
                    'issn' => $output->issn,
                    'eissn' => $output->eissn,
                    'indexing_body' => $output->indexing_body,
                    'journal_url' => $output->journal_url,
                    'article_title' => $output->article_title,
                    'publication_year' => $output->publication_year,
                    'volume' => $output->volume,
                    'issue_number' => $output->issue_number,
                    'page_start' => $output->page_start,
                    'page_end' => $output->page_end,
                    'article_url' => $output->article_url,
                    'doi' => $output->doi,
                ];
            }

            foreach ($this->progressReport->additionalOutputs as $output) {
                if (empty($output->proposal_output_id)) {
                    continue;
                }

                $this->additionalOutputs[$output->proposal_output_id] = [
                    'id' => $output->id,
                    'status' => $output->status,
                    'book_title' => $output->book_title,
                    'publisher_name' => $output->publisher_name,
                    'isbn' => $output->isbn,
                    'publication_year' => $output->publication_year,
                    'total_pages' => $output->total_pages,
                    'publisher_url' => $output->publisher_url,
                    'book_url' => $output->book_url,
                ];
            }
        }
    }

    /**
     * Initialize outputs for new report
     */
    protected function initializeOutputsForNewReport(Proposal $proposal): void
    {
        // Initialize outputs for final report (if any outputs exist)
        foreach ($proposal->outputs->where('category', 'Wajib') as $output) {
            $this->mandatoryOutputs[$output->id] = $this->getEmptyMandatoryOutput();
        }

        foreach ($proposal->outputs->where('category', 'Tambahan') as $output) {
            $this->additionalOutputs[$output->id] = $this->getEmptyAdditionalOutput();
        }
    }

    /**
     * Save the report
     */
    public function save(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        DB::transaction(function () {
            // Only validate files if they are present
            if ($this->substanceFile || $this->realizationFile || $this->presentationFile) {
                $this->validateFinalReportFiles();
            }

            // Save or update the report
            if ($this->progressReport) {
                $this->progressReport->update([
                    'summary_update' => $this->progressReport->summary_update ?? $this->proposal->summary,
                ]);
            } else {
                $this->progressReport = ProgressReport::create([
                    'proposal_id' => $this->proposal->id,
                    'summary_update' => $this->proposal->summary,
                    'reporting_year' => (int) date('Y'),
                    'reporting_period' => 'final',
                    'status' => 'draft',
                ]);
            }

            // Save final report files (only if present)
            $this->saveFinalReportFiles($this->progressReport);
        });

        // Reset file properties after successful save
        $this->reset(['substanceFile', 'realizationFile', 'presentationFile']);

        $this->dispatch('report-saved');
        session()->flash('success', 'Dokumen laporan akhir berhasil disimpan.');
    }

    /**
     * Submit the report
     */
    public function submit(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        DB::transaction(function () {
            // Only validate files if they are present
            if ($this->substanceFile || $this->realizationFile || $this->presentationFile) {
                $this->validateFinalReportFiles();
            }

            // Save or update the report
            if ($this->progressReport) {
                $this->progressReport->update([
                    'summary_update' => $this->progressReport->summary_update ?? $this->proposal->summary,
                    'status' => 'submitted',
                    'submitted_by' => Auth::id(),
                    'submitted_at' => now(),
                ]);
            } else {
                $this->progressReport = ProgressReport::create([
                    'proposal_id' => $this->proposal->id,
                    'summary_update' => $this->proposal->summary,
                    'reporting_year' => (int) date('Y'),
                    'reporting_period' => 'final',
                    'status' => 'submitted',
                    'submitted_by' => Auth::id(),
                    'submitted_at' => now(),
                ]);
            }

            // Save final report files (only if present)
            $this->saveFinalReportFiles($this->progressReport);
        });

        // Reset file properties after successful submit
        $this->reset(['substanceFile', 'realizationFile', 'presentationFile']);

        session()->flash('success', 'Dokumen laporan akhir berhasil diajukan.');
        $this->redirect(route($this->getRouteName()), navigate: true);
    }

    /**
     * Handle substance file upload
     */
    public function updatedSubstanceFile(): void
    {
        if (!$this->canEdit) {
            return;
        }

        try {
            $this->validateFinalReportFiles();

            if ($this->substanceFile) {
                DB::transaction(function () {
                    if (!$this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                session()->flash('success', 'File substansi laporan berhasil diunggah.');
            }
        } catch (\Exception $e) {
            $this->reset('substanceFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Handle realization file upload
     */
    public function updatedRealizationFile(): void
    {
        if (!$this->canEdit) {
            return;
        }

        try {
            $this->validateFinalReportFiles();

            if ($this->realizationFile) {
                DB::transaction(function () {
                    if (!$this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                session()->flash('success', 'File realisasi keterlibatan berhasil diunggah.');
            }
        } catch (\Exception $e) {
            $this->reset('realizationFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Handle presentation file upload
     */
    public function updatedPresentationFile(): void
    {
        if (!$this->canEdit) {
            return;
        }

        try {
            $this->validateFinalReportFiles();

            if ($this->presentationFile) {
                DB::transaction(function () {
                    if (!$this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                session()->flash('success', 'File presentasi hasil berhasil diunggah.');
            }
        } catch (\Exception $e) {
            $this->reset('presentationFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Remove substance file
     */
    public function removeSubstanceFile(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        if ($this->progressReport) {
            $this->progressReport->clearMediaCollection('substance_file');
            session()->flash('success', 'File substansi berhasil dihapus.');
        }
    }

    /**
     * Remove realization file
     */
    public function removeRealizationFile(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        if ($this->progressReport) {
            $this->progressReport->clearMediaCollection('realization_file');
            session()->flash('success', 'File realisasi berhasil dihapus.');
        }
    }

    /**
     * Remove presentation file
     */
    public function removePresentationFile(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        if ($this->progressReport) {
            $this->progressReport->clearMediaCollection('presentation_file');
            session()->flash('success', 'File presentasi berhasil dihapus.');
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
     * Get empty mandatory output structure
     */
    protected function getEmptyMandatoryOutput(): array
    {
        return [
            'id' => null,
            'status_type' => '',
            'author_status' => '',
            'journal_title' => '',
            'issn' => '',
            'eissn' => '',
            'indexing_body' => '',
            'journal_url' => '',
            'article_title' => '',
            'publication_year' => '',
            'volume' => '',
            'issue_number' => '',
            'page_start' => '',
            'page_end' => '',
            'article_url' => '',
            'doi' => '',
        ];
    }

    /**
     * Get empty additional output structure
     */
    protected function getEmptyAdditionalOutput(): array
    {
        return [
            'id' => null,
            'status' => '',
            'book_title' => '',
            'publisher_name' => '',
            'isbn' => '',
            'publication_year' => '',
            'total_pages' => '',
            'publisher_url' => '',
            'book_url' => '',
        ];
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
