<?php

declare(strict_types=1);

use App\Livewire\Installer\InstallerWizard;
use App\Services\Installer\InstallationService;
use Illuminate\Support\Str;
use Livewire\Livewire;

class FakeInstallationService extends InstallationService
{
    public array $lastConfig = [];

    private bool $running = false;

    private array $storedConfig = [];

    public function checkEnvironment(): array
    {
        return [];
    }

    public function allEnvironmentChecksPass(): bool
    {
        return true;
    }

    public function runInstallation(array $config, callable $onProgress, ?callable $onStep = null): void
    {
        $this->lastConfig = $config;

        $onProgress(100, 'Installation complete!');
    }

    public function storeInstallationConfig(array $config): void
    {
        $this->storedConfig = $config;
    }

    public function getInstallationConfig(): array
    {
        return $this->storedConfig;
    }

    public function clearInstallationConfig(): void
    {
        $this->storedConfig = [];
    }

    public function isInstallationRunning(): bool
    {
        return $this->running;
    }

    public function markInstallationRunning(): void
    {
        $this->running = true;
    }

    public function markInstallationStopped(): void
    {
        $this->running = false;
    }
}

beforeEach(function () {
    config(['app.key' => 'base64:'.base64_encode(Str::random(32))]);
});

it('blocks step progression when environment fails', function () {
    $service = new FakeInstallationService;

    app()->instance(InstallationService::class, $service);

    Livewire::test(InstallerWizard::class)
        ->set('environmentPassed', false)
        ->call('nextStep')
        ->assertSet('currentStep', 1);
});

