<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\AdditionalOutput;
use App\Models\Keyword;
use App\Models\MandatoryOutput;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Form;

abstract class ReportForm extends Form
{
    public ?ProgressReport $progressReport = null;
    public Proposal $proposal;

    public string $summaryUpdate = '';
    public string $keywordsInput = '';
    public int $reportingYear;
    public string $reportingPeriod = 'semester_1';

    public array $mandatoryOutputs = [];
    public array $additionalOutputs = [];

    public ?int $editingMandatoryId = null;
    public ?int $editingAdditionalId = null;

    public array $tempMandatoryFiles = [];
    public array $tempAdditionalFiles = [];
    public array $tempAdditionalCerts = [];

    /**
     * Initialize form with Proposal
     */
    public function initWithProposal(Proposal $proposal): void
    {
        $this->proposal = $proposal;
        $this->reportingYear = (int) date('Y');
        $this->reportingPeriod = 'semester_1';
    }

    public function setReport(ProgressReport $report): void
    {
        $this->progressReport = $report;
        $this->summaryUpdate = $report->summary_update ?? '';
        $this->keywordsInput = $report->keywords->pluck('name')->implode('; ');
        $this->reportingYear = (int) $report->reporting_year;
        $this->reportingPeriod = $report->reporting_period;

        // Load mandatory outputs
        foreach ($report->mandatoryOutputs as $output) {
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
            ];
        }

        // Load additional outputs
        foreach ($report->additionalOutputs as $output) {
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
            ];
        }
    }

    public function initializeNewReport(): void
    {
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

    public function rules(): array
    {
        return [
            'summaryUpdate' => ['nullable', 'string', 'min:10'],
            'reportingYear' => ['required', 'integer', 'min:2020', 'max:2099'],
            'reportingPeriod' => ['required', 'string', Rule::in(['semester_1', 'semester_2', 'annual', 'final'])],
        ];
    }

    public function validateReportData(): void
    {
        $this->validate($this->rules());
    }

    public function validateMandatoryOutput(int $outputId): void
    {
        $this->validate([
            "mandatoryOutputs.{$outputId}.status_type" => 'required|in:published,accepted,under_review,rejected',
            "mandatoryOutputs.{$outputId}.author_status" => 'required|in:first_author,co_author,corresponding_author',
            "mandatoryOutputs.{$outputId}.journal_title" => 'required|string|max:255',
            "mandatoryOutputs.{$outputId}.article_title" => 'required|string|max:255',
            "mandatoryOutputs.{$outputId}.publication_year" => 'required|integer|between:2000,2030',
            "mandatoryOutputs.{$outputId}.issn" => 'nullable|string|max:20',
            "mandatoryOutputs.{$outputId}.eissn" => 'nullable|string|max:20',
            "mandatoryOutputs.{$outputId}.journal_url" => 'nullable|url',
            "mandatoryOutputs.{$outputId}.article_url" => 'nullable|url',
            "mandatoryOutputs.{$outputId}.doi" => 'nullable|string|max:255',
        ]);
    }

    public function validateAdditionalOutput(int $outputId): void
    {
        $this->validate([
            "additionalOutputs.{$outputId}.status" => 'required|in:review,editing,published',
            "additionalOutputs.{$outputId}.book_title" => 'required|string|max:255',
            "additionalOutputs.{$outputId}.publisher_name" => 'required|string|max:255',
            "additionalOutputs.{$outputId}.isbn" => 'nullable|string|max:20',
            "additionalOutputs.{$outputId}.publication_year" => 'nullable|integer|between:2000,2030',
            "additionalOutputs.{$outputId}.total_pages" => 'nullable|integer|min:1',
            "additionalOutputs.{$outputId}.publisher_url" => 'nullable|url',
            "additionalOutputs.{$outputId}.book_url" => 'nullable|url',
        ]);
    }

    public function save(ProgressReport $existingReport = null): ProgressReport
    {
        $this->validateReportData();

        DB::beginTransaction();

        try {
            $reportData = [
                'summary_update' => $this->summaryUpdate ?: ($this->progressReport?->summary_update ?? $this->proposal->summary),
                'reporting_year' => $this->reportingYear,
                'reporting_period' => $this->reportingPeriod,
            ];

            if ($existingReport) {
                $existingReport->update($reportData);
                $report = $existingReport;
            } else {
                $report = ProgressReport::create(array_merge($reportData, [
                    'proposal_id' => $this->proposal->id,
                    'status' => 'draft',
                ]));
            }

            $this->progressReport = $report;
            $this->saveKeywords();
            $this->saveMandatoryOutputs();
            $this->saveAdditionalOutputs();

            DB::commit();

            return $report;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function submit(ProgressReport $existingReport = null): ProgressReport
    {
        $report = $this->save($existingReport);

        if ($report) {
            $report->update([
                'status' => 'submitted',
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);
        }

        return $report;
    }

    protected function saveKeywords(): void
    {
        if (empty($this->keywordsInput)) {
            return;
        }

        $keywordNames = array_map('trim', explode(';', $this->keywordsInput));
        $keywords = [];

        foreach ($keywordNames as $name) {
            if (empty($name)) {
                continue;
            }

            $keyword = Keyword::firstOrCreate(['name' => $name]);
            $keywords[] = $keyword->id;
        }

        $this->progressReport->keywords()->sync($keywords);
    }

    protected function saveMandatoryOutputs(): void
    {
        foreach ($this->mandatoryOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (! is_string($proposalOutputId) && ! is_numeric($proposalOutputId))) {
                continue;
            }

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
        }
    }

    protected function saveAdditionalOutputs(): void
    {
        foreach ($this->additionalOutputs as $proposalOutputId => $data) {
            if (empty($proposalOutputId) || (! is_string($proposalOutputId) && ! is_numeric($proposalOutputId))) {
                continue;
            }

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
        }
    }

    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->editingMandatoryId = $proposalOutputId;

        if (! isset($this->mandatoryOutputs[$proposalOutputId])) {
            $this->mandatoryOutputs[$proposalOutputId] = $this->getEmptyMandatoryOutput();
        }
    }

    public function saveMandatoryOutput(int $proposalOutputId): void
    {
        $this->validateMandatoryOutput($proposalOutputId);
    }

    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->editingAdditionalId = $proposalOutputId;

        if (! isset($this->additionalOutputs[$proposalOutputId])) {
            $this->additionalOutputs[$proposalOutputId] = $this->getEmptyAdditionalOutput();
        }
    }

    public function saveAdditionalOutput(int $proposalOutputId): void
    {
        $this->validateAdditionalOutput($proposalOutputId);
    }

    public function closeMandatoryModal(): void
    {
        $this->reset(['editingMandatoryId']);
    }

    public function closeAdditionalModal(): void
    {
        $this->reset(['editingAdditionalId']);
    }

    public function resetForm(): void
    {
        $this->reset([
            'summaryUpdate',
            'keywordsInput',
            'reportingYear',
            'reportingPeriod',
            'mandatoryOutputs',
            'additionalOutputs',
            'editingMandatoryId',
            'editingAdditionalId',
            'tempMandatoryFiles',
            'tempAdditionalFiles',
            'tempAdditionalCerts',
        ]);

        $this->progressReport = null;
    }
}
