<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Models\Proposal;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    public Proposal $proposal;

    public function render(): View
    {
        return view('livewire.community-service.proposal.show', [
            'proposal' => $this->proposal->load([
                'submitter',
                'focusArea',
                'theme',
                'topic',
                'nationalPriority',
            ]),
        ]);
    }
}
