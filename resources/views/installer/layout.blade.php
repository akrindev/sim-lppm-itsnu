<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.4.0
* @link https://tabler.io
* Copyright 2018-2025 The Tabler Authors
* Copyright 2018-2025 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <title>Installation - {{ config('app.name', 'LPPM-ITSNU') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="preconnect" href="https://rsms.me" crossorigin>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    </noscript>
</head>

<body class="d-flex flex-column">
    {{ $slot }}

    @livewireScripts
</body>

</html>
