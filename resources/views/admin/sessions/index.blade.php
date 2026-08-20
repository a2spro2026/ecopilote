@extends('admin.layout')

@section('title', 'Séance')
@section('heading', 'Séance')
@section('subtitle', 'Planning')

@section('content')
@php
    $showForm = $errors->any() && ! old('_statut_row');
    $statutStyles = [
        'actif' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',
        'reportee' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30',
        'annulee' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30',
    ];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div id="seanceTable" class="{{ $showForm ? 'hidden' : '' }} w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Séance</h2>
            <p class="text-sm text-slate-500">{{ $sessions->count() }} séance(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="seanceAdd" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">
                Ajouter
            </button>
            <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Fermer
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1600px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[7%]">
                <col class="w-[7%]">
                <col class="w-[7%]">
                <col class="w-[8%]">
                <col class="w-[7%]">
                <col class="w-[12%]">
                <col class="w-[6%]">
                <col class="w-[6%]">
                <col class="w-[6%]">
                <col class="w-[7%]">
                <col class="w-[9%]">
                <col class="w-[14%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N°/Sé</th>
                    <th>Groupe</th>
                    <th>Matière</th>
                    <th>Niveau</th>
                    <th>Prof</th>
                    <th>Effectif</th>
                    <th>Hr Début</th>
                    <th>Hr Fin</th>
                    <th>N° Salle</th>
                    <th>Statut</th>
                    <th>Remarque</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($sessions as $session)
                    @php
                        $group = $session->group;
                        $statut = $session->statut ?: 'actif';
                        $chip = $statutStyles[$statut] ?? $statutStyles['actif'];
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td class="font-medium text-slate-800 dark:text-slate-100">{{ $session->dateDisplay() }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">{{ $session->displayId() }}</td>
                        <td class="font-semibold text-blue-700 dark:text-blue-300">{{ $group?->displayId() ?: '—' }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $group?->matiere }}">{{ \App\Support\SubjectAbbreviation::display($group?->matiere) }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $niveaux[$group?->niveau] ?? $group?->niveau ?: '—' }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $group?->teacher?->nom_complet }}">{{ $group?->teacher?->nom_complet ?: '—' }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $group?->effectif() ?? 0 }}</td>
                        <td class="tabular-nums text-slate-700 dark:text-slate-200">{{ $session->heureDebutDisplay() }}</td>
                        <td class="tabular-nums text-slate-700 dark:text-slate-200">{{ $session->heureFinDisplay() }}</td>
                        <td class="font-semibold text-indigo-700 dark:text-indigo-300">{{ $session->numero_salle }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.sessions.update', $session) }}" class="session-status-form">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_statut_row" value="1">
                                <select name="statut" data-session-statut class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800 {{ $chip }}">
                                    <option value="actif" @selected($statut === 'actif')>Actif</option>
                                    <option value="reportee" @selected($statut === 'reportee')>Reportée</option>
                                    <option value="annulee" @selected($statut === 'annulee')>Annulée</option>
                                </select>
                                <input type="hidden" name="remarque" value="{{ $session->remarque }}" data-session-remarque-hidden>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.sessions.update', $session) }}" class="session-remark-form flex gap-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_statut_row" value="1">
                                <input type="hidden" name="statut" value="{{ $statut }}" data-session-statut-hidden>
                                <input type="text" name="remarque" value="{{ $session->remarque }}" maxlength="500" placeholder="Remarque…"
                                       data-session-remarque
                                       @disabled($statut === 'actif')
                                       class="min-w-0 flex-1 rounded-lg border px-2 py-1.5 text-xs outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 {{ $statut === 'actif' ? 'cursor-not-allowed border-slate-200 bg-slate-100 text-slate-400 dark:bg-slate-900 dark:text-slate-500' : 'border-slate-200 bg-white text-slate-800 dark:text-slate-100' }}">
                                <button type="submit" @disabled($statut === 'actif') class="rounded-lg bg-slate-900 px-2 py-1.5 text-[11px] font-bold text-white disabled:cursor-not-allowed disabled:opacity-40 dark:bg-white dark:text-slate-900">OK</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="!py-14 text-center text-sm text-slate-500">Aucune séance. Cliquez sur Ajouter pour en planifier une.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="seancePanel" method="POST" action="{{ route('admin.sessions.store') }}"
      class="{{ $showForm ? '' : 'hidden' }} min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf

    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-indigo-500/20 bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-5 text-white">
        <h2 class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Nouvelle séance</h2>
        <div class="rounded-2xl bg-white px-4 py-2 text-sm font-bold text-indigo-700">{{ $nextCode }}</div>
    </div>

    <div class="grid gap-6 bg-slate-50 p-6 dark:bg-slate-950/40 sm:p-8 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Date</label>
            <input type="date" name="date" value="{{ old('date', $defaultDate) }}" min="2026-01-01" max="2026-12-31" required
                   class="ep-keep-case w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">N°/Sé</label>
            <input id="sessionCode" type="text" value="{{ $nextCode }}" readonly
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div class="lg:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Groupe</label>
            <select id="sessionGroup" name="study_group_id" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($groups as $group)
                    <option value="{{ $group['id'] }}" @selected((string) old('study_group_id') === (string) $group['id'])>{{ $group['code'] }} · {{ $group['matiereLabel'] }} · {{ $group['niveauLabel'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Matière</label>
            <input id="sessionSubject" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Niveau</label>
            <input id="sessionLevel" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Prof</label>
            <input id="sessionTeacher" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Effectif</label>
            <input id="sessionEffectif" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Hr Début</label>
            <input type="text" name="heure_debut" value="{{ old('heure_debut', '09:00') }}" required
                   inputmode="numeric" maxlength="5" placeholder="14:00" pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" title="Format 24 h : HH:MM"
                   class="ep-keep-case ep-time-24 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm tabular-nums dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Hr Fin</label>
            <input type="text" name="heure_fin" value="{{ old('heure_fin', '10:00') }}" required
                   inputmode="numeric" maxlength="5" placeholder="16:00" pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$" title="Format 24 h : HH:MM"
                   class="ep-keep-case ep-time-24 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm tabular-nums dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">N° Salle</label>
            <input type="text" name="numero_salle" value="{{ old('numero_salle') }}" required maxlength="32" placeholder="001"
                   class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="seanceCancel" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
        <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20">Valider</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const groups = @json($groups);
    const nextCode = @json($nextCode);
    const table = document.getElementById('seanceTable');
    const panel = document.getElementById('seancePanel');
    const groupSelect = document.getElementById('sessionGroup');
    const subjectInput = document.getElementById('sessionSubject');
    const levelInput = document.getElementById('sessionLevel');
    const teacherInput = document.getElementById('sessionTeacher');
    const effectifInput = document.getElementById('sessionEffectif');

    const syncGroupFields = () => {
        const group = groups.find(item => item.id === Number(groupSelect.value));
        subjectInput.value = group?.matiereLabel || '';
        levelInput.value = group?.niveauLabel || '';
        teacherInput.value = group?.teacher || '';
        effectifInput.value = group ? String(group.effectif) : '';
    };

    const openPanel = () => {
        table.classList.add('hidden');
        panel.classList.remove('hidden');
        syncGroupFields();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    document.getElementById('seanceAdd')?.addEventListener('click', openPanel);
    document.getElementById('seanceCancel')?.addEventListener('click', closePanel);
    groupSelect?.addEventListener('change', syncGroupFields);

    document.querySelectorAll('[data-session-statut]').forEach(select => {
        select.addEventListener('change', () => {
            const row = select.closest('tr');
            const remarkInput = row?.querySelector('[data-session-remarque]');
            const remarkHidden = select.form?.querySelector('[data-session-remarque-hidden]');
            const statutHidden = row?.querySelector('[data-session-statut-hidden]');
            const isActif = select.value === 'actif';

            if (remarkInput) {
                remarkInput.disabled = isActif;
                remarkInput.classList.toggle('cursor-not-allowed', isActif);
                remarkInput.classList.toggle('bg-slate-100', isActif);
                remarkInput.classList.toggle('text-slate-400', isActif);
                if (isActif) {
                    remarkInput.value = '';
                }
            }
            if (remarkHidden) {
                remarkHidden.value = isActif ? '' : (remarkInput?.value || '');
            }
            if (statutHidden) {
                statutHidden.value = select.value;
            }

            if (!isActif && remarkInput && !remarkInput.value.trim()) {
                remarkInput.focus();
                return;
            }

            select.form?.submit();
        });
    });

    if (groupSelect?.value) {
        syncGroupFields();
    } else if (!panel.classList.contains('hidden')) {
        syncGroupFields();
    }
})();
</script>
@endpush
