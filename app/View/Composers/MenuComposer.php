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
            // Dashboard - available for all roles
            [
                'title' => 'Dashboard',
                'icon' => 'home',
                'route' => 'dashboard',
            ],
            // Dosen menu
            [
                'title' => 'Penelitian',
                'icon' => 'puzzle',
                'roles' => ['dosen', 'kepala lppm', 'admin lppm', 'rektor'],
                'children' => [
                    [
                        'title' => 'Usulan',
                        'icon' => 'file-text',
                        'route' => 'research.proposal.index',
                    ],
                    [
                        'title' => 'Perbaikan Usulan',
                        'icon' => 'checkbox',
                        'route' => 'research.proposal-revision.index',
                    ],
                    [
                        'title' => 'Laporan Kemajuan',
                        'icon' => 'report',
                        'route' => 'research.progress-report.index',
                    ],
                    [
                        'title' => 'Laporan Akhir',
                        'icon' => 'file-text',
                        'route' => 'research.final-report.index',
                    ],
                    [
                        'title' => 'Catatan Harian',
                        'icon' => 'layout-2',
                        'route' => 'research.daily-note.index',
                    ],
                ],
            ],
            [
                'title' => 'Pengabdian',
                'icon' => 'gift',
                'roles' => ['dosen', 'kepala lppm', 'admin lppm', 'rektor'],
                'children' => [
                    [
                        'title' => 'Usulan',
                        'icon' => 'file-text',
                        'route' => 'community-service.proposal.index',
                    ],
                    [
                        'title' => 'Perbaikan Usulan',
                        'icon' => 'checkbox',
                        'route' => 'community-service.proposal-revision.index',
                    ],
                    [
                        'title' => 'Laporan Kemajuan',
                        'icon' => 'report',
                        'route' => 'community-service.progress-report.index',
                    ],
                    [
                        'title' => 'Laporan Akhir',
                        'icon' => 'file-text',
                        'route' => 'community-service.final-report.index',
                    ],
                    [
                        'title' => 'Catatan Harian',
                        'icon' => 'layout-2',
                        'route' => 'community-service.daily-note.index',
                    ],
                ],
            ],
            // Kepala LPPM menu
            // [
            //     'title' => 'Persetujuan Usulan',
            //     'icon' => 'checkbox',
            //     'route' => 'proposals.index',
            //     'href' => '/proposals',
            //     'roles' => ['kepala lppm'],
            // ],
            // Dekan menu
            [
                'title' => 'Persetujuan Dekan',
                'icon' => 'clipboard-check',
                'route' => 'dekan.proposals.index',
                'roles' => ['dekan'],
            ],
            // Kepala LPPM menus
            [
                'title' => 'Persetujuan Awal',
                'icon' => 'checkbox',
                'route' => 'kepala-lppm.initial-approval',
                'roles' => ['kepala lppm', 'rektor'],
            ],
            [
                'title' => 'Persetujuan Akhir',
                'icon' => 'circle-check',
                'route' => 'kepala-lppm.final-decision',
                'roles' => ['kepala lppm', 'rektor'],
            ],
            // Admin LPPM - Reviewer Assignment
            [
                'title' => 'Penugasan Reviewer',
                'icon' => 'user-check',
                'route' => 'admin-lppm.assign-reviewers',
                'roles' => ['admin lppm'],
            ],
            // Reviewer menu
            [
                'title' => 'Review Penelitian',
                'icon' => 'lifebuoy',
                'route' => 'review.research',
                'roles' => ['reviewer'],
            ],
            [
                'title' => 'Review Pengabdian',
                'icon' => 'lifebuoy',
                'route' => 'review.community-service',
                'roles' => ['reviewer'],
            ],
            // Laporan - Reports menu
            [
                'title' => 'Laporan',
                'icon' => 'file-analytics',
                'roles' => ['admin lppm', 'rektor'],
                'children' => [
                    [
                        'title' => 'Laporan Penelitian',
                        'icon' => 'report',
                        'route' => 'reports.research',
                    ],
                    [
                        'title' => 'Laporan Luaran',
                        'icon' => 'award',
                        'route' => 'reports.outputs',
                    ],
                ],
            ],
            // kelola pengguna - admin lppm
            [
                'title' => 'Kelola Pengguna',
                'icon' => 'users',
                'roles' => ['admin lppm'],
                'children' => [
                    [
                        'title' => 'Daftar Pengguna',
                        'icon' => 'list',
                        'route' => 'users.index',
                    ],
                    [
                        'title' => 'Buat Pengguna',
                        'icon' => 'user-plus',
                        'route' => 'users.create',
                    ],
                ],
            ],
            // settings:
            // - master data - admin lppm
            [
                'title' => 'Pengaturan',
                'icon' => 'settings',
                'roles' => ['admin lppm'],
                'children' => [
                    [
                        'title' => 'Master Data',
                        'icon' => 'layers',
                        'route' => 'settings.master-data',
                    ],
                    [
                        'title' => 'Jadwal Proposal',
                        'icon' => 'calendar-time',
                        'route' => 'settings.proposal-schedule',
                    ],
                    [
                        'title' => 'Template Proposal',
                        'icon' => 'file-download',
                        'route' => 'settings.proposal-template',
                    ],
                ],
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

        // Format child items if they exist
        $children = null;
        if (isset($item['children']) && is_array($item['children'])) {
            $children = array_values(array_filter(array_map(
                fn (array $child) => $this->formatDropdownItem($child, $user),
                $item['children'],
            )));
        }

        // Check if any child route is active
        $hasActiveChild = false;
        if ($children) {
            foreach ($children as $child) {
                if ($child['active'] ?? false) {
                    $hasActiveChild = true;
                    break;
                }
            }
        }

        $formatted = [
            'type' => isset($item['children']) && count($children ?? []) > 0 ? 'dropdown' : 'link',
            'title' => $item['title'],
            'href' => $this->resolveHref($item),
            'icon' => $item['icon'] ?? null,
            'active' => $this->isActive($item, $routeName) || $hasActiveChild,
        ];

        if ($children) {
            $formatted['dropdown'] = [
                'auto_close' => 'outside',
                'items' => $children,
            ];
            $formatted['children'] = $children;
        }

        return $formatted;
    }

    protected function formatDropdownItem(array $item, ?User $user): ?array
    {
        $allowedRoles = $item['roles'] ?? null;

        if ($allowedRoles !== null && (! $user || ! $user->hasAnyRole($allowedRoles))) {
            return null;
        }

        $routeName = $item['route'] ?? null;

        return [
            'label' => $item['title'],
            'href' => $this->resolveHref($item),
            'prefix_icon' => $item['icon'] ?? null,
            'prefix_icon_class' => 'icon icon-2 icon-inline me-1',
            'route' => $routeName,
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
        $patterns = (array) ($item['active'] ?? array_filter([$routeName]));

        foreach ($patterns as $pattern) {
            if (empty($pattern)) {
                continue;
            }

            if (request()->routeIs($pattern)) {
                return true;
            }

            // For index routes, also check all other actions in the same resource
            if (str_ends_with($pattern, '.index')) {
                $resourceRoute = substr($pattern, 0, -6);

                if (request()->routeIs($resourceRoute . '.*')) {
                    return true;
                }
            }
        }

        return false;
    }
}
