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
    <link href="/dist/css/tabler.min.css?1760227350" rel="stylesheet" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PLUGINS STYLES -->
    <link href="/dist/css/tabler-flags.min.css?1760227350" rel="stylesheet" />
    <link href="/dist/css/tabler-socials.min.css?1760227350" rel="stylesheet" />
    <link href="/dist/css/tabler-payments.min.css?1760227350" rel="stylesheet" />
    <link href="/dist/css/tabler-vendors.min.css?1760227350" rel="stylesheet" />
    <link href="/dist/css/tabler-marketing.min.css?1760227350" rel="stylesheet" />
    <link href="/dist/css/tabler-themes.min.css?1760227350" rel="stylesheet" />
    <!-- END PLUGINS STYLES -->
    <!-- BEGIN DEMO STYLES -->
    {{-- <link href="/preview/css/demo.css?1760227350" rel="stylesheet" /> --}}
    <!-- END DEMO STYLES -->
    <!-- BEGIN CUSTOM FONT -->
    <style>
        @import url("https://rsms.me/inter/inter.css");
    </style>
    <!-- END CUSTOM FONT -->
</head>

<body>
    <!-- BEGIN GLOBAL THEME SCRIPT -->
    <script src="/dist/js/tabler-theme.min.js?1760227350"></script>
    <!-- END GLOBAL THEME SCRIPT -->
    <div class="page">
        @include('components.layouts.header')
        <div class="page-wrapper">
            <!-- BEGIN PAGE HEADER -->
            <!-- END PAGE HEADER -->
            <!-- BEGIN PAGE BODY -->
            <div class="page-body">
                <div class="my-auto container-xl">
                    {{  $slot }}
                </div>
            </div>
            <!-- END PAGE BODY -->
            @include('components.layouts.footer')
        </div>
    </div>
    @include('components.layouts.app.settings')
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="/dist/js/tabler.min.js?1760227350" defer></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <!-- BEGIN DEMO SCRIPTS -->
    {{-- <script src="/preview/js/demo.min.js?1760227350" defer></script> --}}
    <!-- END DEMO SCRIPTS -->
    <!-- BEGIN PAGE SCRIPTS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        var themeConfig = {
          theme: "light",
          "theme-base": "gray",
          "theme-font": "sans-serif",
          "theme-primary": "green",
          "theme-radius": "1",
        };
        var url = new URL(window.location);
        var form = document.getElementById("offcanvasSettings");
        var resetButton = document.getElementById("reset-changes");
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
      });
    </script>
    <!-- END PAGE SCRIPTS -->
</body>

</html>
