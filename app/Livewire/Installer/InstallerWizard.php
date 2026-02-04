<?php

declare(strict_types=1);

namespace App\Livewire\Installer;

use App\Livewire\Forms\Installer\AdminAccountForm;
use App\Livewire\Forms\Installer\DatabaseConfigForm;
use App\Livewire\Forms\Installer\EnvironmentConfigForm;
use App\Livewire\Forms\Installer\InstitutionSetupForm;
use App\Services\Installer\InstallationService;
use Illuminate\Support\Facades\Process;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('installer.layout')]
class InstallerWizard extends Component
{
    public int $currentStep = 1;

    public int $totalSteps = 6;

    /** @var array<int, bool> Track which steps have been completed */
    public array $completedSteps = [];

    public DatabaseConfigForm $databaseForm;

    public EnvironmentConfigForm $environmentForm;

    public InstitutionSetupForm $institutionForm;

    public AdminAccountForm $adminForm;

    public array $environmentChecks = [];

    public bool $environmentPassed = false;

    public bool $databaseTested = false;

    public array $installationProgress = [
        'percent' => 0,
        'message' => '',
        'logs' => [],
        'complete' => false,
        'error' => null,
    ];

    public bool $isInstalling = false;

    public bool $installProcessLaunched = false;

    public ?int $lastProgressTimestamp = null;

    public ?int $installProcessStartedAt = null;

    /**
     * Stored database password - kept on component level to ensure persistence.
     */
    public string $storedDbPassword = '';

    protected InstallationService $installationService;

    public function boot(InstallationService $installationService): void
    {
        $this->installationService = $installationService;
    }

    public function mount(): void
    {
        // Check environment on load
        $this->checkEnvironment();

        // Initialize environment form with current URL
        $this->environmentForm->mount();

        // Note: Form defaults are set in Form classes, not from env()
        // to avoid reading stale values from existing .env file
    }

    public function checkEnvironment(): void
    {
        $this->environmentChecks = $this->installationService->checkEnvironment();
        $this->environmentPassed = $this->installationService->allEnvironmentChecksPass();
    }

    public function nextStep(): void
    {
        if ($this->currentStep >= $this->totalSteps) {
            return;
        }

        // Validate current step before proceeding
        if (! $this->validateAndCheckCurrentStep()) {
            return;
        }

        // Mark current step as completed
        $this->completedSteps[$this->currentStep] = true;
        $this->currentStep++;
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        // Only allow going to completed steps or current step
        if ($step <= $this->currentStep && ($step === 1 || isset($this->completedSteps[$step - 1]))) {
            $this->currentStep = $step;
        }
    }

    public function testDatabaseConnection(): void
    {
        $result = $this->databaseForm->testConnection();

        if ($result['success']) {
            $this->databaseTested = true;
            $this->dispatch('notify', type: 'success', message: $result['message']);
        } else {
            $this->databaseTested = false;
            $this->dispatch('notify', type: 'error', message: $result['message']);
        }
    }

    /**
     * Validate current step and return true if can proceed.
     */
    protected function validateAndCheckCurrentStep(): bool
    {
        return match ($this->currentStep) {
            1 => $this->validateEnvironmentStep(),
            2 => $this->validateDatabaseStep(),
            3 => $this->validateEnvironmentConfigStep(),
            4 => $this->validateInstitutionStep(),
            5 => $this->validateAdminStep(),
            default => true,
        };
    }

    protected function validateEnvironmentStep(): bool
    {
        if (! $this->environmentPassed) {
            $this->dispatch('notify', type: 'error', message: 'Perbaiki masalah environment terlebih dahulu.');

            return false;
        }

        return true;
    }

    protected function validateDatabaseStep(): bool
    {
        $this->databaseForm->syncPasswordFromInput();
        if ($this->databaseForm->dbPassword !== '') {
            $this->storedDbPassword = $this->databaseForm->dbPassword;
        }
        $this->databaseForm->validate();

        if (! $this->databaseTested) {
            // Auto-test connection if not tested yet
            $result = $this->databaseForm->testConnection();

            if (! $result['success']) {
                $this->dispatch('notify', type: 'error', message: $result['message']);

                return false;
            }

            $this->databaseTested = true;
        }

        return true;
    }

    protected function validateEnvironmentConfigStep(): bool
    {
        $this->environmentForm->validate();

        return true;
    }

    protected function validateInstitutionStep(): bool
    {
        $this->institutionForm->validate();

        return true;
    }

    protected function validateAdminStep(): bool
    {
        $this->adminForm->validate();

        return true;
    }

    /**
     * Reset database tested flag when form values change.
     */
    public function updatedDatabaseForm(): void
    {
        $this->databaseTested = false;
    }

