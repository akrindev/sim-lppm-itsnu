<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\HasToast;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    use HasToast;

    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        $message = __('A reset link will be sent if the account exists.');
        session()->flash('status', $message);
        $this->toastInfo($message);
    }
}
