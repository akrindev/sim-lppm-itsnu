<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Proposal;

class ResearchFinalReportForm extends ReportForm
{
    // Override report type for final reports
    protected string $reportType = 'final';

    /**
     * Initialize form with Proposal - force final period
     */
    public function initWithProposal(Proposal $proposal): void
    {
        parent::initWithProposal($proposal);
        $this->reportingPeriod = 'final'; // Force final for final reports
    }

    /**
     * Load existing report data with file status
     */
    public function setReport(\App\Models\ProgressReport $report): void
    {
        parent::setReport($report);

        // Force reporting period to 'final' for final reports
        $this->reportingPeriod = 'final';

        // Load file status for final reports
        foreach ($report->mandatoryOutputs as $output) {
            if (empty($output->proposal_output_id)) {
                continue;
            }

            if (isset($this->mandatoryOutputs[$output->proposal_output_id])) {
                $this->mandatoryOutputs[$output->proposal_output_id]['document_file'] =
                    $output->getFirstMedia('journal_article') ? true : false;
            }
        }

        foreach ($report->additionalOutputs as $output) {
            if (empty($output->proposal_output_id)) {
                continue;
            }

            if (isset($this->additionalOutputs[$output->proposal_output_id])) {
                $this->additionalOutputs[$output->proposal_output_id]['document_file'] =
                    $output->getFirstMedia('book_document') ? true : false;
                $this->additionalOutputs[$output->proposal_output_id]['publication_certificate'] =
                    $output->getFirstMedia('publication_certificate') ? true : false;
            }
        }
    }

    /**
     * Override empty output to include file status
     */
    protected function getEmptyMandatoryOutput(): array
    {
        return array_merge(parent::getEmptyMandatoryOutput(), [
            'document_file' => false,
        ]);
    }

    /**
     * Override empty output to include file status
     */
    protected function getEmptyAdditionalOutput(): array
    {
        return array_merge(parent::getEmptyAdditionalOutput(), [
            'document_file' => false,
            'publication_certificate' => false,
        ]);
    }

    /**
     * Override validation rules to enforce final period
     */
    public function rules(): array
    {
        $rules = parent::rules();
        // Override reportingPeriod to only allow 'final'
        $rules['reportingPeriod'] = ['required', 'string', 'in:final'];

        return $rules;
    }

    /**
     * Override save to ensure summaryUpdate has a default value
     */
    public function save(?\App\Models\ProgressReport $existingReport = null): \App\Models\ProgressReport
    {
        // Ensure summaryUpdate has a value before validation
        if (empty($this->summaryUpdate)) {
            $this->summaryUpdate = $this->progressReport?->summary_update ?? $this->proposal->summary ?? '';
        }

        // Force reporting period to 'final' before validation
        $this->reportingPeriod = 'final';

        return parent::save($existingReport);
    }
}
