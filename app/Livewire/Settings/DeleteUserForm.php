<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use App\Livewire\Concerns\HasToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use HasToast;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $message = 'Akun Anda berhasil dihapus.';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        $this->redirect('/', navigate: true);
    }
}
