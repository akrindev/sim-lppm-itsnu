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
    <title>Sign in</title>
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
    {{-- <link href="./preview/css/demo.css" rel="stylesheet" /> --}}
    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
</head>

<body><!-- BEGIN GLOBAL THEME SCRIPT -->
<script src="/dist/js/tabler-theme.min.js"></script>
<!-- END GLOBAL THEME SCRIPT -->
    {{ $slot }}
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/dist/js/tabler.min.js" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    {{-- <script src="./preview/js/demo.min.js" defer></script> --}}
    <!-- END DEMO SCRIPTS -->
    <!-- BEGIN PAGE SCRIPTS -->
    <script>
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
            var checkItems = function() {
                if (!form) return;
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
            if (form) {
                form.addEventListener("change", function(event) {
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
            }
            if (resetButton) {
                resetButton.addEventListener("click", function() {
                    for (var key in themeConfig) {
                        var value = themeConfig[key];
                        document.documentElement.removeAttribute("data-bs-" + key);
                        window.localStorage.removeItem("tabler-" + key);
                        url.searchParams.delete(key);
                    }
                    checkItems();
                    window.history.pushState({}, "", url);
                });
            }
            checkItems();
        }

        // Reinitialize after Livewire navigation
        document.addEventListener("livewire:navigated", function() {
            initTablerComponents();
            initTablerSettings();
        });
    </script>
    <!-- END PAGE SCRIPTS -->
</body>

</html>
