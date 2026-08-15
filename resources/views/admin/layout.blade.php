<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Centre de contrôle') · ECOPILOTE</title>
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
    $nav = config('admin.navigation', []);
    $pendingStudentDemandes = \App\Models\StudentApplication::query()
        ->where('etat', \App\Models\StudentApplication::ETAT_EN_ATTENTE)
        ->count();
    $pendingTeacherDemandes = \App\Models\TeacherApplication::query()
        ->where('etat', \App\Models\TeacherApplication::ETAT_EN_ATTENTE)
        ->count();
    $pendingTotal = $pendingStudentDemandes + $pendingTeacherDemandes;
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-40 flex w-[280px] -translate-x-full flex-col border-r border-slate-800 bg-slate-950 text-slate-300 transition-transform duration-300 lg:translate-x-0">
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/5 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-emerald-400 text-white shadow">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-extrabold tracking-tight text-white" style="font-family:'Poppins',sans-serif;">ECOPILOTE</p>
                <p class="text-[10px] font-medium uppercase tracking-wider text-emerald-400">Centre de contrôle</p>
            </div>
        </div>

        <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
            @foreach ($nav as $section)
                <div>
                    <p class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">{{ $section['group'] }}</p>
                    <div class="space-y-0.5">
                        @foreach ($section['items'] as $item)
                            @php
                                $customRoute = $item['route'] ?? null;
                                $href = $customRoute
                                    ? route($customRoute)
                                    : route('admin.page.'.$item['key']);
                                $active = $customRoute
                                    ? request()->routeIs($customRoute)
                                    : request()->routeIs('admin.page.'.$item['key']);
                                if (($item['key'] ?? '') === 'classes' && request()->routeIs('admin.classes.show')) {
                                    $active = true;
                                }
                                if (($item['key'] ?? '') === 'professeurs' && request()->routeIs('admin.teachers.*')) {
                                    $active = true;
                                }
                                if (($item['key'] ?? '') === 'eleves' && request()->routeIs('admin.students.show', 'admin.students.edit')) {
                                    $active = true;
                                }
                                $badge = $item['badge'] ?? null;
                                if (($item['key'] ?? '') === 'demandes-eleves') {
                                    $badge = $pendingStudentDemandes > 0 ? $pendingStudentDemandes : null;
                                }
                                if (($item['key'] ?? '') === 'candidatures-profs') {
                                    $badge = $pendingTeacherDemandes > 0 ? $pendingTeacherDemandes : null;
                                }
                            @endphp
                            <a href="{{ $href }}"
                               class="group flex items-center gap-2.5 rounded-xl px-3 py-2 text-[13px] font-medium transition
                                      {{ $active ? 'bg-white/10 text-white shadow-inner' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                                <svg class="h-4.5 w-4.5 shrink-0 {{ $active ? 'text-emerald-400' : 'text-slate-500 group-hover:text-slate-300' }}" style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                @if ($badge)
                                    <span class="min-w-[1.25rem] rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-bold text-white shadow-sm shadow-rose-500/40">{{ $badge }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="shrink-0 border-t border-white/5 p-4">
            <div class="rounded-xl bg-white/5 px-3 py-2.5">
                <p class="text-xs font-semibold text-white">{{ $user->name }}</p>
                <p class="text-[11px] text-emerald-400">{{ $user->roleLabel() }}</p>
            </div>
        </div>
    </aside>

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 z-30 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>

    <div class="flex min-h-screen flex-1 flex-col lg:pl-[280px]">
        {{-- Header --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200/80 bg-white/90 px-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/90 sm:px-6">
            <button type="button" onclick="toggleSidebar()" class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden" aria-label="Menu">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold text-slate-900 dark:text-white sm:text-lg" style="font-family:'Poppins',sans-serif;">@yield('heading', 'Vue générale')</h1>
                <p class="hidden text-xs text-slate-500 dark:text-slate-400 sm:block">@yield('subtitle', 'Centre de contrôle plateforme')</p>
            </div>

            <div class="hidden max-w-xs flex-1 md:block lg:max-w-sm">
                <label class="relative block">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </span>
                    <input type="search" placeholder="Rechercher élève, prof, séance…"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:focus:ring-blue-900/40">
                </label>
            </div>

            <button type="button" onclick="toggleTheme()" class="relative rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-amber-300 dark:hover:bg-slate-800" aria-label="Thème">
                <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
                <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
            </button>

            <div class="relative" id="notifMenuWrap">
                <button type="button" onclick="document.getElementById('notifMenu').classList.toggle('hidden')"
                        class="relative rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Notifications">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                    @if ($pendingTotal > 0)
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-900">{{ $pendingTotal }}</span>
                    @endif
                </button>
                <div id="notifMenu" class="absolute right-0 z-30 mt-2 hidden w-72 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <div class="border-b border-slate-100 px-4 py-2.5 dark:border-slate-800">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Notifications</p>
                    </div>
                    @if ($pendingStudentDemandes > 0)
                        <a href="{{ route('admin.page.demandes-eleves') }}" class="block px-4 py-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                            <p class="font-semibold text-slate-800 dark:text-slate-100">Demandes élèves</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $pendingStudentDemandes }} inscription(s) en attente de validation</p>
                        </a>
                    @endif
                    @if ($pendingTeacherDemandes > 0)
                        <a href="{{ route('admin.page.candidatures-profs') }}" class="block border-t border-slate-100 px-4 py-3 text-sm hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800">
                            <p class="font-semibold text-slate-800 dark:text-slate-100">Candidatures professeurs</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $pendingTeacherDemandes }} candidature(s) en attente</p>
                        </a>
                    @endif
                    @if ($pendingTotal === 0)
                        <p class="px-4 py-6 text-center text-xs text-slate-500">Aucune notification</p>
                    @endif
                </div>
            </div>

            <div class="relative" id="userMenuWrap">
                <button type="button" onclick="document.getElementById('userMenu').classList.toggle('hidden')"
                        class="flex items-center gap-2 rounded-xl border border-slate-200 py-1.5 pl-1.5 pr-2.5 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-emerald-400 text-xs font-bold text-white">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">{{ $user->name }}</span>
                        <span class="block text-[10px] text-slate-500">{{ $user->roleLabel() }}</span>
                    </span>
                </button>
                <div id="userMenu" class="absolute right-0 mt-2 hidden w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <a href="{{ route('admin.page.configuration') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Configuration</a>
                    <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Voir le site</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">Déconnexion</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
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
    document.addEventListener('click', (e) => {
        const wrap = document.getElementById('userMenuWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('userMenu')?.classList.add('hidden');
        }
    });
</script>
@stack('scripts')
</body>
</html>
