<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\FinalReport;

use App\Enums\ProposalStatus;
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

        // Check if proposal is completed
        if ($this->proposal->status !== ProposalStatus::COMPLETED) {
            abort(403, 'Laporan akhir hanya dapat diakses untuk proposal yang sudah selesai.');
        }

        // Check access
        $this->checkAccess();

        // Load existing final report
        $this->progressReport = $proposal->progressReports()->finalReports()->latest()->first();

        if (! $this->progressReport) {
            $this->progressReport = $proposal->progressReports()->latest()->first();
        }

        // Initialize Livewire Form
        $this->form->type = 'final';
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
            session()->flash('error', 'Gagal menyimpan laporan: '.$e->getMessage());
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
                $this->saveMandatoryOutputFile($mandatoryOutput, $proposalOutputId, 'final');
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
                $this->saveAdditionalOutputFile($additionalOutput, $proposalOutputId, 'final');
                $this->saveAdditionalOutputCert($additionalOutput, $proposalOutputId, 'final');
            }
        }
    }

    /**
     * Handle substance file upload (real-time)
     */
    public function updatedSubstanceFile(): void
    {
        if (! $this->canEdit) {
            $this->substanceFile = null;

            return;
        }

        // Validate file
        $this->validateSubstanceFile();
    }

    /**
     * Handle realization file upload (real-time)
     */
    public function updatedRealizationFile(): void
    {
        if (! $this->canEdit) {
            $this->realizationFile = null;

            return;
        }

        // Validate file
        $this->validateRealizationFile();
    }

    /**
     * Handle presentation file upload (real-time)
     */
    public function updatedPresentationFile(): void
    {
        if (! $this->canEdit) {
            $this->presentationFile = null;

            return;
        }

        // Validate file
        $this->validatePresentationFile();
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
     * Save mandatory output after validation
     */
    public function saveMandatoryOutput(int $proposalOutputId): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        $this->form->saveMandatoryOutput($proposalOutputId);
        $this->dispatch('close-modal', detail: ['modalId' => 'modalMandatoryOutput']);
        session()->flash('success', 'Data luaran wajib berhasil disimpan.');
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
        $this->dispatch('close-modal', detail: ['modalId' => 'modalAdditionalOutput']);
        session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
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
        return view('livewire.community-service.final-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