    /**
     * Sync password input to stored password when user types.
     */
    public function updatedDatabaseFormDbPasswordInput(string $value): void
    {
        $this->databaseForm->syncPasswordFromInput();
        if ($this->databaseForm->dbPassword !== '') {
            $this->storedDbPassword = $this->databaseForm->dbPassword;
        }
    }

    public function startInstallation(): void
    {
        if ($this->isInstalling) {
            return;
        }

        $this->databaseForm->normalizeInputs();
        $this->environmentForm->normalizeInputs();
        $this->institutionForm->normalizeInputs();
        $this->adminForm->normalizeInputs();

        // Build and store config for the installation
        $effectiveDbPassword = $this->databaseForm->dbPassword;
        if ($this->storedDbPassword !== '') {
            $effectiveDbPassword = $this->storedDbPassword;
        }

        $this->databaseForm->dbPassword = $effectiveDbPassword;
        $this->storedDbPassword = $effectiveDbPassword;

        // Build config in explicit order to prevent key conflicts
        $dbConfig = $this->databaseForm->getEnvConfig();
        $envConfig = $this->environmentForm->getEnvConfig();

        // Merge ENV configs (database first, environment overrides)
        $config = array_merge($dbConfig, $envConfig);

        // Add non-ENV data for seeders (these are NOT env keys)
        $config['institution'] = $this->institutionForm->getInstitutionData();
        $config['faculties'] = $this->institutionForm->getFacultiesData();
        $config['admin'] = $this->adminForm->getAdminData();

        // Store config in cache for installation to use
        $this->installationService->storeInstallationConfig($config);

        // Initialize progress state
        $this->installationService->storeProgress(0, 'Preparing installation...');

        $this->isInstalling = true;
        $this->installProcessLaunched = false;
        $this->lastProgressTimestamp = null;
        $this->installProcessStartedAt = null;
        $this->installationProgress = [
            'percent' => 0,
            'message' => 'Preparing installation...',
            'logs' => [],
            'complete' => false,
            'error' => null,
        ];

        $this->launchInstallationProcess();
    }

    /**
     * Check installation progress from cache (called by wire:poll).
     * Also triggers installation on first poll if not started yet.
     */
    public function checkProgress(): void
    {
        if (! $this->isInstalling) {
            return;
        }

        $cached = $this->installationService->getProgress();

        // Check if installation has actually started
        if (! $this->installationService->isInstallationRunning() && ! $cached['complete'] && ! $cached['error']) {
            $this->launchInstallationProcess();
        }

        // Update local state from cache
        if ($cached['updated_at'] !== null) {
            if ($this->lastProgressTimestamp !== $cached['updated_at']) {
                $this->lastProgressTimestamp = $cached['updated_at'];

                if ($cached['message'] !== '') {
                    $this->installationProgress['logs'][] = "[{$cached['percent']}%] {$cached['message']}";
                }
            }

            $this->installationProgress['percent'] = $cached['percent'];
            $this->installationProgress['message'] = $cached['message'];

            if ($cached['error']) {
                $this->installationProgress['error'] = $cached['error'];
                $this->isInstalling = false;
            }

            if ($cached['complete']) {
                $this->installationProgress['complete'] = true;
                $this->isInstalling = false;
                $this->dispatch('installation-complete');
            }
        }

        $this->detectStalledInstallation($cached);
    }

    /**
     * Actually run the installation process.
     */
    private function doInstallation(): void
    {
        try {
            // Mark installation as running
            $this->installationService->markInstallationRunning();

            // Get stored config
            $config = $this->installationService->getInstallationConfig();

            if (empty($config)) {
                throw new \Exception('Installation config not found. Please restart the installation.');
            }

            $this->installationService->runInstallation(
                $config,
                function (int $percent, string $message) {
                    $this->installationProgress['logs'][] = "[{$percent}%] {$message}";
                }
            );

            $this->installationProgress['complete'] = true;
            $this->installationProgress['percent'] = 100;
            $this->installationProgress['message'] = 'Installation complete!';

            // Clear caches
            $this->installationService->clearInstallationConfig();

        } catch (\Exception $e) {
            $this->installationProgress['error'] = $e->getMessage();
            $this->installationProgress['logs'][] = "ERROR: {$e->getMessage()}";

            // Store error in cache for polling to pick up
            $this->installationService->storeProgress(
                $this->installationProgress['percent'],
                $this->installationProgress['message'],
                $e->getMessage(),
                false
            );

            $this->dispatch('notify', type: 'error', message: 'Installation failed: '.$e->getMessage());
        } finally {
            $this->installationService->markInstallationStopped();
            $this->isInstalling = false;
        }
    }

