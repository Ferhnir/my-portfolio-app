<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', config('app.name', 'Laravel'))</title> {{-- JetBrains Mono --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
                href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap"
                rel="stylesheet"
        >
        <link rel="stylesheet" href="{{ asset('css/app.css') }}"> @stack('styles')
    </head>
    <body>

    @yield('content')

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
    </body>
</html>