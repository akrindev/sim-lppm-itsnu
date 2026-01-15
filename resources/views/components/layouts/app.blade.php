<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
        <title>{{ $title ?? '' }} - {{ config('app.name') }}</title>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- END PLUGINS STYLES -->
        <!-- BEGIN DEMO STYLES -->
        {{-- <link href="/preview/css/demo.css" rel="stylesheet" /> --}}
        <!-- END DEMO STYLES -->
        <!-- BEGIN CUSTOM FONT -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @import url("https://rsms.me/inter/inter.css");
        </style>
        <!-- END CUSTOM FONT -->
    </head>

    <body>
        <!-- BEGIN GLOBAL THEME SCRIPT -->
        {{-- @vite(['resources/js/theme-config.js']) --}}
        <!-- END GLOBAL THEME SCRIPT -->
        <div class="page">
            @include('components.layouts.header')
            <div class="page-wrapper">
                <!-- BEGIN PAGE HEADER -->
                @if (isset($pageTitle))
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
                        {{ $slot }}
                    </div>
                </div>
                <!-- END PAGE BODY -->
                @include('components.layouts.footer')
            </div>
        </div>

        @stack('scripts')

        {{-- @include('components.layouts.app.settings') --}}
        <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
        {{-- <script src="/dist/js/tabler.min.js?{{ str()->random(5) }}" data-navigate-track></script> --}}
        {{-- @vite(['resources/js/app.js']) --}}
        <!-- END GLOBAL MANDATORY SCRIPTS -->
        <!-- BEGIN DEMO SCRIPTS -->
        {{-- <script src="/dist/js/demo.js" defer></script> --}}
        <!-- END DEMO SCRIPTS -->
        <!-- BEGIN PAGE SCRIPTS -->
        <!-- END PAGE SCRIPTS -->
    </body>

</html>
