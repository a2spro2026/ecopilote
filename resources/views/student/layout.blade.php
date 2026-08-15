@php
    $isEmbed = request()->boolean('embed');
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Mon espace';
    $pageHeading = trim($__env->yieldContent('heading')) ?: 'Mon espace';
    $currentUrl = request()->fullUrlWithoutQuery(['embed']);
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }} · ECOPILOTE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|instrument-sans:400,500,600" rel="stylesheet" />
    <script>
        if (window.self !== window.top) {
            const url = new URL(window.location.href);
            if (url.searchParams.get('embed') !== '1') {
                url.searchParams.set('embed', '1');
                window.location.replace(url.toString());
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
@if ($isEmbed)
    <main class="px-4 py-5 sm:px-5">@yield('content')</main>
    @include('partials.workspace-embed')
    @stack('scripts')
</body>
</html>
@else
@php
    $student = $currentStudent ?? null;
    $nav = [
        ['label' => 'Mon accueil', 'route' => 'student.dashboard', 'icon' => 'M3 12 12 3l9 9M5.25 10.5v9.75h13.5V10.5'],
        ['label' => 'Mes classes', 'route' => 'student.classes', 'icon' => 'M3.75 6.75h16.5M4.5 6.75v12h15v-12M8.25 3.75v3M15.75 3.75v3'],
        ['label' => 'Mes séances', 'route' => 'student.sessions', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 0 1 5.25 5.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25'],
        ['label' => 'Mes devoirs', 'route' => 'student.assignments', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5'],
        ['label' => 'Mes documents', 'route' => 'student.documents', 'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292'],
        ['label' => 'Mon suivi', 'route' => 'student.progress', 'icon' => 'M3 13.125h4.5v7.125H3zM9.75 9h4.5v11.25h-4.5zM16.5 3.75H21v16.5h-4.5z'],
        ['label' => 'Archives', 'route' => 'student.archives', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5'],
        ['label' => 'Notifications', 'route' => 'student.notifications', 'icon' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75'],
        ['label' => 'Mon profil', 'route' => 'student.profile', 'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0'],
    ];
@endphp
<div class="flex min-h-screen">
    <aside id="studentSidebar" class="fixed inset-y-0 left-0 z-40 flex w-[270px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform lg:translate-x-0">
        <div class="flex h-16 items-center gap-3 border-b border-slate-100 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-cyan-500 text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
            </span>
            <div><p class="text-sm font-extrabold text-slate-900" style="font-family:Poppins,sans-serif">ECOPILOTE</p><p class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Espace élève</p></div>
        </div>
        <div class="border-b border-slate-100 p-4">
            <a href="{{ route('student.room') }}" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-cyan-500 px-3 py-2.5 text-sm font-bold text-white shadow-sm">Entrer dans la salle</a>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach($nav as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}" data-workspace-link data-window-title="{{ $item['label'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-semibold transition {{ $active ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </aside>
    <div id="studentOverlay" onclick="toggleStudentSidebar()" class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden"></div>
    <div class="flex h-screen flex-1 flex-col overflow-hidden lg:pl-[270px]">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6">
            <button type="button" onclick="toggleStudentSidebar()" class="rounded-xl border border-slate-200 p-2 lg:hidden" aria-label="Menu"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
            <div class="min-w-0 flex-1"><h1 class="truncate text-base font-bold text-slate-900 sm:text-lg" style="font-family:Poppins,sans-serif">Mon espace</h1><p class="hidden text-xs text-slate-500 sm:block">Ouvrez plusieurs pages et réduisez-les pour les voir ensemble</p></div>
            <a href="{{ route('student.room') }}" class="hidden rounded-xl bg-indigo-600 px-3 py-2 text-xs font-bold text-white sm:inline-flex">Retour à la salle</a>
            <a href="{{ route('student.notifications') }}" class="relative rounded-xl border border-slate-200 p-2 text-slate-600" aria-label="Notifications"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.8 23.8 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022"/></svg><span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-rose-500"></span></a>
            <div class="relative" id="studentUserWrap">
                <button type="button" onclick="document.getElementById('studentUserMenu').classList.toggle('hidden')" class="flex items-center gap-2 rounded-xl border border-slate-200 p-1.5 pr-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-cyan-500 text-xs font-bold text-white">{{ strtoupper(mb_substr($student->nom_complet ?? 'E', 0, 1)) }}</span>
                    <span class="hidden text-left sm:block"><span class="block text-xs font-semibold">{{ $student->nom_complet ?? 'Élève' }}</span><span class="block text-[10px] text-slate-500">{{ $student->niveau_scolaire ?? '' }}</span></span>
                </button>
                <div id="studentUserMenu" class="absolute right-0 mt-2 hidden w-44 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                    <a href="{{ route('student.profile') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-50">Mon profil</a>
                    <form method="POST" action="{{ route('student.logout') }}">@csrf<button class="w-full px-4 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">Déconnexion</button></form>
                </div>
            </div>
        </header>
        <div class="min-h-0 flex-1 bg-slate-100">
            @include('partials.workspace-windows', [
                'storageKey' => 'ecopilote.student.windows',
                'initialTitle' => $pageHeading,
                'initialUrl' => $currentUrl,
                'accent' => 'indigo',
            ])
        </div>
        {{-- Repli sans JavaScript : les fenêtres nécessitent JS, la page reste lisible sans lui. --}}
        <noscript><main class="flex-1 overflow-auto px-4 py-6 sm:px-6 lg:px-8">@yield('content')</main></noscript>
    </div>
</div>
<script>
function toggleStudentSidebar(){document.getElementById('studentSidebar').classList.toggle('-translate-x-full');document.getElementById('studentOverlay').classList.toggle('hidden')}
document.addEventListener('ecopilote:close-sidebar',()=>{document.getElementById('studentSidebar')?.classList.add('-translate-x-full');document.getElementById('studentOverlay')?.classList.add('hidden')});
document.addEventListener('click',e=>{const wrap=document.getElementById('studentUserWrap');if(wrap&&!wrap.contains(e.target))document.getElementById('studentUserMenu')?.classList.add('hidden')});
</script>
</body>
</html>
@endif
