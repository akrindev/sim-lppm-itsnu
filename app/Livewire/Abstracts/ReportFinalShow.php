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
use Illuminate\Support\Facades\Log;

abstract class ReportFinalShow extends ReportShow
{
    use HasFileUploads;
    use ManagesOutputs;

    // Form properties for wire:model bindings
    public string $summaryUpdate = '';

    public string $keywordsInput = '';

    public int $reportingYear;

    public string $reportingPeriod = 'final';

    // Final Report document files (3 files instead of 1)
    public $realizationFile;

    public $presentationFile;

    // Output document file uploads
    public array $tempMandatoryFiles = [];

    public array $tempAdditionalFiles = [];

    public array $tempAdditionalCerts = [];

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

        // Initialize form properties
        $this->reportingYear = (int) date('Y');
        $this->reportingPeriod = 'final';

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

        // Load form properties from report
        $this->summaryUpdate = $report->summary_update ?? '';
        $this->keywordsInput = $report->keywords->pluck('name')->implode('; ');
        $this->reportingYear = $report->reporting_year ?? (int) date('Y');
        $this->reportingPeriod = $report->reporting_period ?? 'final';

        // Load outputs via trait
        $this->loadExistingOutputsForReport($report);
    }

    /**
     * Initialize new report
     */
    protected function initializeNewReport(Proposal $proposal): void
    {
        parent::loadReport();

        // Initialize form properties if not already set
        if (empty($this->summaryUpdate)) {
            $this->summaryUpdate = $proposal->summary ?? '';
        }
        if (empty($this->keywordsInput)) {
            $this->keywordsInput = $proposal->keywords->pluck('name')->implode('; ') ?? '';
        }
        if (empty($this->reportingYear)) {
            $this->reportingYear = (int) date('Y');
        }
        $this->reportingPeriod = 'final';

        // Initialize outputs via trait
        $this->initializeOutputsForNewReport($proposal);
    }

    /**
     * Save Final Report files
     * Only saves files that are still valid UploadedFile instances
     */
    protected function saveFinalReportFiles(ProgressReport $report): void
    {
        // Ensure proposal is loaded
        if (! $this->proposal) {
            throw new \Exception('Proposal not loaded in component');
        }

        // Ensure user has permission
        if (! $this->canEdit) {
            throw new \Exception('Unauthorized: User does not have permission to edit this report');
        }

        // Upload substance file (only if it's still a valid UploadedFile)
        if ($this->substanceFile instanceof \Illuminate\Http\UploadedFile && $this->substanceFile->isValid()) {
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

        // Upload realization file (only if it's still a valid UploadedFile)
        if ($this->realizationFile instanceof \Illuminate\Http\UploadedFile && $this->realizationFile->isValid()) {
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

        // Upload presentation file (only if it's still a valid UploadedFile)
        if ($this->presentationFile instanceof \Illuminate\Http\UploadedFile && $this->presentationFile->isValid()) {
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
        if (! $this->proposal) {
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
                    'document_file' => $output->getFirstMedia('journal_article') ? true : false,
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
                    'document_file' => $output->getFirstMedia('book_document') ? true : false,
                    'publication_certificate' => $output->getFirstMedia('publication_certificate') ? true : false,
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
        if (! $this->canEdit) {
            abort(403);
        }

        // Validate report data
        $this->validateReportData();

        DB::transaction(function () {
            // Save or update the report
            if ($this->progressReport) {
                $this->progressReport->update([
                    'summary_update' => $this->summaryUpdate ?: ($this->progressReport->summary_update ?? $this->proposal->summary),
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                ]);
            } else {
                $this->progressReport = ProgressReport::create([
                    'proposal_id' => $this->proposal->id,
                    'summary_update' => $this->summaryUpdate ?: $this->proposal->summary,
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                    'status' => 'draft',
                ]);
            }

            // Save keywords (works for both new and existing reports)
            $this->saveKeywords();

            // Save final report files (only if files are still UploadedFile instances)
            $this->saveFinalReportFiles($this->progressReport);
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Dokumen laporan akhir berhasil disimpan.');
    }

    /**
     * Submit the report
     */
    public function submit(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        // Validate report data
        $this->validateReportData();

        DB::transaction(function () {
            // Save or update the report
            if ($this->progressReport) {
                $this->progressReport->update([
                    'summary_update' => $this->summaryUpdate ?: ($this->progressReport->summary_update ?? $this->proposal->summary),
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                    'status' => 'submitted',
                    'submitted_by' => Auth::id(),
                    'submitted_at' => now(),
                ]);
            } else {
                $this->progressReport = ProgressReport::create([
                    'proposal_id' => $this->proposal->id,
                    'summary_update' => $this->summaryUpdate ?: $this->proposal->summary,
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                    'status' => 'submitted',
                    'submitted_by' => Auth::id(),
                    'submitted_at' => now(),
                ]);
            }

            // Save keywords (works for both new and existing reports)
            $this->saveKeywords();

            // Save final report files (only if files are still UploadedFile instances)
            $this->saveFinalReportFiles($this->progressReport);
        });

        session()->flash('success', 'Dokumen laporan akhir berhasil diajukan.');
        $this->redirect(route($this->getRouteName()), navigate: true);
    }

    /**
     * Handle substance file upload
     * Auto-save the file and don't reset it
     */
    public function updatedSubstanceFile(): void
    {
        if (! $this->canEdit) {
            $this->reset('substanceFile');

            return;
        }

        // Validate and save
        try {
            $this->validate([
                'substanceFile' => 'nullable|file|mimes:pdf,application/pdf|max:10240',
            ]);

            if ($this->substanceFile) {
                DB::transaction(function () {
                    if (! $this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);

                        // Save keywords for new report
                        $this->saveKeywords();
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                // DON'T reset the file - keep it for save/submit
                session()->flash('success', 'File substansi laporan berhasil diunggah.');
            }
        } catch (\Exception $e) {
            $this->reset('substanceFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
            Log::error('Substance file upload error: ' . $e->getMessage());
        }
    }

    /**
     * Handle realization file upload
     * Auto-save the file and don't reset it
     */
    public function updatedRealizationFile(): void
    {
        if (! $this->canEdit) {
            $this->reset('realizationFile');

            return;
        }

        // Validate and save
        try {
            $this->validate([
                'realizationFile' => 'nullable|file|mimes:pdf,docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            ]);

            if ($this->realizationFile) {
                DB::transaction(function () {
                    if (! $this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);

                        // Save keywords for new report
                        $this->saveKeywords();
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                // DON'T reset the file - keep it for save/submit
                session()->flash('success', 'File realisasi keterlibatan berhasil diunggah.');
            }
        } catch (\Exception $e) {
            $this->reset('realizationFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
            Log::error('Realization file upload error: ' . $e->getMessage());
        }
    }

    /**
     * Handle presentation file upload
     * Auto-save the file and don't reset it
     */
    public function updatedPresentationFile(): void
    {
        if (! $this->canEdit) {
            $this->reset('presentationFile');

            return;
        }

        // Validate and save
        try {
            // Log the MIME type for debugging
            if ($this->presentationFile instanceof \Illuminate\Http\UploadedFile) {
                Log::info('Presentation file MIME type', [
                    'mime' => $this->presentationFile->getMimeType(),
                    'clientMimeType' => $this->presentationFile->getClientMimeType(),
                    'originalName' => $this->presentationFile->getClientOriginalName(),
                ]);
            }

            $this->validate([
                'presentationFile' => 'nullable|file|mimes:pdf,pptx,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/zip|max:51200',
            ]);

            if ($this->presentationFile) {
                DB::transaction(function () {
                    if (! $this->progressReport) {
                        $this->progressReport = ProgressReport::create([
                            'proposal_id' => $this->proposal->id,
                            'summary_update' => $this->proposal->summary,
                            'reporting_year' => (int) date('Y'),
                            'reporting_period' => 'final',
                            'status' => 'draft',
                        ]);

                        // Save keywords for new report
                        $this->saveKeywords();
                    }

                    $this->saveFinalReportFiles($this->progressReport);
                });

                // DON'T reset the file - keep it for save/submit
                session()->flash('success', 'File presentasi hasil berhasil diunggah.');
            }
        } catch (\Exception $e) {
            Log::error('Presentation file upload error: ' . $e->getMessage());
            $this->reset('presentationFile');
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Handle mandatory output file upload
     */
    public function updatedTempMandatoryFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateMandatoryFile($this->editingMandatoryId);
            session()->flash('success', 'File dokumen artikel berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Handle additional output file upload
     */
    public function updatedTempAdditionalFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateAdditionalFile($this->editingAdditionalId);
            session()->flash('success', 'File dokumen buku berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Handle additional output certificate upload
     */
    public function updatedTempAdditionalCerts(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateAdditionalCert($this->editingAdditionalId);
            session()->flash('success', 'File surat keterangan berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Remove substance file
     */
    public function removeSubstanceFile(): void
    {
        if (! $this->canEdit) {
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
        if (! $this->canEdit) {
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
        if (! $this->canEdit) {
            abort(403);
        }

        if ($this->progressReport) {
            $this->progressReport->clearMediaCollection('presentation_file');
            session()->flash('success', 'File presentasi berhasil dihapus.');
        }
    }

    /**
     * Save mandatory output (journal article)
     */
    public function saveMandatoryOutput(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        $proposalOutputId = $this->editingMandatoryId;

        if (! $proposalOutputId || ! isset($this->mandatoryOutputs[$proposalOutputId])) {
            session()->flash('error', 'Data luaran wajib tidak ditemukan.');

            return;
        }

        $data = $this->mandatoryOutputs[$proposalOutputId];

        // Ensure progress report exists
        if (! $this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');

            return;
        }

        DB::transaction(function () use ($proposalOutputId, $data) {
            // Find or create mandatory output
            $output = \App\Models\MandatoryOutput::where('progress_report_id', $this->progressReport->id)
                ->where('proposal_output_id', $proposalOutputId)
                ->first();

            // Convert empty strings to null for integer fields
            $pageStart = ! empty($data['page_start']) ? (int) $data['page_start'] : null;
            $pageEnd = ! empty($data['page_end']) ? (int) $data['page_end'] : null;

            if (! $output) {
                $output = \App\Models\MandatoryOutput::create([
                    'progress_report_id' => $this->progressReport->id,
                    'proposal_output_id' => $proposalOutputId,
                    'status_type' => $data['status_type'] ?? '',
                    'author_status' => $data['author_status'] ?? '',
                    'journal_title' => $data['journal_title'] ?? '',
                    'issn' => $data['issn'] ?? '',
                    'eissn' => $data['eissn'] ?? '',
                    'indexing_body' => $data['indexing_body'] ?? '',
                    'journal_url' => $data['journal_url'] ?? '',
                    'article_title' => $data['article_title'] ?? '',
                    'publication_year' => $data['publication_year'] ? (int) $data['publication_year'] : null,
                    'volume' => $data['volume'] ?? '',
                    'issue_number' => $data['issue_number'] ?? '',
                    'page_start' => $pageStart,
                    'page_end' => $pageEnd,
                    'article_url' => $data['article_url'] ?? '',
                    'doi' => $data['doi'] ?? '',
                ]);
            } else {
                $output->update([
                    'status_type' => $data['status_type'] ?? '',
                    'author_status' => $data['author_status'] ?? '',
                    'journal_title' => $data['journal_title'] ?? '',
                    'issn' => $data['issn'] ?? '',
                    'eissn' => $data['eissn'] ?? '',
                    'indexing_body' => $data['indexing_body'] ?? '',
                    'journal_url' => $data['journal_url'] ?? '',
                    'article_title' => $data['article_title'] ?? '',
                    'publication_year' => $data['publication_year'] ? (int) $data['publication_year'] : null,
                    'volume' => $data['volume'] ?? '',
                    'issue_number' => $data['issue_number'] ?? '',
                    'page_start' => $pageStart,
                    'page_end' => $pageEnd,
                    'article_url' => $data['article_url'] ?? '',
                    'doi' => $data['doi'] ?? '',
                ]);
            }

            // Save file if uploaded
            if (
                isset($this->tempMandatoryFiles[$proposalOutputId]) &&
                $this->tempMandatoryFiles[$proposalOutputId] instanceof \Illuminate\Http\UploadedFile
            ) {
                $file = $this->tempMandatoryFiles[$proposalOutputId];

                $output->clearMediaCollection('journal_article');
                $output
                    ->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->usingFileName($file->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => Auth::id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'final',
                    ])
                    ->toMediaCollection('journal_article');

                // Update the output array to reflect file exists
                $this->mandatoryOutputs[$proposalOutputId]['document_file'] = true;
                // Clear the temp file
                unset($this->tempMandatoryFiles[$proposalOutputId]);
            }
        });

        session()->flash('success', 'Data luaran wajib berhasil disimpan.');
        $this->closeMandatoryModal();
    }

    /**
     * Save additional output (book)
     */
    public function saveAdditionalOutput(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        $proposalOutputId = $this->editingAdditionalId;

        if (! $proposalOutputId || ! isset($this->additionalOutputs[$proposalOutputId])) {
            session()->flash('error', 'Data luaran tambahan tidak ditemukan.');

            return;
        }

        $data = $this->additionalOutputs[$proposalOutputId];

        // Ensure progress report exists
        if (! $this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');

            return;
        }

        DB::transaction(function () use ($proposalOutputId, $data) {
            // Find or create additional output
            $output = \App\Models\AdditionalOutput::where('progress_report_id', $this->progressReport->id)
                ->where('proposal_output_id', $proposalOutputId)
                ->first();

            // Convert empty strings to null for integer fields
            $totalPages = ! empty($data['total_pages']) ? (int) $data['total_pages'] : null;

            if (! $output) {
                $output = \App\Models\AdditionalOutput::create([
                    'progress_report_id' => $this->progressReport->id,
                    'proposal_output_id' => $proposalOutputId,
                    'status' => $data['status'] ?? '',
                    'book_title' => $data['book_title'] ?? '',
                    'publisher_name' => $data['publisher_name'] ?? '',
                    'isbn' => $data['isbn'] ?? '',
                    'publication_year' => $data['publication_year'] ? (int) $data['publication_year'] : null,
                    'total_pages' => $totalPages,
                    'publisher_url' => $data['publisher_url'] ?? '',
                    'book_url' => $data['book_url'] ?? '',
                ]);
            } else {
                $output->update([
                    'status' => $data['status'] ?? '',
                    'book_title' => $data['book_title'] ?? '',
                    'publisher_name' => $data['publisher_name'] ?? '',
                    'isbn' => $data['isbn'] ?? '',
                    'publication_year' => $data['publication_year'] ? (int) $data['publication_year'] : null,
                    'total_pages' => $totalPages,
                    'publisher_url' => $data['publisher_url'] ?? '',
                    'book_url' => $data['book_url'] ?? '',
                ]);
            }

            // Save document file if uploaded
            if (
                isset($this->tempAdditionalFiles[$proposalOutputId]) &&
                $this->tempAdditionalFiles[$proposalOutputId] instanceof \Illuminate\Http\UploadedFile
            ) {
                $file = $this->tempAdditionalFiles[$proposalOutputId];

                $output->clearMediaCollection('book_document');
                $output
                    ->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->usingFileName($file->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => Auth::id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'final',
                    ])
                    ->toMediaCollection('book_document');

                $this->additionalOutputs[$proposalOutputId]['document_file'] = true;
                unset($this->tempAdditionalFiles[$proposalOutputId]);
            }

            // Save certificate file if uploaded
            if (
                isset($this->tempAdditionalCerts[$proposalOutputId]) &&
                $this->tempAdditionalCerts[$proposalOutputId] instanceof \Illuminate\Http\UploadedFile
            ) {
                $file = $this->tempAdditionalCerts[$proposalOutputId];

                $output->clearMediaCollection('publication_certificate');
                $output
                    ->addMedia($file->getRealPath())
                    ->usingName($file->getClientOriginalName())
                    ->usingFileName($file->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => Auth::id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'final',
                    ])
                    ->toMediaCollection('publication_certificate');

                $this->additionalOutputs[$proposalOutputId]['publication_certificate'] = true;
                unset($this->tempAdditionalCerts[$proposalOutputId]);
            }
        });

        session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
        $this->closeAdditionalModal();
    }

    /**
     * Get all keywords for the view
     */
    public function getAllKeywords(): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::orderBy('name')->get();
    }

    /**
     * Save keywords to the report
     */
    protected function saveKeywords(): void
    {
        if (empty($this->keywordsInput) || ! $this->progressReport) {
            return;
        }

        $keywordNames = array_map('trim', explode(';', $this->keywordsInput));
        $keywords = [];

        foreach ($keywordNames as $name) {
            if (empty($name)) {
                continue;
            }

            $keyword = Keyword::firstOrCreate(['name' => $name]);
            $keywords[] = $keyword->id;
        }

        $this->progressReport->keywords()->sync($keywords);
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
            'document_file' => false,
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
            'document_file' => false,
            'publication_certificate' => false,
        ];
    }

    /**
     * Get validation rules
     */
    public function rules(): array
    {
        return [
            'summaryUpdate' => ['nullable', 'string', 'min:10'],
            'keywordsInput' => ['nullable', 'string'],
            'reportingYear' => ['required', 'integer', 'min:2020', 'max:2099'],
            'reportingPeriod' => ['required', 'string', 'in:final'],
        ];
    }

    /**
     * Validate report data
     */
    public function validateReportData(): void
    {
        $this->validate($this->rules());
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
