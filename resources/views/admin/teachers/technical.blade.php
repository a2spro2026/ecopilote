@extends('admin.layout')

@section('title', 'Fiche Professeur')
@section('heading', 'Fiche Professeur')
@section('subtitle', 'Identité, matières, paiement et photo')

@section('content')
@php
    $showForm = $errors->any();
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div id="ficheProfTable" class="{{ $showForm ? 'hidden' : '' }} w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Fiche Professeur</h2>
            <p class="text-sm text-slate-500">{{ $professeurs->count() }} professeur(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="ficheProfAdd" class="rounded-xl bg-gradient-to-r from-violet-600 to-blue-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-violet-600/20">
                Ajouter
            </button>
            <a href="{{ route('admin.page.professeurs') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Fermer
            </a>
        </div>
    </div>

    @if ($professeurs->isEmpty())
        <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-6 py-14 text-center">
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Aucune fiche professeur</p>
                <p class="mt-1 text-xs text-slate-500">Cliquez sur Ajouter pour créer une fiche.</p>
            </div>
        </div>
    @else
        <div class="w-full overflow-x-auto">
            <table class="ep-table min-w-[1280px] w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[8%]">
                    <col class="w-[16%]">
                    <col class="w-[10%]">
                    <col class="w-[18%]">
                    <col class="w-[12%]">
                    <col class="w-[10%]">
                    <col class="w-[10%]">
                    <col class="w-[16%]">
                </colgroup>
                <thead>
                    <tr>
                        <th class="!px-2">ID</th>
                        <th>Nom Complet</th>
                        <th>Statut</th>
                        <th>Matière</th>
                        <th>Mode</th>
                        <th>Paiement</th>
                        <th>Échéance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($professeurs as $p)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="!px-2 font-semibold text-slate-900 dark:text-white">{{ $p->displayId() }}</td>
                            <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $p->nom_complet }}">{{ $p->nom_complet }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->statutLabel() }}</td>
                            <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $p->matiere ?: '—' }}">{{ \App\Support\SubjectAbbreviation::display($p->matiere) }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->paiementLabel() }}</td>
                            <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $p->montantDisplay() }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->periodePaiementLabel() }}</td>
                            <td>
                                <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.teachers.show', $p) }}" title="Voir" aria-label="Voir"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    <button type="button" data-edit-teacher="{{ $p->id }}" title="Modifier" aria-label="Modifier"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.teachers.suspend', $p) }}">
                                        @csrf
                                        <button type="submit" title="Suspendre" aria-label="Suspendre" @disabled($p->etat === 'suspendu')
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-600 text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-40">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.teachers.print', $p) }}" target="_blank" title="Imprimer" aria-label="Imprimer"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829h10.56M6.72 17.443h10.56M6.72 21h10.56A1.72 1.72 0 0 0 19 19.28V9.5H5v9.78A1.72 1.72 0 0 0 6.72 21ZM7 5V3h10v2m2 0H5a3 3 0 0 0-3 3v5h3V9.5h14V13h3V8a3 3 0 0 0-3-3Z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<form id="ficheProfPanel" method="POST" action="{{ route('admin.teachers.technical.store') }}" enctype="multipart/form-data"
      class="{{ $showForm ? '' : 'hidden' }} overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf
    <input type="hidden" name="teacher_id" id="teacherId" value="{{ old('teacher_id') }}">

    <div class="border-b border-slate-200 bg-gradient-to-r from-violet-600 to-blue-500 px-6 py-5 text-white dark:border-slate-800">
        <h2 id="ficheProfPanelTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Nouvelle fiche professeur</h2>
        <p class="mt-1 text-sm text-violet-50">Complétez l’identité, les matières, le paiement et la photo.</p>
    </div>

    <div class="grid gap-6 p-5 lg:grid-cols-[220px_1fr] sm:p-6">
        <aside>
            <label for="teacherPhoto" class="group relative flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-violet-200 bg-violet-50/60 text-center transition hover:border-violet-400 dark:border-slate-700 dark:bg-slate-800">
                <img id="teacherPhotoPreview" class="absolute inset-0 hidden h-full w-full object-cover" alt="Aperçu de la photo">
                <span id="teacherPhotoPlaceholder" class="px-4 text-violet-600 dark:text-violet-300">
                    <svg class="mx-auto h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23l-1.134.175A2.17 2.17 0 0 0 2.25 9.574V18A2.25 2.25 0 0 0 4.5 20.25h15A2.25 2.25 0 0 0 21.75 18V9.574a2.17 2.17 0 0 0-1.802-2.169l-1.134-.175a2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.19 2.19 0 0 0-1.736-1.039h-5.232a2.19 2.19 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/></svg>
                    <strong class="mt-3 block text-sm">Importer Photo</strong>
                    <span class="mt-1 block text-[11px]">JPG, PNG ou WebP · 3 Mo</span>
                </span>
                <input id="teacherPhoto" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
            </label>
            <p id="selectedTeacherLabel" class="mt-3 text-center text-xs font-bold text-slate-500">Nouvelle fiche</p>
        </aside>

        <div class="space-y-6">
            <section class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Nom Complet</label>
                    <input id="teacherName" name="nom_complet" type="text" value="{{ old('nom_complet') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-violet-500 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Statut</label>
                    <select id="teacherStatut" name="statut" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Sélectionner</option>
                        <option value="public" @selected(old('statut') === 'public')>Public</option>
                        <option value="prive" @selected(old('statut') === 'prive')>Privé</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Contact</label>
                    <input id="teacherContact" name="contact" type="text" value="{{ old('contact') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-violet-500 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Ville</label>
                    <input id="teacherCity" name="ville" type="text" value="{{ old('ville') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-violet-500 dark:border-slate-700 dark:bg-slate-800">
                </div>
            </section>

            <section>
                <h3 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Matière</h3>
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($matieres as $matiere)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold hover:border-violet-300 dark:border-slate-700">
                            <input type="checkbox" name="matieres[]" value="{{ $matiere }}" class="teacherSubject rounded border-slate-300 text-violet-600 focus:ring-violet-500" @checked(in_array($matiere, old('matieres', []), true))>
                            {{ $matiere }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Mode</label>
                    <select id="teacherMode" name="paiement" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Sélectionner</option>
                        <option value="salaire" @selected(old('paiement') === 'salaire')>Salaire</option>
                        <option value="commission" @selected(old('paiement') === 'commission')>Commission</option>
                        <option value="pourcentage" @selected(old('paiement') === 'pourcentage')>Pourcentage</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Paiement</label>
                    <input id="teacherPayment" type="number" name="paiement_valeur" value="{{ old('paiement_valeur') }}" min="0" step="0.01" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-violet-500 dark:border-slate-700 dark:bg-slate-800">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Échéance</label>
                    <select id="teacherPeriod" name="periode_paiement" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800">
                        <option value="">Sélectionner</option>
                        <option value="mois" @selected(old('periode_paiement') === 'mois')>Mois</option>
                        <option value="trimestre" @selected(old('periode_paiement') === 'trimestre')>Trimestre</option>
                        <option value="semestre" @selected(old('periode_paiement') === 'semestre')>Semestre</option>
                        <option value="annuel" @selected(old('periode_paiement') === 'annuel')>Annuel</option>
                    </select>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Login</label>
                    <div class="flex overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <input id="teacherLogin" name="login" type="text" value="{{ \App\Support\EcopiloteIdentity::localPart(old('login')) }}" required class="min-w-0 flex-1 border-0 bg-white px-3 py-2.5 text-sm outline-none dark:bg-slate-800">
                        <span class="flex items-center border-l border-slate-200 bg-slate-100 px-3 text-xs font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-950">{{ \App\Support\EcopiloteIdentity::emailSuffix() }}</span>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Mot de passe</label>
                    <input id="teacherPassword" name="access_password" type="text" value="{{ old('access_password') }}" minlength="6" required
                           class="ep-keep-case w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm font-semibold outline-none focus:border-violet-500 dark:border-slate-700 dark:bg-slate-800">
                </div>
            </section>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="ficheProfCancel" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
        <button type="submit" class="rounded-xl bg-gradient-to-r from-violet-600 to-blue-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-violet-600/20">Valider</button>
    </div>
</form>
@endsection

@push('scripts')
@php
    $teachersPayload = $professeurs->map(fn ($teacher) => [
        'id' => $teacher->id,
        'code' => $teacher->displayId(),
        'name' => $teacher->nom_complet,
        'contact' => $teacher->contact,
        'city' => $teacher->ville,
        'status' => $teacher->statut,
        'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) $teacher->matiere)))),
        'mode' => $teacher->paiement,
        'payment' => $teacher->paiement_valeur,
        'period' => $teacher->periode_paiement,
        'login' => \App\Support\EcopiloteIdentity::localPart($teacher->login),
        'password' => $teacher->access_password,
        'photo' => $teacher->photo_url,
    ])->values();
@endphp
<script>
(() => {
    const teachers = @json($teachersPayload);
    const table = document.getElementById('ficheProfTable');
    const panel = document.getElementById('ficheProfPanel');
    const hiddenId = document.getElementById('teacherId');
    const title = document.getElementById('ficheProfPanelTitle');
    const label = document.getElementById('selectedTeacherLabel');
    const nameInput = document.getElementById('teacherName');
    const loginInput = document.getElementById('teacherLogin');
    const preview = document.getElementById('teacherPhotoPreview');
    const placeholder = document.getElementById('teacherPhotoPlaceholder');

    const loginFromName = name => name
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '.')
        .replace(/^\.+|\.+$/g, '');

    const setPhoto = url => {
        if (url) {
            preview.src = url;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            return;
        }
        preview.removeAttribute('src');
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
    };

    const resetForm = () => {
        hiddenId.value = '';
        title.textContent = 'Nouvelle fiche professeur';
        label.textContent = 'Nouvelle fiche';
        nameInput.value = '';
        document.getElementById('teacherContact').value = '';
        document.getElementById('teacherCity').value = '';
        document.getElementById('teacherStatut').value = '';
        document.getElementById('teacherMode').value = '';
        document.getElementById('teacherPayment').value = '';
        document.getElementById('teacherPeriod').value = '';
        loginInput.value = '';
        document.getElementById('teacherPassword').value = '';
        document.getElementById('teacherPhoto').value = '';
        document.querySelectorAll('.teacherSubject').forEach(box => { box.checked = false; });
        setPhoto(null);
    };

    const loadTeacher = teacher => {
        hiddenId.value = teacher.id;
        title.textContent = 'Modifier la fiche professeur';
        label.textContent = `${teacher.code} · ${teacher.name}`;
        nameInput.value = teacher.name ?? '';
        document.getElementById('teacherContact').value = teacher.contact ?? '';
        document.getElementById('teacherCity').value = teacher.city ?? '';
        document.getElementById('teacherStatut').value = teacher.status ?? '';
        document.getElementById('teacherMode').value = teacher.mode ?? '';
        document.getElementById('teacherPayment').value = teacher.payment ?? '';
        document.getElementById('teacherPeriod').value = teacher.period ?? '';
        loginInput.value = teacher.login ?? '';
        document.getElementById('teacherPassword').value = teacher.password ?? '';
        document.querySelectorAll('.teacherSubject').forEach(box => {
            box.checked = teacher.subjects.includes(box.value);
        });
        setPhoto(teacher.photo);
    };

    const openPanel = teacher => {
        if (teacher) {
            loadTeacher(teacher);
        } else {
            resetForm();
        }
        table.classList.add('hidden');
        panel.classList.remove('hidden');
        nameInput.focus();
    };

    const closePanel = () => {
        panel.classList.add('hidden');
        table.classList.remove('hidden');
    };

    document.getElementById('ficheProfAdd')?.addEventListener('click', () => openPanel(null));
    document.getElementById('ficheProfCancel')?.addEventListener('click', closePanel);

    document.querySelectorAll('[data-edit-teacher]').forEach(button => {
        button.addEventListener('click', () => {
            const teacher = teachers.find(item => item.id === Number(button.dataset.editTeacher));
            if (teacher) openPanel(teacher);
        });
    });

    nameInput.addEventListener('input', () => {
        if (hiddenId.value) return;
        loginInput.value = loginFromName(nameInput.value);
    });

    document.getElementById('teacherPhoto').addEventListener('change', event => {
        const file = event.target.files[0];
        if (!file) return;
        setPhoto(URL.createObjectURL(file));
    });

    const oldId = Number(hiddenId.value);
    if (oldId) {
        const teacher = teachers.find(item => item.id === oldId);
        if (teacher) loadTeacher(teacher);
    }
})();
</script>
@endpush
