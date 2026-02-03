<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class AdminAccountForm extends Form
{
    public string $adminName = 'Administrator';

    public string $adminEmail = '';

    public string $adminPassword = '';

    public string $adminPasswordConfirmation = '';

    protected function rules(): array
    {
        return [
            'adminName' => 'required|string|max:255',
            'adminEmail' => 'required|email|max:255',
            'adminPassword' => ['required', Password::min(8)->mixedCase()->numbers()],
            'adminPasswordConfirmation' => 'required|same:adminPassword',
        ];
    }

    protected function messages(): array
    {
        return [
            'adminName.required' => 'Admin name is required',
            'adminEmail.required' => 'Admin email is required',
            'adminEmail.email' => 'Please enter a valid email address',
            'adminPassword.required' => 'Password is required',
            'adminPasswordConfirmation.required' => 'Please confirm the password',
            'adminPasswordConfirmation.same' => 'Passwords do not match',
        ];
    }

    public function getAdminData(): array
    {
        $this->normalizeInputs();

        return [
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'password' => $this->adminPassword,
        ];
    }

    public function normalizeInputs(): void
    {
        $this->adminName = trim($this->adminName);
        $this->adminEmail = trim($this->adminEmail);
        $this->adminPassword = trim($this->adminPassword);
    }
}
