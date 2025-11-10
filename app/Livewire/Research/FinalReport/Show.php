<?php

declare(strict_types=1);

namespace App\Livewire\Research\FinalReport;

use App\Livewire\Abstracts\ReportFinalShow;
use App\Livewire\Forms\ResearchFinalReportForm;

class Show extends ReportFinalShow
{
    /**
     * Get the Form class name
     */
    protected function getFormClass(): string
    {
        return ResearchFinalReportForm::class;
    }

    /**
     * Get the route name for redirection
     */
    protected function getRouteName(): string
    {
        return 'research.final-report.index';
    }

    /**
     * Get the view name
     */
    protected function getViewName(): string
    {
        return 'livewire.research.final-report.show';
    }
}
