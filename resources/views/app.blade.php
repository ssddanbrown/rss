<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>
    <base href="{{ asset('') }}">
    <link rel="icon" type="image/png" sizes="32x32"  href="icons/rss-32.png">
    <link rel="icon" type="image/png" sizes="128x128"  href="icons/rss-128.png">

    @if(!app()->runningUnitTests())
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @inertiaHead
</head>
<body class="h-full">
@inertia
</body>
</html>
