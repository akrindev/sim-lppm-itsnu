<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProgressReport;

use App\Livewire\Abstracts\ReportOutputShow;
use App\Livewire\Forms\CommunityServiceProgressReportForm;

class Show extends ReportOutputShow
{
    /**
     * Get the Form class name
     */
    protected function getFormClass(): string
    {
        return CommunityServiceProgressReportForm::class;
    }

    /**
     * Get the route name for redirection
     */
    protected function getRouteName(): string
    {
        return 'community-service.progress-report.index';
    }

    /**
     * Get the view name
     */
    protected function getViewName(): string
    {
        return 'livewire.community-service.progress-report.show';
    }
}
