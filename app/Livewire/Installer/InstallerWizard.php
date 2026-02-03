<?php

declare(strict_types=1);

namespace App\Livewire\Installer;

use App\Livewire\Forms\Installer\AdminAccountForm;
use App\Livewire\Forms\Installer\DatabaseConfigForm;
use App\Livewire\Forms\Installer\EnvironmentConfigForm;
use App\Livewire\Forms\Installer\InstitutionSetupForm;
use App\Services\Installer\InstallationService;
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

        $this->isInstalling = true;
        $this->installationProgress = [
            'percent' => 0,
            'message' => 'Starting installation...',
            'logs' => [],
            'complete' => false,
            'error' => null,
        ];

        try {
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

            // Set APP_NAME explicitly (institution short name)
            $config['APP_NAME'] = $this->institutionForm->institutionShortName;

            // Add non-ENV data for seeders (these are NOT env keys)
            $config['institution'] = $this->institutionForm->getInstitutionData();
            $config['faculties'] = $this->institutionForm->getFacultiesData();
            $config['admin'] = $this->adminForm->getAdminData();

            $this->installationService->runInstallation(
                $config,
                function (int $percent, string $message) {
                    $this->installationProgress['percent'] = $percent;
                    $this->installationProgress['message'] = $message;
                    $this->installationProgress['logs'][] = "[{$percent}%] {$message}";

                    // Stream updates to browser
                    $this->stream(to: 'installationProgress', content: '');
                }
            );

            $this->installationProgress['complete'] = true;
            $this->installationProgress['percent'] = 100;
            $this->installationProgress['message'] = 'Installation complete!';

            $this->dispatch('installation-complete');
        } catch (\Exception $e) {
            $this->installationProgress['error'] = $e->getMessage();
            $this->installationProgress['logs'][] = "ERROR: {$e->getMessage()}";
            $this->dispatch('notify', type: 'error', message: 'Installation failed: '.$e->getMessage());
        } finally {
            $this->isInstalling = false;
        }
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
