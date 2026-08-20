@extends('admin.layout')

@section('title', 'État d’apprentissage')
@section('heading', 'État d’apprentissage')
@section('subtitle', 'Suivi des séances et des cours')

@section('content')
<div class="w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">État d’apprentissage</h2>
            <p class="text-sm text-slate-500"><span id="learningCount">{{ count($rows) }}</span> ligne(s)</p>
        </div>
        <a href="{{ route('admin.page.eleves') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            Fermer
        </a>
    </div>

    <div class="grid gap-2 border-b border-slate-200 px-5 py-3 dark:border-slate-800 sm:grid-cols-3 sm:px-6">
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Mois</span>
            <select id="filterMois" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                @foreach ($months as $month)
                    <option value="{{ $month['value'] }}" @selected($month['value'] === $currentMonth)>{{ $month['label'] }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Nom élève</span>
            <input id="filterNom" type="search" placeholder="Rechercher un élève…"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Matière</span>
            <select id="filterMatiere" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                <option value="">Toutes</option>
                @foreach ($matieres as $matiere)
                    <option value="{{ $matiere }}">{{ \App\Support\SubjectAbbreviation::display($matiere) }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1100px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[9%]">
                <col class="w-[20%]">
                <col class="w-[10%]">
                <col class="w-[12%]">
                <col class="w-[20%]">
                <col class="w-[13%]">
                <col class="w-[16%]">
            </colgroup>
            <thead>
                <tr>
                    <th class="!px-2">ID</th>
                    <th>Nom Complet</th>
                    <th>Matière</th>
                    <th>Séances / Mois</th>
                    <th>Nom Prof</th>
                    <th>Classe</th>
                    <th>Jrs/Cour</th>
                </tr>
            </thead>
            <tbody id="learningBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($rows as $row)
                    <tr class="learning-row hover:bg-slate-50/80 dark:hover:bg-slate-800/40"
                        data-nom="{{ $row['nom'] }}"
                        data-matiere="{{ $row['matiere'] }}">
                        <td class="!px-2 font-semibold text-slate-900 dark:text-white">{{ $row['code'] }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $row['nom'] }}">{{ $row['nom'] }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $row['matiere'] ?: '—' }}">{{ $row['matiere_abbr'] }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $row['seances'] }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $row['prof'] }}">{{ $row['prof'] }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $row['classe'] }}">{{ $row['classe'] }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $row['jours'] }}</td>
                    </tr>
                @empty
                    <tr id="learningNone">
                        <td colspan="7" class="!py-14 text-center text-sm text-slate-500">Aucun suivi d’apprentissage. Les élèves actifs et leurs matières apparaîtront ici.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p id="learningEmpty" class="hidden px-6 py-14 text-center text-sm text-slate-500">Aucun résultat pour ces filtres.</p>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const nomInput = document.getElementById('filterNom');
    const matiereSelect = document.getElementById('filterMatiere');
    const countEl = document.getElementById('learningCount');
    const emptyEl = document.getElementById('learningEmpty');
    const rows = [...document.querySelectorAll('.learning-row')];

    const fold = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const apply = () => {
        const nom = fold(nomInput?.value);
        const matiere = fold(matiereSelect?.value);
        let visible = 0;

        rows.forEach(row => {
            const matchNom = !nom || fold(row.dataset.nom).includes(nom);
            const matchMatiere = !matiere || fold(row.dataset.matiere) === matiere;
            const show = matchNom && matchMatiere;
            row.classList.toggle('hidden', !show);
            if (show) visible += 1;
        });

        if (countEl) countEl.textContent = String(visible);
        emptyEl?.classList.toggle('hidden', visible !== 0 || rows.length === 0);
    };

    nomInput?.addEventListener('input', apply);
    matiereSelect?.addEventListener('change', apply);
    document.getElementById('filterMois')?.addEventListener('change', apply);
})();
</script>
@endpush
