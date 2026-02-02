<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Installer\DatabaseTester;
use App\Services\Installer\InstallationService;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class InstallCommand extends Command
{
    protected $signature = 'app:install
                            {--force : Force installation even if already installed}';

    protected $description = 'Install LPPM-ITSNU application';

    public function handle(InstallationService $installationService): int
    {
        $this->displayHeader();

        // Check if already installed
        if ($installationService->isInstalled() && ! $this->option('force')) {
            error('Application is already installed!');
            warning('Use --force to reinstall (this will delete all data).');

            return 1;
        }

        // Step 1: Environment Check
        if (! $this->runEnvironmentCheck($installationService)) {
            return 1;
        }

        // Step 2: Database Configuration
        $dbConfig = $this->configureDatabase();
        if ($dbConfig === null) {
            return 1;
        }

        // Step 3: Institution Setup
        $institutionConfig = $this->configureInstitution();

        // Step 4: Admin Account
        $adminConfig = $this->configureAdmin();

        // Confirm installation
        if (! confirm('Ready to install. Continue?', default: true)) {
            warning('Installation cancelled.');

            return 1;
        }

        // Run Installation
        $success = $this->runInstallation($installationService, $dbConfig, $institutionConfig, $adminConfig);

        if ($success) {
            $this->displaySuccess();

            return 0;
        }

        error('Installation failed!');

        return 1;
    }

    private function displayHeader(): void
    {
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════╗');
        $this->line('║         LPPM-ITSNU Auto Installer                      ║');
        $this->line('║         Research Management System                     ║');
        $this->line('╚════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    private function runEnvironmentCheck(InstallationService $service): bool
    {
        info('Step 1: Checking environment...');

        $checks = $service->checkEnvironment();
        $allPassed = true;

        foreach ($checks as $check) {
            $status = $check['status'] ? '✓' : '✗';
            $color = $check['status'] ? 'info' : 'error';
            $this->line("  {$status} {$check['label']}: {$check['current']}");

            if (! $check['status']) {
                $allPassed = false;
            }
        }

        $this->newLine();

        if (! $allPassed) {
            error('Environment checks failed. Please fix the issues above.');

            return false;
        }

        info('All environment checks passed! ✓');
        $this->newLine();

        return true;
    }

    private function configureDatabase(): ?array
    {
        info('Step 2: Database Configuration');

        $defaultConfig = (new DatabaseTester)->getDefaultValues();

        $responses = form()
            ->text(
                label: 'Database Host',
                default: $defaultConfig['host'],
                required: true,
                validate: ['required', 'string'],
                name: 'host'
            )
            ->text(
                label: 'Port',
                default: $defaultConfig['port'],
                required: true,
                validate: ['required', 'numeric'],
                name: 'port'
            )
            ->text(
                label: 'Database Name',
                default: $defaultConfig['database'],
                required: true,
                validate: ['required', 'string'],
                name: 'database'
            )
            ->text(
                label: 'Username',
                default: $defaultConfig['username'],
                required: true,
                validate: ['required', 'string'],
                name: 'username'
            )
            ->password(
                label: 'Password',
                name: 'password'
            )
            ->confirm(
                label: 'Create database if not exists?',
                default: false,
                name: 'create_database'
            )
            ->submit();

        $config = [
            'driver' => 'mariadb',
            'host' => $responses['host'],
            'port' => $responses['port'],
            'database' => $responses['database'],
            'username' => $responses['username'],
            'password' => $responses['password'] ?? '',
        ];

        // Test connection
        $tester = new DatabaseTester;

        $this->newLine();
        $this->write('Testing database connection... ');

        $result = spin(
            fn () => $tester->testCredentialsOnly($config),
            'Testing connection...'
        );

        if (! $result['success']) {
            error("Connection failed: {$result['message']}");

            if (confirm('Try again?', default: true)) {
                return $this->configureDatabase();
            }

            return null;
        }

        info('Connection successful! ✓');

        // Check/create database
        $dbExists = $tester->databaseExists($config);

        if (! $dbExists) {
            if ($responses['create_database']) {
                $this->write('Creating database... ');
                $createResult = $tester->createDatabase($config);

                if (! $createResult['success']) {
                    error("Failed to create database: {$createResult['message']}");

                    return null;
                }

                info('Database created! ✓');
            } else {
                error("Database '{$config['database']}' does not exist.");

                if (confirm('Create it now?', default: true)) {
                    $createResult = $tester->createDatabase($config);

                    if (! $createResult['success']) {
                        error("Failed to create database: {$createResult['message']}");

                        return null;
                    }

                    info('Database created! ✓');
                } else {
                    return null;
                }
            }
        }

        $this->newLine();

        return $config;
    }

    private function configureInstitution(): array
    {
        info('Step 3: Institution Setup');

        $responses = form()
            ->text(
                label: 'Institution Name',
                default: 'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan',
                required: true,
                name: 'name'
            )
            ->text(
                label: 'Short Name',
                default: 'ITSNU Pekalongan',
                required: true,
                name: 'short_name'
            )
            ->textarea(
                label: 'Address',
                name: 'address'
            )
            ->text(
                label: 'Phone',
                name: 'phone'
            )
            ->text(
                label: 'Email',
                default: 'info@itsnu.ac.id',
                name: 'email'
            )
            ->text(
                label: 'Website',
                default: 'https://itsnu.ac.id',
                name: 'website'
            )
            ->submit();

        $this->newLine();

        return [
            'name' => $responses['name'],
            'short_name' => $responses['short_name'],
            'address' => $responses['address'] ?? '',
            'phone' => $responses['phone'] ?? '',
            'email' => $responses['email'] ?? '',
            'website' => $responses['website'] ?? '',
        ];
    }

    private function configureAdmin(): array
    {
        info('Step 4: Admin Account');

        $responses = form()
            ->text(
                label: 'Admin Name',
                default: 'Administrator',
                required: true,
                name: 'name'
            )
            ->text(
                label: 'Email Address',
                required: true,
                validate: ['required', 'email'],
                name: 'email'
            )
            ->password(
                label: 'Password',
                required: true,
                validate: fn ($value) => match (true) {
                    strlen($value) < 8 => 'Password must be at least 8 characters.',
                    ! preg_match('/[A-Z]/', $value) => 'Password must contain at least one uppercase letter.',
                    ! preg_match('/[a-z]/', $value) => 'Password must contain at least one lowercase letter.',
                    ! preg_match('/[0-9]/', $value) => 'Password must contain at least one number.',
                    default => null
                },
                name: 'password'
            )
            ->password(
                label: 'Confirm Password',
                required: true,
                name: 'password_confirmation'
            )
            ->submit();

        // Validate password confirmation
        if ($responses['password'] !== $responses['password_confirmation']) {
            error('Passwords do not match!');

            return $this->configureAdmin();
        }

        $this->newLine();

        return [
            'name' => $responses['name'],
            'email' => $responses['email'],
            'password' => $responses['password'],
        ];
    }

    private function runInstallation(
        InstallationService $service,
        array $dbConfig,
        array $institutionConfig,
        array $adminConfig
    ): bool {
        info('Step 5: Starting Installation...');
        $this->newLine();

        $config = [
            'DB_CONNECTION' => 'mariadb',
            'DB_HOST' => $dbConfig['host'],
            'DB_PORT' => $dbConfig['port'],
            'DB_DATABASE' => $dbConfig['database'],
            'DB_USERNAME' => $dbConfig['username'],
            'DB_PASSWORD' => $dbConfig['password'],
            'APP_NAME' => $institutionConfig['short_name'],
            'APP_URL' => $institutionConfig['website'] ?: 'http://localhost',
            'institution' => $institutionConfig,
            'admin' => $adminConfig,
        ];

        $progress = progress(label: 'Installing', steps: 100);
        $progress->start();

        try {
            $service->runInstallation(
                $config,
                function (int $percent, string $message) use ($progress) {
                    $progress->label("Installing: {$message}");
                    $progress->advance($percent - $progress->progress);
                }
            );

            $progress->finish();

            return true;
        } catch (\Exception $e) {
            $progress->finish();
            error("Installation failed: {$e->getMessage()}");

            return false;
        }
    }

    private function displaySuccess(): void
    {
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════╗');
        $this->line('║         Installation Complete! 🎉                      ║');
        $this->line('╚════════════════════════════════════════════════════════╝');
        $this->newLine();
        info('Your LPPM-ITSNU system is now ready to use.');
        $this->newLine();
        $this->line('You can now log in at: '.config('app.url').'/login');
        $this->newLine();
    }
}
