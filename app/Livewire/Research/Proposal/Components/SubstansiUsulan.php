<?php

namespace App\Livewire\Research\Proposal\Components;

use App\Models\Proposal;
use Livewire\Component;

class SubstansiUsulan extends Component
{
    public Proposal $proposal;

    public function render()
    {
        return view('livewire.research.proposal.components.substansi-usulan');
    }
}
