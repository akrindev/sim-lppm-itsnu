<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Installer\InstallationService;
use Illuminate\Console\Command;

class RunInstallationCommand extends Command
{
    protected $signature = 'app:install-run';

    protected $description = 'Run installer steps from cached configuration.';

    public function handle(InstallationService $installationService): int
    {
        if ($installationService->isInstallationRunning()) {
            $this->info('Installation is already running.');

            return 0;
        }

        $config = $installationService->getInstallationConfig();

        if (empty($config)) {
            $installationService->storeProgress(0, 'Installation config not found.', 'Installation config not found.', false);
            $this->error('Installation config not found.');

            return 1;
        }

        $installationService->markInstallationRunning();
        $installationService->storeProgress(0, 'Starting installation process...');

        try {
            $installationService->runInstallation(
                $config,
                function (int $percent, string $message): void {
                    // Progress is stored by the service for web polling.
                }
            );

            $installationService->clearInstallationConfig();

            return 0;
        } catch (\Throwable $e) {
            $installationService->storeProgress(0, 'Installation failed.', $e->getMessage(), false);
            $this->error('Installation failed: '.$e->getMessage());

            return 1;
        } finally {
            $installationService->markInstallationStopped();
        }
    }
}
