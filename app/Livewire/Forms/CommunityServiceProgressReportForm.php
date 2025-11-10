<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Proposal;

class CommunityServiceProgressReportForm extends ReportForm
{
    public function __construct(Proposal $proposal)
    {
        parent::__construct($proposal);
    }

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