    private function launchInstallationProcess(): void
    {
        if ($this->installProcessLaunched) {
            return;
        }

        $this->installProcessLaunched = true;
        $this->installProcessStartedAt = time();

        try {
            $logPath = storage_path('logs/installer-worker.log');
            $phpBinary = $this->resolvePhpBinary();

            if (PHP_OS_FAMILY === 'Windows') {
                $command = sprintf('start /B "" %s artisan app:install-run > %s 2>&1', $this->escapeWindowsBinary($phpBinary), $this->escapeWindowsArgument($logPath));
                $result = Process::path(base_path())
                    ->run(['cmd', '/c', $command]);
            } else {
                $command = sprintf('%s artisan app:install-run > %s 2>&1 &', escapeshellarg($phpBinary), escapeshellarg($logPath));
                $result = Process::path(base_path())
                    ->run(['sh', '-c', $command]);
            }

            if (! $result->successful()) {
                throw new \RuntimeException($result->errorOutput() ?: 'Failed to launch installation process.');
            }
        } catch (\Throwable $e) {
            $this->installProcessLaunched = false;
            $this->isInstalling = false;
            $this->installationProgress['error'] = 'Gagal memulai proses instalasi: '.$e->getMessage();
            $this->installationService->storeProgress(
                $this->installationProgress['percent'],
                $this->installationProgress['message'],
                $this->installationProgress['error'],
                false
            );
        }
    }

    private function resolvePhpBinary(): string
    {
        $binary = PHP_BINARY;

        if (PHP_SAPI !== 'cli' || str_contains($binary, 'php-fpm') || str_contains($binary, 'php-cgi')) {
            $candidates = $this->getPhpBinaryCandidates();

            foreach ($candidates as $candidate) {
                if ($candidate === 'php') {
                    return $candidate;
                }

                if (is_file($candidate) && is_executable($candidate)) {
                    return $candidate;
                }
            }
        }

        return $binary;
    }

    private function getPhpBinaryCandidates(): array
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return [
                'C:\\xampp\\php\\php.exe',
                'C:\\laragon\\bin\\php\\php.exe',
                'C:\\php\\php.exe',
                'php.exe',
                'php',
            ];
        }

        return [
            '/usr/bin/php',
            '/usr/local/bin/php',
            '/bin/php',
            '/usr/bin/php8.4',
            '/usr/bin/php8.3',
            '/usr/bin/php8.2',
            'php',
        ];
    }

    private function escapeWindowsBinary(string $binary): string
    {
        if ($binary === 'php' || $binary === 'php.exe') {
            return $binary;
        }

        return '"'.str_replace('"', '""', $binary).'"';
    }

    private function escapeWindowsArgument(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }

    private function detectStalledInstallation(array $cached): void
    {
        if (! $this->isInstalling || $cached['complete'] || $cached['error']) {
            return;
        }

        if ($this->installProcessStartedAt === null) {
            return;
        }

        $elapsed = time() - $this->installProcessStartedAt;

        if ($elapsed < 15) {
            return;
        }

        if (! $this->installationService->isInstallationRunning() && $cached['percent'] === 0) {
            $this->installationProgress['error'] = 'Proses instalasi tidak berjalan. Periksa izin eksekusi PHP atau lihat log installer-worker.log.';
            $this->isInstalling = false;

            $this->installationService->storeProgress(
                $this->installationProgress['percent'],
                $this->installationProgress['message'],
                $this->installationProgress['error'],
                false
            );
        }
    }

    /**
     * Retry installation after a failure.
     */
    public function retryInstallation(): void
    {
        // Reset error state but keep logs for context
        $this->installationProgress['error'] = null;
        $this->installationProgress['logs'][] = '--- Retrying installation ---';

        // Clear previous state
        $this->installationService->clearProgress();
        $this->installationService->markInstallationStopped();

        // Start installation again
        $this->startInstallation();
    }

    public function addFaculty(): void
    {
        $this->institutionForm->addFaculty();
    }

    public function removeFaculty(int $index): void
    {
        $this->institutionForm->removeFaculty($index);
    }

    /**
     * Check if a step is completed.
     */
    public function isStepCompleted(int $step): bool
    {
        return isset($this->completedSteps[$step]) && $this->completedSteps[$step];
    }

    /**
     * Get options for environment config selects.
     */
    public function getEnvironmentOptions(): array
    {
        return EnvironmentConfigForm::getOptions();
    }

    public function render(): View
    {
        return view('installer.wizard', [
            'envOptions' => $this->getEnvironmentOptions(),
        ]);
    }
}
