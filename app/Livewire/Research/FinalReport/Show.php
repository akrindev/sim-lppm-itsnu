<?php

declare(strict_types=1);

namespace App\Livewire\Research\FinalReport;

use App\Enums\ProposalStatus;
use App\Livewire\Forms\ResearchFinalReportForm;
use App\Livewire\Traits\ReportAccess;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\Keyword;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
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

        if (! $this->progressReport) {
            $this->progressReport = $proposal->progressReports()->latest()->first();
        }

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

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;
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
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
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
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
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
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
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
            // Validate file via form
            foreach ($this->form->tempMandatoryFiles as $proposalOutputId => $file) {
                $this->validate([
                    "form.tempMandatoryFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                ]);
            }
            session()->flash('success', 'File dokumen artikel berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
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
            // Validate file via form
            foreach ($this->form->tempAdditionalFiles as $proposalOutputId => $file) {
                $this->validate([
                    "form.tempAdditionalFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                ]);
            }
            session()->flash('success', 'File dokumen buku berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah file: ' . $e->getMessage());
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
            // Validate file via form
            foreach ($this->form->tempAdditionalCerts as $proposalOutputId => $file) {
                $this->validate([
                    "form.tempAdditionalCerts.{$proposalOutputId}" => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                ]);
            }
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
     * Render the view
     */
    public function render()
    {
        return view('livewire.research.final-report.show', [
            'allKeywords' => $this->getAllKeywords(),
        ]);
    }
}