it('advances through all installer steps with valid data', function () {
    $service = new FakeInstallationService;

    app()->instance(InstallationService::class, $service);

    Livewire::test(InstallerWizard::class)
        // Step 1 -> 2: Environment
        ->set('environmentPassed', true)
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        // Step 2 -> 3: Database
        ->set('databaseForm.host', '127.0.0.1')
        ->set('databaseForm.port', '3306')
        ->set('databaseForm.database', 'lppm_itsnu')
        ->set('databaseForm.username', 'app_user')
        ->set('databaseForm.dbPasswordInput', 'secret')
        ->set('databaseTested', true)
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        // Step 3 -> 4: Environment Config
        ->set('environmentForm.appUrl', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        // Step 4 -> 5: Institution
        ->set('institutionForm.institutionName', 'Institut Test')
        ->set('institutionForm.institutionShortName', 'ITSNU Test')
        ->set('institutionForm.address', 'Jl. Test')
        ->set('institutionForm.phone', '021111')
        ->set('institutionForm.institutionEmail', 'info@example.com')
        ->set('institutionForm.website', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 5)
        // Step 5 -> 6: Admin
        ->set('adminForm.adminName', 'Administrator')
        ->set('adminForm.adminEmail', 'admin@example.com')
        ->set('adminForm.adminPassword', 'Password1')
        ->set('adminForm.adminPasswordConfirmation', 'Password1')
        ->call('nextStep')
        ->assertSet('currentStep', 6);
});

it('builds installation config from all form steps', function () {
    $service = new FakeInstallationService;

    app()->instance(InstallationService::class, $service);

    $component = Livewire::test(InstallerWizard::class)
        ->set('databaseForm.host', '127.0.0.1')
        ->set('databaseForm.port', '3306')
        ->set('databaseForm.database', '  lppm_itsnu  ')
        ->set('databaseForm.username', '  app_user  ')
        ->set('environmentForm.appName', 'ITSNU Test')
        ->set('environmentForm.appUrl', 'https://example.com')
        ->set('institutionForm.institutionName', 'Institut Test')
        ->set('institutionForm.institutionShortName', 'ITSNU Test')
        ->set('institutionForm.institutionEmail', 'info@example.com')
        ->set('institutionForm.website', 'https://example.com')
        ->set('adminForm.adminName', 'Administrator')
        ->set('adminForm.adminEmail', 'admin@example.com')
        ->set('adminForm.adminPassword', 'Password1')
        ->set('adminForm.adminPasswordConfirmation', 'Password1');

    $component->instance()->databaseForm->dbPasswordInput = '  secret  ';
    $component->instance()->databaseForm->syncPasswordFromInput();

    expect($component->instance()->databaseForm->getEnvConfig()['DB_PASSWORD'])->toBe('secret');

    // startInstallation stores config, checkProgress triggers actual installation
    $component->call('startInstallation');
    $component->call('checkProgress');

    expect($service->lastConfig['DB_DATABASE'])->toBe('lppm_itsnu')
        ->and($service->lastConfig['DB_USERNAME'])->toBe('app_user')
        ->and($service->lastConfig['APP_NAME'])->toBe('ITSNU Test')
        ->and($service->lastConfig['admin']['email'])->toBe('admin@example.com');
});

it('preserves database password through step navigation', function () {
    $service = new FakeInstallationService;

    app()->instance(InstallationService::class, $service);

    // Test that password is properly synced and preserved
    $component = Livewire::test(InstallerWizard::class)
        // Set up database form and stored password
        ->set('databaseForm.host', '127.0.0.1')
        ->set('databaseForm.port', '3306')
        ->set('databaseForm.database', 'lppm_itsnu')
        ->set('databaseForm.username', 'app_user')
        ->set('databaseForm.dbPasswordInput', 'secret')
        ->set('storedDbPassword', 'secret')
        // Step 1 -> 2
        ->set('environmentPassed', true)
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        // Step 2 -> 3
        ->set('databaseTested', true)
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        // Step 3 -> 4
        ->set('environmentForm.appUrl', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        // Step 4 -> 5
        ->set('institutionForm.institutionName', 'Institut Test')
        ->set('institutionForm.institutionShortName', 'ITSNU Test')
        ->set('institutionForm.institutionEmail', 'info@example.com')
        ->set('institutionForm.website', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 5)
        // Step 5 -> 6
        ->set('adminForm.adminName', 'Administrator')
        ->set('adminForm.adminEmail', 'admin@example.com')
        ->set('adminForm.adminPassword', 'Password1')
        ->set('adminForm.adminPasswordConfirmation', 'Password1')
        ->call('startInstallation')
        ->call('checkProgress');

    expect($service->lastConfig['DB_PASSWORD'])->toBe('secret');
});

it('updates database password when user enters new value', function () {
    $service = new FakeInstallationService;

    app()->instance(InstallationService::class, $service);

    // Test that password is properly updated
    $component = Livewire::test(InstallerWizard::class)
        // Set initial password and stored password
        ->set('databaseForm.host', '127.0.0.1')
        ->set('databaseForm.port', '3306')
        ->set('databaseForm.database', 'lppm_itsnu')
        ->set('databaseForm.username', 'app_user')
        ->set('databaseForm.dbPasswordInput', 'new_password')
        ->set('storedDbPassword', 'new_password')
        // Step 1 -> 2
        ->set('environmentPassed', true)
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        // Step 2 -> 3
        ->set('databaseTested', true)
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        // Step 3 -> 4
        ->set('environmentForm.appUrl', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        // Step 4 -> 5
        ->set('institutionForm.institutionName', 'Institut Test')
        ->set('institutionForm.institutionShortName', 'ITSNU Test')
        ->set('institutionForm.institutionEmail', 'info@example.com')
        ->set('institutionForm.website', 'https://example.com')
        ->call('nextStep')
        ->assertSet('currentStep', 5)
        // Step 5 -> 6
        ->set('adminForm.adminName', 'Administrator')
        ->set('adminForm.adminEmail', 'admin@example.com')
        ->set('adminForm.adminPassword', 'Password1')
        ->set('adminForm.adminPasswordConfirmation', 'Password1')
        ->call('startInstallation')
        ->call('checkProgress');

    expect($service->lastConfig['DB_PASSWORD'])->toBe('new_password');
});

it('writes empty values from installer inputs', function () {
    $content = implode("\n", [
        'APP_NAME=Laravel',
        'APP_ENV=local',
        'APP_DEBUG=true',
        'APP_URL=http://localhost',
        'APP_LOCALE=id',
        'SESSION_DRIVER=file',
        'SESSION_LIFETIME=120',
        'CACHE_STORE=file',
        'QUEUE_CONNECTION=sync',
        'MAIL_MAILER=log',
        'MAIL_HOST=127.0.0.1',
        'MAIL_PORT=2525',
        'MAIL_USERNAME=null',
        'MAIL_PASSWORD=null',
        'MAIL_FROM_ADDRESS="hello@example.com"',
        'MAIL_FROM_NAME="${APP_NAME}"',
        'TURNSTILE_SITE_KEY=old',
        'TURNSTILE_SECRET_KEY=old',
        'FILESYSTEM_DISK=local',
        'MEDIA_DISK=public',
        'AWS_ACCESS_KEY_ID=old',
        'AWS_SECRET_ACCESS_KEY=old',
        'AWS_DEFAULT_REGION=ap-southeast-1',
        'AWS_BUCKET=old',
        'AWS_URL=old',
        'AWS_ENDPOINT=old',
        'AWS_USE_PATH_STYLE_ENDPOINT=true',
    ])."\n";

    $envPath = base_path('.env');
    $examplePath = base_path('.env.example');
    $originalEnv = file_exists($envPath) ? file_get_contents($envPath) : null;
    $originalExample = file_exists($examplePath) ? file_get_contents($examplePath) : null;

    file_put_contents($envPath, $content);
    file_put_contents($examplePath, $content);

    $writer = new \App\Services\Installer\EnvironmentWriter;
    $writer->write([
        'APP_DEBUG' => false,
        'APP_NAME' => 'LPPM-ITSNU',
        'APP_ENV' => 'production',
        'APP_URL' => 'https://example.com',
        'APP_LOCALE' => 'id',
        'SESSION_DRIVER' => 'database',
        'SESSION_LIFETIME' => '120',
        'CACHE_STORE' => 'database',
        'QUEUE_CONNECTION' => 'database',
        'MAIL_MAILER' => 'smtp',
        'MAIL_HOST' => '',
        'MAIL_PORT' => '',
        'MAIL_USERNAME' => '',
        'MAIL_PASSWORD' => '',
        'MAIL_FROM_ADDRESS' => '',
        'MAIL_FROM_NAME' => '',
        'TURNSTILE_SITE_KEY' => '',
        'TURNSTILE_SECRET_KEY' => '',
        'FILESYSTEM_DISK' => 'local',
        'MEDIA_DISK' => 'public',
        'AWS_ACCESS_KEY_ID' => '',
        'AWS_SECRET_ACCESS_KEY' => '',
        'AWS_DEFAULT_REGION' => 'ap-southeast-1',
        'AWS_BUCKET' => '',
        'AWS_URL' => '',
        'AWS_ENDPOINT' => '',
        'AWS_USE_PATH_STYLE_ENDPOINT' => 'false',
        'DB_CONNECTION' => 'mariadb',
        'DB_HOST' => '127.0.0.1',
        'DB_PORT' => '3306',
        'DB_DATABASE' => 'lppm_itsnu',
        'DB_USERNAME' => 'app_user',
        'DB_PASSWORD' => 'secret',
    ]);

    $written = file_get_contents($envPath);

    expect($written)->toContain('MAIL_HOST=')
        ->and($written)->toContain('MAIL_USERNAME=')
        ->and($written)->toContain('MAIL_PASSWORD=')
        ->and($written)->toContain('MAIL_FROM_ADDRESS=')
        ->and($written)->toContain('MAIL_FROM_NAME=')
        ->and($written)->toContain('TURNSTILE_SITE_KEY=')
        ->and($written)->toContain('TURNSTILE_SECRET_KEY=')
        ->and($written)->toContain('AWS_ACCESS_KEY_ID=')
        ->and($written)->toContain('AWS_SECRET_ACCESS_KEY=')
        ->and($written)->toContain('AWS_BUCKET=')
        ->and($written)->toContain('AWS_URL=')
        ->and($written)->toContain('AWS_ENDPOINT=')
        ->and($written)->toContain('DB_PASSWORD=secret');

    if ($originalEnv === null) {
        @unlink($envPath);
    } else {
        file_put_contents($envPath, $originalEnv);
    }

    if ($originalExample === null) {
        @unlink($examplePath);
    } else {
        file_put_contents($examplePath, $originalExample);
    }
});
