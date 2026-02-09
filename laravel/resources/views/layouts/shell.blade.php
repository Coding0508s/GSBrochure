<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>
    <title>@yield('title', 'GrapeSEED Brochure')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#590091",
                        secondary: "#28a745",
                        "background-light": "#F9FAFB",
                        "background-dark": "#111827",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1F2937",
                        "border-light": "#E5E7EB",
                        "border-dark": "#374151",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                        sans: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        lg: "0.75rem",
                        xl: "1rem",
                    },
                },
            },
        };
    </script>
    @stack('styles')
</head>
<body class="bg-background-light dark:bg-background-dark text-gray-900 dark:text-gray-100 font-sans transition-colors duration-200">
    @php
        $path = request()->path();
        $navRequest = ($path === 'requestbrochure');
        $navList = ($path === 'requestbrochure-list');
        $navAdmin = (str_starts_with($path, 'admin'));
        $navMain = ($path === '' || $path === '/');
    @endphp
    <div class="flex min-h-screen">
        <aside class="w-64 bg-surface-light dark:bg-surface-dark border-r border-border-light dark:border-border-dark hidden md:flex flex-col fixed h-full z-10">
            <div class="p-6 border-b border-border-light dark:border-border-dark flex items-center gap-3">
                <span class="material-icons text-primary text-3xl">local_library</span>
                <span class="font-bold text-lg text-primary tracking-tight">GrapeSEED</span>
            </div>
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ $navRequest ? 'bg-primary/10 text-primary rounded-lg dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors' }}" href="{{ url('requestbrochure') }}">
                    <span class="material-icons text-xl">description</span>
                    브로셔 신청
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ $navList ? 'bg-primary/10 text-primary rounded-lg dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors' }}" href="{{ url('requestbrochure-list') }}">
                    <span class="material-icons text-xl">history</span>
                    신청 내역 조회
                </a>
                @if($path !== 'requestbrochure' && $path !== 'requestbrochure-list')
                <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium {{ $navAdmin ? 'bg-primary/10 text-primary rounded-lg dark:bg-primary/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors' }}" href="{{ url('admin/login') }}">
                    <span class="material-icons text-xl">settings</span>
                    관리자 페이지
                </a>
                @endif

            </nav>
            <a class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors mt-auto" href="{{ url('/') }}">
                <span class="material-icons text-xl">home</span>
                메인으로 돌아가기
            </a>
            <div class="p-4 border-t border-border-light dark:border-border-dark">
                <div class="flex items-center gap-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <span class="material-icons text-gray-500 dark:text-gray-400 text-sm">person</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">User</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">@yield('sidebar-footer-label', 'GrapeSEED Brochure')</p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 md:ml-64 p-4 md:p-8 lg:p-12 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script>window.API_BASE_URL = '{{ url("/api") }}';</script>
    @stack('scripts')
</body>
</html>
