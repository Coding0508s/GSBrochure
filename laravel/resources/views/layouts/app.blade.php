<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GS Brochure')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: '맑은 고딕', sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; background-color: #440b86; color: white; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn:hover { background-color: #0ca22c; }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    <script>window.API_BASE_URL = '{{ url("/api") }}';</script>
    @stack('scripts')
</body>
</html>
