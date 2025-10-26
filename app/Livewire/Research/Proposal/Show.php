<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    public Proposal $proposal;

    public function render(): View
    {
        return view('livewire.research.proposal.show', [
            'proposal' => $this->proposal->load([
                'submitter',
                'focusArea',
                'researchScheme',
                'theme',
                'topic',
                'nationalPriority',
            ]),
        ]);
    }
}
