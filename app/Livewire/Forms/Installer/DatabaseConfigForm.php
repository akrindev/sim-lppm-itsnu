<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use App\Services\Installer\DatabaseTester;
use Livewire\Form;

class DatabaseConfigForm extends Form
{
    public string $host = '127.0.0.1';

    public string $port = '3306';

    public string $database = 'lppm_itsnu';

    public string $username = 'root';

    public string $password = '';

    public bool $createDatabase = false;

    protected function rules(): array
    {
        return [
            'host' => 'required|string|max:255',
            'port' => 'required|numeric|between:1,65535',
            'database' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'createDatabase' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'host.required' => 'Database host is required',
            'port.required' => 'Database port is required',
            'port.numeric' => 'Port must be a number',
            'database.required' => 'Database name is required',
            'database.regex' => 'Database name can only contain letters, numbers, and underscores',
            'username.required' => 'Database username is required',
        ];
    }

    public function toArray(): array
    {
        return [
            'driver' => 'mariadb',
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function testConnection(): array
    {
        $this->validate();

        $tester = new DatabaseTester;

        // First test credentials only
        $credentialsTest = $tester->testCredentialsOnly($this->toArray());

        if (! $credentialsTest['success']) {
            return $credentialsTest;
        }

        // Check if database exists
        $databaseExists = $tester->databaseExists($this->toArray());

        if (! $databaseExists && ! $this->createDatabase) {
            return [
                'success' => false,
                'message' => "Database '{$this->database}' does not exist. Check 'Create database' to create it automatically.",
                'database_exists' => false,
            ];
        }

        if (! $databaseExists && $this->createDatabase) {
            $createResult = $tester->createDatabase($this->toArray());

            if (! $createResult['success']) {
                return $createResult;
            }
        }

        // Now test full connection with database
        return $tester->testConnection($this->toArray());
    }

    public function getEnvConfig(): array
    {
        return [
            'DB_CONNECTION' => 'mariadb',
            'DB_HOST' => $this->host,
            'DB_PORT' => $this->port,
            'DB_DATABASE' => $this->database,
            'DB_USERNAME' => $this->username,
            'DB_PASSWORD' => $this->password,
            'DB_CHARSET' => 'utf8mb4',
            'DB_COLLATION' => 'utf8mb4_unicode_ci',
            'DB_PREFIX' => '',
            'DB_STRICT' => 'true',
            'DB_ENGINE' => '',
        ];
    }
}
