<?php

declare(strict_types=1);

namespace App\Services\Installer;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallationService
{
    private const LOCK_FILE = 'app/.installed';

    private const ENV_BACKUP_PREFIX = '.env.backup.';

    public function isInstalled(): bool
    {
        // Check lock file
        if (File::exists($this->getLockFilePath())) {
            return true;
        }

        // Check if users table exists and has records
        try {
            if (! DB::select("SHOW TABLES LIKE 'users'")) {
                return false;
            }
            $userCount = DB::table('users')->count();

            return $userCount > 0;
        } catch (Exception) {
            return false;
        }
    }

    public function getInstallationStatus(): array
    {
        $status = [
            'env_exists' => File::exists(base_path('.env')),
            'env_example_exists' => File::exists(base_path('.env.example')),
            'key_generated' => ! empty(config('app.key')),
            'database_connected' => false,
            'migrations_run' => false,
            'users_exist' => false,
            'storage_linked' => File::exists(public_path('storage')),
        ];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $status['database_connected'] = true;

            // Check if migrations table exists
            $tables = DB::select("SHOW TABLES LIKE 'migrations'");
            if (! empty($tables)) {
                $status['migrations_run'] = true;
            }

            // Check if users exist
            $tables = DB::select("SHOW TABLES LIKE 'users'");
            if (! empty($tables)) {
                $status['users_exist'] = DB::table('users')->count() > 0;
            }
        } catch (Exception) {
            // Leave defaults
        }

