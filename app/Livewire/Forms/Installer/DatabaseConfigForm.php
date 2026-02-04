<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use App\Services\Installer\DatabaseTester;
use Livewire\Attributes\Locked;
use Livewire\Form;

class DatabaseConfigForm extends Form
{
    public string $host = '127.0.0.1';

    public string $port = '3306';

    public string $database = 'lppm_itsnu';

    public string $username = 'root';

    /**
     * User-facing password input. Keep separate so empty input does not
     * overwrite stored password when navigating steps.
     */
    public string $dbPasswordInput = '';

    /**
     * Stored password used for connection and .env.
     * #[Locked] ensures this is always persisted across requests.
     */
    #[Locked]
    public string $dbPassword = '';

    public bool $createDatabase = false;

    protected function rules(): array
    {
        return [
            'host' => 'required|string|max:255',
            'port' => 'required|numeric|between:1,65535',
            'database' => 'required|string|max:64|regex:/^[a-zA-Z0-9_]+$/',
            'username' => 'required|string|max:255',
            'dbPasswordInput' => 'nullable|string|max:255',
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
        $this->syncPasswordFromInput();

        return [
            'driver' => 'mariadb',
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->dbPassword,
        ];
    }

    public function testConnection(): array
    {
        $this->syncPasswordFromInput();
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

    public function normalizeInputs(): void
    {
        $this->host = trim($this->host);
        $this->port = trim((string) $this->port);
        $this->database = trim($this->database);
        $this->username = trim($this->username);
        $this->syncPasswordFromInput();
        $this->dbPassword = trim($this->dbPassword);
    }

    public function getEnvConfig(): array
    {
        $this->normalizeInputs();

        return [
            'DB_CONNECTION' => 'mariadb',
            'DB_HOST' => $this->host,
            'DB_PORT' => $this->port,
            'DB_DATABASE' => $this->database,
            'DB_USERNAME' => $this->username,
            'DB_PASSWORD' => $this->dbPassword,
            'DB_CHARSET' => 'utf8mb4',
            'DB_COLLATION' => 'utf8mb4_unicode_ci',
            'DB_PREFIX' => '',
            'DB_STRICT' => 'true',
            'DB_ENGINE' => '',
        ];
    }

    /**
     * Only overwrite stored password when user provides a new input.
     */
    public function syncPasswordFromInput(): void
    {
        if ($this->dbPasswordInput === '') {
            return;
        }

        $this->dbPassword = trim($this->dbPasswordInput);
    }

    /**
     * Auto-sync password when input is updated via Livewire.
     */
    public function updatedDbPasswordInput(): void
    {
        $this->syncPasswordFromInput();
    }
}
