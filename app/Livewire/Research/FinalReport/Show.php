<?php

declare(strict_types=1);

namespace App\Livewire\Research\FinalReport;

use App\Enums\ProposalStatus;
use App\Livewire\Forms\ResearchFinalReportForm;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ManagesOutputs;
use App\Livewire\Traits\ReportAccess;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use HasFileUploads;
    use ManagesOutputs;
    use ReportAccess;
    use ReportAuthorization;
    use WithFileUploads;

    // Form instance - Livewire v3 Form pattern
    public ResearchFinalReportForm $form;

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

        // Initialize Livewire Form
        $this->form->initWithProposal($this->proposal);

        if ($this->progressReport) {
            // Load existing report data into form
            $this->form->setReport($this->progressReport);

            // Load output arrays to component for display
            $this->mandatoryOutputs = $this->form->mandatoryOutputs;
            $this->additionalOutputs = $this->form->additionalOutputs;
        } else {
            // Initialize new report structure
            $this->form->initializeNewReport();
        }
    }

    /**
     * Save the report as draft
     */
    public function save(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Sync output arrays back
            $this->mandatoryOutputs = $this->form->mandatoryOutputs;
            $this->additionalOutputs = $this->form->additionalOutputs;
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan akhir berhasil disimpan.');
    }

    /**
     * Submit the report
     */
    public function submit(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        DB::transaction(function () {
            // Submit report via form
            $report = $this->form->submit($this->progressReport);
            $this->progressReport = $report;
        });

        session()->flash('success', 'Laporan akhir berhasil diajukan.');
        $this->redirect(route('research.final-report.index'), navigate: true);
    }

    /**
     * Handle substance file upload (real-time)
     */
    public function updatedFormSubstanceFile(): void
    {
        if (! $this->canEdit) {
            $this->form->substanceFile = null;

            return;
        }

        // Validate file
        $this->validate([
            'form.substanceFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $this->form->handleFileUpload('substanceFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File substansi laporan berhasil diunggah.');
        } catch (\Exception $e) {
            $this->form->substanceFile = null;
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
        }
    }

    /**
     * Handle realization file upload (real-time)
     */
    public function updatedFormRealizationFile(): void
    {
        if (! $this->canEdit) {
            $this->form->realizationFile = null;

            return;
        }

        // Validate file
        $this->validate([
            'form.realizationFile' => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $this->form->handleFileUpload('realizationFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File realisasi keterlibatan berhasil diunggah.');
        } catch (\Exception $e) {
            $this->form->realizationFile = null;
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
        }
    }

    /**
     * Handle presentation file upload (real-time)
     */
    public function updatedFormPresentationFile(): void
    {
        if (! $this->canEdit) {
            $this->form->presentationFile = null;

            return;
        }

        // Validate file
        $this->validate([
            'form.presentationFile' => 'required|file|mimes:pdf,ppt,pptx|max:51200',
        ]);

        try {
            $this->form->handleFileUpload('presentationFile');
            $this->progressReport = $this->form->progressReport;
            session()->flash('success', 'File presentasi hasil berhasil diunggah.');
        } catch (\Exception $e) {
            $this->form->presentationFile = null;
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
        }
    }

    /**
     * Handle mandatory output file upload (real-time)
     */
    public function updatedFormTempMandatoryFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateMandatoryFile($this->editingMandatoryId);
            session()->flash('success', 'File dokumen artikel berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
        }
    }

    /**
     * Handle additional output file upload (real-time)
     */
    public function updatedFormTempAdditionalFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateAdditionalFile($this->editingAdditionalId);
            session()->flash('success', 'File dokumen buku berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
        }
    }

    /**
     * Handle additional output certificate upload (real-time)
     */
    public function updatedFormTempAdditionalCerts(): void
    {
        if (! $this->canEdit) {
            return;
        }

        try {
            // Auto-save the file
            $this->validateAdditionalCert($this->editingAdditionalId);
            session()->flash('success', 'File surat keterangan berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: '.$e->getMessage());
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

        if (! $this->progressReport) {
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
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
        }
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

        if (! $this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');

            return;
        }

        try {
            // Sync to form
            $this->form->additionalOutputs = $this->additionalOutputs;
            $this->form->tempAdditionalFiles = $this->tempAdditionalFiles;
            $this->form->tempAdditionalCerts = $this->form->tempAdditionalCerts;
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
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
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
        return view('livewire.research.final-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
