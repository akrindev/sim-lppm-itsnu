<?php

declare(strict_types=1);

namespace App\Livewire\Research\ProgressReport;

use App\Models\AdditionalOutput;
use App\Models\Keyword;
use App\Models\MandatoryOutput;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Proposal $proposal;

    public ?ProgressReport $progressReport = null;

    // Form fields
    public string $summaryUpdate = '';

    public array $selectedKeywords = [];

    public int $reportingYear;

    public string $reportingPeriod = 'semester_1';

    // Arrays for outputs (indexed by proposal_output_id)
    public array $mandatoryOutputs = [];

    public array $additionalOutputs = [];

    // Track which output is being edited
    public ?int $editingMandatoryId = null;

    public ?int $editingAdditionalId = null;

    // Temporary file uploads
    public array $tempMandatoryFiles = [];

    public array $tempAdditionalFiles = [];

    public array $tempAdditionalCerts = [];

    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;

        // Check authorization
        if (! $this->canEditProgressReport()) {
            abort(403, 'Anda tidak memiliki akses untuk membuat laporan kemajuan proposal ini.');
        }

        // Load latest progress report or initialize new
        $this->progressReport = $proposal->progressReports()->latest()->first();

        if ($this->progressReport) {
            $this->loadExistingReport();
        } else {
            $this->initializeNewReport();
        }
    }

    protected function canEditProgressReport(): bool
    {
        $user = Auth::user();

        // Proposal owner can edit
        if ($this->proposal->submitter_id === $user->id) {
            return true;
        }

        // Accepted team members can edit
        $isTeamMember = $this->proposal->teamMembers()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        return $isTeamMember;
    }

    protected function loadExistingReport(): void
    {
        $this->summaryUpdate = $this->progressReport->summary_update ?? '';
        $this->reportingYear = $this->progressReport->reporting_year;
        $this->reportingPeriod = $this->progressReport->reporting_period;
        $this->selectedKeywords = $this->progressReport->keywords()->pluck('keywords.id')->toArray();

        // Load existing mandatory outputs
        foreach ($this->progressReport->mandatoryOutputs as $output) {
            // Skip if proposal_output_id is null or empty
            if (empty($output->proposal_output_id)) {
                continue;
            }

            $this->mandatoryOutputs[$output->proposal_output_id] = [
                'id' => $output->id,
                'status_type' => $output->status_type,
                'author_status' => $output->author_status,
                'journal_title' => $output->journal_title,
                'issn' => $output->issn,
                'eissn' => $output->eissn,
                'indexing_body' => $output->indexing_body,
                'journal_url' => $output->journal_url,
                'article_title' => $output->article_title,
                'publication_year' => $output->publication_year,
                'volume' => $output->volume,
                'issue_number' => $output->issue_number,
                'page_start' => $output->page_start,
                'page_end' => $output->page_end,
                'article_url' => $output->article_url,
                'doi' => $output->doi,
                'document_file' => $output->document_file,
            ];
        }

        // Load existing additional outputs
        foreach ($this->progressReport->additionalOutputs as $output) {
            // Skip if proposal_output_id is null or empty
            if (empty($output->proposal_output_id)) {
                continue;
            }

            $this->additionalOutputs[$output->proposal_output_id] = [
                'id' => $output->id,
                'status' => $output->status,
                'book_title' => $output->book_title,
                'publisher_name' => $output->publisher_name,
                'isbn' => $output->isbn,
                'publication_year' => $output->publication_year,
                'total_pages' => $output->total_pages,
                'publisher_url' => $output->publisher_url,
                'book_url' => $output->book_url,
                'document_file' => $output->document_file,
                'publication_certificate' => $output->publication_certificate,
            ];
        }
    }

    protected function initializeNewReport(): void
    {
        $this->summaryUpdate = $this->proposal->summary ?? '';
        $this->reportingYear = (int) date('Y');
        $this->selectedKeywords = $this->proposal->keywords()->pluck('keywords.id')->toArray();

        // Initialize empty arrays for planned outputs
        foreach ($this->proposal->outputs->where('category', 'Wajib') as $output) {
            $this->mandatoryOutputs[$output->id] = $this->getEmptyMandatoryOutput();
        }

        foreach ($this->proposal->outputs->where('category', 'Tambahan') as $output) {
            $this->additionalOutputs[$output->id] = $this->getEmptyAdditionalOutput();
        }
    }

    protected function getEmptyMandatoryOutput(): array
    {
        return [
            'id' => null,
            'status_type' => '',
            'author_status' => '',
            'journal_title' => '',
            'issn' => '',
            'eissn' => '',
            'indexing_body' => '',
            'journal_url' => '',
            'article_title' => '',
            'publication_year' => '',
            'volume' => '',
            'issue_number' => '',
            'page_start' => '',
            'page_end' => '',
            'article_url' => '',
            'doi' => '',
            'document_file' => '',
        ];
    }

    protected function getEmptyAdditionalOutput(): array
    {
        return [
            'id' => null,
            'status' => '',
            'book_title' => '',
            'publisher_name' => '',
            'isbn' => '',
            'publication_year' => '',
            'total_pages' => '',
            'publisher_url' => '',
            'book_url' => '',
            'document_file' => '',
            'publication_certificate' => '',
        ];
    }

    public function save(): void
    {
        $this->validate([
            'summaryUpdate' => 'required|min:100',
            'reportingYear' => 'required|numeric|between:2020,2030',
            'reportingPeriod' => 'required|in:semester_1,semester_2,annual',
        ]);

        DB::transaction(function () {
            // Create or update progress report
            if ($this->progressReport) {
                $this->progressReport->update([
                    'summary_update' => $this->summaryUpdate,
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                ]);
            } else {
                $this->progressReport = ProgressReport::create([
                    'proposal_id' => $this->proposal->id,
                    'summary_update' => $this->summaryUpdate,
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                    'status' => 'draft',
                ]);
            }

            // Sync keywords
            $this->progressReport->keywords()->sync($this->selectedKeywords);

            // Save mandatory outputs
            $this->saveMandatoryOutputs();

            // Save additional outputs
            $this->saveAdditionalOutputs();
        });

        session()->flash('success', 'Laporan kemajuan berhasil disimpan sebagai draft.');
        $this->dispatch('alert', type: 'success', message: 'Laporan kemajuan berhasil disimpan sebagai draft.');
    }

    protected function saveMandatoryOutputs(): void
    {
        foreach ($this->mandatoryOutputs as $proposalOutputId => $data) {
            // Skip if invalid proposal_output_id (empty string, null, or not valid)
            if (empty($proposalOutputId) || ! is_string($proposalOutputId) && ! is_numeric($proposalOutputId)) {
                continue;
            }

            // Skip if no data entered
            if (empty($data['status_type']) && empty($data['journal_title'])) {
                continue;
            }

            // Handle file upload
            $filePath = $data['document_file'] ?? null;
            if (isset($this->tempMandatoryFiles[$proposalOutputId])) {
                $filePath = $this->tempMandatoryFiles[$proposalOutputId]->store('progress-reports/'.$this->proposal->id.'/mandatory', 'public');
            }

            $outputData = [
                'progress_report_id' => $this->progressReport->id,
                'proposal_output_id' => $proposalOutputId,
                'status_type' => $data['status_type'] ?? null,
                'author_status' => $data['author_status'] ?? null,
                'journal_title' => $data['journal_title'] ?? null,
                'issn' => $data['issn'] ?? null,
                'eissn' => $data['eissn'] ?? null,
                'indexing_body' => $data['indexing_body'] ?? null,
                'journal_url' => $data['journal_url'] ?? null,
                'article_title' => $data['article_title'] ?? null,
                'publication_year' => ! empty($data['publication_year']) ? $data['publication_year'] : null,
                'volume' => $data['volume'] ?? null,
                'issue_number' => $data['issue_number'] ?? null,
                'page_start' => ! empty($data['page_start']) ? (int) $data['page_start'] : null,
                'page_end' => ! empty($data['page_end']) ? (int) $data['page_end'] : null,
                'article_url' => $data['article_url'] ?? null,
                'doi' => $data['doi'] ?? null,
                'document_file' => $filePath,
            ];

            if (isset($data['id']) && $data['id']) {
                MandatoryOutput::where('id', $data['id'])->update($outputData);
            } else {
                MandatoryOutput::create($outputData);
            }
        }
    }

    protected function saveAdditionalOutputs(): void
    {
        foreach ($this->additionalOutputs as $proposalOutputId => $data) {
            // Skip if invalid proposal_output_id (empty string, null, or not valid)
            if (empty($proposalOutputId) || ! is_string($proposalOutputId) && ! is_numeric($proposalOutputId)) {
                continue;
            }

            // Skip if no data entered
            if (empty($data['status']) && empty($data['book_title'])) {
                continue;
            }

            // Handle file uploads
            $documentPath = $data['document_file'] ?? null;
            if (isset($this->tempAdditionalFiles[$proposalOutputId])) {
                $documentPath = $this->tempAdditionalFiles[$proposalOutputId]->store('progress-reports/'.$this->proposal->id.'/additional', 'public');
            }

            $certPath = $data['publication_certificate'] ?? null;
            if (isset($this->tempAdditionalCerts[$proposalOutputId])) {
                $certPath = $this->tempAdditionalCerts[$proposalOutputId]->store('progress-reports/'.$this->proposal->id.'/certificates', 'public');
            }

            $outputData = [
                'progress_report_id' => $this->progressReport->id,
                'proposal_output_id' => $proposalOutputId,
                'status' => $data['status'] ?? null,
                'book_title' => $data['book_title'] ?? null,
                'publisher_name' => $data['publisher_name'] ?? null,
                'isbn' => $data['isbn'] ?? null,
                'publication_year' => ! empty($data['publication_year']) ? $data['publication_year'] : null,
                'total_pages' => ! empty($data['total_pages']) ? (int) $data['total_pages'] : null,
                'publisher_url' => $data['publisher_url'] ?? null,
                'book_url' => $data['book_url'] ?? null,
                'document_file' => $documentPath,
                'publication_certificate' => $certPath,
            ];

            if (isset($data['id']) && $data['id']) {
                AdditionalOutput::where('id', $data['id'])->update($outputData);
            } else {
                AdditionalOutput::create($outputData);
            }
        }
    }

    public function submit()
    {
        $this->validate([
            'summaryUpdate' => 'required|min:100',
            'reportingYear' => 'required|numeric|between:2020,2030',
            'reportingPeriod' => 'required|in:semester_1,semester_2,annual',
        ]);

        // Save first
        $this->save();

        // Then submit
        if ($this->progressReport) {
            $this->progressReport->update([
                'status' => 'submitted',
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            session()->flash('success', 'Laporan kemajuan berhasil diajukan.');
            $this->dispatch('alert', type: 'success', message: 'Laporan kemajuan berhasil diajukan.');

            return $this->redirect(route('research.progress-report.index'), navigate: true);
        }
    }

    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->editingMandatoryId = $proposalOutputId;

        // Load existing data if available
        if (! isset($this->mandatoryOutputs[$proposalOutputId])) {
            $this->mandatoryOutputs[$proposalOutputId] = $this->getEmptyMandatoryOutput();
        }

        // Ensure the data structure is properly initialized to prevent reset issues
        if (empty($this->mandatoryOutputs[$proposalOutputId])) {
            $this->mandatoryOutputs[$proposalOutputId] = $this->getEmptyMandatoryOutput();
        }
    }

    public function saveMandatoryOutput(): void
    {
        if (! $this->editingMandatoryId) {
            return;
        }

        // Validation for single output
        $this->validate([
            "mandatoryOutputs.{$this->editingMandatoryId}.journal_title" => 'nullable|string|max:255',
            "mandatoryOutputs.{$this->editingMandatoryId}.article_title" => 'nullable|string|max:255',
            "mandatoryOutputs.{$this->editingMandatoryId}.publication_year" => 'nullable|integer|between:2000,2030',
        ]);

        // Don't reset editing state immediately to prevent form reset
        // $this->reset('editingMandatoryId'); // REMOVED - this was causing the reset issue

        // Save is handled by main save() method
        $this->dispatch('close-modal', detail: ['modalId' => 'modalMandatoryOutput']);
        // $this->dispatch('alert', type: 'success', message: 'Data luaran wajib berhasil disimpan.');
        // flash
        session()->flash('success', 'Data luaran wajib berhasil disimpan.');
    }

    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->editingAdditionalId = $proposalOutputId;

        if (! isset($this->additionalOutputs[$proposalOutputId])) {
            $this->additionalOutputs[$proposalOutputId] = $this->getEmptyAdditionalOutput();
        }

        // Ensure the data structure is properly initialized to prevent reset issues
        if (empty($this->additionalOutputs[$proposalOutputId])) {
            $this->additionalOutputs[$proposalOutputId] = $this->getEmptyAdditionalOutput();
        }
    }

    public function saveAdditionalOutput(): void
    {
        if (! $this->editingAdditionalId) {
            return;
        }

        // Validation for single output
        $this->validate([
            "additionalOutputs.{$this->editingAdditionalId}.book_title" => 'nullable|string|max:255',
            "additionalOutputs.{$this->editingAdditionalId}.publisher_name" => 'nullable|string|max:255',
            "additionalOutputs.{$this->editingAdditionalId}.publication_year" => 'nullable|integer|between:2000,2030',
        ]);

        // Don't reset editing state immediately to prevent form reset
        // $this->reset('editingAdditionalId'); // REMOVED - this was causing the reset issue

        $this->dispatch('close-modal', detail: ['modalId' => 'modalAdditionalOutput']);
        // $this->dispatch('alert', type: 'success', message: 'Data luaran tambahan berhasil disimpan.');

        // flash
        session()->flash('success', 'Data luaran tambahan berhasil disimpan.');

        // $this->editingAdditionalId = null;
    }

    public function closeMandatoryModal(): void
    {
        // Clear editing state when modal is actually closed
        $this->reset('editingMandatoryId');
    }

    public function closeAdditionalModal(): void
    {
        // Clear editing state when modal is actually closed
        $this->reset('editingAdditionalId');
    }

    public function render()
    {
        $allKeywords = Keyword::orderBy('name')->get();

        return view('livewire.research.progress-report.show', [
            'allKeywords' => $allKeywords,
        ]);
    }
}
