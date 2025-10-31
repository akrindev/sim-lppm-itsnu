<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'User Details', 'pageTitle' => '', 'pageSubtitle' => 'User profile and metadata overview'])]
class Show extends Component
{
    public string $userId;

    /**
     * Boot the component with the selected user.
     */
    public function mount(User $user): void
    {
        $this->userId = $user->getKey();
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        $user = $this->user;

        return view('livewire.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Resolve the currently viewed user.
     */
    public function getUserProperty(): User
    {
        return User::query()
            ->with(['roles', 'identity'])
            ->findOrFail($this->userId);
    }
}