        return $status;
    }

    public function runInstallation(array $config, callable $onProgress): void
    {
        $steps = [
            ['name' => 'backup_env', 'label' => 'Backing up existing configuration...', 'weight' => 5],
            ['name' => 'write_env', 'label' => 'Writing environment file...', 'weight' => 5],
            ['name' => 'clear_config', 'label' => 'Clearing configuration cache...', 'weight' => 2],
            ['name' => 'generate_key', 'label' => 'Generating application key...', 'weight' => 5],
            ['name' => 'run_migrations', 'label' => 'Running database migrations...', 'weight' => 50],
            ['name' => 'run_seeders', 'label' => 'Seeding master data...', 'weight' => 25],
            ['name' => 'create_admin', 'label' => 'Creating admin account...', 'weight' => 5],
            ['name' => 'storage_link', 'label' => 'Creating storage link...', 'weight' => 3],
            ['name' => 'finalize', 'label' => 'Finalizing installation...', 'weight' => 2],
        ];

        $totalWeight = array_sum(array_column($steps, 'weight'));
        $currentWeight = 0;

        foreach ($steps as $step) {
            $onProgress($this->calculatePercent($currentWeight, $totalWeight), $step['label']);

            match ($step['name']) {
                'backup_env' => $this->backupEnvFile(),
                'write_env' => $this->writeEnvFile($config),
                'clear_config' => $this->clearConfigCache(),
                'generate_key' => $this->generateKey(),
                'run_migrations' => $this->runMigrations($onProgress, $currentWeight, $totalWeight, $step['weight']),
                'run_seeders' => $this->runSeeders($onProgress, $currentWeight, $totalWeight, $step['weight'], $config),
                'create_admin' => $this->createAdminUser($config['admin'] ?? []),
                'storage_link' => $this->createStorageLink(),
                'finalize' => $this->finalizeInstallation(),
                default => null,
            };

            $currentWeight += $step['weight'];
        }

        $onProgress(100, 'Installation complete!');
    }

    private function backupEnvFile(): void
    {
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $backupPath = base_path(self::ENV_BACKUP_PREFIX.now()->format('Y-m-d-His'));
            File::copy($envPath, $backupPath);
        }
    }

    private function writeEnvFile(array $config): void
    {
        $writer = new EnvironmentWriter;
        $writer->write($config);
    }

    private function generateKey(): void
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    private function clearConfigCache(): void
    {
        try {
            Artisan::call('config:clear');
        } catch (Exception) {
            // Ignore failures during installation.
        }
    }

    private function runMigrations(callable $onProgress, int &$currentWeight, int $totalWeight, int $stepWeight): void
    {
        // Get migration files to calculate progress
        $migrationFiles = File::files(database_path('migrations'));
        $totalMigrations = count($migrationFiles);
        $processedMigrations = 0;

        // We can't easily get real-time migration progress, so we'll simulate
        // In reality, migrations run all at once via Artisan
        $dbEnv = $this->buildDatabaseEnv();
        $this->applyDatabaseEnv($dbEnv);
        if (empty($dbEnv['DB_PASSWORD'])) {
            throw new Exception('Database password is empty. Please re-enter it in the installer.');
        }

        Artisan::call('migrate', ['--force' => true, '--step' => true]);

        // Simulate progress during migration
        for ($i = 0; $i < 10; $i++) {
            $processedMigrations = min($totalMigrations, (int) ($totalMigrations * ($i + 1) / 10));
            $progress = $currentWeight + ($stepWeight * ($i + 1) / 10);
            $onProgress(
                $this->calculatePercent($progress, $totalWeight),
                "Running migrations... ({$processedMigrations}/{$totalMigrations})"
            );
            usleep(100000); // 100ms for visual effect
        }
    }

    private function runSeeders(callable $onProgress, int &$currentWeight, int $totalWeight, int $stepWeight, array $config = []): void
    {
        $seeders = [
            'RoleSeeder',
            'InstitutionSeeder',
            'ResearchSchemeSeeder',
            'TktSeeder',
            'FocusAreaSeeder',
            'NationalPrioritySeeder',
            'KeywordSeeder',
            'MacroResearchGroupSeeder',
            'BudgetGroupSeeder',
            'BudgetComponentSeeder',
            'ReviewCriteriaSeeder',
            'ScienceClusterSeeder',
            'FacultySeeder',
            'StudyProgramSeeder',
            'ThemeSeeder',
            'TopicSeeder',
            'AdminUserSeeder',
        ];

        $totalSeeders = count($seeders);

        // Store dynamic config for seeders to use
        if (! empty($config)) {
            $this->storeDynamicSeedersConfig($config);
        }

        foreach ($seeders as $index => $seederClass) {
            $dbEnv = $this->buildDatabaseEnv();
            $this->applyDatabaseEnv($dbEnv);
            if (empty($dbEnv['DB_PASSWORD'])) {
                throw new Exception('Database password is empty. Please re-enter it in the installer.');
            }

            Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);

            $progress = $currentWeight + ($stepWeight * ($index + 1) / $totalSeeders);
            $onProgress(
                $this->calculatePercent($progress, $totalWeight),
                "Running seeders... ({$seederClass})"
            );
        }

        // Clear the dynamic config after seeding
        $this->clearDynamicSeedersConfig();
    }

    private function createAdminUser(array $adminConfig): void
    {
        // Store admin config in a temporary location for the seeder to use
        cache()->put('installer_admin_config', $adminConfig, now()->addHour());

        // Update the admin user with custom details
        if (! empty($adminConfig['email'])) {
            \App\Models\User::where('email', 'superadmin@email.com')
                ->update([
                    'email' => $adminConfig['email'],
                    'name' => $adminConfig['name'] ?? 'Administrator',
                ]);
        }

        if (! empty($adminConfig['password'])) {
            \App\Models\User::where('email', $adminConfig['email'] ?? 'superadmin@email.com')
                ->update([
                    'password' => bcrypt($adminConfig['password']),
                ]);
        }

        cache()->forget('installer_admin_config');
    }

    private function storeDynamicSeedersConfig(array $config): void
    {
        // Store institution config for seeders
        if (! empty($config['institution'])) {
            cache()->put('installer_institution_config', $config['institution'], now()->addHour());
        }

        // Store faculties config for seeders
        if (! empty($config['faculties'])) {
            cache()->put('installer_faculties_config', $config['faculties'], now()->addHour());
        }

        // Store admin config for seeders
        if (! empty($config['admin'])) {
            cache()->put('installer_admin_config', $config['admin'], now()->addHour());
        }
    }

    private function clearDynamicSeedersConfig(): void
    {
        cache()->forget('installer_institution_config');
        cache()->forget('installer_faculties_config');
        cache()->forget('installer_admin_config');
    }

    private function createStorageLink(): void
    {
        try {
            Artisan::call('storage:link', ['--force' => true]);
        } catch (Exception) {
            // Storage link might already exist or fail silently
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildDatabaseEnv(): array
    {
        $writer = new EnvironmentWriter;
        $current = $writer->readCurrent();

        return [
            'DB_CONNECTION' => $current['DB_CONNECTION'] ?? 'mariadb',
            'DB_HOST' => $current['DB_HOST'] ?? '127.0.0.1',
            'DB_PORT' => $current['DB_PORT'] ?? '3306',
            'DB_DATABASE' => $current['DB_DATABASE'] ?? 'laravel',
            'DB_USERNAME' => $current['DB_USERNAME'] ?? 'root',
            'DB_PASSWORD' => $current['DB_PASSWORD'] ?? '',
        ];
    }

    /**
     * @param  array<string, string>  $envVars
     */
    private function applyDatabaseEnv(array $envVars): void
    {
        foreach ($envVars as $key => $value) {
            if ($value === '') {
                continue;
            }

            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        config([
            'database.default' => $envVars['DB_CONNECTION'],
            'database.connections.'.$envVars['DB_CONNECTION'] => [
                'driver' => $envVars['DB_CONNECTION'],
                'host' => $envVars['DB_HOST'],
                'port' => $envVars['DB_PORT'],
                'database' => $envVars['DB_DATABASE'],
                'username' => $envVars['DB_USERNAME'],
                'password' => $envVars['DB_PASSWORD'],
            ],
        ]);

        DB::purge($envVars['DB_CONNECTION']);
        DB::reconnect($envVars['DB_CONNECTION']);
    }

    private function finalizeInstallation(): void
    {
        // Clear caches
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        // Create lock file
        File::put($this->getLockFilePath(), now()->toDateTimeString());
    }

    public function lockInstallation(): void
    {
        File::put($this->getLockFilePath(), now()->toDateTimeString());
    }

    public function unlockInstallation(): void
    {
        if (File::exists($this->getLockFilePath())) {
            File::delete($this->getLockFilePath());
        }
    }

    private function getLockFilePath(): string
    {
        return storage_path(self::LOCK_FILE);
    }

    private function calculatePercent(float $current, float $total): int
    {
        return (int) round(($current / $total) * 100);
    }

    public function checkEnvironment(): array
    {
        $checks = [];

        // PHP Version
        $phpVersion = PHP_VERSION;
        $checks['php_version'] = [
            'label' => 'PHP Version >= 8.2',
            'status' => version_compare($phpVersion, '8.2.0', '>='),
            'current' => $phpVersion,
            'required' => '8.2.0',
        ];

        // Required Extensions
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'json', 'ctype', 'xml', 'bcmath', 'fileinfo'];
        foreach ($requiredExtensions as $extension) {
            $checks["ext_{$extension}"] = [
                'label' => "Extension: {$extension}",
                'status' => extension_loaded($extension),
                'current' => extension_loaded($extension) ? 'Installed' : 'Missing',
                'required' => 'Required',
            ];
        }

        // Writable Directories
        $writableDirs = [
            'storage' => storage_path(),
            'bootstrap_cache' => base_path('bootstrap/cache'),
            'storage_app' => storage_path('app'),
            'storage_logs' => storage_path('logs'),
        ];

        foreach ($writableDirs as $key => $path) {
            $checks["writable_{$key}"] = [
                'label' => 'Writable: '.basename($path),
                'status' => is_writable($path),
                'current' => is_writable($path) ? 'Writable' : 'Not Writable',
                'required' => 'Writable',
            ];
        }

        // .env.example exists
        $checks['env_example'] = [
            'label' => '.env.example exists',
            'status' => File::exists(base_path('.env.example')),
            'current' => File::exists(base_path('.env.example')) ? 'Found' : 'Missing',
            'required' => 'Required',
        ];

        return $checks;
    }

    public function allEnvironmentChecksPass(): bool
    {
        $checks = $this->checkEnvironment();

        return collect($checks)->every(fn ($check) => $check['status']);
    }
}
