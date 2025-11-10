<?php

declare(strict_types=1);

namespace App\Livewire\Research\ProgressReport;

use App\Livewire\Abstracts\ReportOutputShow;
use App\Livewire\Forms\ResearchProgressReportForm;

class Show extends ReportOutputShow
{
    /**
     * Get the Form class name
     */
    protected function getFormClass(): string
    {
        return ResearchProgressReportForm::class;
    }

    /**
     * Get the route name for redirection
     */
    protected function getRouteName(): string
    {
        return 'research.progress-report.index';
    }

    /**
     * Get the view name
     */
    protected function getViewName(): string
    {
        return 'livewire.research.progress-report.show';
    }
}
