<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Proposal;

class ResearchProgressReportForm extends ReportForm
{
    public function __construct(Proposal $proposal)
    {
        parent::__construct($proposal);
    }
}
