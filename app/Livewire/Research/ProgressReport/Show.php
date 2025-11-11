<?php

declare(strict_types=1);

namespace App\Livewire\Research\ProgressReport;

use App\Livewire\Forms\ResearchProgressReportForm;
use App\Livewire\Traits\HasFileUploads;
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
    use ReportAccess;
    use ReportAuthorization;
    use WithFileUploads;

    // Form instance - Livewire v3 Form pattern
    public ResearchProgressReportForm $form;

    /**
     * Mount the component
     */
    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;
        $this->checkAccess();
        $this->loadReport();

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

        // Validate substance file if present
        $this->validateSubstanceFile();

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Save substance file
            $this->saveSubstanceFile($report);
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan kemajuan berhasil disimpan.');
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
        });

        session()->flash('success', 'Laporan kemajuan berhasil diajukan.');
        $this->redirect(route('research.progress-report.index'), navigate: true);
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
        return view('livewire.research.progress-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
