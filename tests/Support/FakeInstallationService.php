<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Services\Installer\InstallationService;

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
