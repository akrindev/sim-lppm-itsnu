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

    public bool $canEdit = false;

    // Form fields
    public string $summaryUpdate = '';

    public string $keywordsInput = '';

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

    // Progress Report document files
    public $substanceFile;

    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;

        // Check if user can view this proposal
        if (! $this->canViewProgressReport()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat laporan kemajuan proposal ini.');
        }

        // Check if user can edit (only submitter)
        $this->canEdit = $this->isSubmitter();

        // Load latest progress report or initialize new
        $this->progressReport = $proposal->progressReports()->latest()->first();

        if ($this->progressReport) {
            $this->loadExistingReport();
        } else {
            $this->initializeNewReport();
        }
    }

    protected function canViewProgressReport(): bool
    {
        $user = Auth::user();

        // Proposal owner can view
        if ($this->proposal->submitter_id === $user->id) {
            return true;
        }

        // Accepted team members can view
        $isTeamMember = $this->proposal->teamMembers()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        return $isTeamMember;
    }

    protected function isSubmitter(): bool
    {
        return $this->proposal->submitter_id === Auth::id();
    }

    protected function loadExistingReport(): void
    {
        $this->summaryUpdate = $this->progressReport->summary_update ?? '';
        $this->reportingYear = $this->progressReport->reporting_year;
        $this->reportingPeriod = $this->progressReport->reporting_period;
        $this->keywordsInput = $this->progressReport->keywords()->pluck('name')->join('; ');

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
        $this->keywordsInput = $this->proposal->keywords()->pluck('name')->join('; ');

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
        // Only submitter can save
        if (! $this->canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit laporan ini.');
        }

        $this->validate([
            'summaryUpdate' => 'required|min:100',
            'keywordsInput' => 'nullable|string|max:1000',
            'reportingYear' => 'required|numeric|between:2020,2030',
            'reportingPeriod' => 'required|in:semester_1,semester_2,annual',
            'substanceFile' => 'nullable|file|mimes:pdf|max:10240',
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

            // Upload substance file using Media Library
            if ($this->substanceFile) {
                $this->progressReport->clearMediaCollection('substance_file');
                $this->progressReport
                    ->addMedia($this->substanceFile->getRealPath())
                    ->usingName($this->substanceFile->getClientOriginalName())
                    ->usingFileName($this->substanceFile->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => auth()->id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'progress',
                    ])
                    ->toMediaCollection('substance_file');
            }

            // Handle keywords: parse from semicolon-separated input
            $keywordNames = $this->parseKeywordsInput($this->keywordsInput);
            $keywordIds = $this->processKeywords($keywordNames);

            // Sync keywords
            $this->progressReport->keywords()->sync($keywordIds);

            // Update input with cleaned keywords from database
            $this->keywordsInput = $this->progressReport->keywords()->pluck('name')->join('; ');

            // Save mandatory outputs
            $this->saveMandatoryOutputs();

            // Save additional outputs
            $this->saveAdditionalOutputs();
        });

        session()->flash('success', 'Laporan kemajuan berhasil disimpan sebagai draft.');
        $this->dispatch('alert', type: 'success', message: 'Laporan kemajuan berhasil disimpan sebagai draft.');
    }

    /**
     * Parse keywords from semicolon-separated string.
     *
     * @param  string  $input  Semicolon-separated keywords
     * @return array Array of keyword names (trimmed, non-empty, unique)
     */
    protected function parseKeywordsInput(string $input): array
    {
        if (empty(trim($input))) {
            return [];
        }

        // Split by semicolon
        $keywords = explode(';', $input);

        // Trim whitespace and filter empty
        $keywords = array_map('trim', $keywords);
        $keywords = array_filter($keywords, fn ($k) => ! empty($k));

        // Remove duplicates (case-insensitive)
        $uniqueKeywords = [];
        $seen = [];

        foreach ($keywords as $keyword) {
            $lowerKeyword = strtolower($keyword);
            if (! in_array($lowerKeyword, $seen)) {
                $uniqueKeywords[] = $keyword;
                $seen[] = $lowerKeyword;
            }
        }

        return $uniqueKeywords;
    }

    /**
     * Process keywords array, creating new keywords if they don't exist.
     *
     * @param  array  $keywordNames  Array of keyword names (strings)
     * @return array Array of keyword IDs
     */
    protected function processKeywords(array $keywordNames): array
    {
        $keywordIds = [];

        foreach ($keywordNames as $name) {
            if (! empty(trim($name))) {
                // Create new keyword if it doesn't exist
                $keyword = Keyword::firstOrCreate(
                    ['name' => trim($name)],
                    ['name' => trim($name)]
                );
                $keywordIds[] = $keyword->id;
            }
        }

        return $keywordIds;
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
            ];

            if (isset($data['id']) && $data['id']) {
                $mandatoryOutput = MandatoryOutput::find($data['id']);
                $mandatoryOutput->update($outputData);
            } else {
                $mandatoryOutput = MandatoryOutput::create($outputData);
            }

            // Handle file upload using Media Library
            if (isset($this->tempMandatoryFiles[$proposalOutputId])) {
                $mandatoryOutput->clearMediaCollection('journal_article');
                $mandatoryOutput
                    ->addMedia($this->tempMandatoryFiles[$proposalOutputId]->getRealPath())
                    ->usingName($this->tempMandatoryFiles[$proposalOutputId]->getClientOriginalName())
                    ->usingFileName($this->tempMandatoryFiles[$proposalOutputId]->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => auth()->id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'progress',
                    ])
                    ->toMediaCollection('journal_article');
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
            ];

            if (isset($data['id']) && $data['id']) {
                $additionalOutput = AdditionalOutput::find($data['id']);
                $additionalOutput->update($outputData);
            } else {
                $additionalOutput = AdditionalOutput::create($outputData);
            }

            // Handle book document upload using Media Library
            if (isset($this->tempAdditionalFiles[$proposalOutputId])) {
                $additionalOutput->clearMediaCollection('book_document');
                $additionalOutput
                    ->addMedia($this->tempAdditionalFiles[$proposalOutputId]->getRealPath())
                    ->usingName($this->tempAdditionalFiles[$proposalOutputId]->getClientOriginalName())
                    ->usingFileName($this->tempAdditionalFiles[$proposalOutputId]->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => auth()->id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'progress',
                    ])
                    ->toMediaCollection('book_document');
            }

            // Handle publication certificate upload using Media Library
            if (isset($this->tempAdditionalCerts[$proposalOutputId])) {
                $additionalOutput->clearMediaCollection('publication_certificate');
                $additionalOutput
                    ->addMedia($this->tempAdditionalCerts[$proposalOutputId]->getRealPath())
                    ->usingName($this->tempAdditionalCerts[$proposalOutputId]->getClientOriginalName())
                    ->usingFileName($this->tempAdditionalCerts[$proposalOutputId]->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => auth()->id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => 'progress',
                    ])
                    ->toMediaCollection('publication_certificate');
            }
        }
    }

    public function submit()
    {
        // Only submitter can submit
        if (! $this->canEdit) {
            abort(403, 'Anda tidak memiliki akses untuk mengajukan laporan ini.');
        }

        $this->validate([
            'summaryUpdate' => 'required|min:100',
            'keywordsInput' => 'nullable|string|max:1000',
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
    }

    public function saveMandatoryOutput(): void
    {
        if (! $this->editingMandatoryId) {
            return;
        }

        // Validation for single output
        $this->validate([
            "mandatoryOutputs.{$this->editingMandatoryId}.status_type" => 'required|in:published,accepted,under_review,rejected',
            "mandatoryOutputs.{$this->editingMandatoryId}.author_status" => 'required|in:first_author,co_author,corresponding_author',
            "mandatoryOutputs.{$this->editingMandatoryId}.journal_title" => 'required|string|max:255',
            "mandatoryOutputs.{$this->editingMandatoryId}.article_title" => 'required|string|max:255',
            "mandatoryOutputs.{$this->editingMandatoryId}.publication_year" => 'required|integer|between:2000,2030',
            "mandatoryOutputs.{$this->editingMandatoryId}.issn" => 'nullable|string|max:20',
            "mandatoryOutputs.{$this->editingMandatoryId}.eissn" => 'nullable|string|max:20',
            "mandatoryOutputs.{$this->editingMandatoryId}.journal_url" => 'nullable|url',
            "mandatoryOutputs.{$this->editingMandatoryId}.article_url" => 'nullable|url',
            "mandatoryOutputs.{$this->editingMandatoryId}.doi" => 'nullable|string|max:255',
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
    }

    public function saveAdditionalOutput(): void
    {
        if (! $this->editingAdditionalId) {
            return;
        }

        // Validation for single output
        $this->validate([
            "additionalOutputs.{$this->editingAdditionalId}.status" => 'required|in:review,editing,published',
            "additionalOutputs.{$this->editingAdditionalId}.book_title" => 'required|string|max:255',
            "additionalOutputs.{$this->editingAdditionalId}.publisher_name" => 'required|string|max:255',
            "additionalOutputs.{$this->editingAdditionalId}.isbn" => 'nullable|string|max:20',
            "additionalOutputs.{$this->editingAdditionalId}.publication_year" => 'nullable|integer|between:2000,2030',
            "additionalOutputs.{$this->editingAdditionalId}.total_pages" => 'nullable|integer|min:1',
            "additionalOutputs.{$this->editingAdditionalId}.publisher_url" => 'nullable|url',
            "additionalOutputs.{$this->editingAdditionalId}.book_url" => 'nullable|url',
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
        // Clear editing state when modal is closed
        $this->reset(['editingMandatoryId', 'mandatoryOutputs']);
    }

    public function closeAdditionalModal(): void
    {
        // Clear editing state when modal is closed
        $this->reset(['editingAdditionalId', 'additionalOutputs']);
    }

    public function render()
    {
        $allKeywords = Keyword::orderBy('name')->get();

        return view('livewire.research.progress-report.show', [
            'allKeywords' => $allKeywords,
        ]);
    }
}
