@extends('admin.layout')

@section('title', 'Salles')
@section('heading', 'Salles')
@section('subtitle', 'Planning')

@section('content')
<style>
    @keyframes salle-blink-actif {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.15); background-color: #ecfdf5; }
        50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0.28); background-color: #86efac; }
    }
    @keyframes salle-blink-reportee {
        0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.15); background-color: #fffbeb; }
        50% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0.32); background-color: #fbbf24; }
    }
    @keyframes salle-blink-annulee {
        0%, 100% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.15); background-color: #fff1f2; }
        50% { box-shadow: 0 0 0 10px rgba(244, 63, 94, 0.28); background-color: #fb7185; }
    }
    .salle-blink-actif { animation: salle-blink-actif 1.15s ease-in-out infinite; border-color: #10b981 !important; }
    .salle-blink-reportee { animation: salle-blink-reportee 1.15s ease-in-out infinite; border-color: #f59e0b !important; }
    .salle-blink-annulee { animation: salle-blink-annulee 1.15s ease-in-out infinite; border-color: #f43f5e !important; }
</style>

<div id="sallesBoard" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Salles</h2>
            <p class="text-sm text-slate-500">{{ $occupees }} salle(s) occupée(s) · 20 salles</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold">
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-emerald-500"></span> Actif</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-amber-400"></span> Reportée</span>
            <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-rose-500"></span> Annulée</span>
            <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</a>
        </div>
    </div>

    <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
        @foreach ($salles as $salle)
            @php
                $blink = match ($salle['statut']) {
                    'actif' => 'salle-blink-actif',
                    'reportee' => 'salle-blink-reportee',
                    'annulee' => 'salle-blink-annulee',
                    default => '',
                };
            @endphp
            <button type="button"
                    data-open-room="{{ $salle['id'] }}"
                    class="salle-card rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:shadow-md dark:border-slate-700 dark:bg-slate-800 {{ $blink }}">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Salle</p>
                <h3 class="mt-1 text-2xl font-black text-black" style="font-family:'Poppins',sans-serif;">{{ $salle['code'] }}</h3>
                <p class="mt-2 text-xs font-semibold {{ $salle['occupe'] ? 'text-black' : 'text-slate-400' }}">
                    {{ $salle['occupe'] ? $salle['statutLabel'] : 'Libre' }}
                </p>
            </button>
        @endforeach
    </div>
</div>

<section id="salleInfoPanel" class="hidden min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-white/80">Fiche salle</p>
            <h2 id="salleInfoTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Salle</h2>
        </div>
        <span id="salleInfoStatut" class="rounded-xl bg-white px-3 py-1.5 text-xs font-extrabold text-slate-800"></span>
    </div>
    <dl id="salleInfoFields" class="grid gap-3 p-6 sm:grid-cols-2 sm:p-8"></dl>
    <div id="salleInfoActions" class="hidden border-t border-slate-200 bg-white px-6 py-5 dark:border-slate-800 dark:bg-slate-900">
        <p class="mb-3 text-[11px] font-bold uppercase tracking-wide text-slate-500">Rejoindre la séance en cours</p>
        <div class="flex flex-wrap gap-3">
            <a id="salleListenLink" href="#" data-window-title="Écouter la salle"
               class="inline-flex min-w-[140px] flex-1 items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-extrabold text-emerald-800 transition hover:bg-emerald-100 sm:flex-none">
                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.75v6m6-6v6m-3-9.75V4.5A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m6 0V6.75A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m0 0H6.75A2.25 2.25 0 0 0 4.5 11.25v1.5A2.25 2.25 0 0 0 6.75 15H9m6 0h2.25A2.25 2.25 0 0 0 18 13.5v-1.5A2.25 2.25 0 0 0 15.75 9H15"/>
                </svg>
                Écouter
            </a>
            <a id="salleWatchLink" href="#" data-window-title="Voir la salle"
               class="inline-flex min-w-[140px] flex-1 items-center justify-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm font-extrabold text-blue-800 transition hover:bg-blue-100 sm:flex-none">
                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
                Voir
            </a>
        </div>
        <p id="salleInfoActionsHint" class="mt-3 hidden text-xs text-slate-500">Écouter : entendre le prof et les élèves. Voir : entrer dans la salle et suivre le cours en visuel.</p>
    </div>
    <div id="salleInfoInactive" class="hidden border-t border-slate-200 bg-amber-50 px-6 py-4 text-sm text-amber-800 dark:border-slate-800 dark:bg-amber-950/30 dark:text-amber-200">
        Observation disponible uniquement pour une séance <strong>active</strong> avec salle affectée.
    </div>
    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="salleInfoClose" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
    </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const salles = @json($salles);
    const board = document.getElementById('sallesBoard');
    const panel = document.getElementById('salleInfoPanel');
    const fields = document.getElementById('salleInfoFields');
    const title = document.getElementById('salleInfoTitle');
    const statutEl = document.getElementById('salleInfoStatut');
    const actions = document.getElementById('salleInfoActions');
    const actionsHint = document.getElementById('salleInfoActionsHint');
    const inactive = document.getElementById('salleInfoInactive');
    const listenLink = document.getElementById('salleListenLink');
    const watchLink = document.getElementById('salleWatchLink');

    const escapeHtml = value => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    const field = (label, value) => `
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800">
            <dt class="text-xs text-slate-500">${escapeHtml(label)}</dt>
            <dd class="text-sm font-medium text-slate-800 dark:text-slate-100">${escapeHtml(value || '—')}</dd>
        </div>`;

    const openInfo = salle => {
        title.textContent = salle.nom;
        statutEl.textContent = salle.statutLabel;
        actions.classList.add('hidden');
        actionsHint.classList.add('hidden');
        inactive.classList.add('hidden');

        if (!salle.session) {
            fields.innerHTML = field('État', 'Libre');
        } else {
            const s = salle.session;
            fields.innerHTML = [
                field('Salle', salle.nom),
                field('N°/Sé', s.code),
                field('Date', s.date),
                field('Groupe', s.group),
                field('Matière', s.matiereLabel),
                field('Niveau', s.niveau),
                field('Prof', s.teacher),
                field('Élèves', (s.eleves || []).join(', ')),
                field('Hr Début', s.start),
                field('Hr Fin', s.end),
                field('Statut', s.statutLabel),
                field('Remarque', s.statut === 'actif' ? '' : s.remarque),
            ].join('');

            if (s.statut === 'actif' && s.listenUrl && s.watchUrl) {
                listenLink.href = s.listenUrl;
                watchLink.href = s.watchUrl;
                listenLink.dataset.windowTitle = `Écouter · ${salle.nom}`;
                watchLink.dataset.windowTitle = `Voir · ${salle.nom}`;
                actions.classList.remove('hidden');
                actionsHint.classList.remove('hidden');
            } else {
                inactive.classList.remove('hidden');
            }
        }
        board.classList.add('hidden');
        panel.classList.remove('hidden');
    };

    const closeInfo = () => {
        panel.classList.add('hidden');
        board.classList.remove('hidden');
    };

    document.querySelectorAll('[data-open-room]').forEach(button => {
        button.addEventListener('click', () => {
            const salle = salles.find(item => item.id === Number(button.dataset.openRoom));
            if (salle) openInfo(salle);
        });
    });

    document.getElementById('salleInfoClose')?.addEventListener('click', closeInfo);
})();
</script>
@endpush
