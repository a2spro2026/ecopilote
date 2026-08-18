@extends('admin.layout')

@section('title', 'Nouvelle classe')
@section('heading', 'Nouvelle classe')
@section('subtitle', 'Enseignement')

@section('content')
@php
    $teachersJson = collect($professeurs)->values();
    $studentsJson = collect($eleves)->values();
@endphp

@if (session('success'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="classForm" method="POST" action="{{ route('admin.classes.store') }}" class="class-create-layout">
    @csrf
    <input type="hidden" name="numero" value="{{ $classNumber }}">
    <div id="elevesHidden"></div>

    <div class="class-create-main">
        <div class="class-create-blocks">
        {{-- Informations générales --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Informations générales</h2>
                    <p class="text-[11px] text-slate-500">Identité et paramétrage</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Numéro de classe</label>
                    <input type="text" value="{{ $classNumber }}" readonly
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Statut</label>
                    <select name="statut" id="fieldStatut" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                        <option value="active" @selected(old('statut', 'active') === 'active')>Active</option>
                        <option value="suspendue" @selected(old('statut') === 'suspendue')>Suspendue</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Matière</label>
                    <select name="matiere" id="fieldMatiere" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Sélectionner…</option>
                        @foreach ($matieres as $m)
                            <option value="{{ $m }}" @selected(old('matiere') === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Niveau scolaire</label>
                    <select name="niveau" id="fieldNiveau" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Sélectionner…</option>
                        @foreach ($niveaux as $key => $n)
                            <option value="{{ $key }}" @selected(old('niveau') === $key)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Type</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50 dark:border-slate-700 dark:has-[:checked]:bg-violet-500/10">
                            <input type="radio" name="type" id="typeIndividuelle" value="individuelle" class="text-violet-600" @checked(old('type', 'individuelle') === 'individuelle')>
                            <span>
                                <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">Individuelle</span>
                                <span class="block text-[10px] text-slate-500">1 seul élève</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:border-slate-700 dark:has-[:checked]:bg-blue-500/10">
                            <input type="radio" name="type" id="typeGroupe" value="groupe" class="text-blue-600" @checked(old('type') === 'groupe')>
                            <span>
                                <span class="block text-xs font-semibold text-slate-800 dark:text-slate-100">Groupe</span>
                                <span class="block text-[10px] text-slate-500">Plusieurs élèves</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </section>

        {{-- Élèves --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-violet-500 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m-7.5 2.72a3 3 0 0 1-4.682 2.72 9.094 9.094 0 0 1 3.741.479"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Élèves</h2>
                    <p class="text-[11px] text-slate-500" id="studentsHint">Choisissez une matière et un niveau</p>
                </div>
            </div>

            <input type="search" id="studentSearch" autocomplete="off"
                   class="mb-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-100 dark:border-slate-700 dark:bg-slate-800"
                   placeholder="Rechercher un élève…">
            <div id="studentResults" class="mb-2 max-h-56 space-y-1.5 overflow-y-auto"></div>
            <div id="selectedStudents" class="grid gap-2 sm:grid-cols-2"></div>
        </section>

        {{-- Professeur --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-500 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Professeur</h2>
                    <p class="text-[11px] text-slate-500" id="teacherHint">Choisissez une matière</p>
                </div>
            </div>

            <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Rechercher</label>
            <input type="search" id="teacherSearch" autocomplete="off"
                   class="mb-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800"
                   placeholder="Nom, matière…">
            <input type="hidden" name="professeur_id" id="fieldProfesseurId" value="{{ old('professeur_id') }}">

            <div id="teacherResults" class="mb-2 max-h-56 space-y-1.5 overflow-y-auto"></div>
            <div id="teacherCard" class="hidden rounded-xl border border-emerald-200 bg-emerald-50/60 p-3 dark:border-emerald-500/30 dark:bg-emerald-500/10"></div>
        </section>

        {{-- Planning --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500 text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/></svg>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Planning</h2>
                    <p class="text-[11px] text-slate-500">Jours, horaires et période</p>
                </div>
            </div>

            <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">Jour(s)</label>
            <div class="mb-3 flex flex-wrap gap-1.5">
                @foreach ($jours as $jour)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="jours[]" value="{{ $jour }}" class="peer sr-only day-check" @checked(collect(old('jours', []))->contains($jour))>
                        <span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-800 dark:border-slate-700 dark:text-slate-300 dark:peer-checked:bg-amber-500/15 dark:peer-checked:text-amber-200">{{ $jour }}</span>
                    </label>
                @endforeach
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Heure de début</label>
                    <input type="time" name="heure_debut" id="fieldHeureDebut" value="{{ old('heure_debut', '09:00') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Heure de fin</label>
                    <input type="time" name="heure_fin" id="fieldHeureFin" value="{{ old('heure_fin', '10:00') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Date de début</label>
                    <input type="date" name="date_debut" id="fieldDateDebut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Date de fin</label>
                    <input type="date" name="date_fin" id="fieldDateFin" value="{{ old('date_fin') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 dark:border-slate-700 dark:bg-slate-800 disabled:opacity-50">
                    <label class="mt-1.5 flex items-center gap-2 text-[11px] font-medium text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="sans_date_fin" id="fieldSansFin" value="1" class="rounded border-slate-300 text-amber-600" @checked(old('sans_date_fin'))>
                        Sans date de fin
                    </label>
                </div>
            </div>
        </section>
        </div>

        <div class="mt-4 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.page.classes') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                Annuler
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 hover:opacity-95">
                Créer la classe
            </button>
        </div>
    </div>

    {{-- Résumé --}}
    <aside class="class-create-summary">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Résumé de la classe</p>
            <p id="sumNumero" class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $classNumber }}</p>
            <p id="sumEmptyHint" class="mt-2 text-[11px] leading-relaxed text-slate-400">
                Complétez le formulaire : les informations apparaîtront ici en temps réel.
            </p>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Matière</dt>
                    <dd id="sumMatiere" class="text-right font-semibold text-slate-400">Non renseignée</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Niveau</dt>
                    <dd id="sumNiveau" class="text-right font-semibold text-slate-400">Non renseigné</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Type</dt>
                    <dd id="sumType" class="text-right font-semibold text-slate-800 dark:text-slate-100">Individuelle</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Professeur</dt>
                    <dd id="sumProf" class="text-right font-semibold text-slate-400">Non sélectionné</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Élèves</dt>
                    <dd id="sumElevesCount" class="text-right font-semibold text-slate-400">0</dd>
                </div>
                <div class="border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="mb-1.5 text-slate-500">Liste</dt>
                    <dd id="sumElevesList" class="flex flex-wrap gap-1.5 text-xs font-medium text-slate-400">Aucun élève pour le moment</dd>
                </div>
                <div class="border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="mb-1.5 text-slate-500">Jours</dt>
                    <dd id="sumJours" class="flex flex-wrap justify-end gap-1.5 text-right font-semibold text-slate-400">Aucun jour</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-slate-100 pb-2 dark:border-slate-800">
                    <dt class="shrink-0 text-slate-500">Horaire</dt>
                    <dd id="sumHoraire" class="text-right font-semibold text-slate-400">—</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="shrink-0 text-slate-500">Statut</dt>
                    <dd id="sumStatut" class="text-right font-semibold text-emerald-600">Active</dd>
                </div>
            </dl>

            <p id="formError" class="mt-4 hidden rounded-xl bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700 dark:bg-rose-500/10 dark:text-rose-300"></p>
        </div>
    </aside>
</form>
@endsection

@push('scripts')
<script>
(() => {
    const teachers = @json($teachersJson);
    const students = @json($studentsJson);
    const classNumber = @json($classNumber);
    const levelLabels = @json($niveaux);
    const oldStudentIds = @json(array_map('intval', old('eleves', [])));

    let selectedTeacher = teachers.find(t => String(t.id) === String(document.getElementById('fieldProfesseurId').value)) || null;
    let selectedStudents = students.filter(s => oldStudentIds.includes(Number(s.id)));

    const els = {
        matiere: document.getElementById('fieldMatiere'),
        niveau: document.getElementById('fieldNiveau'),
        statut: document.getElementById('fieldStatut'),
        profId: document.getElementById('fieldProfesseurId'),
        teacherSearch: document.getElementById('teacherSearch'),
        teacherResults: document.getElementById('teacherResults'),
        teacherCard: document.getElementById('teacherCard'),
        studentSearch: document.getElementById('studentSearch'),
        studentResults: document.getElementById('studentResults'),
        selectedStudents: document.getElementById('selectedStudents'),
        studentsHint: document.getElementById('studentsHint'),
        teacherHint: document.getElementById('teacherHint'),
        heureDebut: document.getElementById('fieldHeureDebut'),
        heureFin: document.getElementById('fieldHeureFin'),
        dateDebut: document.getElementById('fieldDateDebut'),
        dateFin: document.getElementById('fieldDateFin'),
        sansFin: document.getElementById('fieldSansFin'),
        elevesHidden: document.getElementById('elevesHidden'),
        formError: document.getElementById('formError'),
        form: document.getElementById('classForm'),
        emptyHint: document.getElementById('sumEmptyHint'),
    };

    const filled = 'text-right font-semibold text-slate-800 dark:text-slate-100';
    const empty = 'text-right font-semibold text-slate-400';

    function classType() {
        return document.querySelector('input[name="type"]:checked')?.value || 'individuelle';
    }

    function setText(id, value, isEmpty) {
        const node = document.getElementById(id);
        if (!node) return;
        node.textContent = value;
        node.className = isEmpty ? empty : filled;
    }

    function hasSubject(list, subject) {
        if (!subject) return false;
        const wanted = String(subject).trim().toLowerCase();
        return (list || []).some((item) => String(item).trim().toLowerCase() === wanted);
    }

    function studentLevelKey(student) {
        if (student.niveau_key) return student.niveau_key;
        const text = String(student.niveau || '').toLowerCase();
        if (text.includes('coran')) return 'coran';
        if (text.includes('prim') || /\bcp\b|\bce1\b|\bce2\b|\bcm1\b|\bcm2\b/.test(text)) return 'primaire';
        if (text.includes('lyc') || text.includes('2nde') || text.includes('1ère') || text.includes('1ere') || text.includes('terminale')) return 'lycee';
        if (text.includes('coll') || text.includes('6ème') || text.includes('6eme') || text.includes('5ème') || text.includes('5eme') || text.includes('4ème') || text.includes('4eme') || text.includes('3ème') || text.includes('3eme') || text.includes('3e ')) return 'college';
        return null;
    }

    function studentMatchesFilters(student, matiere, niveau) {
        if (!matiere || !niveau) return false;
        if (studentLevelKey(student) !== niveau) return false;
        return hasSubject(student.matieres, matiere);
    }

    function compatibleTeachers() {
        const matiere = els.matiere.value;
        const q = els.teacherSearch.value.trim().toLowerCase();
        if (!matiere) return [];
        return teachers.filter(t => {
            if (t.statut !== 'validé') return false;
            if (!hasSubject(t.matieres, matiere)) return false;
            if (!q) return true;
            return t.nom.toLowerCase().includes(q) || t.matieres.join(' ').toLowerCase().includes(q);
        });
    }

    function renderTeachers() {
        const matiere = els.matiere.value;
        if (els.teacherHint) {
            els.teacherHint.textContent = matiere
                ? 'Tous les professeurs de cette matière'
                : 'Choisissez une matière';
        }

        if (!matiere) {
            els.teacherResults.innerHTML = `<p class="text-xs text-slate-500">Sélectionnez une matière pour afficher les professeurs.</p>`;
            return;
        }

        const list = compatibleTeachers();
        els.teacherResults.innerHTML = list.length ? list.map(t => `
            <button type="button" data-id="${t.id}" class="teacher-pick flex w-full items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5 text-left text-sm hover:border-emerald-400 hover:bg-emerald-50 dark:border-slate-700 dark:hover:bg-emerald-500/10">
                <span>
                    <span class="block font-semibold text-slate-800 dark:text-slate-100">${t.nom}</span>
                    <span class="block text-xs text-slate-500">${t.matieres.join(', ')}</span>
                </span>
                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Validé</span>
            </button>
        `).join('') : `<p class="text-xs text-slate-500">Aucun professeur validé pour cette matière.</p>`;

        els.teacherResults.querySelectorAll('.teacher-pick').forEach(btn => {
            btn.addEventListener('click', () => {
                selectedTeacher = teachers.find(t => t.id === Number(btn.dataset.id));
                els.profId.value = selectedTeacher.id;
                renderTeacherCard();
                updateSummary();
            });
        });
    }

    function renderTeacherCard() {
        if (!selectedTeacher) {
            els.teacherCard.classList.add('hidden');
            els.teacherCard.innerHTML = '';
            return;
        }
        els.teacherCard.classList.remove('hidden');
        els.teacherCard.innerHTML = `
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-bold text-slate-900 dark:text-white">${selectedTeacher.nom}</p>
                    <p class="mt-1 text-xs text-slate-600 dark:text-slate-300"><span class="font-semibold">Matières :</span> ${selectedTeacher.matieres.join(', ')}</p>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300"><span class="font-semibold">Niveaux :</span> ${selectedTeacher.niveaux.join(', ')}</p>
                    <p class="mt-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Statut : Validé</p>
                </div>
                <button type="button" id="clearTeacher" class="text-xs font-semibold text-rose-600">Retirer</button>
            </div>`;
        document.getElementById('clearTeacher')?.addEventListener('click', () => {
            selectedTeacher = null;
            els.profId.value = '';
            renderTeacherCard();
            updateSummary();
        });
    }

    function renderStudentResults() {
        const matiere = els.matiere.value;
        const niveau = els.niveau.value;
        const q = els.studentSearch.value.trim().toLowerCase();
        const selectedIds = selectedStudents.map(s => s.id);

        if (!matiere || !niveau) {
            els.studentResults.innerHTML = `<p class="text-xs text-slate-500">Sélectionnez une matière et un niveau pour afficher les élèves.</p>`;
            return;
        }

        const list = students.filter(s => {
            if (selectedIds.includes(s.id)) return false;
            if (!studentMatchesFilters(s, matiere, niveau)) return false;
            if (!q) return true;
            return s.nom.toLowerCase().includes(q)
                || String(s.niveau).toLowerCase().includes(q)
                || (s.matieres || []).join(' ').toLowerCase().includes(q);
        });

        els.studentResults.innerHTML = list.length ? list.map(s => `
            <button type="button" data-id="${s.id}" class="student-pick flex w-full items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5 text-left text-sm hover:border-violet-400 hover:bg-violet-50 dark:border-slate-700 dark:hover:bg-violet-500/10">
                <span>
                    <span class="block font-semibold text-slate-800 dark:text-slate-100">${s.nom}</span>
                    <span class="block text-xs text-slate-500">${s.niveau}${(s.matieres || []).length ? ' · ' + s.matieres.join(', ') : ''}</span>
                </span>
                <span class="text-xs font-semibold text-violet-600">Ajouter</span>
            </button>
        `).join('') : `<p class="text-xs text-slate-500">Aucun élève pour cette matière et ce niveau.</p>`;

        els.studentResults.querySelectorAll('.student-pick').forEach(btn => {
            btn.addEventListener('click', () => {
                const student = students.find(s => s.id === Number(btn.dataset.id));
                if (!student) return;
                if (classType() === 'individuelle') {
                    selectedStudents = [student];
                } else if (!selectedStudents.find(s => s.id === student.id)) {
                    selectedStudents.push(student);
                }
                renderSelectedStudents();
                renderStudentResults();
                updateSummary();
            });
        });
    }

    function renderSelectedStudents() {
        syncElevesHidden();
        if (!els.matiere.value || !els.niveau.value) {
            els.studentsHint.textContent = 'Choisissez une matière et un niveau';
        } else {
            els.studentsHint.textContent = classType() === 'individuelle'
                ? 'Élèves du niveau et de la matière choisis — 1 élève'
                : 'Élèves du niveau et de la matière choisis';
        }

        els.selectedStudents.innerHTML = selectedStudents.length ? selectedStudents.map(s => `
            <div class="rounded-2xl border border-violet-200 bg-violet-50/50 p-3 dark:border-violet-500/30 dark:bg-violet-500/10">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">${s.nom}</p>
                        <p class="text-xs text-slate-500">${s.niveau}</p>
                    </div>
                    <button type="button" data-id="${s.id}" class="remove-student text-xs font-semibold text-rose-600">Retirer</button>
                </div>
            </div>
        `).join('') : `<p class="text-xs text-slate-500 sm:col-span-2">Aucun élève sélectionné.</p>`;

        els.selectedStudents.querySelectorAll('.remove-student').forEach(btn => {
            btn.addEventListener('click', () => {
                selectedStudents = selectedStudents.filter(s => s.id !== Number(btn.dataset.id));
                renderSelectedStudents();
                renderStudentResults();
                updateSummary();
            });
        });
    }

    function syncElevesHidden() {
        els.elevesHidden.innerHTML = selectedStudents.map(s =>
            `<input type="hidden" name="eleves[]" value="${s.id}">`
        ).join('');
    }

    function updateSummary() {
        document.getElementById('sumNumero').textContent = classNumber;

        const matiere = els.matiere.value;
        const niveau = els.niveau.value;
        const niveauLabel = niveau ? (levelLabels[niveau] || niveau) : '';
        const typeLabel = classType() === 'individuelle' ? 'Individuelle' : 'Groupe';
        const days = [...document.querySelectorAll('.day-check:checked')].map(c => c.value);
        const hasSchedule = Boolean(els.heureDebut.value && els.heureFin.value);

        setText('sumMatiere', matiere || 'Non renseignée', !matiere);
        setText('sumNiveau', niveauLabel || 'Non renseigné', !niveau);
        setText('sumType', typeLabel, false);
        setText('sumProf', selectedTeacher?.nom || 'Non sélectionné', !selectedTeacher);

        const countEl = document.getElementById('sumElevesCount');
        countEl.textContent = String(selectedStudents.length);
        countEl.className = selectedStudents.length ? filled : empty;

        const listEl = document.getElementById('sumElevesList');
        if (selectedStudents.length) {
            listEl.className = 'flex flex-wrap gap-1.5';
            listEl.innerHTML = selectedStudents.map(s =>
                `<span class="inline-flex rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 dark:bg-violet-500/15 dark:text-violet-300">${s.nom}</span>`
            ).join('');
        } else {
            listEl.className = 'text-xs font-medium text-slate-400';
            listEl.textContent = 'Aucun élève pour le moment';
        }

        const daysEl = document.getElementById('sumJours');
        if (days.length) {
            daysEl.className = 'flex flex-wrap justify-end gap-1.5';
            daysEl.innerHTML = days.map(d =>
                `<span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-500/15 dark:text-amber-200">${d}</span>`
            ).join('');
        } else {
            daysEl.className = 'text-right font-semibold text-slate-400';
            daysEl.textContent = 'Aucun jour';
        }

        setText('sumHoraire', hasSchedule ? `${els.heureDebut.value} – ${els.heureFin.value}` : '—', !hasSchedule);

        const statutEl = document.getElementById('sumStatut');
        const isActive = els.statut.value === 'active';
        statutEl.textContent = isActive ? 'Active' : 'Suspendue';
        statutEl.className = 'text-right font-semibold ' + (isActive ? 'text-emerald-600' : 'text-amber-600');

        const hasContent = Boolean(matiere || niveau || selectedTeacher || selectedStudents.length || days.length);
        els.emptyHint.classList.toggle('hidden', hasContent);
    }

    function showError(msg) {
        els.formError.textContent = msg;
        els.formError.classList.remove('hidden');
    }

    function validateClient() {
        els.formError.classList.add('hidden');

        if (!selectedTeacher || selectedTeacher.statut !== 'validé') {
            showError('Sélectionnez un professeur validé.');
            return false;
        }
        if (els.matiere.value && !hasSubject(selectedTeacher.matieres, els.matiere.value)) {
            showError('Ce professeur n’enseigne pas la matière sélectionnée.');
            return false;
        }
        if (!selectedStudents.length) {
            showError('Ajoutez au moins un élève.');
            return false;
        }
        if (!selectedStudents.every(s => studentMatchesFilters(s, els.matiere.value, els.niveau.value))) {
            showError('Chaque élève doit correspondre à la matière et au niveau sélectionnés.');
            return false;
        }
        if (classType() === 'individuelle' && selectedStudents.length > 1) {
            showError('Une classe individuelle ne peut contenir qu’un seul élève.');
            return false;
        }
        const days = [...document.querySelectorAll('.day-check:checked')];
        if (!days.length) {
            showError('Sélectionnez au moins un jour.');
            return false;
        }
        if (!els.heureDebut.value || !els.heureFin.value || els.heureFin.value <= els.heureDebut.value) {
            showError('L’horaire est invalide (fin après début).');
            return false;
        }
        if (!els.sansFin.checked) {
            if (!els.dateFin.value) {
                showError('Indiquez une date de fin ou cochez « Sans date de fin ».');
                return false;
            }
            if (els.dateFin.value < els.dateDebut.value) {
                showError('La date de fin ne peut pas être antérieure à la date de début.');
                return false;
            }
        }
        return true;
    }

    els.sansFin.addEventListener('change', () => {
        els.dateFin.disabled = els.sansFin.checked;
        if (els.sansFin.checked) els.dateFin.value = '';
        updateSummary();
    });

    document.querySelectorAll('input[name="type"]').forEach(r => {
        r.addEventListener('change', () => {
            if (classType() === 'individuelle' && selectedStudents.length > 1) {
                selectedStudents = selectedStudents.slice(0, 1);
            }
            renderSelectedStudents();
            renderStudentResults();
            updateSummary();
        });
    });

    function syncFilters() {
        const matiere = els.matiere.value;
        const niveau = els.niveau.value;

        selectedStudents = selectedStudents.filter(s => studentMatchesFilters(s, matiere, niveau));
        if (selectedTeacher && matiere && !hasSubject(selectedTeacher.matieres, matiere)) {
            selectedTeacher = null;
            els.profId.value = '';
            renderTeacherCard();
        }

        renderSelectedStudents();
        renderStudentResults();
        renderTeachers();
    }

    els.form.addEventListener('input', (e) => {
        if (['fieldMatiere', 'fieldNiveau'].includes(e.target.id)) {
            syncFilters();
        }
        updateSummary();
    });

    els.form.addEventListener('change', (e) => {
        if (e.target.classList.contains('day-check') || ['fieldMatiere', 'fieldNiveau', 'fieldStatut', 'fieldHeureDebut', 'fieldHeureFin', 'fieldDateDebut', 'fieldDateFin'].includes(e.target.id)) {
            if (['fieldMatiere', 'fieldNiveau'].includes(e.target.id)) {
                syncFilters();
            }
            updateSummary();
        }
    });

    els.teacherSearch.addEventListener('input', renderTeachers);
    els.studentSearch.addEventListener('input', renderStudentResults);

    els.form.addEventListener('submit', (e) => {
        syncElevesHidden();
        if (!validateClient()) e.preventDefault();
    });

    if (els.sansFin.checked) els.dateFin.disabled = true;
    renderTeachers();
    renderTeacherCard();
    renderSelectedStudents();
    renderStudentResults();
    updateSummary();
})();
</script>
@endpush
