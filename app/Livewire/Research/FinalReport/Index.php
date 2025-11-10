<?php

declare(strict_types=1);

namespace App\Livewire\Research\FinalReport;

use App\Enums\ProposalStatus;
use App\Livewire\Abstracts\ReportIndex;

class Index extends ReportIndex
{
    protected function getDetailableType(): string
    {
        return 'App\Models\Research';
    }

    protected function getStatusFilter(): array
    {
        return [ProposalStatus::COMPLETED];
    }

    protected function getViewName(): string
    {
        return 'livewire.research.final-report.index';
    }

    protected function getRelations(): array
    {
        return [
            'submitter.identity',
            'researchScheme',
            'focusArea',
            'progressReports' => fn ($q) => $q->finalReports()->latest(),
        ];
    }
}
