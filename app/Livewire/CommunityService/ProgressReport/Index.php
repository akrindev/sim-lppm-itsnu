<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProgressReport;

use App\Enums\ProposalStatus;
use App\Livewire\Abstracts\ReportIndex;

class Index extends ReportIndex
{
    protected function getDetailableType(): string
    {
        return 'App\Models\CommunityService';
    }

    protected function getStatusFilter(): array
    {
        return [ProposalStatus::APPROVED, ProposalStatus::COMPLETED];
    }

    protected function getViewName(): string
    {
        return 'livewire.community-service.progress-report.index';
    }

    protected function getRelations(): array
    {
        return [
            'submitter.identity',
            'focusArea',
            'progressReports' => fn ($q) => $q->latest(),
        ];
    }
}
