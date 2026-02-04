<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Livewire\Forms\Installer\EnvironmentConfigForm;
use App\Services\Installer\DatabaseTester;
use App\Services\Installer\InstallationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Prompts\Prompt;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\form;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class InstallCommand extends Command
{
    protected $signature = 'app:install
                            {--force : Force installation even if already installed}
                            {--quick : Skip optional configuration (use defaults)}';

    protected $description = 'Install LPPM-ITSNU application';

    public function handle(InstallationService $installationService): int
    {
        $this->registerPromptFallbacks();
        $this->displayHeader();

        // Check if already installed
        if ($installationService->isInstalled() && ! $this->option('force')) {
            if (! File::exists(storage_path('app/.installed'))) {
                $installationService->lockInstallation();
                note('Installer lock file was missing. Created storage/app/.installed.');
            }

            info('Application is already installed!');
            warning('Use --force to reinstall (this will delete all data).');

            return 0;
        }

        // Step 1: Environment Check
        if (! $this->runEnvironmentCheck($installationService)) {
            return 1;
        }

        // Step 2: Environment Configuration
        $envConfig = $this->configureEnvironment();

        // Step 3: Database Configuration
        $dbConfig = $this->configureDatabase();
        if ($dbConfig === null) {
            return 1;
        }

        // Step 4: Institution Setup
        $institutionConfig = $this->configureInstitution();

        // Step 5: Faculties Setup
        $facultiesConfig = $this->configureFaculties();

        // Step 6: Admin Account
        $adminConfig = $this->configureAdmin();

        // Confirm installation
        $this->displaySummary($envConfig, $dbConfig, $institutionConfig, $facultiesConfig, $adminConfig);

        if (! confirm('Ready to install. Continue?', default: true)) {
            warning('Installation cancelled.');

            return 1;
        }

        // Run Installation
        $success = $this->runInstallation(
            $installationService,
            $envConfig,
            $dbConfig,
            $institutionConfig,
            $facultiesConfig,
            $adminConfig
        );

        if ($success) {
            $this->displaySuccess($envConfig['APP_URL'] ?? 'http://localhost');

            return 0;
        }

        error('Installation failed!');

        return 1;
    }

    private function registerPromptFallbacks(): void
    {
        $isInteractive = $this->input?->isInteractive() ?? true;
        $isWindows = function_exists('windows_os') ? windows_os() : false;
        $shouldFallback = ! $isInteractive || $isWindows || app()->runningUnitTests();

        Prompt::fallbackWhen($shouldFallback);
    }

    private function displayHeader(): void
    {
        $this->newLine();
        $this->line('<fg=cyan>╔════════════════════════════════════════════════════════╗</>');
        $this->line('<fg=cyan>║</>         <fg=white;options=bold>LPPM-ITSNU Auto Installer</>                      <fg=cyan>║</>');
        $this->line('<fg=cyan>║</>         <fg=gray>Research Management System</>                     <fg=cyan>║</>');
        $this->line('<fg=cyan>╚════════════════════════════════════════════════════════╝</>');
        $this->newLine();
    }

    private function runEnvironmentCheck(InstallationService $service): bool
    {
        info('Step 1/6: Checking environment...');

        $checks = $service->checkEnvironment();
        $allPassed = true;
        $missingExtensions = [];

        foreach ($checks as $check) {
            $status = $check['status'] ? '<fg=green>✓</>' : '<fg=red>✗</>';
            $this->line("  {$status} {$check['label']}: {$check['current']}");

            if (! $check['status']) {
                $allPassed = false;
            }
        }

        $this->newLine();

        if (! $allPassed) {
            foreach ($checks as $key => $check) {
                if (! $check['status'] && str_starts_with($key, 'ext_')) {
                    $missingExtensions[] = substr($key, 4);
                }
            }

            if (! empty($missingExtensions)) {
                $this->displayExtensionInstallHints($missingExtensions);
            }

            error('Environment checks failed. Please fix the issues above.');

            return false;
        }

        info('All environment checks passed!');
        $this->newLine();

        return true;
    }

    private function displayExtensionInstallHints(array $extensions): void
    {
        $extensions = array_values(array_unique($extensions));
        $packages = $this->buildExtensionPackages($extensions);

        $this->newLine();
        warning('Missing PHP extensions detected: '.implode(', ', $extensions));

        if (PHP_OS_FAMILY === 'Linux') {
            $commands = $this->buildLinuxExtensionInstallCommands($packages);

            if (! empty($commands)) {
                note('Install commands (Linux):');
                foreach ($commands as $command) {
                    $this->line("  {$command}");
                }
            } else {
                note('Install the missing PHP extensions using your package manager.');
            }
        } else {
            note('Install the missing PHP extensions using your OS package manager.');
        }

        $this->newLine();
    }

    private function buildExtensionPackages(array $extensions): array
    {
        $phpVersion = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
        $map = [
            'pdo_mysql' => "php{$phpVersion}-mysql",
            'pdo' => "php{$phpVersion}-pdo",
        ];

        $packages = [];
        foreach ($extensions as $extension) {
            $packages[] = $map[$extension] ?? "php{$phpVersion}-{$extension}";
        }

        return array_values(array_unique($packages));
    }

    private function buildLinuxExtensionInstallCommands(array $packages): array
    {
        $commands = [];

        if (file_exists('/usr/bin/apt-get')) {
            $commands[] = 'sudo apt-get update && sudo apt-get install -y '.implode(' ', $packages);
        }

        if (file_exists('/usr/bin/dnf')) {
            $commands[] = 'sudo dnf install -y '.implode(' ', $packages);
        }

        if (file_exists('/usr/bin/yum')) {
            $commands[] = 'sudo yum install -y '.implode(' ', $packages);
        }

        if (file_exists('/usr/bin/pacman')) {
            $commands[] = 'sudo pacman -S '.implode(' ', $packages);
        }

        return $commands;
    }

    private function configureEnvironment(): array
    {
        info('Step 2/6: Environment Configuration');

        $isQuick = $this->option('quick');
        $options = EnvironmentConfigForm::getOptions();

        // Basic configuration (always asked)
        $responses = form()
            ->text(
                label: 'Application Name',
                default: 'LPPM ITSNU',
                required: true,
                name: 'app_name'
            )
            ->text(
                label: 'Application URL',
                default: 'http://localhost',
                required: true,
                validate: ['app_url' => 'url'],
                hint: 'e.g., https://lppm.itsnu.ac.id',
                name: 'app_url'
            )
            ->select(
                label: 'Environment',
                options: $options['appEnv'],
                default: 'production',
                name: 'app_env'
            )
            ->select(
                label: 'Locale',
                options: $options['appLocale'],
                default: 'id',
                name: 'app_locale'
            )
            ->confirm(
                label: 'Enable Debug Mode?',
                default: false,
                hint: 'Only enable for development',
                name: 'app_debug'
            )
            ->submit();

        $config = [
            'APP_NAME' => $responses['app_name'],
            'APP_URL' => rtrim($responses['app_url'], '/'),
            'APP_ENV' => $responses['app_env'],
            'APP_DEBUG' => $responses['app_debug'] ? 'true' : 'false',
            'APP_LOCALE' => $responses['app_locale'],
        ];

        // Skip advanced config if quick mode
        if ($isQuick) {
            $this->newLine();
            note('Using default settings for session, cache, queue, mail, and storage.');
            $this->newLine();

            return array_merge($config, [
                'SESSION_DRIVER' => 'file',
                'SESSION_LIFETIME' => '120',
                'CACHE_STORE' => 'file',
                'QUEUE_CONNECTION' => 'sync',
                'MAIL_MAILER' => 'log',
                'FILESYSTEM_DISK' => 'local',
                'MEDIA_DISK' => 'public',
            ]);
        }

        $config = array_merge($config, $this->configureAdvancedEnvironment($options, $config['APP_NAME']));

        $this->newLine();

        return $config;
    }

    private function configureAdvancedEnvironment(array $options, string $appName): array
    {
        $this->newLine();
        note('Session, Cache & Queue Configuration');

        $responses = form()
            ->select(
                label: 'Session Driver',
                options: $options['sessionDriver'],
                default: 'file',
                name: 'session_driver'
            )
            ->text(
                label: 'Session Lifetime (minutes)',
                default: '120',
                required: true,
                validate: ['session_lifetime' => 'numeric|min:1'],
                name: 'session_lifetime'
            )
            ->select(
                label: 'Cache Store',
                options: $options['cacheStore'],
                default: 'file',
                name: 'cache_store'
            )
            ->select(
                label: 'Queue Connection',
                options: $options['queueConnection'],
                default: 'sync',
                name: 'queue_connection'
            )
            ->submit();

        $config = [
            'SESSION_DRIVER' => $responses['session_driver'],
            'SESSION_LIFETIME' => $responses['session_lifetime'],
            'CACHE_STORE' => $responses['cache_store'],
            'QUEUE_CONNECTION' => $responses['queue_connection'],
        ];

        $config = array_merge($config, $this->configureMailSettings($options, $appName));
        $config = array_merge($config, $this->configureStorageSettings($options));
        $config = array_merge($config, $this->configureTurnstile());

        return $config;
    }

    private function configureMailSettings(array $options, string $appName): array
    {
        $this->newLine();
        note('Mail Configuration');

        $mailer = select(
            label: 'Mail Driver',
            options: $options['mailMailer'],
            default: 'log'
        );

        $encryption = select(
            label: 'Encryption',
            options: $options['mailEncryption'],
            default: 'tls'
        );

        $config = [
            'MAIL_MAILER' => $mailer,
            'MAIL_ENCRYPTION' => $encryption === 'null' ? '' : $encryption,
        ];

        if ($mailer === 'smtp') {
            $responses = form()
                ->text(
                    label: 'SMTP Host',
                    default: 'smtp.gmail.com',
                    name: 'mail_host'
                )
                ->text(
                    label: 'SMTP Port',
                    default: '587',
                    validate: ['mail_port' => 'nullable|numeric'],
                    name: 'mail_port'
                )
                ->text(
                    label: 'SMTP Username',
                    name: 'mail_username'
                )
                ->password(
                    label: 'SMTP Password',
                    name: 'mail_password'
                )
                ->submit();

            $config = array_merge($config, [
                'MAIL_HOST' => $responses['mail_host'],
                'MAIL_PORT' => $responses['mail_port'],
                'MAIL_USERNAME' => $responses['mail_username'],
                'MAIL_PASSWORD' => $responses['mail_password'],
            ]);
        }

        $responses = form()
            ->text(
                label: 'From Address',
                validate: ['mail_from_address' => 'nullable|email'],
                name: 'mail_from_address'
            )
            ->text(
                label: 'From Name',
                default: $appName,
                name: 'mail_from_name'
            )
            ->submit();

        return array_merge($config, [
            'MAIL_FROM_ADDRESS' => $responses['mail_from_address'],
            'MAIL_FROM_NAME' => $responses['mail_from_name'],
        ]);
    }

    private function configureTurnstile(): array
    {
        $this->newLine();
        note('Cloudflare Turnstile Configuration');

        $responses = form()
            ->text(
                label: 'Site Key',
                hint: 'Leave empty to skip (Cloudflare Dashboard)',
                name: 'site_key'
            )
            ->password(
                label: 'Secret Key',
                hint: 'Leave empty to skip',
                name: 'secret_key'
            )
            ->submit();

        return [
            'TURNSTILE_SITE_KEY' => $responses['site_key'],
            'TURNSTILE_SECRET_KEY' => $responses['secret_key'],
        ];
    }

    private function configureStorageSettings(array $options): array
    {
        $this->newLine();
        note('Storage Configuration');

        $responses = form()
            ->select(
                label: 'Filesystem Disk',
                options: $options['filesystemDisk'],
                default: 'local',
                name: 'filesystem_disk'
            )
            ->select(
                label: 'Media Disk',
                options: $options['mediaDisk'],
                default: 'public',
                name: 'media_disk'
            )
            ->submit();

        $config = [
            'FILESYSTEM_DISK' => $responses['filesystem_disk'],
            'MEDIA_DISK' => $responses['media_disk'],
        ];

        if ($responses['filesystem_disk'] === 's3' || $responses['media_disk'] === 's3') {
            $config = array_merge($config, $this->configureS3Storage($options));
        }

        return $config;
    }

    private function configureS3Storage(array $options): array
    {
        $this->newLine();
        note('S3/Object Storage Configuration');

        $responses = form()
            ->text(
                label: 'Access Key ID',
                required: true,
                name: 'access_key'
            )
            ->password(
                label: 'Secret Access Key',
                required: true,
                name: 'secret_key'
            )
            ->select(
                label: 'Region',
                options: $options['awsRegion'],
                default: 'ap-southeast-1',
                name: 'region'
            )
            ->text(
                label: 'Bucket Name',
                required: true,
                name: 'bucket'
            )
            ->text(
                label: 'Endpoint URL (optional)',
                hint: 'For S3-compatible storage like MinIO, DigitalOcean Spaces',
                name: 'endpoint'
            )
            ->text(
                label: 'Public URL (optional)',
                hint: 'CDN or public URL for assets',
                name: 'url'
            )
            ->confirm(
                label: 'Use Path Style Endpoint?',
                default: false,
                hint: 'Required for MinIO and some S3-compatible services',
                name: 'path_style'
            )
            ->submit();

        return [
            'AWS_ACCESS_KEY_ID' => $responses['access_key'],
            'AWS_SECRET_ACCESS_KEY' => $responses['secret_key'],
            'AWS_DEFAULT_REGION' => $responses['region'],
            'AWS_BUCKET' => $responses['bucket'],
            'AWS_ENDPOINT' => $responses['endpoint'] ?? '',
            'AWS_URL' => $responses['url'] ?? '',
            'AWS_USE_PATH_STYLE_ENDPOINT' => $responses['path_style'] ? 'true' : 'false',
        ];
    }

    private function configureDatabase(): ?array
    {
        info('Step 3/6: Database Configuration');

        $defaultConfig = (new DatabaseTester)->getDefaultValues();

        $responses = form()
            ->text(
                label: 'Database Host',
                default: $defaultConfig['host'],
                required: true,
                name: 'host'
            )
            ->text(
                label: 'Port',
                default: $defaultConfig['port'],
                required: true,
                validate: ['port' => 'numeric'],
                name: 'port'
            )
            ->text(
                label: 'Database Name',
                default: $defaultConfig['database'],
                required: true,
                name: 'database'
            )
            ->text(
                label: 'Username',
                default: $defaultConfig['username'],
                required: true,
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

        $result = spin(
            fn () => $tester->testCredentialsOnly($config),
            'Testing database connection...'
        );

        if (! $result['success']) {
            error("Connection failed: {$result['message']}");

            if (confirm('Try again?', default: true)) {
                return $this->configureDatabase();
            }

            return null;
        }

        info('Connection successful!');

        // Check/create database
        $dbExists = $tester->databaseExists($config);

        if (! $dbExists) {
            if ($responses['create_database']) {
                $createResult = spin(
                    fn () => $tester->createDatabase($config),
                    'Creating database...'
                );

                if (! $createResult['success']) {
                    error("Failed to create database: {$createResult['message']}");

                    return null;
                }

                info('Database created!');
            } else {
                error("Database '{$config['database']}' does not exist.");

                if (confirm('Create it now?', default: true)) {
                    $createResult = $tester->createDatabase($config);

                    if (! $createResult['success']) {
                        error("Failed to create database: {$createResult['message']}");

                        return null;
                    }

                    info('Database created!');
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
        info('Step 4/6: Institution Setup');

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
                validate: ['email' => 'nullable|email'],
                name: 'email'
            )
            ->text(
                label: 'Website',
                default: 'https://itsnu.ac.id',
                validate: ['website' => 'nullable|url'],
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

    private function configureFaculties(): array
    {
        info('Step 5/6: Faculties Setup');

        $faculties = [
            ['name' => 'SAINTEK', 'code' => 'SAINTEK'],
            ['name' => 'DEKABITA', 'code' => 'DEKABITA'],
        ];

        $this->line('  Default faculties:');
        foreach ($faculties as $index => $faculty) {
            $this->line('    '.($index + 1).". {$faculty['name']} ({$faculty['code']})");
        }
        $this->newLine();

        if (confirm('Use default faculties?', default: true)) {
            $this->newLine();

            return $faculties;
        }

        // Custom faculties
        $faculties = [];
        $addMore = true;

        while ($addMore) {
            $this->newLine();
            $facultyNumber = count($faculties) + 1;
            note("Faculty #{$facultyNumber}");

            $responses = form()
                ->text(
                    label: 'Faculty Name',
                    required: true,
                    hint: 'e.g., Fakultas Teknik',
                    name: 'name'
                )
                ->text(
                    label: 'Faculty Code',
                    required: true,
                    hint: 'e.g., FT',
                    name: 'code'
                )
                ->submit();

            $faculties[] = [
                'name' => $responses['name'],
                'code' => strtoupper($responses['code']),
            ];

            $addMore = confirm('Add another faculty?', default: count($faculties) < 2);
        }

        if (empty($faculties)) {
            warning('No faculties configured. Using defaults.');
            $faculties = [
                ['name' => 'SAINTEK', 'code' => 'SAINTEK'],
                ['name' => 'DEKABITA', 'code' => 'DEKABITA'],
            ];
        }

        $this->newLine();

        return $faculties;
    }

    private function configureAdmin(): array
    {
        info('Step 6/6: Admin Account');

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
                validate: ['email' => 'email'],
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

    private function displaySummary(
        array $envConfig,
        array $dbConfig,
        array $institutionConfig,
        array $facultiesConfig,
        array $adminConfig
    ): void {
        $this->newLine();
        $this->line('<fg=cyan>╔════════════════════════════════════════════════════════╗</>');
        $this->line('<fg=cyan>║</>         <fg=white;options=bold>Installation Summary</>                            <fg=cyan>║</>');
        $this->line('<fg=cyan>╚════════════════════════════════════════════════════════╝</>');
        $this->newLine();

        $this->line('<fg=yellow>Environment:</>');
        $this->line("  App Name: {$envConfig['APP_NAME']}");
        $this->line("  URL: {$envConfig['APP_URL']}");
        $this->line("  Environment: {$envConfig['APP_ENV']}");
        if (isset($envConfig['APP_LOCALE'])) {
            $this->line("  Locale: {$envConfig['APP_LOCALE']}");
        }
        if (isset($envConfig['APP_DEBUG'])) {
            $this->line("  Debug: {$envConfig['APP_DEBUG']}");
        }
        $this->newLine();

        $this->line('<fg=yellow>Database:</>');
        $this->line("  Host: {$dbConfig['host']}:{$dbConfig['port']}");
        $this->line("  Database: {$dbConfig['database']}");
        $this->line("  Username: {$dbConfig['username']}");
        $this->newLine();

        $this->line('<fg=yellow>Institution:</>');
        $this->line("  Name: {$institutionConfig['name']}");
        $this->line("  Short: {$institutionConfig['short_name']}");
        $this->newLine();

        $this->line('<fg=yellow>Faculties:</>');
        foreach ($facultiesConfig as $faculty) {
            $this->line("  - {$faculty['name']} ({$faculty['code']})");
        }
        $this->newLine();

        $this->line('<fg=yellow>Admin:</>');
        $this->line("  Name: {$adminConfig['name']}");
        $this->line("  Email: {$adminConfig['email']}");
        $this->newLine();
    }

    private function runInstallation(
        InstallationService $service,
        array $envConfig,
        array $dbConfig,
        array $institutionConfig,
        array $facultiesConfig,
        array $adminConfig
    ): bool {
        $this->newLine();
        info('Starting installation...');
        $this->newLine();

        $config = array_merge($envConfig, [
            'DB_CONNECTION' => 'mariadb',
            'DB_HOST' => $dbConfig['host'],
            'DB_PORT' => $dbConfig['port'],
            'DB_DATABASE' => $dbConfig['database'],
            'DB_USERNAME' => $dbConfig['username'],
            'DB_PASSWORD' => $dbConfig['password'],
            'institution' => $institutionConfig,
            'faculties' => $facultiesConfig,
            'admin' => $adminConfig,
        ]);

        try {
            $service->runInstallation(
                $config,
                function (int $percent, string $message): void {},
                function (array $step, callable $runStep) {
                    spin($runStep, $step['label']);
                }
            );

            return true;
        } catch (\Exception $e) {
            error("Installation failed: {$e->getMessage()}");

            return false;
        }
    }

    private function displaySuccess(string $appUrl): void
    {
        $this->newLine();
        $this->line('<fg=green>╔════════════════════════════════════════════════════════╗</>');
        $this->line('<fg=green>║</>         <fg=white;options=bold>Installation Complete!</>                          <fg=green>║</>');
        $this->line('<fg=green>╚════════════════════════════════════════════════════════╝</>');
        $this->newLine();
        info('Your LPPM-ITSNU system is now ready to use.');
        $this->newLine();
        $this->line("You can now log in at: <fg=cyan>{$appUrl}/login</>");
        $this->newLine();
    }
}
