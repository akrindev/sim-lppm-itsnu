<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class InstallerMiddleware
{
    private array $installerRoutes = [
        'install',
        'install.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Check if on installer route first (before checking installation status)
        $isInstallerRoute = $this->isInstallerRoute($request);

        // Check if this is a Livewire request (update, upload, etc.)
        $isLivewireRequest = $this->isLivewireRequest($request);

        // Try to check if installed, but handle errors gracefully
        try {
            $isInstalled = $this->isInstalled();
        } catch (\Exception) {
            // If we can't determine installation status (e.g., no database, no key)
            // treat it as not installed
            $isInstalled = false;
        }

        // When not installed, avoid database-backed services
        if (! $isInstalled) {
            config([
                'cache.default' => 'file',
                'session.driver' => 'file',
                'queue.default' => 'sync',
                'telescope.enabled' => false,
                'debugbar.enabled' => false,
            ]);
        }

        // If not installed and not on installer route, redirect to installer
        // But allow Livewire requests to pass through (they handle their own auth)
        if (! $isInstalled && ! $isInstallerRoute && ! $isLivewireRequest) {
            return redirect()->route('install');
        }

        // If installed and trying to access installer, redirect to home
        if ($isInstalled && $isInstallerRoute) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }

    /**
     * Check if application is installed - only check lock file.
     */
    private function isInstalled(): bool
    {
        return File::exists($this->getLockFilePath());
    }

    private function getLockFilePath(): string
    {
        return storage_path('app/.installed');
    }

    private function isInstallerRoute(Request $request): bool
    {
        $currentRoute = $request->route()?->getName() ?? '';
        $currentPath = $request->path();

        // Check if current route name matches installer routes
        foreach ($this->installerRoutes as $route) {
            if (str_ends_with($route, '.*')) {
                $prefix = substr($route, 0, -2);
                if (str_starts_with($currentRoute, $prefix)) {
                    return true;
                }
            } elseif ($currentRoute === $route) {
                return true;
            }
        }

        // Check if path starts with install
        if (str_starts_with($currentPath, 'install')) {
            return true;
        }

        return false;
    }

    /**
     * Check if this is a Livewire request (update, upload, preview, assets).
     */
    private function isLivewireRequest(Request $request): bool
    {
        $path = $request->path();

        // Livewire uses path like: livewire-{hash}/update, livewire-{hash}/livewire.js, etc.
        // Match any path starting with livewire-{hash}/
        return (bool) preg_match('/^livewire-[a-f0-9]+\//', $path);
    }
}
