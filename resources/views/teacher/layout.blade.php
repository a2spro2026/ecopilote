@php
    $isEmbed = request()->boolean('embed');
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Mon Bureau';
    $pageHeading = trim($__env->yieldContent('heading')) ?: 'Mon Bureau';
    $currentUrl = request()->fullUrlWithoutQuery(['embed']);
    $workspacePrefix = '/espace-prof';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} · {{ config('app.brand') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|instrument-sans:400,500,600" rel="stylesheet" />
    @include('partials.workspace-frame-guard', ['workspacePrefix' => $workspacePrefix])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
@if ($isEmbed)
    <main class="px-4 py-5 sm:px-5">
        @yield('content')
    </main>
    @include('partials.workspace-embed')
    @stack('scripts')
</body>
</html>
@else
@php
    $teacher = $currentTeacher ?? null;
    $nav = [
        ['group' => 'Mon bureau', 'items' => [
            ['key' => 'bureau', 'label' => 'Mon Bureau', 'route' => 'teacher.bureau', 'icon' => 'M2.25 12 12 3l9.75 9M4.5 10.5V21h15V10.5'],
        ]],
        ['group' => 'Enseignement', 'items' => [
            ['key' => 'seances', 'label' => 'Mes Séances', 'route' => 'teacher.seances', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25'],
            ['key' => 'classes', 'label' => 'Mes Classes', 'route' => 'teacher.classes', 'icon' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6'],
            ['key' => 'eleves', 'label' => 'Mes Élèves', 'route' => 'teacher.eleves', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952M4.501 20.118a7.5 7.5 0 0 1 14.998 0'],
        ]],
        ['group' => 'Ressources', 'items' => [
            ['key' => 'bibliotheque', 'label' => 'Ma Bibliothèque', 'route' => 'teacher.bibliotheque', 'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25v14.25'],
            ['key' => 'exercices', 'label' => 'Exercices & Devoirs', 'route' => 'teacher.exercices', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25'],
            ['key' => 'archives', 'label' => 'Archives', 'route' => 'teacher.archives', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5'],
        ]],
        ['group' => 'Suivi', 'items' => [
            ['key' => 'suivi', 'label' => 'Suivi Pédagogique', 'route' => 'teacher.suivi', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75'],
        ]],
        ['group' => 'Autres', 'items' => [
            ['key' => 'notifications', 'label' => 'Notifications', 'route' => 'teacher.notifications', 'icon' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75'],
            ['key' => 'profil', 'label' => 'Mon Profil', 'route' => 'teacher.profil', 'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0'],
        ]],
    ];
@endphp

<div class="flex min-h-screen">
    <aside id="teacherSidebar" class="fixed inset-y-0 left-0 z-40 flex w-[272px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300">
        <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-100 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-emerald-500 text-white shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">{{ config('app.brand') }}</p>
                <p class="text-[10px] font-medium uppercase tracking-wider text-emerald-600">Espace enseignant</p>
            </div>
            <button type="button" onclick="toggleTeacherSidebar(false)" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50" aria-label="Masquer le menu">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </button>
        </div>

        <div class="border-b border-slate-100 px-4 py-3">
            <a href="{{ route('teacher.salle') }}" data-mdi-skip class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-95">
                Entrer dans la salle
            </a>
        </div>

        <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-4">
            @foreach ($nav as $section)
                <div>
                    <p class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ $section['group'] }}</p>
                    <div class="space-y-0.5">
                        @foreach ($section['items'] as $item)
                            @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                            <a href="{{ route($item['route']) }}" data-window-title="{{ $item['label'] }}"
                               class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-[13px] font-medium transition {{ $active ? 'bg-emerald-50 text-emerald-800' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </aside>

    <div id="teacherOverlay" onclick="toggleTeacherSidebar()" class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden"></div>

    <div id="teacherMain" class="flex h-screen flex-1 flex-col overflow-hidden transition-[padding] duration-300 ease-out">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6">
            @include('partials.sidebar-toggle', ['tone' => 'teacher', 'onclick' => 'toggleTeacherSidebar()', 'controls' => 'teacherSidebar'])
            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold text-slate-900 sm:text-lg" style="font-family:'Poppins',sans-serif;">Bureau pédagogique</h1>
                <p class="hidden text-xs text-slate-500 sm:block">Les pages s’ouvrent en fenêtres, comme un bureau de travail</p>
            </div>
            <a href="{{ route('teacher.salle') }}" data-mdi-skip
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-3 py-2 text-xs font-bold text-white shadow-sm transition hover:opacity-90"
               aria-label="Retour à la salle">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h-3A4.5 4.5 0 0 0 3 10.5v3A4.5 4.5 0 0 0 7.5 18h3m3-12h3a4.5 4.5 0 0 1 4.5 4.5v3a4.5 4.5 0 0 1-4.5 4.5h-3M8 12h8"/>
                </svg>
                <span class="hidden sm:inline">Retour à la salle</span>
            </a>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Professeur validé
            </span>
            <a href="{{ route('teacher.notifications') }}" class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-50" aria-label="Notifications">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0"/></svg>
            </a>
            @include('partials.school-logout', ['action' => route('teacher.logout'), 'tone' => 'teacher'])
            <div class="relative" id="teacherUserWrap">
                <button type="button" onclick="document.getElementById('teacherUserMenu').classList.toggle('hidden')" class="flex items-center gap-2 rounded-xl border border-slate-200 py-1.5 pl-1.5 pr-2.5 hover:bg-slate-50">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-emerald-500 text-xs font-bold text-white">
                        {{ strtoupper(mb_substr($teacher->nom_complet ?? 'P', 0, 1)) }}
                    </span>
                    <span class="hidden text-left leading-tight sm:block">
                        <span class="block text-xs font-semibold text-slate-800">{{ $teacher->nom_complet ?? 'Professeur' }}</span>
                        <span class="block text-[10px] text-slate-500">{{ $teacher->matiere ?? 'Enseignant' }}</span>
                    </span>
                </button>
                <div id="teacherUserMenu" class="absolute right-0 mt-2 hidden w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                    <a href="{{ route('teacher.profil') }}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Mon profil</a>
                </div>
            </div>
        </header>

        <div class="flex min-h-0 flex-1 flex-col">
            @include('partials.workspace-mdi', [
                'storageKey' => 'ecopilote.teacher.mdi',
                'urlPrefix' => $workspacePrefix,
                'initialTitle' => $pageHeading,
                'initialUrl' => $currentUrl,
                'accent' => 'emerald',
            ])
        </div>
        <noscript><main class="flex-1 overflow-auto px-4 py-6 sm:px-6 lg:px-8">@yield('content')</main></noscript>
    </div>
</div>

<script>
    const TEACHER_SIDEBAR_KEY = 'ecopilote.teacher.sidebarOpen';
    const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

    function setTeacherSidebarOpen(open) {
        const sidebar = document.getElementById('teacherSidebar');
        const overlay = document.getElementById('teacherOverlay');
        const main = document.getElementById('teacherMain');
        const btn = document.getElementById('sidebarToggleBtn');
        sidebar.classList.toggle('-translate-x-full', !open);
        sidebar.classList.toggle('translate-x-0', open);
        if (isDesktop()) {
            overlay.classList.add('hidden');
            main.style.paddingLeft = open ? '272px' : '0px';
            localStorage.setItem(TEACHER_SIDEBAR_KEY, open ? '1' : '0');
        } else {
            overlay.classList.toggle('hidden', !open);
            main.style.paddingLeft = '0px';
        }
        btn?.setAttribute('aria-expanded', String(open));
        btn?.setAttribute('title', open ? 'Masquer le menu' : 'Afficher le menu');
        document.getElementById('sidebarIconOpen')?.classList.toggle('hidden', !open);
        document.getElementById('sidebarIconClosed')?.classList.toggle('hidden', open);
    }

    function toggleTeacherSidebar(force) {
        const currentlyOpen = document.getElementById('teacherSidebar').classList.contains('translate-x-0');
        setTeacherSidebarOpen(typeof force === 'boolean' ? force : !currentlyOpen);
    }

    setTeacherSidebarOpen(isDesktop() ? localStorage.getItem(TEACHER_SIDEBAR_KEY) !== '0' : false);
    window.addEventListener('resize', () => setTeacherSidebarOpen(isDesktop() ? localStorage.getItem(TEACHER_SIDEBAR_KEY) !== '0' : false));
    document.addEventListener('ecopilote:close-sidebar', () => setTeacherSidebarOpen(false));
    document.addEventListener('click', (e) => {
        const wrap = document.getElementById('teacherUserWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('teacherUserMenu')?.classList.add('hidden');
        }
    });
</script>
</body>
</html>
@endif
