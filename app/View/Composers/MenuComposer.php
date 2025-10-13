<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view): void
    {
        $view->with('headerMenuItems', $this->menuItems(Auth::user()));
    }

    protected function menuItems(?User $user): array
    {
        $items = [
            [
                'title' => 'Dashboard',
                'icon' => 'home',
                'route' => 'dashboard',
            ],
            [
                'title' => 'Proposal',
                'icon' => 'file-text',
                'route' => 'proposals.index',
                'href' => '/proposals',
            ],
            [
                'title' => 'Laporan Penelitian',
                'icon' => 'report',
                'route' => 'reports.research',
                'href' => '/laporan-penelitian',
                'roles' => ['admin lppm', 'rektor'],
            ],
            [
                'title' => 'Kelola User',
                'icon' => 'users',
                'route' => 'users.index',
                'href' => '/users',
                'roles' => ['admin lppm'],
            ],
        ];

        return array_values(array_filter(array_map(
            fn (array $item) => $this->formatItem($item, $user),
            $items,
        )));
    }

    protected function formatItem(array $item, ?User $user): ?array
    {
        $allowedRoles = $item['roles'] ?? null;

        if ($allowedRoles !== null && (! $user || ! $user->hasAnyRole($allowedRoles))) {
            return null;
        }

        $routeName = $item['route'] ?? null;

        return [
            'type' => 'link',
            'title' => $item['title'],
            'href' => $this->resolveHref($item),
            'icon' => $item['icon'] ?? null,
            'active' => $this->isActive($item, $routeName),
        ];
    }

    protected function resolveHref(array $item): string
    {
        $routeName = $item['route'] ?? null;

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        $href = $item['href'] ?? null;

        if (empty($href) || $href === '#') {
            return '#';
        }

        if (str_starts_with($href, 'http')) {
            return $href;
        }

        return url($href);
    }

    protected function isActive(array $item, ?string $routeName): bool
    {
        $patterns = $item['active'] ?? array_filter([$routeName]);

        foreach ($patterns as $pattern) {
            if ($pattern && request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }
}
