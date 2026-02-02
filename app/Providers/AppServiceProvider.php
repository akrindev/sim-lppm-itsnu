<?php

namespace App\Providers;

use App\Models\Proposal;
use App\Observers\ProposalObserver;
use App\View\Composers\MenuComposer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // CRITICAL: Set file-based drivers BEFORE any service tries to use database
        // This must happen in register() before boot() is called
        if (! $this->isInstalled()) {
            config([
                'cache.default' => 'file',
                'session.driver' => 'file',
                'queue.default' => 'sync',
                'telescope.enabled' => false,
                'debugbar.enabled' => false,
            ]);

            return;
        }

        // Only register Telescope when app is installed
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Only run observers when installed
        if ($this->isInstalled()) {
            View::composer('components.layouts.header', MenuComposer::class);
            Proposal::observe(ProposalObserver::class);
        }
    }

    private function isInstalled(): bool
    {
        // Only check lock file - this is the definitive marker
        $lockFile = storage_path('app/.installed');

        return File::exists($lockFile);
    }
}
