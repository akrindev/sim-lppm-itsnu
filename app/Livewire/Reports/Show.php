<?php

declare(strict_types=1);

namespace App\Livewire\Reports;

use App\Livewire\Abstracts\ReportShow;
use App\Livewire\Traits\ReportAuthorization;
use App\Models\AdditionalOutput;
use App\Models\Keyword;
use App\Models\MandatoryOutput;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class Show extends ReportShow
{
    use WithFileUploads;
    use ReportAuthorization;

    // Form data dari ReportForm
    public string $summaryUpdate = '';
    public string $keywordsInput = '';
    public int $reportingYear;
    public string $reportingPeriod = 'semester_1';

    // Arrays for outputs
    public array $mandatoryOutputs = [];
    public array $additionalOutputs = [];

    // Track editing state
    public ?int $editingMandatoryId = null;
    public ?int $editingAdditionalId = null;

    // File uploads
    public $substanceFile;
    public array $tempMandatoryFiles = [];
    public array $tempAdditionalFiles = [];
    public array $tempAdditionalCerts = [];

    // Report configuration
    protected array $config = [];

    public function mount(Proposal $proposal, string $type = 'research-progress'): void
    {
        $this->proposal = $proposal;
        $this->config = $this->getConfig($type);

        $this->checkAccess();
        $this->loadReport();

        $this->reportingYear = (int) date('Y');

        if ($this->progressReport) {
            $this->loadExistingReport();
        } else {
            $this->initializeNewReport();
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

    protected function loadExistingReport(): void
    {
        $this->summaryUpdate = $this->progressReport->summary_update ?? '';
        $this->reportingYear = $this->progressReport->reporting_year;
        $this->reportingPeriod = $this->progressReport->reporting_period;
        $this->keywordsInput = $this->progressReport->keywords()->pluck('name')->join('; ');

        foreach ($this->progressReport->mandatoryOutputs as $output) {
            if (empty($output->proposal_output_id)) continue;

            $this->mandatoryOutputs[$output->proposal_output_id] = $this->mapMandatoryOutput($output);
        }

        foreach ($this->progressReport->additionalOutputs as $output) {
            if (empty($output->proposal_output_id)) continue;

            $this->additionalOutputs[$output->proposal_output_id] = $this->mapAdditionalOutput($output);
        }
    }

    protected function initializeNewReport(): void
    {
        $this->summaryUpdate = $this->proposal->summary ?? '';
        $this->keywordsInput = $this->proposal->keywords()->pluck('name')->join('; ');

        foreach ($this->proposal->outputs->where('category', 'Wajib') as $output) {
            $this->mandatoryOutputs[$output->id] = $this->getEmptyMandatoryOutput();
        }

        foreach ($this->proposal->outputs->where('category', 'Tambahan') as $output) {
            $this->additionalOutputs[$output->id] = $this->getEmptyAdditionalOutput();
        }
    }

    protected function mapMandatoryOutput($output): array
    {
        return [
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
        ];
    }

    protected function mapAdditionalOutput($output): array
    {
        return [
            'id' => $output->id,
            'status' => $output->status,
            'book_title' => $output->book_title,
            'publisher_name' => $output->publisher_name,
            'isbn' => $output->isbn,
            'publication_year' => $output->publication_year,
            'total_pages' => $output->total_pages,
            'publisher_url' => $output->publisher_url,
            'book_url' => $output->book_url,
        ];
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
        ];
    }

    public function save(): void
    {
        if (!$this->canEdit) abort(403);

        $this->validate([
            'summaryUpdate' => 'required|min:100',
            'keywordsInput' => 'nullable|string|max:1000',
            'reportingYear' => 'required|integer|between:2020,2030',
            'reportingPeriod' => 'required|in:semester_1,semester_2,annual,final',
            'substanceFile' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        DB::transaction(function () {
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

            if ($this->substanceFile) {
                $this->progressReport->clearMediaCollection('substance_file');
                $this->progressReport
                    ->addMedia($this->substanceFile->getRealPath())
                    ->usingName($this->substanceFile->getClientOriginalName())
                    ->usingFileName($this->substanceFile->hashName())
                    ->withCustomProperties([
                        'uploaded_by' => auth()->id(),
                        'proposal_id' => $this->proposal->id,
                        'report_type' => $this->config['route'] ?? 'progress',
                    ])
                    ->toMediaCollection('substance_file');
            }

            $this->syncKeywords();
            $this->saveOutputs();
        });

        $this->dispatch('report-saved');
        session()->flash('success', 'Laporan berhasil disimpan.');
    }

    public function submit(): void
    {
        if (!$this->canEdit) abort(403);

        $this->save();

        if ($this->progressReport) {
            $this->progressReport->update([
                'status' => 'submitted',
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            // Ensure config is initialized before redirect
            if (empty($this->config) || !isset($this->config['route'])) {
                $this->config = $this->getConfig('research-progress');
            }

            session()->flash('success', 'Laporan berhasil diajukan.');
            $this->redirect(route($this->config['route']), navigate: true);
        }
    }

    protected function syncKeywords(): void
    {
        if (empty($this->keywordsInput)) return;

        $keywordNames = array_map('trim', explode(';', $this->keywordsInput));
        $keywords = [];

        foreach ($keywordNames as $name) {
            if (empty($name)) continue;
            $keyword = Keyword::firstOrCreate(['name' => $name], ['name' => $name]);
            $keywords[] = $keyword->id;
        }

        $this->progressReport->keywords()->sync($keywords);
        $this->keywordsInput = $this->progressReport->keywords()->pluck('name')->join('; ');
    }

    protected function saveOutputs(): void
    {
        foreach ($this->mandatoryOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (empty($data['status_type']) && empty($data['journal_title']))) continue;

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
                'publication_year' => !empty($data['publication_year']) ? $data['publication_year'] : null,
                'volume' => $data['volume'] ?? null,
                'issue_number' => $data['issue_number'] ?? null,
                'page_start' => !empty($data['page_start']) ? (int) $data['page_start'] : null,
                'page_end' => !empty($data['page_end']) ? (int) $data['page_end'] : null,
                'article_url' => $data['article_url'] ?? null,
                'doi' => $data['doi'] ?? null,
            ];

            $mandatoryOutput = isset($data['id']) && $data['id']
                ? tap(MandatoryOutput::find($data['id']))->update($outputData)
                : MandatoryOutput::create($outputData);

            if (isset($this->tempMandatoryFiles[$proposalOutputId])) {
                $mandatoryOutput->clearMediaCollection('journal_article');
                $mandatoryOutput
                    ->addMedia($this->tempMandatoryFiles[$proposalOutputId]->getRealPath())
                    ->usingName($this->tempMandatoryFiles[$proposalOutputId]->getClientOriginalName())
                    ->usingFileName($this->tempMandatoryFiles[$proposalOutputId]->hashName())
                    ->withCustomProperties(['uploaded_by' => auth()->id()])
                    ->toMediaCollection('journal_article');
            }
        }

        foreach ($this->additionalOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (empty($data['status']) && empty($data['book_title']))) continue;

            $outputData = [
                'progress_report_id' => $this->progressReport->id,
                'proposal_output_id' => $proposalOutputId,
                'status' => $data['status'] ?? null,
                'book_title' => $data['book_title'] ?? null,
                'publisher_name' => $data['publisher_name'] ?? null,
                'isbn' => $data['isbn'] ?? null,
                'publication_year' => !empty($data['publication_year']) ? $data['publication_year'] : null,
                'total_pages' => !empty($data['total_pages']) ? (int) $data['total_pages'] : null,
                'publisher_url' => $data['publisher_url'] ?? null,
                'book_url' => $data['book_url'] ?? null,
            ];

            $additionalOutput = isset($data['id']) && $data['id']
                ? tap(AdditionalOutput::find($data['id']))->update($outputData)
                : AdditionalOutput::create($outputData);

            if (isset($this->tempAdditionalFiles[$proposalOutputId])) {
                $additionalOutput->clearMediaCollection('book_document');
                $additionalOutput
                    ->addMedia($this->tempAdditionalFiles[$proposalOutputId]->getRealPath())
                    ->usingName($this->tempAdditionalFiles[$proposalOutputId]->getClientOriginalName())
                    ->usingFileName($this->tempAdditionalFiles[$proposalOutputId]->hashName())
                    ->withCustomProperties(['uploaded_by' => auth()->id()])
                    ->toMediaCollection('book_document');
            }
        }
    }

    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->editingMandatoryId = $proposalOutputId;
        if (!isset($this->mandatoryOutputs[$proposalOutputId])) {
            $this->mandatoryOutputs[$proposalOutputId] = $this->getEmptyMandatoryOutput();
        }
    }

    public function saveMandatoryOutput(): void
    {
        if (!$this->editingMandatoryId) return;

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

        $this->dispatch('close-modal', detail: ['modalId' => 'modalMandatoryOutput']);
        session()->flash('success', 'Data luaran wajib berhasil disimpan.');
    }

    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->editingAdditionalId = $proposalOutputId;
        if (!isset($this->additionalOutputs[$proposalOutputId])) {
            $this->additionalOutputs[$proposalOutputId] = $this->getEmptyAdditionalOutput();
        }
    }

    public function saveAdditionalOutput(): void
    {
        if (!$this->editingAdditionalId) return;

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

        $this->dispatch('close-modal', detail: ['modalId' => 'modalAdditionalOutput']);
        session()->flash('success', 'Data luaran tambahan berhasil disimpan.');
    }

    public function closeMandatoryModal(): void
    {
        $this->reset(['editingMandatoryId', 'mandatoryOutputs']);
    }

    public function closeAdditionalModal(): void
    {
        $this->reset(['editingAdditionalId', 'additionalOutputs']);
    }

    public function render()
    {
        if (empty($this->config) || !isset($this->config['view'])) {
            // Re-initialize config if missing (e.g., after component hydration)
            $this->config = $this->getConfig('research-progress');
        }

        $allKeywords = Keyword::orderBy('name')->get();

        return view($this->config['view'], [
            'allKeywords' => $allKeywords,
        ]);
    }
}
