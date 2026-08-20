@extends('admin.layout')

@section('title', 'Demandes')
@section('heading', 'Demandes')
@section('subtitle', 'Département élèves')

@section('content')
@php
    $suffix = $emailSuffix ?? \App\Support\EcopiloteIdentity::emailSuffix();
    $showEdit = old('_form') === 'demande_edit';
    $matieres = $matieres ?? [];
    $niveaux = $niveaux ?? [];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div id="demandeTable" class="{{ $showEdit ? 'hidden' : '' }} w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Demandes</h2>
            <p class="text-sm text-slate-500">{{ $demandes->count() }} demande(s) · renseignez Login et Mot de passe avant de valider</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            Fermer
        </a>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1400px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[7%]">
                <col class="w-[7%]">
                <col class="w-[12%]">
                <col class="w-[9%]">
                <col class="w-[8%]">
                <col class="w-[7%]">
                <col class="w-[7%]">
                <col class="w-[8%]">
                <col class="w-[14%]">
                <col class="w-[9%]">
                <col class="w-[12%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Demande</th>
                    <th>Nom Complet</th>
                    <th>Contact</th>
                    <th>Ville</th>
                    <th>Niveau</th>
                    <th>Matière</th>
                    <th>Type Cour</th>
                    <th>Login</th>
                    <th>Mot de passe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($demandes as $d)
                    @php
                        $isValidee = $d->etat === 'validee';
                        $formId = 'demande-validate-'.$d->id;
                        $loginLocal = \App\Support\EcopiloteIdentity::localPart($d->login ?: \App\Support\EcopiloteIdentity::loginFromName($d->nom_complet));
                        $shownLogin = $d->login ?: ($d->student?->login);
                        $shownPassword = $d->access_password;
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 {{ $isValidee ? 'opacity-70' : '' }}">
                        <td class="whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $d->created_at?->format('d/m/Y') ?: '—' }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">{{ $d->displayId() }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $d->nom_complet }}">{{ $d->nom_complet }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->contact }}">{{ $d->contact }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->ville }}">{{ $d->ville }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->niveau_scolaire }}">{{ $d->niveau_scolaire }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $d->matiere }}">{{ \App\Support\SubjectAbbreviation::display($d->matiere) }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $d->typeCoursLabel() }}</td>
                        <td>
                            @if ($isValidee)
                                <span class="ep-keep-case block truncate text-xs font-semibold text-slate-800 dark:text-slate-100" title="{{ $shownLogin }}">{{ $shownLogin ?: '—' }}</span>
                            @else
                                <div class="flex overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                                    <input form="{{ $formId }}" type="text" name="login" required
                                           value="{{ old('login', $loginLocal) }}"
                                           autocomplete="off" autocapitalize="off" spellcheck="false"
                                           class="ep-keep-case min-w-0 flex-1 border-0 bg-transparent px-2 py-1.5 text-xs outline-none"
                                           placeholder="identifiant">
                                    <span class="ep-keep-case flex shrink-0 items-center border-l border-slate-200 bg-slate-100 px-1.5 text-[10px] font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900">{{ $suffix }}</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if ($isValidee)
                                <span class="ep-keep-case block truncate font-mono text-xs font-semibold text-slate-800 dark:text-slate-100" title="{{ $shownPassword }}">{{ $shownPassword ?: '—' }}</span>
                            @else
                                <input form="{{ $formId }}" type="text" name="access_password" required minlength="6"
                                       value="{{ old('access_password', $d->access_password ?: '') }}"
                                       autocomplete="off"
                                       class="ep-keep-case w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 font-mono text-xs outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"
                                       placeholder="min. 6 car.">
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                <button type="button" data-edit-demande="{{ $d->id }}" title="Modifier" aria-label="Modifier"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                                @unless ($isValidee)
                                    <form id="{{ $formId }}" method="POST" action="{{ route('admin.students.applications.validate', $d) }}">
                                        @csrf
                                        <button type="submit" title="Valider" aria-label="Valider"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-white transition hover:bg-emerald-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.applications.pending', $d) }}">
                                        @csrf
                                        <button type="submit" title="En attente" aria-label="En attente"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-white transition
                                                    {{ $d->etat === 'en_attente'
                                                        ? 'bg-amber-500 ring-2 ring-amber-300 ring-offset-1 dark:ring-offset-slate-900'
                                                        : 'bg-amber-400 hover:bg-amber-500' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.applications.suspend', $d) }}">
                                        @csrf
                                        <button type="submit" title="Suspendre" aria-label="Suspendre"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-white transition
                                                    {{ $d->etat === 'suspendue'
                                                        ? 'bg-rose-600 ring-2 ring-rose-300 ring-offset-1 dark:ring-offset-slate-900'
                                                        : 'bg-rose-500 hover:bg-rose-600' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30">Validée</span>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="!py-14 text-center text-sm text-slate-500">Aucune demande pour le moment. Les inscriptions du portail étudiant apparaîtront ici.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<form id="demandePanel" method="POST" action=""
      class="{{ $showEdit ? '' : 'hidden' }} min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @csrf
    @method('PATCH')
    <input type="hidden" name="_form" value="demande_edit">

    <div class="flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-white/80">Modifier la demande</p>
            <h2 id="demandeEditTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Demande</h2>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-900">Enregistrer</button>
            <button type="button" id="demandeEditClose" class="rounded-xl border border-white/40 px-5 py-2.5 text-sm font-bold text-white hover:bg-white/10">Fermer</button>
        </div>
    </div>

    <div class="grid gap-4 p-6 sm:grid-cols-2 sm:p-8">
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Nom Complet</label>
            <input id="editNom" type="text" name="nom_complet" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Contact</label>
            <input id="editContact" type="text" name="contact" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Contact Tuteur</label>
            <input id="editContactTuteur" type="text" name="contact_tuteur" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Ville</label>
            <input id="editVille" type="text" name="ville" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Niveau</label>
            <input id="editNiveau" type="text" name="niveau_scolaire" required list="niveauxList" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
            <datalist id="niveauxList">
                @foreach ($niveaux as $key => $label)
                    <option value="{{ $label }}">{{ $key }}</option>
                @endforeach
            </datalist>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Matière</label>
            <input id="editMatiere" type="text" name="matiere" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Type Cour</label>
            <select id="editTypeCours" name="type_cours" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800">
                <option value="individuel">Individuel</option>
                <option value="en_groupe">En Groupe</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-slate-600">Login</label>
            <div class="flex overflow-hidden rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                <input id="editLogin" type="text" name="login" autocomplete="off" class="ep-keep-case min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-sm outline-none">
                <span class="ep-keep-case flex shrink-0 items-center border-l border-slate-200 bg-slate-100 px-3 text-xs font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-900">{{ $suffix }}</span>
            </div>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-semibold text-slate-600">Mot de passe</label>
            <input id="editPassword" type="text" name="access_password" minlength="6" autocomplete="off"
                   class="ep-keep-case w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 font-mono text-sm outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800"
                   placeholder="Laisser vide pour ne pas changer">
        </div>
    </div>
</form>
@endsection

@push('scripts')
@php
    $demandesPayload = $demandes->mapWithKeys(function ($d) {
        return [$d->id => [
            'id' => $d->id,
            'code' => $d->displayId(),
            'nom_complet' => $d->nom_complet,
            'contact' => $d->contact,
            'contact_tuteur' => $d->contact_tuteur,
            'ville' => $d->ville,
            'niveau_scolaire' => $d->niveau_scolaire,
            'matiere' => $d->matiere,
            'type_cours' => $d->type_cours,
            'login' => \App\Support\EcopiloteIdentity::localPart($d->login ?: \App\Support\EcopiloteIdentity::loginFromName($d->nom_complet)),
            'access_password' => $d->access_password ?: '',
            'updateUrl' => route('admin.students.applications.update', $d),
        ]];
    });
@endphp
<script>
(() => {
    const demandes = @json($demandesPayload);

    const table = document.getElementById('demandeTable');
    const panel = document.getElementById('demandePanel');
    const title = document.getElementById('demandeEditTitle');

    const openEdit = (id) => {
        const d = demandes[id];
        if (!d || !panel) return;
        panel.action = d.updateUrl;
        title.textContent = d.code + ' · ' + d.nom_complet;
        document.getElementById('editNom').value = d.nom_complet || '';
        document.getElementById('editContact').value = d.contact || '';
        document.getElementById('editContactTuteur').value = d.contact_tuteur || '';
        document.getElementById('editVille').value = d.ville || '';
        document.getElementById('editNiveau').value = d.niveau_scolaire || '';
        document.getElementById('editMatiere').value = d.matiere || '';
        document.getElementById('editTypeCours').value = d.type_cours || 'en_groupe';
        document.getElementById('editLogin').value = d.login || '';
        document.getElementById('editPassword').value = d.access_password || '';
        table?.classList.add('hidden');
        panel.classList.remove('hidden');
    };

    const closeEdit = () => {
        panel?.classList.add('hidden');
        table?.classList.remove('hidden');
    };

    document.querySelectorAll('[data-edit-demande]').forEach((btn) => {
        btn.addEventListener('click', () => openEdit(Number(btn.dataset.editDemande)));
    });
    document.getElementById('demandeEditClose')?.addEventListener('click', closeEdit);
})();
</script>
@endpush
