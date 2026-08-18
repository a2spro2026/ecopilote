@php
    $navLinks = [
        ['label' => 'Home',           'route' => 'home'],
        ['label' => 'Administration', 'route' => 'admin.dashboard'],
        ['label' => 'Catégories',     'route' => 'categories'],
        ['label' => 'Activités',      'route' => 'activites'],
        ['label' => 'Portail Profs',  'route' => 'portail.profs'],
        ['label' => 'Portail Étudiant','route' => 'portail.etudiant'],
    ];
@endphp

<header class="sticky top-0 z-50">
    <div class="nav-glow relative overflow-hidden bg-gradient-to-r from-blue-950 via-blue-900 to-blue-800 backdrop-blur-xl border-b border-white/10">

        {{-- Étoiles brillantes --}}
        <div class="pointer-events-none absolute inset-0 z-0">
            <span class="nav-star" style="top:22%; left:8%;  width:4px;  height:4px; animation-delay:0s;"></span>
            <span class="nav-star" style="top:60%; left:14%; width:2px;  height:2px; animation-delay:0.6s;"></span>
            <span class="nav-star" style="top:35%; left:24%; width:3px;  height:3px; animation-delay:1.2s;"></span>
            <span class="nav-star" style="top:70%; left:33%; width:2px;  height:2px; animation-delay:0.3s;"></span>
            <span class="nav-star" style="top:18%; left:45%; width:4px;  height:4px; animation-delay:1.8s;"></span>
            <span class="nav-star" style="top:55%; left:52%; width:2px;  height:2px; animation-delay:0.9s;"></span>
            <span class="nav-star" style="top:28%; left:63%; width:3px;  height:3px; animation-delay:2.2s;"></span>
            <span class="nav-star" style="top:72%; left:70%; width:2px;  height:2px; animation-delay:0.4s;"></span>
            <span class="nav-star" style="top:30%; left:80%; width:4px;  height:4px; animation-delay:1.5s;"></span>
            <span class="nav-star" style="top:62%; left:88%; width:3px;  height:3px; animation-delay:2.6s;"></span>
            <span class="nav-star" style="top:20%; left:94%; width:2px;  height:2px; animation-delay:1s;"></span>
        </div>

        <nav class="relative z-10 flex h-24 w-full items-center justify-between gap-6 px-4 sm:px-6 lg:px-10">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-4 shrink-0">
                <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-emerald-400 text-white shadow-lg shadow-emerald-500/30 ring-1 ring-white/20">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21.5a12.083 12.083 0 01-6.16-10.922L12 14z" />
                    </svg>
                </span>
                <span class="text-4xl font-extrabold tracking-tight bg-gradient-to-r from-white to-emerald-300 bg-clip-text text-transparent" style="font-family:'Poppins',sans-serif;">
                    {{ config('app.brand') }}
                </span>
            </a>

            {{-- Desktop nav links (floating pill) --}}
            <div class="hidden lg:flex items-center gap-1 rounded-full border border-white/15 bg-white/10 p-1.5 backdrop-blur">
                @foreach ($navLinks as $link)
                    @php $active = request()->routeIs($link['route']); @endphp
                    <a href="{{ route($link['route']) }}"
                       class="rounded-full px-5 py-2.5 text-[15px] font-semibold transition-all duration-200
                              {{ $active
                                    ? 'bg-white text-blue-800 shadow-md shadow-blue-950/40'
                                    : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            {{-- User profile --}}
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-3 rounded-full border border-white/15 bg-white/10 py-1.5 pl-4 pr-1.5 backdrop-blur">
                    <div class="text-right leading-tight">
                        <p class="text-sm font-semibold text-white">Mme Hanan</p>
                        <p class="text-xs font-medium text-emerald-300">Directrice Générale</p>
                    </div>
                    <span class="relative">
                        <img src="https://i.pravatar.cc/80?img=47" alt="Mme Hanan"
                             class="h-10 w-10 rounded-full object-cover ring-2 ring-white/40 shadow">
                        <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-blue-900 bg-emerald-400"></span>
                    </span>
                </div>

                {{-- Mobile menu button --}}
                <button type="button" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')"
                        class="lg:hidden inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/10 p-2.5 text-white backdrop-blur hover:bg-white/20"
                        aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </nav>
    </div>

    {{-- Mobile nav --}}
    <div id="mobileMenu" class="hidden lg:hidden border-b border-white/10 bg-blue-900/95 backdrop-blur-xl px-4 py-3 shadow-lg">
        <div class="flex flex-col gap-1">
            @foreach ($navLinks as $link)
                @php $active = request()->routeIs($link['route']); @endphp
                <a href="{{ route($link['route']) }}"
                   class="rounded-xl px-4 py-2.5 text-sm font-semibold transition
                          {{ $active ? 'bg-white text-blue-800 shadow' : 'text-blue-100 hover:bg-white/10' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</header>
