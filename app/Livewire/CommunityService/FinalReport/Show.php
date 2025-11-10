<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\FinalReport;

use App\Livewire\Abstracts\ReportFinalShow;
use App\Livewire\Forms\CommunityServiceFinalReportForm;

class Show extends ReportFinalShow
{
    /**
     * Get the Form class name
     */
    protected function getFormClass(): string
    {
        return CommunityServiceFinalReportForm::class;
    }

    /**
     * Get the route name for redirection
     */
    protected function getRouteName(): string
    {
        return 'community-service.final-report.index';
    }

    /**
     * Get the view name
     */
    protected function getViewName(): string
    {
        return 'livewire.community-service.final-report.show';
    }
}
