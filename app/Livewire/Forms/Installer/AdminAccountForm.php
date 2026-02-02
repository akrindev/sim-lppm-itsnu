<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class AdminAccountForm extends Form
{
    public string $name = 'Administrator';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'passwordConfirmation' => 'required|same:password',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Admin name is required',
            'email.required' => 'Admin email is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'passwordConfirmation.required' => 'Please confirm the password',
            'passwordConfirmation.same' => 'Passwords do not match',
        ];
    }

    public function getAdminData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
