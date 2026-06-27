<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Administration') · ECOPILOTE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">
@php
    $user = auth()->user();
    $grouped = [];
    foreach ($user->modules() as $moduleKey => $module) {
        $grouped[$module['group']][$moduleKey] = $module;
    }
@endphp

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-gradient-to-b from-blue-950 to-blue-900 text-blue-100 shadow-xl transition-transform lg:translate-x-0">
        <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-emerald-400 text-white shadow ring-1 ring-white/20">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z" />
                </svg>
            </span>
            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white to-emerald-300 bg-clip-text text-transparent" style="font-family:'Poppins',sans-serif;">ECOPILOTE</span>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
            <a href="{{ route('admin.dashboard') }}"
               @class([
                   'group/dash relative flex items-center gap-3 overflow-hidden rounded-2xl px-3 py-3 text-sm font-bold transition-all',
                   'bg-gradient-to-r from-blue-500 to-emerald-400 text-white shadow-lg shadow-emerald-500/25 ring-1 ring-white/20' => request()->routeIs('admin.dashboard'),
                   'text-blue-100 hover:bg-white/10 hover:text-white' => ! request()->routeIs('admin.dashboard'),
               ])>
                <span @class([
                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition group-hover/dash:scale-105',
                    'bg-white/20 text-white ring-1 ring-white/30' => request()->routeIs('admin.dashboard'),
                    'bg-gradient-to-br from-blue-500 to-emerald-400 text-white shadow ring-1 ring-white/20' => ! request()->routeIs('admin.dashboard'),
                ])>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                    </svg>
                </span>
                <span class="flex-1">Tableau de bord</span>
            </a>

            @foreach ($grouped as $groupName => $modules)
                <div>
                    <p class="px-4 pb-2 text-xs font-semibold uppercase tracking-wider text-blue-300/70">{{ $groupName }}</p>
                    <div class="space-y-1">
                        @foreach ($modules as $key => $module)
                            @php $active = request()->routeIs("admin.module.$key"); @endphp
                            <a href="{{ route("admin.module.$key") }}"
                               class="group/item flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition-all
                                      {{ $active ? 'bg-white text-blue-900 shadow-lg' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $module['color'] ?? 'from-blue-500 to-emerald-400' }} text-white shadow ring-1 ring-white/20 transition group-hover/item:scale-105">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $module['icon'] }}" />
                                    </svg>
                                </span>
                                <span class="flex-1">{{ $module['label'] }}</span>
                                <svg class="h-4 w-4 opacity-0 transition group-hover/item:opacity-60 {{ $active ? 'opacity-60' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    {{-- Overlay mobile --}}
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

    {{-- Contenu --}}
    <div class="flex min-h-screen flex-1 flex-col lg:pl-72">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-blue-900 dark:text-white sm:text-xl" style="font-family:'Poppins',sans-serif;">@yield('heading', 'Tableau de bord')</h1>
            </div>

            <div class="flex items-center gap-3">
                {{-- Bascule mode sombre --}}
                <button type="button" onclick="toggleTheme()" aria-label="Basculer le mode sombre"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-amber-300 dark:hover:bg-slate-800">
                    <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </button>

                <div class="hidden text-right leading-tight sm:block">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $user->name }}</p>
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ $user->roleLabel() }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-emerald-400 text-sm font-bold text-white shadow ring-2 ring-white dark:ring-slate-800">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-red-500/40 dark:hover:bg-red-500/10 dark:hover:text-red-400">
                        Déconnexion
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 px-4 py-8 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('adminSidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebarOverlay').classList.toggle('hidden');
    }
    function toggleTheme() {
        const root = document.documentElement;
        root.classList.toggle('dark');
        localStorage.setItem('theme', root.classList.contains('dark') ? 'dark' : 'light');
    }
</script>
@stack('scripts')
</body>
</html>
