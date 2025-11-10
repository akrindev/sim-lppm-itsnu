<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use App\Models\ProgressReport;
use App\Models\Proposal;
use App\Models\AdditionalOutput;
use App\Models\MandatoryOutput;

trait ManagesOutputs
{
    // Arrays for outputs (indexed by proposal_output_id)
    public array $mandatoryOutputs = [];
    public array $additionalOutputs = [];

    // Track which output is being edited
    public ?int $editingMandatoryId = null;
    public ?int $editingAdditionalId = null;

    /**
     * Load existing report data into output arrays
     */
    public function loadExistingReport(ProgressReport $report): void
    {
        // Load existing mandatory outputs
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

        // Load existing additional outputs
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

    /**
     * Initialize new report with empty output arrays
     */
    public function initializeNewReport(Proposal $proposal): void
    {
        foreach ($proposal->outputs->where('category', 'Wajib') as $output) {
            $this->mandatoryOutputs[$output->id] = $this->getEmptyMandatoryOutput();
        }

        foreach ($proposal->outputs->where('category', 'Tambahan') as $output) {
            $this->additionalOutputs[$output->id] = $this->getEmptyAdditionalOutput();
        }
    }

    /**
     * Get empty mandatory output structure
     */
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

    /**
     * Get empty additional output structure
     */
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

    /**
     * Open modal for editing mandatory output
     */
    public function editMandatoryOutput(int $proposalOutputId): void
    {
        $this->editingMandatoryId = $proposalOutputId;

        if (! isset($this->mandatoryOutputs[$proposalOutputId])) {
            $this->mandatoryOutputs[$proposalOutputId] = $this->getEmptyMandatoryOutput();
        }
    }

    /**
     * Open modal for editing additional output
     */
    public function editAdditionalOutput(int $proposalOutputId): void
    {
        $this->editingAdditionalId = $proposalOutputId;

        if (! isset($this->additionalOutputs[$proposalOutputId])) {
            $this->additionalOutputs[$proposalOutputId] = $this->getEmptyAdditionalOutput();
        }
    }

    /**
     * Close mandatory output modal
     */
    public function closeMandatoryModal(): void
    {
        $this->reset(['editingMandatoryId']);
    }

    /**
     * Close additional output modal
     */
    public function closeAdditionalModal(): void
    {
        $this->reset(['editingAdditionalId']);
    }

    /**
     * Reset output arrays
     */
    public function resetOutputs(): void
    {
        $this->reset([
            'mandatoryOutputs',
            'additionalOutputs',
            'editingMandatoryId',
            'editingAdditionalId',
        ]);
    }
}
