<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\FinalReport;

use App\Enums\ProposalStatus;
use App\Livewire\Forms\CommunityServiceFinalReportForm;
use App\Livewire\Traits\HasFileUploads;
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
    use ReportAccess;
    use ReportAuthorization;
    use WithFileUploads;

    // Form instance - Livewire v3 Form pattern
    public CommunityServiceFinalReportForm $form;

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

        try {
            DB::transaction(function () {
                // Save report via form
                $report = $this->form->save($this->progressReport);
                $this->progressReport = $report;

                // Save report files
                $this->saveSubstanceFile($report, 'final');
                $this->saveRealizationFile($report, 'final');
                $this->savePresentationFile($report, 'final');

                // Save output files
                $this->saveOutputFiles($report);
            });

            $this->dispatch('report-saved');
            session()->flash('success', 'Laporan akhir berhasil disimpan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let Livewire handle validation errors
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
        }
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

            // Save report files
            $this->saveSubstanceFile($report, 'final');
            $this->saveRealizationFile($report, 'final');
            $this->savePresentationFile($report, 'final');

            // Save output files
            $this->saveOutputFiles($report);
        });

        session()->flash('success', 'Laporan akhir berhasil diajukan.');
        $this->redirect(route('community-service.final-report.index'), navigate: true);
    }

    /**
     * Save all output files
     */
    protected function saveOutputFiles($report): void
    {
        // Save mandatory output files
        foreach ($this->form->mandatoryOutputs as $proposalOutputId => $data) {
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

            if ($mandatoryOutput && isset($this->tempMandatoryFiles[$proposalOutputId])) {
                $this->saveMandatoryOutputFile($mandatoryOutput, $proposalOutputId, 'final');
            }
        }

        // Save additional output files
        foreach ($this->form->additionalOutputs as $proposalOutputId => $data) {
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
                if (isset($this->tempAdditionalFiles[$proposalOutputId])) {
                    $this->saveAdditionalOutputFile($additionalOutput, $proposalOutputId, 'final');
                }
                if (isset($this->tempAdditionalCerts[$proposalOutputId])) {
                    $this->saveAdditionalOutputCert($additionalOutput, $proposalOutputId, 'final');
                }
            }
        }
    }

    /**
     * Handle substance file upload (real-time validation)
     */
    public function updatedSubstanceFile(): void
    {
        if (! $this->canEdit) {
            $this->substanceFile = null;

            return;
        }

        $this->validateSubstanceFile();
    }

    /**
     * Handle realization file upload (real-time validation)
     */
    public function updatedRealizationFile(): void
    {
        if (! $this->canEdit) {
            $this->realizationFile = null;

            return;
        }

        $this->validateRealizationFile();
    }

    /**
     * Handle presentation file upload (real-time validation)
     */
    public function updatedPresentationFile(): void
    {
        if (! $this->canEdit) {
            $this->presentationFile = null;

            return;
        }

        $this->validatePresentationFile();
    }

    /**
     * Handle mandatory output file upload (real-time validation)
     */
    public function updatedTempMandatoryFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        foreach ($this->tempMandatoryFiles as $proposalOutputId => $file) {
            $this->validate([
                "tempMandatoryFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);
        }
    }

    /**
     * Handle additional output file upload (real-time validation)
     */
    public function updatedTempAdditionalFiles(): void
    {
        if (! $this->canEdit) {
            return;
        }

        foreach ($this->tempAdditionalFiles as $proposalOutputId => $file) {
            $this->validate([
                "tempAdditionalFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);
        }
    }

    /**
     * Handle additional output certificate upload (real-time validation)
     */
    public function updatedTempAdditionalCerts(): void
    {
        if (! $this->canEdit) {
            return;
        }

        foreach ($this->tempAdditionalCerts as $proposalOutputId => $file) {
            $this->validate([
                "tempAdditionalCerts.{$proposalOutputId}" => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);
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
     * Edit mandatory output - open modal
     */
    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->form->editMandatoryOutput($proposalOutputId);
    }

    /**
     * Save mandatory output (journal article)
     */
    public function saveMandatoryOutput(int $proposalOutputId): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        if (! $this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');

            return;
        }

        try {
            // Ensure form has the progress report reference
            $this->form->progressReport = $this->progressReport;

            // Save via form
            $this->form->saveMandatoryOutputWithFile($proposalOutputId);

            session()->flash('success', 'Data luaran wajib berhasil disimpan.');
            $this->dispatch('close-modal', detail: ['modalId' => 'modalMandatoryOutput']);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * Edit additional output - open modal
     */
    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->form->editAdditionalOutput($proposalOutputId);
    }

    /**
     * Save additional output (book)
     */
    public function saveAdditionalOutput(int $proposalOutputId): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        if (! $this->progressReport) {
            session()->flash('error', 'Laporan belum dibuat. Silakan upload file substansi terlebih dahulu.');

            return;
        }

        try {
            // Ensure form has the progress report reference
            $this->form->progressReport = $this->progressReport;

            // Save via form
            $this->form->saveAdditionalOutputWithFile($proposalOutputId);

            session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
            $this->dispatch('close-modal', detail: ['modalId' => 'modalAdditionalOutput']);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
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
     * Get all keywords for the view
     */
    public function getAllKeywords(): \Illuminate\Database\Eloquent\Collection
    {
        return Keyword::orderBy('name')->get();
    }

    /**
     * Get mandatory output model by proposal output ID
     */
    #[Computed]
    public function getMandatoryOutput(): ?\App\Models\MandatoryOutput
    {
        if (!$this->progressReport || !$this->form->editingMandatoryId) {
            return null;
        }

        return \App\Models\MandatoryOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingMandatoryId)
            ->first();
    }

    /**
     * Get additional output model by proposal output ID
     */
    #[Computed]
    public function getAdditionalOutput(): ?\App\Models\AdditionalOutput
    {
        if (!$this->progressReport || !$this->form->editingAdditionalId) {
            return null;
        }

        return \App\Models\AdditionalOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingAdditionalId)
            ->first();
    }

    /**
     * Render the view
     */
    public function render()
    {
        return view('livewire.community-service.final-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
