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

    // Form instance
    public $form;

    // Form properties exposed to Blade (mirrors form properties)
    public string $summaryUpdate = '';
    public string $keywordsInput = '';
    public int $reportingYear;
    public string $reportingPeriod = 'final';

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

        // Check if proposal is completed
        if ($this->proposal->status !== ProposalStatus::COMPLETED) {
            abort(403, 'Laporan akhir hanya dapat diakses untuk proposal yang sudah selesai.');
        }

        // Check access
        $this->checkAccess();

        // Load existing final report
        $this->progressReport = $proposal->progressReports()->finalReports()->latest()->first();

        // Initialize form
        $formClass = $this->getFormClass();
        $this->form = new $formClass();
        $this->form->initWithProposal($this->proposal);
        $this->reportingYear = (int) date('Y');
        $this->reportingPeriod = 'final';

        if ($this->progressReport) {
            $this->form->setReport($this->progressReport);
            // Sync form properties to component properties
            $this->syncFormToComponent();
        } else {
            $this->form->initializeNewReport();
        }
    }

    /**
     * Sync form properties to component properties
     */
    protected function syncFormToComponent(): void
    {
        $this->summaryUpdate = $this->form->summaryUpdate;
        $this->keywordsInput = $this->form->keywordsInput;
        $this->reportingYear = $this->form->reportingYear;
        $this->reportingPeriod = $this->form->reportingPeriod;

        // Sync output arrays
        $this->mandatoryOutputs = $this->form->mandatoryOutputs;
        $this->additionalOutputs = $this->form->additionalOutputs;
    }

    /**
     * Sync component properties to form properties
     */
    protected function syncComponentToForm(): void
    {
        $this->form->summaryUpdate = $this->summaryUpdate;
        $this->form->keywordsInput = $this->keywordsInput;
        $this->form->reportingYear = $this->reportingYear;
        $this->form->reportingPeriod = $this->reportingPeriod;

        // Sync output arrays back to form
        $this->form->mandatoryOutputs = $this->mandatoryOutputs;
        $this->form->additionalOutputs = $this->additionalOutputs;
    }



    /**
     * Save the report
     */
    public function save(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        // Sync component properties to form before saving
        $this->syncComponentToForm();

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Sync back to component
            $this->syncFormToComponent();
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan akhir berhasil disimpan.');
    }

    /**
     * Submit the report
     */
    public function submit(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        // Sync component properties to form before submitting
        $this->syncComponentToForm();

        DB::transaction(function () {
            // Submit report via form
            $report = $this->form->submit($this->progressReport);
            $this->progressReport = $report;
        });

        session()->flash('success', 'Laporan akhir berhasil diajukan.');
        $this->redirect(route($this->getRouteName()), navigate: true);
    }

    /**
     * Handle substance file upload
     */
    public function updatedSubstanceFile(): void
    {
        if (!$this->canEdit) {
            $this->reset('substanceFile');
            return;
        }

        // Validate file at component level
        $this->validate([
            'substanceFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $this->form->substanceFile = $this->substanceFile;
            $this->form->handleFileUpload('substanceFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File substansi laporan berhasil diunggah.');
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
            $this->reset('realizationFile');
            return;
        }

        // Validate file at component level
        $this->validate([
            'realizationFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $this->form->realizationFile = $this->realizationFile;
            $this->form->handleFileUpload('realizationFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File realisasi keterlibatan berhasil diunggah.');
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
            $this->reset('presentationFile');
            return;
        }

        // Validate file at component level
        $this->validate([
            'presentationFile' => 'required|file|mimes:pdf,ppt,pptx|max:10240',
        ]);

        try {
            $this->form->presentationFile = $this->presentationFile;
            $this->form->handleFileUpload('presentationFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File presentasi hasil berhasil diunggah.');
        } catch (\Exception $e) {
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
        if (!$this->canEdit) {
            abort(403);
        }

        $proposalOutputId = $this->editingMandatoryId;

        if (!$proposalOutputId || !isset($this->mandatoryOutputs[$proposalOutputId])) {
            session()->flash('error', 'Data luaran wajib tidak ditemukan.');
            return;
        }

        if (!$this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');
            return;
        }

        try {
            // Sync to form
            $this->form->mandatoryOutputs = $this->mandatoryOutputs;
            $this->form->tempMandatoryFiles = $this->tempMandatoryFiles;
            $this->form->progressReport = $this->progressReport;

            // Save via form
            $this->form->saveMandatoryOutputWithFile($proposalOutputId);

            // Sync back
            $this->mandatoryOutputs = $this->form->mandatoryOutputs;
            $this->tempMandatoryFiles = $this->form->tempMandatoryFiles;

            session()->flash('success', 'Data luaran wajib berhasil disimpan.');
            $this->closeMandatoryModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Save additional output (book)
     */
    public function saveAdditionalOutput(): void
    {
        if (!$this->canEdit) {
            abort(403);
        }

        $proposalOutputId = $this->editingAdditionalId;

        if (!$proposalOutputId || !isset($this->additionalOutputs[$proposalOutputId])) {
            session()->flash('error', 'Data luaran tambahan tidak ditemukan.');
            return;
        }

        if (!$this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');
            return;
        }

        try {
            // Sync to form
            $this->form->additionalOutputs = $this->additionalOutputs;
            $this->form->tempAdditionalFiles = $this->tempAdditionalFiles;
            $this->form->tempAdditionalCerts = $this->tempAdditionalCerts;
            $this->form->progressReport = $this->progressReport;

            // Save via form
            $this->form->saveAdditionalOutputWithFile($proposalOutputId);

            // Sync back
            $this->additionalOutputs = $this->form->additionalOutputs;
            $this->tempAdditionalFiles = $this->form->tempAdditionalFiles;
            $this->tempAdditionalCerts = $this->form->tempAdditionalCerts;

            session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
            $this->closeAdditionalModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
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
