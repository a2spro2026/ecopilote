@extends('admin.layout')

@section('title', 'Classes')
@section('heading', 'Classes')
@section('subtitle', 'Gestion et suivi des classes')

@section('content')
@php
    $statusMeta = [
        'active' => ['label' => 'Active', 'dot' => 'bg-emerald-500', 'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30'],
        'suspendue' => ['label' => 'Suspendue', 'dot' => 'bg-amber-400', 'chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30'],
        'terminee' => ['label' => 'Terminée', 'dot' => 'bg-slate-400', 'chip' => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600'],
    ];
    $typeMeta = [
        'individuelle' => 'Individuelle',
        'groupe' => 'Groupe',
    ];
@endphp

{{-- Zone figée : stats + filtres ne défilent plus --}}
<div class="classes-lock-page">
    <div class="classes-lock-toolbar space-y-3">
        <div class="flex items-center gap-2">
            @foreach ([
                ['key' => 'total', 'label' => 'Total', 'tone' => 'from-blue-500 to-indigo-500', 'icon' => 'school'],
                ['key' => 'active', 'label' => 'Actives', 'tone' => 'from-emerald-500 to-teal-500', 'icon' => 'check'],
                ['key' => 'suspendue', 'label' => 'Suspendues', 'tone' => 'from-amber-400 to-orange-500', 'icon' => 'pause'],
                ['key' => 'terminee', 'label' => 'Terminées', 'tone' => 'from-slate-400 to-slate-600', 'icon' => 'done'],
            ] as $stat)
                <div class="flex min-w-0 flex-1 items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $stat['tone'] }} text-white">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            @if ($stat['icon'] === 'school')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6"/>
                            @elseif ($stat['icon'] === 'check')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            @elseif ($stat['icon'] === 'pause')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            @endif
                        </svg>
                    </span>
                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-[10px] font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;" data-stat="{{ $stat['key'] }}">0</p>
                    </div>
                </div>
            @endforeach

            <a href="{{ route('admin.classes.create') }}"
               class="ml-auto inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:opacity-95">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nouvelle classe
            </a>
        </div>

        <div class="grid grid-cols-2 gap-2 md:grid-cols-3 xl:grid-cols-6">
            <select id="filterStatut" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Statut</option>
                <option value="active">Active</option>
                <option value="suspendue">Suspendue</option>
                <option value="terminee">Terminée</option>
            </select>
            <select id="filterMatiere" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Matière</option>
                @foreach ($filterOptions['matieres'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>
            <select id="filterNiveau" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Niveau</option>
                @foreach ($filterOptions['niveaux'] as $n)
                    <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
            <select id="filterProf" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Professeur</option>
                @foreach ($filterOptions['professeurs'] as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
            <select id="filterType" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Type</option>
                <option value="individuelle">Individuelle</option>
                <option value="groupe">Groupe</option>
            </select>
            <button type="button" id="filterReset"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                Réinitialiser
            </button>
        </div>
    </div>

    <div class="classes-lock-scroll">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            <span id="resultCount">0</span> classe(s) affichée(s)
        </p>

        <div id="classesGrid" class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>

        <div id="classesEmpty" class="mt-8 hidden rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-12 text-center dark:border-slate-700 dark:bg-slate-900">
            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Aucune classe ne correspond aux filtres</p>
            <p class="mt-1 text-xs text-slate-500">Modifiez les filtres ou réinitialisez.</p>
        </div>
    </div>
</div>

<template id="classCardTpl">
    <article class="class-card relative flex flex-col rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-start justify-between gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;" data-field="numero"></span>
                <span class="status-badge inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset">
                    <span class="status-dot h-1.5 w-1.5 rounded-full"></span>
                    <span class="status-label"></span>
                </span>
            </div>
            <div class="relative" data-menu-wrap>
                <button type="button" class="menu-toggle rounded-lg border border-slate-200 p-1.5 text-slate-500 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800" aria-label="Actions">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/></svg>
                </button>
                <div class="menu-panel absolute right-0 z-10 mt-1 hidden w-44 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <a href="#" class="action-voir block px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Voir</a>
                    <button type="button" class="action-modifier block w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">Modifier</button>
                    <button type="button" data-action="suspendre" class="action-suspendre block w-full px-3 py-2 text-left text-sm text-amber-700 hover:bg-amber-50 dark:text-amber-300 dark:hover:bg-amber-500/10">Suspendre</button>
                    <button type="button" data-action="reactiver" class="action-reactiver block w-full px-3 py-2 text-left text-sm text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-500/10">Réactiver</button>
                    <button type="button" data-action="terminer" class="action-terminer block w-full px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-800">Terminer</button>
                </div>
            </div>
        </div>

        <div class="mt-3 space-y-1.5 text-sm">
            <p class="font-bold text-slate-900 dark:text-white" data-field="matiere"></p>
            <p class="text-slate-500 dark:text-slate-400" data-field="niveau"></p>
            <p class="text-xs font-medium text-slate-500"><span data-field="type"></span></p>
            <p class="pt-1 text-slate-700 dark:text-slate-200">👨‍🏫 <span data-field="prof"></span></p>
            <p class="text-slate-700 dark:text-slate-200">👥 <span data-field="eleves"></span></p>
        </div>

        <div class="mt-3 space-y-1 border-t border-slate-100 pt-3 text-sm text-slate-600 dark:border-slate-800 dark:text-slate-300">
            <p>📅 <span data-field="jours"></span></p>
            <p>🕐 <span data-field="horaires"></span></p>
        </div>

        <div class="mt-3 text-xs font-medium text-slate-600 dark:text-slate-300" data-field="presence"></div>

        <a href="#" class="btn-voir mt-4 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
            Voir la classe
        </a>
    </article>
</template>

<script>
(() => {
    const statusMeta = @json($statusMeta);
    const typeMeta = @json($typeMeta);
    const showBase = @json(url('/administration/classes'));
    let classes = @json($classes);

    const grid = document.getElementById('classesGrid');
    const empty = document.getElementById('classesEmpty');
    const tpl = document.getElementById('classCardTpl');
    const resultCount = document.getElementById('resultCount');

    const els = {
        statut: document.getElementById('filterStatut'),
        matiere: document.getElementById('filterMatiere'),
        niveau: document.getElementById('filterNiveau'),
        prof: document.getElementById('filterProf'),
        type: document.getElementById('filterType'),
        reset: document.getElementById('filterReset'),
    };

    function presenceHtml(c) {
        if (!c.presence) {
            return '<span class="text-slate-500">Aucune séance réalisée</span>';
        }
        const p = c.presence.presents ?? 0;
        const a = c.presence.absents ?? 0;
        const total = p + a;
        if (total > 0 && a === 0) {
            return `Présence : ${p} / ${total}`;
        }
        return `<span class="text-emerald-600 dark:text-emerald-400">🟢 ${p} présent${p > 1 ? 's' : ''}</span>
                <span class="mx-2 text-slate-300">·</span>
                <span class="text-rose-600 dark:text-rose-400">🔴 ${a} absent${a > 1 ? 's' : ''}</span>`;
    }

    function filtered() {
        return classes.filter((c) => {
            if (els.statut.value && c.statut !== els.statut.value) return false;
            if (els.matiere.value && c.matiere !== els.matiere.value) return false;
            if (els.niveau.value && c.niveau !== els.niveau.value) return false;
            if (els.prof.value && c.professeur.nom !== els.prof.value) return false;
            if (els.type.value && c.type !== els.type.value) return false;
            return true;
        });
    }

    function updateStats(list) {
        const counts = { total: list.length, active: 0, suspendue: 0, terminee: 0 };
        list.forEach((c) => { if (counts[c.statut] !== undefined) counts[c.statut]++; });
        Object.keys(counts).forEach((k) => {
            const node = document.querySelector(`[data-stat="${k}"]`);
            if (node) node.textContent = counts[k];
        });
    }

    function applyMenuVisibility(card, statut) {
        card.querySelector('.action-suspendre').classList.toggle('hidden', statut !== 'active');
        card.querySelector('.action-reactiver').classList.toggle('hidden', statut !== 'suspendue');
        card.querySelector('.action-terminer').classList.toggle('hidden', statut === 'terminee');
    }

    function render() {
        const list = filtered();
        updateStats(list);
        resultCount.textContent = String(list.length);
        grid.innerHTML = '';
        empty.classList.toggle('hidden', list.length > 0);

        list.forEach((c) => {
            const node = tpl.content.cloneNode(true);
            const card = node.querySelector('.class-card');
            const st = statusMeta[c.statut] || statusMeta.terminee;
            const url = `${showBase}/${c.id}`;

            card.dataset.id = c.id;
            card.querySelector('[data-field="numero"]').textContent = c.numero;
            const badge = card.querySelector('.status-badge');
            badge.className = `status-badge inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset ${st.chip}`;
            card.querySelector('.status-dot').className = `status-dot h-1.5 w-1.5 rounded-full ${st.dot}`;
            card.querySelector('.status-label').textContent = st.label;

            card.querySelector('[data-field="matiere"]').textContent = c.matiere;
            card.querySelector('[data-field="niveau"]').textContent = c.niveau;
            card.querySelector('[data-field="type"]').textContent = typeMeta[c.type] || c.type;
            card.querySelector('[data-field="prof"]').textContent = c.professeur.nom;
            const n = (c.eleves || []).length;
            card.querySelector('[data-field="eleves"]').textContent = `${n} élève${n > 1 ? 's' : ''}`;
            card.querySelector('[data-field="jours"]').textContent = (c.jours || []).join(' · ');
            card.querySelector('[data-field="horaires"]').textContent = `${c.heure_debut} → ${c.heure_fin}`;
            card.querySelector('[data-field="presence"]').innerHTML = presenceHtml(c);

            card.querySelector('.btn-voir').href = url;
            card.querySelector('.action-voir').href = url;

            applyMenuVisibility(card, c.statut);

            const toggle = card.querySelector('.menu-toggle');
            const panel = card.querySelector('.menu-panel');
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                document.querySelectorAll('.menu-panel').forEach((p) => { if (p !== panel) p.classList.add('hidden'); });
                panel.classList.toggle('hidden');
            });

            card.querySelectorAll('[data-action]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const action = btn.dataset.action;
                    const item = classes.find((x) => x.id === c.id);
                    if (!item) return;
                    if (action === 'suspendre') item.statut = 'suspendue';
                    if (action === 'reactiver') item.statut = 'active';
                    if (action === 'terminer') item.statut = 'terminee';
                    panel.classList.add('hidden');
                    render();
                });
            });

            grid.appendChild(node);
        });
    }

    Object.values(els).forEach((el) => {
        if (el && el !== els.reset) el.addEventListener('change', render);
    });

    els.reset.addEventListener('click', () => {
        els.statut.value = '';
        els.matiere.value = '';
        els.niveau.value = '';
        els.prof.value = '';
        els.type.value = '';
        render();
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.menu-panel').forEach((p) => p.classList.add('hidden'));
    });

    render();
})();
</script>
@endsection
