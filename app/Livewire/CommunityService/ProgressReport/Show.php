<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProgressReport;

use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\ReportForm;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ManagesOutputs;
use App\Livewire\Traits\ReportAccess;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use HasFileUploads;
    use HasToast;
    use ManagesOutputs;
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
            $this->loadExistingReport($this->progressReport);
        } else {
            // Initialize new report structure
            $this->form->initializeNewReport();
            $this->initializeNewReport($this->proposal);
        }
    }

    /**
     * Lifecycle hook: when temp mandatory file is uploaded
     */
    public function updatedTempMandatoryFiles(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateMandatoryFile((int) $key);
        }
    }

    /**
     * Lifecycle hook: when temp additional file is uploaded
     */
    public function updatedTempAdditionalFiles(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateAdditionalFile((int) $key);
        }
    }

    /**
     * Lifecycle hook: when temp additional certificate is uploaded
     */
    public function updatedTempAdditionalCerts(mixed $value, string $key): void
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $this->validateAdditionalCert((int) $key);
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

        // Validate substance file if present
        $this->validateSubstanceFile();

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Save substance file
            $this->saveSubstanceFile($report);

            // Save output files
            $this->saveOutputFiles($report);
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
            // Submit report via form
            $report = $this->form->submit($this->progressReport);
            $this->progressReport = $report;

            // Save substance file
            $this->saveSubstanceFile($report);

            // Save output files
            $this->saveOutputFiles($report);
        });

        $message = 'Laporan kemajuan berhasil diajukan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
        $this->redirect(route('community-service.progress-report.index'), navigate: true);
    }

    /**
     * Save all output files
     */
    protected function saveOutputFiles($report): void
    {
        // Save mandatory output files
        foreach ($this->mandatoryOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (! is_string($proposalOutputId) && ! is_numeric($proposalOutputId))) {
                continue;
            }

            if (empty($data['status_type']) && empty($data['journal_title'])) {
                continue;
            }

            // Find the mandatory output
            $mandatoryOutput = \App\Models\MandatoryOutput::where('progress_report_id', $report->id)
                ->where('proposal_output_id', $proposalOutputId)
                ->first();

            if ($mandatoryOutput) {
                $this->saveMandatoryOutputFile($mandatoryOutput, $proposalOutputId);
            }
        }

        // Save additional output files
        foreach ($this->additionalOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (! is_string($proposalOutputId) && ! is_numeric($proposalOutputId))) {
                continue;
            }

            if (empty($data['status']) && empty($data['book_title'])) {
                continue;
            }

            // Find the additional output
            $additionalOutput = \App\Models\AdditionalOutput::where('progress_report_id', $report->id)
                ->where('proposal_output_id', $proposalOutputId)
                ->first();

            if ($additionalOutput) {
                $this->saveAdditionalOutputFile($additionalOutput, $proposalOutputId);
                $this->saveAdditionalOutputCert($additionalOutput, $proposalOutputId);
            }
        }
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

        // Close modal using js() method - Livewire v3 pattern
        $this->js("
            const modal = document.getElementById('modalMandatoryOutput');
            if (modal) {
                const bsModal = window.getBsModal ? window.getBsModal(modal) : (window.bootstrap?.Modal?.getInstance(modal) || window.tabler?.bootstrap?.Modal?.getInstance(modal));
                if (bsModal) bsModal.hide();
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

        // Close modal using js() method - Livewire v3 pattern
        $this->js("
            const modal = document.getElementById('modalAdditionalOutput');
            if (modal) {
                const bsModal = window.getBsModal ? window.getBsModal(modal) : (window.bootstrap?.Modal?.getInstance(modal) || window.tabler?.bootstrap?.Modal?.getInstance(modal));
                if (bsModal) bsModal.hide();
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
        return view('livewire.community-service.progress-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
