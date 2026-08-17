@extends('admin.layout')

@section('title', 'Modifier '.$professeur->displayId())
@section('heading', 'Modifier '.$professeur->displayId())
@section('subtitle', 'Fiche professeur')

@section('content')
<a href="{{ route('admin.page.professeurs') }}" class="mb-5 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
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

<form method="POST" action="{{ route('admin.teachers.update', $professeur) }}" class="max-w-3xl space-y-4">
    @csrf
    @method('PUT')

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-4 text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Informations</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">ID</label>
                <input type="text" value="{{ $professeur->displayId() }}" readonly
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Nom Complet</label>
                <input type="text" name="nom_complet" value="{{ old('nom_complet', $professeur->nom_complet) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Login</label>
                <div class="flex overflow-hidden rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                    <input type="text" value="{{ \App\Support\EcopiloteIdentity::localPart(old('login', $professeur->login ?: \App\Support\EcopiloteIdentity::loginFromName(old('nom_complet', $professeur->nom_complet)))) }}" readonly
                           class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm font-semibold text-slate-700 outline-none dark:text-slate-200">
                    <span class="flex shrink-0 items-center border-l border-slate-200 bg-slate-100 px-3 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        @ecopilote.ma
                    </span>
                </div>
                <p class="mt-1 text-[11px] text-slate-500">Le login est généré automatiquement à partir du nom (ex. nadia.el.amrani@ecopilote.ma).</p>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Mot de passe</label>
                <input type="text" name="access_password" value="{{ old('access_password', $professeur->access_password) }}" required minlength="6"
                       class="ep-keep-case w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Contact</label>
                <input type="text" name="contact" value="{{ old('contact', $professeur->contact) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Ville</label>
                <input type="text" name="ville" value="{{ old('ville', $professeur->ville) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Matière</label>
                <input type="text" name="matiere" value="{{ old('matiere', $professeur->matiere) }}" required
                       class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Statut</label>
                <select name="statut" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="public" @selected(old('statut', $professeur->statut) === 'public')>Public</option>
                    <option value="prive" @selected(old('statut', $professeur->statut) === 'prive')>Privé</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Disponibilité</label>
                <select name="disponibilite" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="immediat" @selected(old('disponibilite', $professeur->disponibilite) === 'immediat')>Immédiat</option>
                    <option value="a_negocier" @selected(old('disponibilite', $professeur->disponibilite) === 'a_negocier')>À négocier</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">État</label>
                <select name="etat" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="actif" @selected(old('etat', $professeur->etat) === 'actif')>Actif</option>
                    <option value="en_attente" @selected(old('etat', $professeur->etat) === 'en_attente')>En Attente</option>
                    <option value="suspendu" @selected(old('etat', $professeur->etat) === 'suspendu')>Suspendu</option>
                </select>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="mb-1 text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Paiement</h2>
        <p class="mb-4 text-xs text-slate-500">À remplir manuellement</p>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Mode</label>
                <select name="paiement" id="fieldPaiement"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="">Sélectionner…</option>
                    <option value="salaire" @selected(old('paiement', $professeur->paiement) === 'salaire')>Salaire</option>
                    <option value="commission" @selected(old('paiement', $professeur->paiement) === 'commission')>Commission</option>
                    <option value="pourcentage" @selected(old('paiement', $professeur->paiement) === 'pourcentage')>Pourcentage</option>
                </select>
            </div>
            <div id="paiementValeurWrap" class="{{ in_array(old('paiement', $professeur->paiement), ['salaire', 'commission', 'pourcentage'], true) ? '' : 'hidden' }}">
                <label id="paiementValeurLabel" class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Montant</label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" name="paiement_valeur" id="fieldPaiementValeur"
                           value="{{ old('paiement_valeur', $professeur->paiement_valeur !== null ? number_format((float) $professeur->paiement_valeur, 2, '.', '') : '') }}"
                           placeholder="0.00"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 pr-14 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800"
                           @if (in_array(old('paiement', $professeur->paiement), ['salaire', 'commission', 'pourcentage'], true)) required @endif>
                    <span id="paiementValeurSuffix" class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-xs font-semibold text-slate-400">
                        {{ old('paiement', $professeur->paiement) === 'pourcentage' ? '%' : '.00' }}
                    </span>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">Type Paiement</label>
                <select name="type_paiement" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:border-slate-700 dark:bg-slate-800">
                    <option value="">Sélectionner…</option>
                    <option value="vir" @selected(old('type_paiement', $professeur->type_paiement) === 'vir')>Vir</option>
                    <option value="chq" @selected(old('type_paiement', $professeur->type_paiement) === 'chq')>Chq</option>
                    <option value="vers" @selected(old('type_paiement', $professeur->type_paiement) === 'vers')>Vers</option>
                    <option value="esp" @selected(old('type_paiement', $professeur->type_paiement) === 'esp')>Esp</option>
                </select>
            </div>
        </div>
    </section>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20">
            Enregistrer
        </button>
        <a href="{{ route('admin.page.professeurs') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
            Annuler
        </a>
    </div>
</form>

<script>
(() => {
    const select = document.getElementById('fieldPaiement');
    const wrap = document.getElementById('paiementValeurWrap');
    const input = document.getElementById('fieldPaiementValeur');
    const label = document.getElementById('paiementValeurLabel');
    const suffix = document.getElementById('paiementValeurSuffix');

    function sync() {
        const type = select.value;
        const show = type === 'salaire' || type === 'commission' || type === 'pourcentage';

        wrap.classList.toggle('hidden', !show);
        input.required = show;
        label.textContent = 'Montant';

        if (type === 'pourcentage') {
            suffix.textContent = '%';
            input.placeholder = 'Saisir le pourcentage';
        } else if (show) {
            suffix.textContent = '.00';
            input.placeholder = type === 'salaire' ? 'Saisir le salaire' : 'Saisir la commission';
        } else {
            input.value = '';
        }
    }

    select.addEventListener('change', sync);
    sync();
})();
</script>
@endsection
