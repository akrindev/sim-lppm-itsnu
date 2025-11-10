<?php

declare(strict_types=1);

namespace App\Livewire\Abstracts;

use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ManagesOutputs;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class ReportOutputShow extends ReportShow
{
    use HasFileUploads;
    use ManagesOutputs;

    // Form instance
    public $form;

    // Form properties exposed to Blade (mirrors form properties)
    public string $summaryUpdate = '';
    public string $keywordsInput = '';
    public int $reportingYear;
    public string $reportingPeriod = 'semester_1';

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
        $this->checkAccess();
        $this->loadReport();

        // Initialize form
        $formClass = $this->getFormClass();
        $this->form = new $formClass();
        $this->form->initWithProposal($this->proposal);
        $this->reportingYear = (int) date('Y');
        $this->reportingPeriod = 'semester_1';

        if ($this->progressReport) {
            $this->loadExistingReport($this->progressReport);
            $this->form->setReport($this->progressReport);
            // Sync form properties to component properties
            $this->syncFormToComponent();
        } else {
            $this->initializeNewReport($this->proposal);
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

        // Validate substance file
        $this->validateSubstanceFile();

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);

            // Save substance file
            $this->saveSubstanceFile($report);

            // Save output files
            $this->saveOutputFiles($report);
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan kemajuan berhasil disimpan.');
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

            // Save substance file
            $this->saveSubstanceFile($report);

            // Save output files
            $this->saveOutputFiles($report);
        });

        session()->flash('success', 'Laporan kemajuan berhasil diajukan.');
        $this->redirect(route($this->getRouteName()), navigate: true);
    }

    /**
     * Save all output files
     */
    protected function saveOutputFiles(ProgressReport $report): void
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
        $this->form->saveMandatoryOutput($proposalOutputId);
        $this->dispatch('close-modal', detail: ['modalId' => 'modalMandatoryOutput']);
        session()->flash('success', 'Data luaran wajib berhasil disimpan.');
    }

    /**
     * Save additional output after validation
     */
    public function saveAdditionalOutput(int $proposalOutputId): void
    {
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
