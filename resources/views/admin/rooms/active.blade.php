@extends('admin.layout')

@section('title', 'Salles actives')
@section('heading', 'Salles actives')
@section('subtitle', 'Classes virtuelles')

@section('content')
@php
    $toneBg = [
        'blue' => 'from-blue-500 to-indigo-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'amber' => 'from-amber-400 to-orange-500',
        'violet' => 'from-violet-500 to-purple-600',
        'indigo' => 'from-indigo-500 to-blue-600',
        'teal' => 'from-teal-500 to-cyan-600',
        'rose' => 'from-rose-500 to-pink-600',
    ];
@endphp

<style>
    @keyframes salle-blink {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.0); background-color: #fff; }
        25% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.35); background-color: #ecfdf5; }
        50% { box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.55); background-color: #d1fae5; }
        75% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.25); background-color: #ecfdf5; }
    }
    .salle-blink {
        animation: salle-blink 0.7s ease-in-out;
        border-color: #10b981 !important;
    }
    .dark .salle-blink {
        background-color: rgba(6, 78, 59, 0.35) !important;
    }
</style>

<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Salles en cours</h2>
        <p class="text-sm text-slate-500">Cliquez sur une salle pour voir le détail de la séance</p>
    </div>
    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
        <span class="relative flex h-2 w-2">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
        </span>
        {{ count($salles) }} actives
    </span>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($salles as $salle)
        <button
            type="button"
            class="salle-card group w-full rounded-2xl border border-slate-200/80 bg-white p-5 text-left shadow-sm transition hover:border-emerald-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:border-slate-800 dark:bg-slate-900 dark:hover:border-emerald-700"
            data-salle-id="{{ $salle['id'] }}"
            aria-expanded="false"
        >
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $salle['nom'] }}</h3>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $salle['matiere'] }} · {{ $salle['niveau'] }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br {{ $toneBg[$salle['tone']] ?? $toneBg['emerald'] }} text-white shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546"/>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                <span>{{ $salle['professeur'] }}</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $salle['debut'] }} – {{ $salle['fin'] }}</span>
            </div>
            <div class="mt-3 flex gap-2 text-[11px] font-semibold">
                <span class="rounded-lg bg-emerald-50 px-2 py-1 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">{{ count($salle['presents']) }} présents</span>
                <span class="rounded-lg bg-rose-50 px-2 py-1 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">{{ count($salle['absents']) }} absents</span>
            </div>
        </button>
    @endforeach
</div>

{{-- Panneau détail salle — contenu d'origine, centré --}}
<div id="sallePanel" class="fixed inset-0 z-50 hidden items-center justify-center p-4" aria-hidden="true">
    <div id="salleBackdrop" class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
    <div class="relative z-10 max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white/95 px-5 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Salle ouverte</p>
                <h3 id="panelNom" class="text-lg font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">—</h3>
            </div>
            <button type="button" id="salleClose" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3.5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                Fermer
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-5 p-5">
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                <p class="text-xs font-medium text-slate-500">Professeur</p>
                <p id="panelProf" class="mt-1 text-base font-bold text-slate-900 dark:text-white">—</p>
                <p id="panelMeta" class="mt-1 text-xs text-slate-500"></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/80 p-4 dark:border-emerald-900/50 dark:bg-emerald-950/30">
                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Heure de début</p>
                    <p id="panelDebut" class="mt-1 text-lg font-extrabold text-emerald-800 dark:text-emerald-300">—</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-800/50">
                    <p class="text-xs font-medium text-slate-500">Heure de fin</p>
                    <p id="panelFin" class="mt-1 text-lg font-extrabold text-slate-900 dark:text-white">—</p>
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Présents</h4>
                    <span id="panelPresentsCount" class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">0</span>
                </div>
                <ul id="panelPresents" class="space-y-1.5 rounded-2xl border border-slate-100 bg-white p-3 dark:border-slate-800 dark:bg-slate-950/40"></ul>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Absents</h4>
                    <span id="panelAbsentsCount" class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700 dark:bg-rose-950 dark:text-rose-300">0</span>
                </div>
                <ul id="panelAbsents" class="space-y-1.5 rounded-2xl border border-slate-100 bg-white p-3 dark:border-slate-800 dark:bg-slate-950/40"></ul>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const salles = @json($salles);
    const byId = Object.fromEntries(salles.map(s => [s.id, s]));
    const panel = document.getElementById('sallePanel');
    const backdrop = document.getElementById('salleBackdrop');
    const closeBtn = document.getElementById('salleClose');

    function listItems(names, emptyLabel, tone) {
        if (!names.length) {
            return `<li class="px-2 py-2 text-sm text-slate-400">${emptyLabel}</li>`;
        }
        const dot = tone === 'emerald' ? 'bg-emerald-500' : 'bg-rose-500';
        return names.map(n => `
            <li class="flex items-center gap-2 rounded-xl px-2 py-1.5 text-sm text-slate-700 dark:text-slate-200">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full ${dot}"></span>
                ${n}
            </li>
        `).join('');
    }

    function openSalle(salle) {
        document.getElementById('panelNom').textContent = salle.nom;
        document.getElementById('panelProf').textContent = salle.professeur;
        document.getElementById('panelMeta').textContent = `${salle.matiere} · ${salle.niveau}`;
        document.getElementById('panelDebut').textContent = salle.debut;
        document.getElementById('panelFin').textContent = salle.fin;
        document.getElementById('panelPresentsCount').textContent = salle.presents.length;
        document.getElementById('panelAbsentsCount').textContent = salle.absents.length;
        document.getElementById('panelPresents').innerHTML = listItems(salle.presents, 'Aucun présent', 'emerald');
        document.getElementById('panelAbsents').innerHTML = listItems(salle.absents, 'Aucun absent', 'rose');

        panel.classList.remove('hidden');
        panel.classList.add('flex');
        panel.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeSalle() {
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        panel.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        document.querySelectorAll('.salle-card').forEach(c => c.setAttribute('aria-expanded', 'false'));
    }

    document.querySelectorAll('.salle-card').forEach(card => {
        card.addEventListener('click', () => {
            const salle = byId[card.dataset.salleId];
            if (!salle) return;

            card.classList.remove('salle-blink');
            void card.offsetWidth;
            card.classList.add('salle-blink');
            card.setAttribute('aria-expanded', 'true');

            window.setTimeout(() => openSalle(salle), 450);
        });
    });

    closeBtn.addEventListener('click', closeSalle);
    backdrop.addEventListener('click', closeSalle);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.classList.contains('hidden')) closeSalle();
    });
})();
</script>
@endsection


