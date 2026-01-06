<?php

namespace App\Livewire\Research\DailyNote;

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
        // Only show approved or completed proposals for logbook
        return [
            ProposalStatus::APPROVED,
            ProposalStatus::COMPLETED,
            ProposalStatus::UNDER_REVIEW,
            ProposalStatus::REVIEWED,
        ];
    }

    protected function getViewName(): string
    {
        return 'livewire.research.daily-note.index';
    }

    protected function getRelations(): array
    {
        return [
            'submitter.identity',
            'researchScheme',
            'focusArea',
        ];
    }
}
