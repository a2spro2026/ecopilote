@extends('admin.layout')

@section('title', 'Modifier '.$eleve->displayId())
@section('heading', 'Modifier '.$eleve->displayId())
@section('subtitle', 'Fiche élève')

@section('content')
<a href="{{ route('admin.page.eleves') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
    ← Retour à la liste
</a>

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.students.update', $eleve) }}" class="max-w-3xl space-y-4">
    @csrf
    @method('PUT')

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-4 text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Informations</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">ID</label>
                <input type="text" value="{{ $eleve->displayId() }}" readonly
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Nom Complet</label>
                <input type="text" name="nom_complet" value="{{ old('nom_complet', $eleve->nom_complet) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Contact</label>
                <input type="text" name="contact" value="{{ old('contact', $eleve->contact) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Contact Tuteur</label>
                <input type="text" name="contact_tuteur" value="{{ old('contact_tuteur', $eleve->contact_tuteur) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Ville</label>
                <input type="text" name="ville" value="{{ old('ville', $eleve->ville) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Niveau Scolaire</label>
                <input type="text" name="niveau_scolaire" value="{{ old('niveau_scolaire', $eleve->niveau_scolaire) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Matière</label>
                <input type="text" name="matiere" value="{{ old('matiere', $eleve->matiere) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Type Cours</label>
                <select name="type_cours" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="individuel" @selected(old('type_cours', $eleve->type_cours) === 'individuel')>Individuel</option>
                    <option value="en_groupe" @selected(old('type_cours', $eleve->type_cours) === 'en_groupe')>En Groupe</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">État</label>
                <select name="etat" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="actif" @selected(old('etat', $eleve->etat) === 'actif')>Actif</option>
                    <option value="en_attente" @selected(old('etat', $eleve->etat) === 'en_attente')>En Attente</option>
                    <option value="suspendu" @selected(old('etat', $eleve->etat) === 'suspendu')>Suspendu</option>
                </select>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-1 text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Paiement</h2>
        <p class="mb-4 text-xs text-slate-500">À remplir manuellement</p>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Paiement</label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" name="paiement"
                           value="{{ old('paiement', $eleve->paiement !== null ? number_format((float) $eleve->paiement, 2, '.', '') : '') }}"
                           placeholder="0.00"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 pr-12 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-semibold text-slate-400">.00</span>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Échéance</label>
                <input type="date" name="echeance" value="{{ old('echeance', $eleve->echeance?->format('Y-m-d')) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
        </div>
    </section>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20">
            Enregistrer
        </button>
        <a href="{{ route('admin.page.eleves') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Annuler
        </a>
    </div>
</form>
@endsection
