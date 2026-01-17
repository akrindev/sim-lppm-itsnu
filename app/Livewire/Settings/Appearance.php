<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Appearance extends Component
{
    public function mount()
    {
        if (! auth()->user()->hasRole('admin lppm')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
