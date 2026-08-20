@extends('admin.layout')

@section('title', 'Groupe')
@section('heading', 'Groupe')
@section('subtitle', 'Planning')

@section('content')
@php
    $showForm = $errors->any();
    $niveaux = $niveaux ?? [];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div id="groupeTable" class="{{ $showForm ? 'hidden' : '' }} w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Groupe</h2>
            <p class="text-sm text-slate-500">{{ $groups->count() }} groupe(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="groupeAdd" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">
                Ajouter
            </button>
            <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Fermer
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1100px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[12%]">
                <col class="w-[12%]">
                <col class="w-[12%]">
                <col class="w-[22%]">
                <col class="w-[12%]">
                <col class="w-[14%]">
                <col class="w-[16%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Réf/G</th>
                    <th>Matière</th>
                    <th>Niveau</th>
                    <th>Nom Prof</th>
                    <th>Effectif</th>
                    <th>Revenus</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($groups as $group)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td class="font-semibold text-slate-900 dark:text-white">{{ $group->displayId() }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $group->matiere }}">{{ \App\Support\SubjectAbbreviation::display($group->matiere) }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $niveaux[$group->niveau] ?? $group->niveau }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $group->teacher?->nom_complet }}">{{ $group->teacher?->nom_complet ?: '—' }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $group->effectif() }}</td>
                        <td class="font-semibold text-emerald-700 dark:text-emerald-300">{{ $group->revenueDisplay() }}</td>
                        <td>
                            <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                <button type="button" data-view-group="{{ $group->id }}" title="Voir" aria-label="Voir"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </button>
                                <button type="button" data-edit-group="{{ $group->id }}" title="Modifier" aria-label="Modifier"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="!py-14 text-center text-sm text-slate-500">Aucun groupe. Cliquez sur Ajouter pour en créer un.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="groupePanel" method="POST" action="{{ route('admin.groups.store') }}"
      class="{{ $showForm ? '' : 'hidden' }} min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf
    <input type="hidden" name="group_id" id="groupId" value="{{ old('group_id') }}">

    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-blue-500/20 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white">
        <h2 id="groupePanelTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Nouveau groupe</h2>
        <div class="rounded-2xl bg-white px-5 py-2.5 shadow-lg shadow-blue-900/25">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Revenus</p>
            <p id="groupRevenueValue" class="text-xl font-black tabular-nums leading-none text-slate-900">0.00</p>
        </div>
    </div>

    <div class="grid gap-6 bg-slate-50 p-6 dark:bg-slate-950/40 sm:p-8 lg:grid-cols-2">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Réf/G</label>
            <input id="groupCode" type="text" value="{{ $nextCode }}" readonly
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-800 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Matière</label>
            <select id="groupSubject" name="matiere" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($matieres as $matiere)
                    <option value="{{ $matiere }}" @selected(old('matiere') === $matiere)>{{ \App\Support\SubjectAbbreviation::display($matiere) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Niveau</label>
            <select id="groupLevel" name="niveau" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($niveaux as $key => $label)
                    <option value="{{ $key }}" @selected(old('niveau') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Nom Prof</label>
            <select id="groupTeacher" name="teacher_id" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                <option value="">Sélectionner…</option>
                @foreach ($professeurs as $prof)
                    <option value="{{ $prof['id'] }}"
                            data-matieres="{{ implode(',', $prof['matieres']) }}"
                            data-niveau="{{ $prof['niveau_key'] }}"
                            @selected((string) old('teacher_id') === (string) $prof['id'])>
                        {{ $prof['nom'] }}
                    </option>
                @endforeach
            </select>
            <p id="teacherHint" class="mt-1 text-xs text-slate-500">Choisissez d’abord une matière et un niveau.</p>
        </div>
        <div class="lg:col-span-2">
            <label class="mb-2 block text-xs font-semibold text-slate-600 dark:text-slate-300">Élève</label>
            <div id="studentList" class="grid max-h-[22rem] gap-2 overflow-y-auto rounded-2xl border border-blue-100 bg-white p-3 dark:border-slate-700 dark:bg-slate-900 sm:grid-cols-2">
                @foreach ($eleves as $eleve)
                    <label class="student-option hidden cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm hover:border-emerald-400 hover:bg-emerald-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-emerald-500/40 dark:hover:bg-emerald-500/10"
                           data-matieres="{{ implode(',', $eleve['matieres']) }}"
                           data-niveau="{{ $eleve['niveau_key'] }}"
                           data-paiement="{{ number_format((float) $eleve['paiement'], 2, '.', '') }}">
                        <input type="checkbox" name="eleves[]" value="{{ $eleve['id'] }}" class="student-check rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(collect(old('eleves', []))->contains($eleve['id']))>
                        <span class="min-w-0">
                            <span class="block truncate font-semibold text-slate-800 dark:text-slate-100">{{ $eleve['nom'] }}</span>
                            <span class="block text-[11px] text-slate-500">{{ $eleve['code'] }} · {{ $eleve['niveau'] }} · Paiement {{ number_format((float) $eleve['paiement'], 2, '.', '') }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <p id="studentHint" class="mt-2 text-xs text-slate-500"></p>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="groupeCancel" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
        <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">Valider</button>
    </div>
</form>

<section id="groupeView" class="hidden min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-blue-500/20 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white">
        <h2 class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Fiche groupe</h2>
    </div>
    <dl id="groupeViewFields" class="grid gap-3 p-6 sm:grid-cols-2 sm:p-8"></dl>
    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="groupeViewClose" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
    </div>
</section>
@endsection

@push('scripts')
@php
    $groupsPayload = $groups->map(fn ($group) => [
        'id' => $group->id,
        'code' => $group->displayId(),
        'matiere' => $group->matiere,
        'matiereLabel' => \App\Support\SubjectAbbreviation::display($group->matiere),
        'niveau' => $group->niveau,
        'niveauLabel' => $niveaux[$group->niveau] ?? $group->niveau,
        'teacherId' => $group->teacher_id,
        'teacher' => $group->teacher?->nom_complet,
        'effectif' => $group->effectif(),
        'revenue' => $group->revenueDisplay(),
        'eleves' => $group->students->pluck('id')->all(),
        'eleveNames' => $group->students->pluck('nom_complet')->all(),
    ])->values();
@endphp
<script>
(() => {
    const groups = @json($groupsPayload);
    const nextCode = @json($nextCode);
    const table = document.getElementById('groupeTable');
    const panel = document.getElementById('groupePanel');
    const viewPanel = document.getElementById('groupeView');
    const hiddenId = document.getElementById('groupId');
    const title = document.getElementById('groupePanelTitle');
    const codeInput = document.getElementById('groupCode');
    const subjectSelect = document.getElementById('groupSubject');
    const levelSelect = document.getElementById('groupLevel');
    const teacherSelect = document.getElementById('groupTeacher');
    const studentOptions = [...document.querySelectorAll('.student-option')];
    const teacherOptions = [...teacherSelect.querySelectorAll('option')].filter(opt => opt.value);

    const fold = value => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();

    const hasSubject = (raw, wanted) => {
        if (!wanted) return false;
        const needle = fold(wanted);
        return String(raw || '').split(',').some(part => fold(part) === needle);
    };

    const matchesLevel = (raw, wanted) => !raw || raw === wanted;

    const syncLists = (keepTeacher = '') => {
        const matiere = subjectSelect.value;
        const niveau = levelSelect.value;
        const ready = Boolean(matiere && niveau);

        teacherOptions.forEach(opt => {
            const show = ready && hasSubject(opt.dataset.matieres, matiere) && matchesLevel(opt.dataset.niveau, niveau);
            opt.hidden = !show;
            opt.disabled = !show;
        });
        if (keepTeacher && teacherSelect.querySelector(`option[value="${keepTeacher}"]:not([disabled])`)) {
            teacherSelect.value = keepTeacher;
        } else if (teacherSelect.selectedOptions[0]?.disabled) {
            teacherSelect.value = '';
        }
        document.getElementById('teacherHint').textContent = ready
            ? 'Professeurs correspondant à la matière et au niveau.'
            : 'Choisissez d’abord une matière et un niveau.';

        let visibleStudents = 0;
        studentOptions.forEach(label => {
            const box = label.querySelector('.student-check');
            const show = ready && hasSubject(label.dataset.matieres, matiere) && matchesLevel(label.dataset.niveau, niveau);
            label.hidden = !show;
            label.classList.toggle('hidden', !show);
            label.classList.toggle('flex', show);
            if (!show) box.checked = false;
            else visibleStudents += 1;
        });
        document.getElementById('studentHint').textContent = ready
            ? (visibleStudents ? `${visibleStudents} élève(s) correspondant.` : 'Aucun élève pour cette matière et ce niveau.')
            : '';
        updateRevenue();
    };

    const updateRevenue = () => {
        const total = [...document.querySelectorAll('.student-check:checked')].reduce((sum, box) => {
            const label = box.closest('.student-option');
            if (!label || label.hidden || label.classList.contains('hidden')) return sum;
            return sum + Number(label.getAttribute('data-paiement') || 0);
        }, 0);
        const formatted = total.toFixed(2);
        const valueEl = document.getElementById('groupRevenueValue');
        if (valueEl) valueEl.textContent = formatted;
        document.querySelectorAll('.student-option').forEach(label => {
            const box = label.querySelector('.student-check');
            const on = Boolean(box?.checked) && !label.hidden && !label.classList.contains('hidden');
            label.classList.toggle('border-emerald-400', on);
            label.classList.toggle('bg-emerald-50', on);
            label.classList.toggle('ring-2', on);
            label.classList.toggle('ring-emerald-200', on);
        });
    };

    const resetForm = () => {
        hiddenId.value = '';
        title.textContent = 'Nouveau groupe';
        codeInput.value = nextCode;
        subjectSelect.value = '';
        levelSelect.value = '';
        teacherSelect.value = '';
        studentOptions.forEach(label => {
            label.querySelector('.student-check').checked = false;
        });
        syncLists();
    };

    const loadGroup = group => {
        hiddenId.value = group.id;
        title.textContent = 'Modifier le groupe';
        codeInput.value = group.code;
        subjectSelect.value = group.matiere;
        levelSelect.value = group.niveau;
        syncLists(String(group.teacherId));
        teacherSelect.value = String(group.teacherId);
        const selected = new Set((group.eleves || []).map(Number));
        studentOptions.forEach(label => {
            const box = label.querySelector('.student-check');
            box.checked = selected.has(Number(box.value));
            if (box.checked) {
                label.hidden = false;
                label.classList.remove('hidden');
                label.classList.add('flex');
            }
        });
        updateRevenue();
    };

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

    const openView = group => {
        document.getElementById('groupeViewFields').innerHTML = [
            field('Réf/G', group.code),
            field('Matière', group.matiereLabel),
            field('Niveau', group.niveauLabel),
            field('Nom Prof', group.teacher),
            field('Effectif', String(group.effectif)),
            field('Revenus', group.revenue),
            field('Élèves', (group.eleveNames || []).join(', ')),
        ].join('');
        table.classList.add('hidden');
        panel.classList.add('hidden');
        viewPanel.classList.remove('hidden');
    };

    const closeView = () => {
        viewPanel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    const openPanel = group => {
        viewPanel.classList.add('hidden');
        if (group) loadGroup(group);
        else resetForm();
        table.classList.add('hidden');
        panel.classList.remove('hidden');
        subjectSelect.focus();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    document.getElementById('groupeAdd')?.addEventListener('click', () => openPanel(null));
    document.getElementById('groupeCancel')?.addEventListener('click', closePanel);
    document.getElementById('groupeViewClose')?.addEventListener('click', closeView);
    subjectSelect.addEventListener('change', () => syncLists(teacherSelect.value));
    levelSelect.addEventListener('change', () => syncLists(teacherSelect.value));
    document.getElementById('studentList').addEventListener('change', updateRevenue);
    document.querySelectorAll('.student-check').forEach(box => {
        box.addEventListener('change', updateRevenue);
    });

    document.querySelectorAll('[data-view-group]').forEach(button => {
        button.addEventListener('click', () => {
            const group = groups.find(item => item.id === Number(button.dataset.viewGroup));
            if (group) openView(group);
        });
    });
    document.querySelectorAll('[data-edit-group]').forEach(button => {
        button.addEventListener('click', () => {
            const group = groups.find(item => item.id === Number(button.dataset.editGroup));
            if (group) openPanel(group);
        });
    });

    const oldId = Number(hiddenId.value);
    if (oldId) {
        const group = groups.find(item => item.id === oldId);
        if (group) loadGroup(group);
        else syncLists(teacherSelect.value);
    } else {
        syncLists();
    }
})();
</script>
@endpush
