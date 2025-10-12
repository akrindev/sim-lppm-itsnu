<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view): void
    {
        $view->with('headerMenuItems', $this->menuItems());
    }

    protected function menuItems(): array
    {
        return [
            [
                'type' => 'link',
                'title' => 'Home',
                'href' => './',
                'icon' => 'home',
            ],
            [
                'type' => 'dropdown',
                'title' => 'Interface',
                'icon' => 'package',
                'active' => true,
                'toggle_href' => '#navbar-base',
                'dropdown' => [
                    'layout' => 'columns',
                    'columns' => [
                        [
                            [
                                'type' => 'link',
                                'label' => 'Accordion',
                                'href' => './accordion.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Alerts',
                                'href' => './alerts.html',
                            ],
                            [
                                'type' => 'dropend',
                                'label' => 'Authentication',
                                'href' => '#sidebar-authentication',
                                'children' => [
                                    [
                                        'type' => 'link',
                                        'label' => 'Sign in',
                                        'href' => './sign-in.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Sign in link',
                                        'href' => './sign-in-link.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Sign in with illustration',
                                        'href' => './sign-in-illustration.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Sign in with cover',
                                        'href' => './sign-in-cover.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Sign up',
                                        'href' => './sign-up.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Forgot password',
                                        'href' => './forgot-password.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Terms of service',
                                        'href' => './terms-of-service.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Lock screen',
                                        'href' => './auth-lock.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => '2 step verification',
                                        'href' => './2-step-verification.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => '2 step verification code',
                                        'href' => './2-step-verification-code.html',
                                    ],
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Avatars',
                                'href' => './avatars.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Badges',
                                'href' => './badges.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Blank page',
                                'href' => './blank.html',
                                'class' => 'dropdown-item active',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Buttons',
                                'href' => './buttons.html',
                            ],
                            [
                                'type' => 'dropend',
                                'label' => 'Cards',
                                'href' => '#sidebar-cards',
                                'children' => [
                                    [
                                        'type' => 'link',
                                        'label' => 'Sample cards',
                                        'href' => './cards.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Card actions',
                                        'href' => './card-actions.html',
                                        'badge' => [
                                            'text' => 'New',
                                            'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                        ],
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Cards Masonry',
                                        'href' => './cards-masonry.html',
                                    ],
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Carousel',
                                'href' => './carousel.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Colors',
                                'href' => './colors.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Data grid',
                                'href' => './datagrid.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Dropdowns',
                                'href' => './dropdowns.html',
                            ],
                            [
                                'type' => 'dropend',
                                'label' => 'Error pages',
                                'href' => '#sidebar-error',
                                'children' => [
                                    [
                                        'type' => 'link',
                                        'label' => '404 page',
                                        'href' => './error-404.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => '500 page',
                                        'href' => './error-500.html',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Maintenance page',
                                        'href' => './error-maintenance.html',
                                    ],
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Lists',
                                'href' => './lists.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Modals',
                                'href' => './modals.html',
                            ],
                        ],
                        [
                            [
                                'type' => 'link',
                                'label' => 'Markdown',
                                'href' => './markdown.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Navigation',
                                'href' => './navigation.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Offcanvas',
                                'href' => './offcanvas.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Pagination',
                                'href' => './pagination.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Placeholder',
                                'href' => './placeholder.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Segmented control',
                                'href' => './segmented-control.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Scroll spy',
                                'href' => './scroll-spy.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Social icons',
                                'href' => './social-icons.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Stars rating',
                                'href' => './stars-rating.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Steps',
                                'href' => './steps.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Tables',
                                'href' => './tables.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Tabs',
                                'href' => './tabs.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Tags',
                                'href' => './tags.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Toasts',
                                'href' => './toasts.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Typography',
                                'href' => './typography.html',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Forms',
                'icon' => 'checkbox',
                'toggle_href' => '#navbar-form',
                'dropdown' => [
                    'layout' => 'list',
                    'items' => [
                        [
                            'type' => 'link',
                            'label' => 'Form elements',
                            'href' => './form-elements.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Form layouts',
                            'href' => './form-layout.html',
                            'badge' => [
                                'text' => 'New',
                                'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Extra',
                'icon' => 'star',
                'toggle_href' => '#navbar-extra',
                'dropdown' => [
                    'layout' => 'columns',
                    'columns' => [
                        [
                            [
                                'type' => 'link',
                                'label' => 'Activity',
                                'href' => './activity.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Chat',
                                'href' => './chat.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Cookie banner',
                                'href' => './cookie-banner.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Empty page',
                                'href' => './empty.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'FAQ',
                                'href' => './faq.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Gallery',
                                'href' => './gallery.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Invoice',
                                'href' => './invoice.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Job listing',
                                'href' => './job-listing.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'License',
                                'href' => './license.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Logs',
                                'href' => './logs.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Marketing',
                                'href' => './marketing/index.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Music',
                                'href' => './music.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Page loader',
                                'href' => './page-loader.html',
                            ],
                        ],
                        [
                            [
                                'type' => 'link',
                                'label' => 'Photogrid',
                                'href' => './photogrid.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Pricing cards',
                                'href' => './pricing.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Pricing table',
                                'href' => './pricing-table.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Search results',
                                'href' => './search-results.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Settings',
                                'href' => './settings.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Signatures',
                                'href' => './signatures.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Tasks',
                                'href' => './tasks.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Text features',
                                'href' => './text-features.html',
                                'badge' => [
                                    'text' => 'New',
                                    'class' => 'bg-green-lt ms-auto text-uppercase badge badge-sm',
                                ],
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Trial ended',
                                'href' => './trial-ended.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Uptime monitor',
                                'href' => './uptime.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Users',
                                'href' => './users.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Widgets',
                                'href' => './widgets.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Wizard',
                                'href' => './wizard.html',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Layout',
                'icon' => 'layout-2',
                'toggle_href' => '#navbar-layout',
                'dropdown' => [
                    'layout' => 'columns',
                    'columns' => [
                        [
                            [
                                'type' => 'link',
                                'label' => 'Boxed',
                                'href' => './layout-boxed.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Combined',
                                'href' => './layout-combo.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Condensed',
                                'href' => './layout-condensed.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Fluid',
                                'href' => './layout-fluid.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Fluid vertical',
                                'href' => './layout-fluid-vertical.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Horizontal',
                                'href' => './layout-horizontal.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Navbar dark',
                                'href' => './layout-navbar-dark.html',
                            ],
                        ],
                        [
                            [
                                'type' => 'link',
                                'label' => 'Navbar overlap',
                                'href' => './layout-navbar-overlap.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Navbar sticky',
                                'href' => './layout-navbar-sticky.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Right vertical',
                                'href' => './layout-vertical-right.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'RTL mode',
                                'href' => './layout-rtl.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Vertical',
                                'href' => './layout-vertical.html',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'Vertical transparent',
                                'href' => './layout-vertical-transparent.html',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Plugins',
                'icon' => 'puzzle',
                'toggle_href' => '#navbar-plugins',
                'dropdown' => [
                    'layout' => 'list',
                    'items' => [
                        [
                            'type' => 'link',
                            'label' => 'Charts',
                            'href' => './charts.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Color picker',
                            'href' => './colorpicker.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Datatables',
                            'href' => './datatables.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Dropzone',
                            'href' => './dropzone.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Fullcalendar',
                            'href' => './fullcalendar.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Inline player',
                            'href' => './inline-player.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Lightbox',
                            'href' => './lightbox.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Map',
                            'href' => './maps.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Map fullsize',
                            'href' => './map-fullsize.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Map vector',
                            'href' => './maps-vector.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Turbo loader',
                            'href' => './turbo-loader.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'WYSIWYG editor',
                            'href' => './wysiwyg.html',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Addons',
                'icon' => 'gift',
                'toggle_href' => '#navbar-addons',
                'dropdown' => [
                    'layout' => 'list',
                    'items' => [
                        [
                            'type' => 'link',
                            'label' => 'Icons',
                            'href' => './icons.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Emails',
                            'href' => './emails.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Flags',
                            'href' => './flags.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Illustrations',
                            'href' => './illustrations.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Payment providers',
                            'href' => './payment-providers.html',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'dropdown',
                'title' => 'Help',
                'icon' => 'lifebuoy',
                'toggle_href' => '#navbar-help',
                'dropdown' => [
                    'layout' => 'list',
                    'items' => [
                        [
                            'type' => 'link',
                            'label' => 'Documentation',
                            'href' => 'https://tabler.io/docs',
                            'attributes' => [
                                'target' => '_blank',
                                'rel' => 'noopener',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Changelog',
                            'href' => './changelog.html',
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Source code',
                            'href' => 'https://github.com/tabler/tabler',
                            'attributes' => [
                                'target' => '_blank',
                                'rel' => 'noopener',
                            ],
                        ],
                        [
                            'type' => 'link',
                            'label' => 'Sponsor project!',
                            'href' => 'https://github.com/sponsors/codecalm',
                            'class' => 'text-pink dropdown-item',
                            'prefix_icon' => 'heart',
                            'prefix_icon_class' => 'icon-inline me-1 icon icon-2',
                            'attributes' => [
                                'target' => '_blank',
                                'rel' => 'noopener',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
