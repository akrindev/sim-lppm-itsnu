<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Proposal;

class CommunityServiceFinalReportForm extends ReportForm
{
    public function __construct()
    {
        // Form properties will be initialized via initWithProposal()
    }

    /**
     * Initialize form with Proposal - force final period
     */
    public function initWithProposal(Proposal $proposal): void
    {
        parent::initWithProposal($proposal);
        $this->reportingPeriod = 'final'; // Force final for final reports
    }

    /**
     * Get the report type
     */
    protected function getReportType(): string
    {
        return 'final';
    }

    /**
     * Get file validation rules for final report (3 files)
     */
    protected function getFileValidationRules(): array
    {
        return [
            'substanceFile' => 'nullable|file|mimes:pdf,application/pdf|max:10240',
            'realizationFile' => 'nullable|file|mimes:pdf,docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:10240',
            'presentationFile' => 'nullable|file|mimes:pdf,pptx,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/zip|max:51200',
        ];
    }

    /**
     * Load existing report data with file status
     */
    public function setReport(\App\Models\ProgressReport $report): void
    {
        parent::setReport($report);

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
        return array_merge(parent::rules(), [
            'reportingPeriod' => ['required', 'string', 'in:final'],
        ]);
    }
}
