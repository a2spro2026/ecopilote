@extends('admin.layout')

@section('title', 'Fiche Technique Élève')
@section('heading', 'Fiche Technique Élève')
@section('subtitle', 'Accès, matières, paiement et photo')

@section('content')
@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.students.technical.store') }}" enctype="multipart/form-data" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf
    <input type="hidden" name="student_id" id="studentId" value="{{ old('student_id') }}">

    <div class="border-b border-slate-200 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white dark:border-slate-800">
        <h2 class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Renseigner la fiche de l’élève</h2>
        <p class="mt-1 text-sm text-blue-50">Recherchez l’élève par son ID ou son nom, puis complétez les informations.</p>
    </div>

    <div class="grid gap-6 p-5 lg:grid-cols-[220px_1fr] sm:p-6">
        <aside>
            <label for="studentPhoto" class="group relative flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-blue-200 bg-blue-50/60 text-center transition hover:border-blue-400 dark:border-slate-700 dark:bg-slate-800">
                <img id="photoPreview" class="absolute inset-0 hidden h-full w-full object-cover" alt="Aperçu de la photo">
                <span id="photoPlaceholder" class="px-4 text-blue-600 dark:text-blue-300">
                    <svg class="mx-auto h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23l-1.134.175A2.17 2.17 0 0 0 2.25 9.574V18A2.25 2.25 0 0 0 4.5 20.25h15A2.25 2.25 0 0 0 21.75 18V9.574a2.17 2.17 0 0 0-1.802-2.169l-1.134-.175a2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.19 2.19 0 0 0-1.736-1.039h-5.232a2.19 2.19 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/></svg>
                    <strong class="mt-3 block text-sm">Importer une photo</strong>
                    <span class="mt-1 block text-[11px]">JPG, PNG ou WebP · 3 Mo</span>
                </span>
                <input id="studentPhoto" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
            </label>
            <p id="selectedStudentLabel" class="mt-3 text-center text-xs font-bold text-slate-500">Aucun élève sélectionné</p>
        </aside>

        <div class="space-y-6">
            <section class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="studentCode" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">ID de l’élève</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-8.25 6 2.25-2.25h9A2.25 2.25 0 0 0 18.75 13.5v-6A2.25 2.25 0 0 0 16.5 5.25h-9A2.25 2.25 0 0 0 5.25 7.5v10.5Z"/></svg>
                        </span>
                        <input id="studentCode" list="studentIdOptions" type="text" autocomplete="off" placeholder="Ex. EL0001"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm font-semibold uppercase outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:focus:ring-blue-900/30">
                        <datalist id="studentIdOptions">@foreach($eleves as $eleve)<option value="{{ $eleve->displayId() }}">{{ $eleve->nom_complet }}</option>@endforeach</datalist>
                    </div>
                </div>
                <div>
                    <label for="studentName" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nom complet</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0"/></svg>
                        </span>
                        <input id="studentName" list="studentNameOptions" type="text" autocomplete="off" placeholder="Ex. Yasmine Alaoui"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm font-semibold outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800 dark:focus:ring-blue-900/30">
                        <datalist id="studentNameOptions">@foreach($eleves as $eleve)<option value="{{ $eleve->nom_complet }}">{{ $eleve->displayId() }}</option>@endforeach</datalist>
                    </div>
                </div>
                <p id="studentSearchHint" class="text-xs font-semibold text-slate-500 sm:col-span-2">Saisissez l’ID pour afficher le nom, ou le nom pour afficher l’ID.</p>
            </section>

            <section class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Nom du tuteur</label><input id="guardianName" name="tuteur_nom" type="text" value="{{ old('tuteur_nom') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"></div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Contact du tuteur</label><input id="guardianContact" name="contact_tuteur" type="text" value="{{ old('contact_tuteur') }}" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"></div>
            </section>

            <section>
                <h3 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Matières</h3>
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach($matieres as $matiere)
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold hover:border-blue-300 dark:border-slate-700">
                            <input type="checkbox" name="matieres[]" value="{{ $matiere }}" class="subjectCheckbox rounded border-slate-300 text-blue-600 focus:ring-blue-500" @checked(in_array($matiere, old('matieres', []), true))>
                            {{ $matiere }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Paiement</label><input id="studentPayment" type="number" name="paiement" value="{{ old('paiement') }}" min="0" step="0.01" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"></div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Mode de paiement</label><select id="studentPaymentMode" name="mode_paiement" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"><option value="">Sélectionner</option><option value="virement">Virement</option><option value="cheque">Chèque</option><option value="especes">Espèces</option><option value="versement">Versement</option></select></div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Échéance</label><select id="studentPeriod" name="periode_paiement" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-800"><option value="">Sélectionner</option><option value="mois">Mois</option><option value="trimestre">Trimestre</option><option value="semestre">Semestre</option><option value="annuel">Annuel</option></select></div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Login</label>
                    <div class="flex overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <input id="studentLogin" name="login" type="text" value="{{ \App\Support\EcopiloteIdentity::localPart(old('login')) }}" required class="min-w-0 flex-1 border-0 bg-white px-3 py-2.5 text-sm outline-none dark:bg-slate-800">
                        <span class="flex items-center border-l border-slate-200 bg-slate-100 px-3 text-xs font-bold text-slate-500 dark:border-slate-700 dark:bg-slate-950">@ecopilote.ma</span>
                    </div>
                </div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-600 dark:text-slate-300">Mot de passe</label><input id="studentPassword" name="access_password" type="text" value="{{ old('access_password') }}" minlength="6" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm font-semibold outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"></div>
            </section>
        </div>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <a href="{{ route('admin.page.eleves') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</a>
        <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">Valider</button>
    </div>
</form>
@endsection

@push('scripts')
@php
    $studentsPayload = $eleves->map(fn ($student) => [
        'id' => $student->id,
        'code' => $student->displayId(),
        'name' => $student->nom_complet,
        'guardian' => $student->tuteur_nom,
        'guardianContact' => $student->contact_tuteur,
        'subjects' => array_map('trim', explode(',', (string) $student->matiere)),
        'payment' => $student->paiement,
        'mode' => $student->mode_paiement,
        'period' => $student->periode_paiement,
        'login' => \App\Support\EcopiloteIdentity::localPart($student->login),
        'password' => $student->access_password,
        'photo' => $student->photo_url,
    ])->values();
@endphp
<script>
(() => {
    const students = @json($studentsPayload);
    const codeInput = document.getElementById('studentCode');
    const nameInput = document.getElementById('studentName');
    const hint = document.getElementById('studentSearchHint');
    const hiddenId = document.getElementById('studentId');
    const label = document.getElementById('selectedStudentLabel');
    const normalize = value => (value ?? '').toString().trim().toLowerCase();

    const clearSelection = message => {
        hiddenId.value = '';
        label.textContent = 'Aucun élève sélectionné';
        hint.textContent = message;
        hint.className = 'text-xs font-semibold text-slate-500 sm:col-span-2';
    };

    const loadStudent = student => {
        if (!student) return;
        hiddenId.value = student.id;
        label.textContent = `${student.code} · ${student.name}`;
        hint.textContent = `Élève sélectionné : ${student.code} · ${student.name}`;
        hint.className = 'text-xs font-semibold text-emerald-600 dark:text-emerald-400 sm:col-span-2';
        document.getElementById('guardianName').value = student.guardian ?? '';
        document.getElementById('guardianContact').value = student.guardianContact ?? '';
        document.getElementById('studentPayment').value = student.payment ?? '';
        document.getElementById('studentPaymentMode').value = student.mode ?? '';
        document.getElementById('studentPeriod').value = student.period ?? '';
        document.getElementById('studentLogin').value = student.login ?? '';
        document.getElementById('studentPassword').value = student.password ?? '';
        document.querySelectorAll('.subjectCheckbox').forEach(box => box.checked = student.subjects.includes(box.value));
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        if (student.photo) { preview.src = student.photo; preview.classList.remove('hidden'); placeholder.classList.add('hidden'); }
        else { preview.removeAttribute('src'); preview.classList.add('hidden'); placeholder.classList.remove('hidden'); }
    };
    const findByCode = value => {
        const query = normalize(value);
        if (!query) return null;
        const exact = students.find(student => normalize(student.code) === query || String(student.id) === query);
        if (exact) return exact;
        const partial = students.filter(student => normalize(student.code).includes(query));
        return partial.length === 1 ? partial[0] : null;
    };

    const findByName = value => {
        const query = normalize(value);
        if (query.length < 2) return null;
        const exact = students.find(student => normalize(student.name) === query);
        if (exact) return exact;
        const partial = students.filter(student => normalize(student.name).includes(query));
        return partial.length === 1 ? partial[0] : null;
    };

    codeInput.addEventListener('input', () => {
        const student = findByCode(codeInput.value);
        if (student) {
            nameInput.value = student.name;
            loadStudent(student);
        } else {
            nameInput.value = '';
            clearSelection(codeInput.value.trim() ? 'Aucun élève ne correspond à cet ID.' : 'Saisissez l’ID pour afficher le nom, ou le nom pour afficher l’ID.');
        }
    });

    nameInput.addEventListener('input', () => {
        const student = findByName(nameInput.value);
        if (student) {
            codeInput.value = student.code;
            loadStudent(student);
        } else {
            codeInput.value = '';
            clearSelection(nameInput.value.trim() ? 'Aucun élève ne correspond à ce nom.' : 'Saisissez l’ID pour afficher le nom, ou le nom pour afficher l’ID.');
        }
    });

    document.getElementById('studentPhoto').addEventListener('change', event => {
        const file = event.target.files[0];
        if (!file) return;
        const preview = document.getElementById('photoPreview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        document.getElementById('photoPlaceholder').classList.add('hidden');
    });
    const oldId = Number(hiddenId.value);
    if (oldId) {
        const student = students.find(item => item.id === oldId);
        if (student) {
            codeInput.value = student.code;
            nameInput.value = student.name;
            loadStudent(student);
        }
    }
})();
</script>
@endpush
