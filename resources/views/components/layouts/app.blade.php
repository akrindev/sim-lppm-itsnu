<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.4.0
* @link https://tabler.io
* Copyright 2018-2025 The Tabler Authors
* Copyright 2018-2025 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ $title ?? '' }} - {{ config('app.name') }}</title>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/dist/css/tabler.min.css" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <link href="/dist/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="/dist/css/tabler-socials.min.css" rel="stylesheet" />
    <link href="/dist/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="/dist/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="/dist/css/tabler-marketing.min.css" rel="stylesheet" />
    <link href="/dist/css/tabler-themes.min.css" rel="stylesheet" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    {{-- <link href="/preview/css/demo.css" rel="stylesheet" /> --}}
    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
</head>

<body>
    <!-- BEGIN GLOBAL THEME SCRIPT -->
    <script src="/dist/js/tabler-theme.min.js" data-navigate-track></script>
    <!-- END GLOBAL THEME SCRIPT -->
    <div class="page">
        @include('components.layouts.header')
        <div class="page-wrapper">
            <!-- BEGIN PAGE HEADER -->
            @if(isset($pageTitle))
                <x-page-header :title="$pageTitle" :subtitle="$pageSubtitle ?? null">
                    {{ $pageHeader ?? '' }}
                    @isset($pageActions)
                        <x-slot:actions>
                            {{ $pageActions }}
                        </x-slot:actions>
                    @endisset
                </x-page-header>
            @endif
            <!-- END PAGE HEADER -->
            <!-- BEGIN PAGE BODY -->
            <div class="page-body">
                <div class="container-xl">
                    {{  $slot }}
                </div>
            </div>
            <!-- END PAGE BODY -->
            @include('components.layouts.footer')
        </div>
    </div>
    @include('components.layouts.app.settings')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/dist/js/tabler.min.js?{{ str()->random(4) }}" data-navigate-track></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    {{-- <script src="/preview/js/demo.min.js?1760227350" defer></script> --}}
    <!-- END DEMO SCRIPTS -->
    <!-- BEGIN PAGE SCRIPTS -->
    <script>
        /* ==========================================================================
           Tabler Theme Controls
           ========================================================================== */
        const initTablerThemeControls = () => {
            const form = document.getElementById('offcanvasSettings');
            const resetButton = document.getElementById('reset-changes');

            if (!form || !resetButton || form.dataset.tablerThemeBound === 'true') {
                return;
            }

            form.dataset.tablerThemeBound = 'true';

            const themeConfig = {
                theme: 'light',
                'theme-base': 'gray',
                'theme-font': 'sans-serif',
                'theme-primary': 'green',
                'theme-radius': '1',
            };

            const url = new URL(window.location.href);

            const syncControls = () => {
                Object.keys(themeConfig).forEach((key) => {
                    const value = window.localStorage[`tabler-${key}`] ?? themeConfig[key];
                    form.querySelectorAll(`[name="${key}"]`).forEach((radio) => {
                        radio.checked = radio.value === value;
                    });
                });
            };

            form.addEventListener('change', (event) => {
                const { name, value } = event.target;

                if (!Object.prototype.hasOwnProperty.call(themeConfig, name)) {
                    return;
                }

                document.documentElement.setAttribute(`data-bs-${name}`, value);
                window.localStorage.setItem(`tabler-${name}`, value);
                url.searchParams.set(name, value);
                window.history.pushState({}, '', url);
            });

            resetButton.addEventListener('click', () => {
                Object.keys(themeConfig).forEach((key) => {
                    document.documentElement.removeAttribute(`data-bs-${key}`);
                    window.localStorage.removeItem(`tabler-${key}`);
                    url.searchParams.delete(key);
                });

                syncControls();
                window.history.pushState({}, '', url);
            });

            syncControls();
        };
        function initTablerComponents(root = document) {
            // Dropdowns
            const Dropdown = window.bootstrap?.Dropdown;
            const Tooltip = window.bootstrap?.Tooltip;
            const Popover = window.bootstrap?.Popover;
            const Toast = window.bootstrap?.Toast;

            if (Dropdown) {
                let dropdownTriggerList = [].slice.call(root.querySelectorAll('[data-bs-toggle="dropdown"]'));
                dropdownTriggerList.forEach(dropdownTriggerEl => {
                    const options = {
                        boundary: dropdownTriggerEl.getAttribute('data-bs-boundary') === 'viewport' ? document.querySelector('.btn') : 'clippingParents'
                    };
                    // getOrCreate avoids creating multiple instances for same element
                    Dropdown.getOrCreateInstance(dropdownTriggerEl, options);
                });
            }

            // Tooltips
            if (Tooltip) {
                let tooltipTriggerList = [].slice.call(root.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(tooltipTriggerEl => {
                    const options = {
                        delay: { show: 50, hide: 50 },
                        html: (tooltipTriggerEl.getAttribute('data-bs-html') === 'true') ?? false,
                        placement: tooltipTriggerEl.getAttribute('data-bs-placement') ?? 'auto'
                    };
                    Tooltip.getOrCreateInstance(tooltipTriggerEl, options);
                });
            }

            // Popovers
            if (Popover) {
                let popoverTriggerList = [].slice.call(root.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(popoverTriggerEl => {
                    const options = {
                        delay: { show: 50, hide: 50 },
                        html: (popoverTriggerEl.getAttribute('data-bs-html') === 'true') ?? false,
                        placement: popoverTriggerEl.getAttribute('data-bs-placement') ?? 'auto'
                    };
                    Popover.getOrCreateInstance(popoverTriggerEl, options);
                });
            }

            // Switch icons (simple click toggle) — ensure listener bound only once
            let switchesTriggerList = [].slice.call(root.querySelectorAll('[data-bs-toggle="switch-icon"]'));
            switchesTriggerList.forEach(switchTriggerEl => {
                if (!switchTriggerEl.dataset.tablerInit) {
                    switchTriggerEl.addEventListener('click', e => {
                        e.stopPropagation();
                        switchTriggerEl.classList.toggle('active');
                    });
                    switchTriggerEl.dataset.tablerInit = '1';
                }
            });

            // Toast triggers — bind click once and use getOrCreateInstance for target toast
            if (Toast) {
                let toastsTriggerList = [].slice.call(root.querySelectorAll('[data-bs-toggle="toast"]'));
                toastsTriggerList.forEach(toastTriggerEl => {
                    const targetSelector = toastTriggerEl.getAttribute('data-bs-target') || toastTriggerEl.dataset.bsTarget;
                    if (!targetSelector) return;
                    const targetEl = document.querySelector(targetSelector);
                    if (!targetEl) return;

                    // ensure toast instance exists
                    Toast.getOrCreateInstance(targetEl);

                    if (!toastTriggerEl.dataset.tablerInit) {
                        toastTriggerEl.addEventListener('click', () => {
                            const instance = Toast.getOrCreateInstance(targetEl);
                            instance.show();
                        });
                        toastTriggerEl.dataset.tablerInit = '1';
                    }
                });
            }
        }

        /* ==========================================================================
           Boot Tabler
           ========================================================================== */
        const bootTabler = () => {
            initTablerThemeControls();
            initTablerComponents(document);
        };

        document.addEventListener('livewire:navigated', () => {
            bootTabler();

            // Hook into Livewire's morph lifecycle to reinitialize after DOM updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                // Reinitialize Bootstrap components after Livewire morphs the DOM
                setTimeout(() => initTablerComponents(document), 0);
            });

            // Hook into commit lifecycle for additional initialization if needed
            Livewire.hook('commit', ({ component, succeed }) => {
                succeed(({ snapshot, effects }) => {
                    // After message is processed, reinitialize in next microtask
                    queueMicrotask(() => {
                        initTablerComponents(document);
                    });
                });
            });
        });
    </script>
    <!-- END PAGE SCRIPTS -->
</body>

</html>
