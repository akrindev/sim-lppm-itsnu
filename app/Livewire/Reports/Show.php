<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Livewire\Forms\ReportForm;
use App\Livewire\Traits\HasFileUploads;
use App\Livewire\Traits\ReportAccess;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\AdditionalOutput;
use App\Models\Keyword;
use App\Models\MandatoryOutput;
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
    public ReportForm $form;

    // Report configuration
    protected array $config = [];

    public function mount(Proposal $proposal, string $type = 'research-progress'): void
    {
        $this->proposal = $proposal;
        $this->config = $this->getConfig($type);

        $this->checkAccess();
        $this->loadReport();

        // Determine report type for ReportForm
        $reportType = str_contains($type, 'final') ? 'final' : 'progress';

        // Initialize Livewire Form
        $this->form->type = $reportType;
        $this->form->initWithProposal($this->proposal);

        if ($this->progressReport) {
            // Load existing report data into form
            $this->form->setReport($this->progressReport);
        } else {
            // Initialize new report structure
            $this->form->initializeNewReport();
        }
    }

    protected function getConfig(string $type): array
    {
        $configs = [
            'research-progress' => [
                'view' => 'livewire.research.progress-report.show',
                'route' => 'research.progress-report.index',
            ],
            'research-final' => [
                'view' => 'livewire.research.final-report.show',
                'route' => 'research.final-report.index',
            ],
            'community-service-progress' => [
                'view' => 'livewire.community-service.progress-report.show',
                'route' => 'community-service.progress-report.index',
            ],
            'community-service-final' => [
                'view' => 'livewire.community-service.final-report.show',
                'route' => 'community-service.final-report.index',
            ],
        ];

        return $configs[$type] ?? $configs['research-progress'];
    }

    public function save(): void
    {
        if (! $this->canEdit) {
            abort(403);
        }

        // Validate substance file if present
        if ($this->form->substanceFile) {
            $this->validate([
                'form.substanceFile' => 'nullable|file|mimes:pdf|max:10240',
            ]);
        }

        DB::transaction(function () {
            // Save report via form
            $report = $this->form->save($this->progressReport);
            $this->progressReport = $report;

            // Save substance file if provided
            $this->saveSubstanceFile($report);
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan berhasil disimpan.');
    }

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

        // Ensure config is initialized before redirect
        if (empty($this->config) || ! isset($this->config['route'])) {
            $this->config = $this->getConfig('research-progress');
        }

        session()->flash('success', 'Laporan berhasil diajukan.');
        $this->redirect(route($this->config['route']), navigate: true);
    }

    public function updatedTempMandatoryFiles($value, $key): void
    {
        $this->saveMandatoryOutput((int) $key, closeModal: false, validate: false);
    }

    public function updatedTempAdditionalFiles($value, $key): void
    {
        $this->saveAdditionalOutput((int) $key, closeModal: false, validate: false);
    }

    public function updatedTempAdditionalCerts($value, $key): void
    {
        $this->saveAdditionalOutput((int) $key, closeModal: false, validate: false);
    }

    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->form->editMandatoryOutput($proposalOutputId);
    }

    public function saveMandatoryOutput(?int $proposalOutputId = null, bool $closeModal = true, bool $validate = true): void
    {
        if (! $this->canEdit || ! $this->progressReport) {
            return;
        }

        // Use editing ID if no parameter provided (for backward compatibility)
        $proposalOutputId = $proposalOutputId ?? $this->form->editingMandatoryId;

        if (! $proposalOutputId) {
            return;
        }

        try {
            // Ensure form has the progress report reference
            $this->form->progressReport = $this->progressReport;

            // Transfer files from component to form
            if (isset($this->tempMandatoryFiles[$proposalOutputId])) {
                $this->form->tempMandatoryFiles[$proposalOutputId] = $this->tempMandatoryFiles[$proposalOutputId];
            }

            // Save via form
            $this->form->saveMandatoryOutputWithFile($proposalOutputId, $validate);

            // Clear the component's temp files
            unset($this->tempMandatoryFiles[$proposalOutputId]);

            if ($closeModal) {
                $this->dispatch('close-modal', modalId: 'modalMandatoryOutput');
            }
            
            if ($closeModal) {
                session()->flash('success', 'Data luaran wajib berhasil disimpan.');
            } else {
                 // For auto-save, maybe a toast? or just silent success + UI update
                 // We can skip flash to avoid annoying popups, or use a different key.
                 session()->flash('success', 'File berhasil diupload.');
            }
            
            // Refresh parent report to update UI status
            if ($this->progressReport) {
                $this->progressReport->refresh();
                $this->progressReport->load('mandatoryOutputs');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // If auto-saving (closeModal = false), we might want to suppress some errors or just show them
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->form->editAdditionalOutput($proposalOutputId);
    }

    public function saveAdditionalOutput(?int $proposalOutputId = null, bool $closeModal = true, bool $validate = true): void
    {
        if (! $this->canEdit || ! $this->progressReport) {
            return;
        }

        // Use editing ID if no parameter provided (for backward compatibility)
        $proposalOutputId = $proposalOutputId ?? $this->form->editingAdditionalId;

        if (! $proposalOutputId) {
            return;
        }

        try {
            // Ensure form has the progress report reference
            $this->form->progressReport = $this->progressReport;

            // Transfer files from component to form
            if (isset($this->tempAdditionalFiles[$proposalOutputId])) {
                $this->form->tempAdditionalFiles[$proposalOutputId] = $this->tempAdditionalFiles[$proposalOutputId];
            }
            if (isset($this->tempAdditionalCerts[$proposalOutputId])) {
                $this->form->tempAdditionalCerts[$proposalOutputId] = $this->tempAdditionalCerts[$proposalOutputId];
            }

            // Save via form
            $this->form->saveAdditionalOutputWithFile($proposalOutputId, $validate);

            // Clear the component's temp files
            unset($this->tempAdditionalFiles[$proposalOutputId]);
            unset($this->tempAdditionalCerts[$proposalOutputId]);

            if ($closeModal) {
                $this->dispatch('close-modal', modalId: 'modalAdditionalOutput');
            }
            
            if ($closeModal) {
                session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
            } else {
                session()->flash('success', 'File berhasil diupload.');
            }

            // Refresh parent report to update UI status
            if ($this->progressReport) {
                $this->progressReport->refresh();
                $this->progressReport->load('additionalOutputs');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function closeMandatoryModal(): void
    {
        $this->form->closeMandatoryModal();
    }

    public function closeAdditionalModal(): void
    {
        $this->form->closeAdditionalModal();
    }

    /**
     * Get mandatory output model for editing
     */
    #[\Livewire\Attributes\Computed]
    public function mandatoryOutput(): ?MandatoryOutput
    {
        if (! $this->progressReport || ! $this->form->editingMandatoryId) {
            return null;
        }

        return MandatoryOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingMandatoryId)
            ->first();
    }

    /**
     * Get additional output model for editing
     */
    #[\Livewire\Attributes\Computed]
    public function additionalOutput(): ?AdditionalOutput
    {
        if (! $this->progressReport || ! $this->form->editingAdditionalId) {
            return null;
        }

        return AdditionalOutput::where('progress_report_id', $this->progressReport->id)
            ->where('proposal_output_id', $this->form->editingAdditionalId)
            ->first();
    }

    public function render()
    {
        if (empty($this->config) || ! isset($this->config['view'])) {
            // Re-initialize config if missing (e.g., after component hydration)
            $this->config = $this->getConfig('research-progress');
        }

        $allKeywords = Keyword::orderBy('name')->get();

        return view($this->config['view'], [
            'allKeywords' => $allKeywords,
            'editingMandatoryId' => $this->form->editingMandatoryId,
            'editingAdditionalId' => $this->form->editingAdditionalId,
        ]);
    }
}
