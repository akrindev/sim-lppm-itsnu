<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

class ResearchProgressReportForm extends ReportForm
{
    // Form is initialized via parent::initWithProposal() method
    // No constructor needed - parent Form class doesn't require one

    /**
     * Get the report type
     */
    protected function getReportType(): string
    {
        return 'progress';
    }

    /**
     * Get file validation rules for progress report (1 file only)
     */
    protected function getFileValidationRules(): array
    {
        return [
            'substanceFile' => 'nullable|file|mimes:pdf,application/pdf|max:10240',
        ];
    }
}
