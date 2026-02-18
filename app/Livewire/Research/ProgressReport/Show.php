<?php

declare(strict_types=1);

namespace App\Livewire\Research\ProgressReport;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\ReportForm;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ReportAccess;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\Proposal;
use App\Services\MasterDataService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use HasFileUploads;
    use HasToast;
    use ReportAccess;
    use ReportAuthorization;
    use WithFileUploads;

    // Form instance - Livewire v3 Form pattern
    public ReportForm $form;

    /**
     * Mount the component
     */
    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;
        $this->checkAccess();
        $this->loadReport();

        // Initialize Livewire Form
        $this->form->type = 'progress';
        $this->form->initWithProposal($this->proposal);

        if ($this->progressReport) {
            // Load existing report data into form
            $this->form->setReport($this->progressReport);
        } else {
            // Initialize new report structure
            $this->form->initializeNewReport();
        }
    }

    /**
     * Lifecycle hook: when substance file is uploaded
     */
    public function updatedSubstanceFile(): void
    {
        $this->validateSubstanceFile();

        $this->form->substanceFile = $this->substanceFile;
        $this->form->handleFileUpload('substanceFile');
        $this->progressReport = $this->form->progressReport; // Refresh local reference

        $message = 'File substansi berhasil diupload.';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        // Optional: clear the input if desired, but keeping it shows what was selected.
        // But for consistency with other inputs, we might want to keep it or let the view update.
    }

    /**
     * Lifecycle hook: when temp mandatory file is uploaded
     */
    public function updatedTempMandatoryFiles(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateMandatoryFile((int) $key);

            $this->form->tempMandatoryFiles[(int) $key] = $value;
            $this->form->saveMandatoryOutputWithFile((int) $key);

            // Refresh report reference in case it was created
            $this->progressReport = $this->form->progressReport;

            // Clear temp file to hide preview and allow showing the saved file
            unset($this->tempMandatoryFiles[$key]);

            $message = 'File luaran wajib berhasil disimpan.';
            session()->flash('success', $message);
            $this->toastSuccess($message);
        }
    }

    /**
     * Lifecycle hook: when temp additional file is uploaded
     */
    public function updatedTempAdditionalFiles(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateAdditionalFile((int) $key);

            $this->form->tempAdditionalFiles[(int) $key] = $value;
            $this->form->saveAdditionalOutputWithFile((int) $key);

            // Refresh report reference
            $this->progressReport = $this->form->progressReport;

            // Clear temp file
            unset($this->tempAdditionalFiles[$key]);

            $message = 'File luaran tambahan berhasil disimpan.';
            session()->flash('success', $message);
            $this->toastSuccess($message);
        }
    }

    /**
     * Lifecycle hook: when temp additional certificate is uploaded
     */
    public function updatedTempAdditionalCerts(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateAdditionalCert((int) $key);

            $this->form->tempAdditionalCerts[(int) $key] = $value;
            $this->form->saveAdditionalOutputWithFile((int) $key);

            // Refresh report reference
            $this->progressReport = $this->form->progressReport;

            // Clear temp file
            unset($this->tempAdditionalCerts[$key]);

            $message = 'Sertifikat berhasil disimpan.';
            session()->flash('success', $message);
            $this->toastSuccess($message);
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

        // Validate substance file if presence check needed, but it's optional in draft usually
        // $this->validateSubstanceFile();

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Substance file is already saved if uploaded via auto-save,
            // but if user selected file without auto-save triggering (unlikely given updated hook), check again?
            if ($this->substanceFile) {
                $this->form->substanceFile = $this->substanceFile;
                $this->form->saveReportFiles($report);
            }
        });

        $this->dispatch('report-saved');
        $message = 'Laporan kemajuan berhasil disimpan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
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
            // Ensure substance file is passed if exists
            if ($this->substanceFile) {
                $this->form->substanceFile = $this->substanceFile;
            }

            // Submit report via form
            $report = $this->form->submit($this->progressReport);
            $this->progressReport = $report;
        });

        $message = 'Laporan kemajuan berhasil diajukan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
        $this->redirect(route('research.progress-report.index'), navigate: true);
    }

    #[Computed]
    public function reportEndorsementTemplateUrl(): ?string
    {
        return app(MasterDataService::class)->getReportEndorsementTemplateUrl('research');
    }

    /**
     * Save all output files
     */
    protected function saveOutputFiles($report): void
    {
        // Files are now saved via saveMandatoryOutputWithFile() and saveAdditionalOutputWithFile()
        // This method is kept for compatibility but does nothing as files are handled in form
    }

    /**
     * Open mandatory output modal
     */
    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->form->editMandatoryOutput($proposalOutputId);
    }

    /**
     * Open additional output modal
     */
    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->form->editAdditionalOutput($proposalOutputId);
    }

    /**
     * Close mandatory modal
     */
    public function closeMandatoryModal(): void
    {
        $this->form->closeMandatoryModal();
    }

    /**
     * Close additional modal
     */
    public function closeAdditionalModal(): void
    {
        $this->form->closeAdditionalModal();
    }

    /**
     * Save mandatory output after validation
     */
    public function saveMandatoryOutput(int $proposalOutputId): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        $this->form->saveMandatoryOutput($proposalOutputId);
        $this->progressReport = $this->form->progressReport; // Sync

        // Close modal using robust JS
        $this->js("
            const modalEl = document.getElementById('modalMandatoryOutput');
            if (modalEl) {
                const modal = window.getBsModal ? window.getBsModal(modalEl) : (window.bootstrap?.Modal?.getOrCreateInstance(modalEl) || window.tabler?.bootstrap?.Modal?.getOrCreateInstance(modalEl));
                if (modal) modal.hide();
            }
        ");

        $message = 'Data luaran wajib berhasil disimpan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    /**
     * Save additional output after validation
     */
    public function saveAdditionalOutput(int $proposalOutputId): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        $this->form->saveAdditionalOutput($proposalOutputId);
        $this->progressReport = $this->form->progressReport; // Sync

        // Close modal using robust JS
        $this->js("
            const modalEl = document.getElementById('modalAdditionalOutput');
            if (modalEl) {
                const modal = window.getBsModal ? window.getBsModal(modalEl) : (window.bootstrap?.Modal?.getOrCreateInstance(modalEl) || window.tabler?.bootstrap?.Modal?.getOrCreateInstance(modalEl));
                if (modal) modal.hide();
            }
        ");

        $message = 'Data luaran tambahan berhasil disimpan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    /**
     * Validate mandatory output
     */
    public function validateMandatoryOutput(int $proposalOutputId): void
    {
        $this->form->validateMandatoryOutput($proposalOutputId);
    }

    /**
     * Validate additional output
     */
    public function validateAdditionalOutput(int $proposalOutputId): void
    {
        $this->form->validateAdditionalOutput($proposalOutputId);
    }

    /**
     * Get mandatory output model for editing
     */
    #[Computed]
    public function mandatoryOutput(): ?\App\Models\MandatoryOutput
    {
        if (! $this->progressReport || ! $this->form->editingMandatoryId) {
            return null;
        }

        return \App\Models\MandatoryOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingMandatoryId)
            ->first();
    }

    /**
     * Get additional output model for editing
     */
    #[Computed]
    public function additionalOutput(): ?\App\Models\AdditionalOutput
    {
        if (! $this->progressReport || ! $this->form->editingAdditionalId) {
            return null;
        }

        return \App\Models\AdditionalOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingAdditionalId)
            ->first();
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
        return view('livewire.research.progress-report.show', [
            'allKeywords' => $this->getAllKeywords(),
            'editingMandatoryId' => $this->form->editingMandatoryId,
            'editingAdditionalId' => $this->form->editingAdditionalId,
        ]);
    }
}
