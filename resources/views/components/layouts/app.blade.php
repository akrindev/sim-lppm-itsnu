<!DOCTYPE html>
<html lang="en" data-bs-theme-primary="teal">
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
    <script src="/dist/js/tabler-theme.min.js"></script>
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

    {{-- @include('components.layouts.app.settings') --}}
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/dist/js/tabler.min.js?{{ str()->random(5) }}" data-navigate-track></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    <script src="/dist/js/demo.js" defer></script>
    <!-- END DEMO SCRIPTS -->
    <!-- BEGIN PAGE SCRIPTS -->

    <script>
        // Function to initialize Tabler components
        function initTablerComponents() {
            // Reinitialize Bootstrap dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Reinitialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Reinitialize Bootstrap popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Reinitialize Bootstrap collapse
            var collapseElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="collapse"]'));
            collapseElementList.map(function (collapseEl) {
                return new bootstrap.Collapse(collapseEl, {
                    toggle: false
                });
            });

            // Reinitialize Bootstrap modals if needed
            var modalElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
            modalElementList.map(function (modalEl) {
                return new bootstrap.Modal(modalEl);
            });
        }

        function initTablerSettings() {
            var themeConfig = {
              theme: "light",
              "theme-base": "gray",
              "theme-font": "sans-serif",
              "theme-primary": "green",
              "theme-radius": "1",
            };

            document.documentElement.setAttribute("data-bs-theme-primary", "green");
            window.localStorage.setItem("tabler-theme-primary", "green");

            var url = new URL(window.location);
            var form = document.getElementById("offcanvasSettings");
            var resetButton = document.getElementById("reset-changes");

            if (!form || !resetButton) return; // Guard clause if elements don't exist

            var checkItems = function () {
              for (var key in themeConfig) {
                var value = window.localStorage["tabler-" + key] || themeConfig[key];
                if (!!value) {
                  var radios = form.querySelectorAll(`[name="${key}"]`);
                  if (!!radios) {
                    radios.forEach((radio) => {
                      radio.checked = radio.value === value;
                    });
                  }
                }
              }
            };
            form.addEventListener("change", function (event) {
              var target = event.target,
                name = target.name,
                value = target.value;
              for (var key in themeConfig) {
                if (name === key) {
                  document.documentElement.setAttribute("data-bs-" + key, value);
                  window.localStorage.setItem("tabler-" + key, value);
                  url.searchParams.set(key, value);
                }
              }
              window.history.pushState({}, "", url);
            });
            resetButton.addEventListener("click", function () {
              for (var key in themeConfig) {
                var value = themeConfig[key];
                document.documentElement.removeAttribute("data-bs-" + key);
                window.localStorage.removeItem("tabler-" + key);
                url.searchParams.delete(key);
              }
              checkItems();
              window.history.pushState({}, "", url);
            });
            checkItems();
        }

        // Initialize on DOM ready
        document.addEventListener("DOMContentLoaded", function () {
            initTablerComponents();
            initTablerSettings();
        });

        // Reinitialize after Livewire navigation
        document.addEventListener("livewire:navigated", function () {
            initTablerComponents();
            initTablerSettings();
        });

        // Also listen for Livewire's navigate event (for smoother transitions)
        document.addEventListener("livewire:navigate", function () {
            // Clean up existing dropdowns before navigation
            var existingDropdowns = document.querySelectorAll('.dropdown-menu.show');
            existingDropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('show');
            });

            // Close any open modals
            var existingModals = document.querySelectorAll('.modal.show');
            existingModals.forEach(function(modal) {
                var bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) {
                    bsModal.hide();
                }
            });
        });
    </script>
    <!-- END PAGE SCRIPTS -->
</body>
</html>
