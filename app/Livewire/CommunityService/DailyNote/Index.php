<?php

namespace App\Livewire\CommunityService\DailyNote;

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
        return [
            ProposalStatus::COMPLETED,
        ];
    }

    protected function getViewName(): string
    {
        return 'livewire.community-service.daily-note.index';
    }

    protected function getRelations(): array
    {
        return [
            'submitter.identity',
            'focusArea',
        ];
    }
}
