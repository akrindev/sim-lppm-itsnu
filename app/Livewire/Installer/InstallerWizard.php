<?php

declare(strict_types=1);

namespace App\Livewire\Installer;

use App\Livewire\Forms\Installer\AdminAccountForm;
use App\Livewire\Forms\Installer\DatabaseConfigForm;
use App\Livewire\Forms\Installer\InstitutionSetupForm;
use App\Services\Installer\InstallationService;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('installer.layout')]
class InstallerWizard extends Component
{
    public int $currentStep = 1;

    public int $totalSteps = 5;

    public DatabaseConfigForm $databaseForm;

    public InstitutionSetupForm $institutionForm;

    public AdminAccountForm $adminForm;

    public array $environmentChecks = [];

    public bool $environmentPassed = false;

    public array $installationProgress = [
        'percent' => 0,
        'message' => '',
        'logs' => [],
        'complete' => false,
        'error' => null,
    ];

    public bool $isInstalling = false;

    protected InstallationService $installationService;

    public function boot(InstallationService $installationService): void
    {
        $this->installationService = $installationService;
    }

    public function mount(): void
    {
        // Check environment on load
        $this->checkEnvironment();

        // Set default values from current env if exists
        $this->databaseForm->host = env('DB_HOST', '127.0.0.1');
        $this->databaseForm->port = env('DB_PORT', '3306');
        $this->databaseForm->database = env('DB_DATABASE', 'lppm_itsnu');
        $this->databaseForm->username = env('DB_USERNAME', 'root');
    }

    public function checkEnvironment(): void
    {
        $this->environmentChecks = $this->installationService->checkEnvironment();
        $this->environmentPassed = $this->installationService->allEnvironmentChecksPass();
    }

    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->validateCurrentStep();
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        // Only allow going to completed steps or next step
        if ($step <= $this->currentStep || $step === $this->currentStep + 1) {
            $this->currentStep = $step;
        }
    }

    public function testDatabaseConnection(): void
    {
        $result = $this->databaseForm->testConnection();

        if ($result['success']) {
            $this->dispatch('notify', type: 'success', message: $result['message']);
        } else {
            $this->dispatch('notify', type: 'error', message: $result['message']);
        }
    }

    public function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            2 => $this->databaseForm->validate(),
            3 => $this->institutionForm->validate(),
            4 => $this->adminForm->validate(),
            default => null,
        };
    }

    public function startInstallation(): void
    {
        if ($this->isInstalling) {
            return;
        }

        $this->isInstalling = true;
        $this->installationProgress = [
            'percent' => 0,
            'message' => 'Starting installation...',
            'logs' => [],
            'complete' => false,
            'error' => null,
        ];

        try {
            $config = [
                ...$this->databaseForm->getEnvConfig(),
                'APP_NAME' => $this->institutionForm->institutionShortName,
                'APP_URL' => $this->institutionForm->website,
                'institution' => $this->institutionForm->getInstitutionData(),
                'faculties' => $this->institutionForm->getFacultiesData(),
                'admin' => $this->adminForm->getAdminData(),
            ];

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

    public function render(): View
    {
        return view('installer.wizard');
    }
}
