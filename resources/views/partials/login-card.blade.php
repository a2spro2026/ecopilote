@php
    // Paramètres attendus : $title, $subtitle, $action, $passwordId, $iconPath
    $iconPath = $iconPath ?? 'M12 14l9-5-9-5-9 5 9 5z';
    $showRegister = $showRegister ?? false;
    $registerPanel = $registerPanel ?? null;
    $registerAction = $registerAction ?? '#';
    $loginLabel = $loginLabel ?? 'E-mail';
    $loginName = $loginName ?? 'email';
    $loginType = $loginType ?? 'email';
    $loginPlaceholder = $loginPlaceholder ?? 'votre.identifiant';
    $loginSuffix = $loginSuffix ?? ($loginType === 'email' ? \App\Support\EcopiloteIdentity::emailSuffix() : null);
    $loginValue = old($loginName);
    if ($loginSuffix) {
        $loginValue = \App\Support\EcopiloteIdentity::localPart($loginValue);
    }
    $openRegister = $errors->any() && in_array(old('_form'), ['prof_register', 'etudiant_register'], true)
        && (($registerPanel === 'prof' && old('_form') === 'prof_register')
            || ($registerPanel === 'etudiant' && old('_form') === 'etudiant_register'));
@endphp

<section class="relative flex min-h-[calc(100vh-6rem)] items-center justify-center bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 px-4 py-16">

    {{-- décor lumineux --}}
    <div class="pointer-events-none absolute inset-0 opacity-30"
         style="background-image:radial-gradient(circle at 15% 20%, #3b82f6 0, transparent 35%), radial-gradient(circle at 85% 80%, #10b981 0, transparent 35%);"></div>

    <div class="relative w-full max-w-md">
        <div class="overflow-hidden rounded-3xl border border-white/15 bg-white shadow-2xl">

            {{-- En-tête --}}
            <div class="relative bg-gradient-to-r from-blue-700 via-blue-800 to-emerald-600 px-8 pt-8 pb-10 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30 backdrop-blur">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                    </svg>
                </div>
                <h1 class="mt-4 text-2xl font-extrabold text-white" style="font-family:'Poppins',sans-serif;">{{ $title }}</h1>
                <p class="mt-1 text-sm text-blue-100">{{ $subtitle }}</p>
            </div>

            {{-- Corps --}}
            <div class="px-8 py-8">
                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ $action }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $loginLabel }}</label>
                        <div class="relative flex overflow-hidden rounded-xl border border-slate-300 bg-slate-50 transition focus-within:border-blue-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-100">
                            <span class="pointer-events-none flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input type="{{ $loginSuffix ? 'text' : $loginType }}"
                                   name="{{ $loginName }}"
                                   value="{{ $loginValue }}"
                                   required
                                   autocomplete="username"
                                   placeholder="{{ $loginPlaceholder }}"
                                   class="min-w-0 flex-1 border-0 bg-transparent py-3 pl-3 pr-3 text-sm outline-none">
                            @if ($loginSuffix)
                                <span class="flex shrink-0 items-center border-l border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-600">
                                    {{ $loginSuffix }}
                                </span>
                            @endif
                        </div>
                        @if ($loginSuffix)
                            <p class="mt-1.5 text-xs text-slate-500">Saisissez uniquement votre identifiant — le domaine {{ $loginSuffix }} est ajouté automatiquement.</p>
                        @endif
                        @error($loginName) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Mot de passe</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </span>
                            <input id="{{ $passwordId }}" type="password" name="password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-11 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                            <button type="button" onclick="const p=document.getElementById('{{ $passwordId }}');p.type=p.type==='password'?'text':'password';"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-blue-600" aria-label="Afficher">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Se souvenir de moi
                        </label>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-blue-700 to-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:from-blue-800 hover:to-emerald-700 hover:-translate-y-0.5">
                        Se connecter
                    </button>
                </form>

                @if ($showRegister)
                    <div class="mt-6 border-t border-slate-200 pt-5 text-center">
                        <p class="text-sm text-slate-500">Nouveau ?
                            @if ($registerPanel === 'prof')
                                <button type="button" onclick="openProfRegister()"
                                        class="font-semibold text-emerald-600 hover:text-emerald-700">
                                    S'inscrire →
                                </button>
                            @elseif ($registerPanel === 'etudiant')
                                <button type="button" onclick="openEtudiantRegister()"
                                        class="font-semibold text-emerald-600 hover:text-emerald-700">
                                    S'inscrire →
                                </button>
                            @else
                                <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-700">S'inscrire en ligne →</a>
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-blue-100">
            <a href="{{ route('home') }}" class="font-semibold text-white hover:underline">← Retour à l'accueil</a>
        </p>
    </div>

    @if ($registerPanel === 'prof')
        <div id="profRegisterOverlay"
             class="{{ $openRegister && old('_form') === 'prof_register' ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-3 backdrop-blur-sm"
             onclick="if (event.target === this) closeProfRegister()">
            <div class="relative w-full max-w-xs overflow-hidden rounded-2xl border border-white/15 bg-white shadow-2xl"
                 role="dialog" aria-modal="true" aria-labelledby="profRegisterTitle">
                <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-emerald-600 px-4 py-3">
                    <h2 id="profRegisterTitle" class="text-base font-extrabold text-white" style="font-family:'Poppins',sans-serif;">
                        S'inscrire
                    </h2>
                    <p class="mt-0.5 text-xs text-blue-100">Candidature professeur</p>
                </div>

                <form method="POST" action="{{ $registerAction }}" class="space-y-2.5 px-4 py-4">
                    @csrf
                    <input type="hidden" name="_form" value="prof_register">

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Nom complet</label>
                        <input type="text" name="nom_complet" value="{{ old('nom_complet') }}" required
                               placeholder="Ex. Mme Alami"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('nom_complet') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Contact</label>
                        <input type="text" name="contact" value="{{ old('contact') }}" required
                               placeholder="Téléphone ou e-mail"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville') }}" required
                               placeholder="Ex. Casablanca"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('ville') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Matières enseignées</label>
                            <span class="text-[10px] font-medium text-slate-400">Une ou plusieurs</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-2">
                            @foreach ([
                                'Mathématiques',
                                'Physique-Chimie',
                                'Français',
                                'Anglais',
                                'SVT',
                                'Histoire-Géographie',
                                'Informatique',
                                'Arabe',
                            ] as $subject)
                                <label class="flex cursor-pointer items-center gap-2 rounded-lg bg-white px-2 py-1.5 text-[11px] font-medium text-slate-700 ring-1 ring-slate-200 transition hover:ring-blue-300">
                                    <input type="checkbox"
                                           name="matieres[]"
                                           value="{{ $subject }}"
                                           @checked(in_array($subject, old('matieres', []), true))
                                           class="h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span>{{ $subject }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('matieres') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('matieres.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Niveau</label>
                        <select name="niveau" required
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="">Sélectionner…</option>
                            <option value="primaire" @selected(old('niveau') === 'primaire')>Primaire</option>
                            <option value="college" @selected(old('niveau') === 'college')>Collège</option>
                            <option value="lycee" @selected(old('niveau') === 'lycee')>Lycée</option>
                            <option value="universitaire" @selected(old('niveau') === 'universitaire')>Universitaire</option>
                        </select>
                        @error('niveau') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Statut</label>
                        <select name="statut" required
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="">Sélectionner…</option>
                            <option value="public" @selected(old('statut') === 'public')>Public</option>
                            <option value="prive" @selected(old('statut') === 'prive')>Privé</option>
                        </select>
                        @error('statut') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Disponibilité</label>
                        <select name="disponibilite" required
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="">Sélectionner…</option>
                            <option value="immediat" @selected(old('disponibilite') === 'immediat')>Immédiat</option>
                            <option value="a_negocier" @selected(old('disponibilite') === 'a_negocier')>À négocier</option>
                        </select>
                        @error('disponibilite') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-1">
                        <button type="submit"
                                class="w-full rounded-lg bg-gradient-to-r from-blue-700 to-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-md shadow-blue-600/20 transition hover:from-blue-800 hover:to-emerald-700">
                            Envoyer
                        </button>
                        <button type="button" onclick="closeProfRegister()"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Fermer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openProfRegister() {
                const el = document.getElementById('profRegisterOverlay');
                if (!el) return;
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            function closeProfRegister() {
                const el = document.getElementById('profRegisterOverlay');
                if (!el) return;
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeProfRegister();
            });
            @if ($openRegister && old('_form') === 'prof_register')
                openProfRegister();
            @endif
        </script>
    @endif

    @if ($registerPanel === 'etudiant')
        <div id="etudiantRegisterOverlay"
             class="{{ $openRegister && old('_form') === 'etudiant_register' ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-3 backdrop-blur-sm"
             onclick="if (event.target === this) closeEtudiantRegister()">
            <div class="relative max-h-[90vh] w-full max-w-xs overflow-y-auto overflow-x-hidden rounded-2xl border border-white/15 bg-white shadow-2xl"
                 role="dialog" aria-modal="true" aria-labelledby="etudiantRegisterTitle">
                <div class="sticky top-0 bg-gradient-to-r from-blue-700 via-blue-800 to-emerald-600 px-4 py-3">
                    <h2 id="etudiantRegisterTitle" class="text-base font-extrabold text-white" style="font-family:'Poppins',sans-serif;">
                        S'inscrire
                    </h2>
                    <p class="mt-0.5 text-xs text-blue-100">Inscription étudiant</p>
                </div>

                <form method="POST" action="{{ $registerAction }}" class="space-y-2.5 px-4 py-4">
                    @csrf
                    <input type="hidden" name="_form" value="etudiant_register">

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Nom Complet</label>
                        <input type="text" name="nom_complet" value="{{ old('nom_complet') }}" required
                               placeholder="Ex. Yassine Bennani"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('nom_complet') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Contact</label>
                        <input type="text" name="contact" value="{{ old('contact') }}" required
                               placeholder="Téléphone ou e-mail"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Contact Tuteur</label>
                        <input type="text" name="contact_tuteur" value="{{ old('contact_tuteur') }}" required
                               placeholder="Téléphone du tuteur"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('contact_tuteur') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Ville</label>
                        <input type="text" name="ville" value="{{ old('ville') }}" required
                               placeholder="Ex. Casablanca"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('ville') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Niveau Scolaire</label>
                        <input type="text" name="niveau_scolaire" value="{{ old('niveau_scolaire') }}" required
                               placeholder="Ex. 2nde, 1ère, Terminale…"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('niveau_scolaire') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Matière</label>
                        <input type="text" name="matiere" value="{{ old('matiere') }}" required
                               placeholder="Ex. Mathématiques"
                               class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        @error('matiere') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Type Cours</label>
                        <select name="type_cours" required
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                            <option value="">Sélectionner…</option>
                            <option value="individuel" @selected(old('type_cours') === 'individuel')>Individuel</option>
                            <option value="en_groupe" @selected(old('type_cours') === 'en_groupe')>En Groupe</option>
                        </select>
                        @error('type_cours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col gap-2 pt-1">
                        <button type="submit"
                                class="w-full rounded-lg bg-gradient-to-r from-blue-700 to-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-md shadow-blue-600/20 transition hover:from-blue-800 hover:to-emerald-700">
                            Envoyer
                        </button>
                        <button type="button" onclick="closeEtudiantRegister()"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Fermer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEtudiantRegister() {
                const el = document.getElementById('etudiantRegisterOverlay');
                if (!el) return;
                el.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            function closeEtudiantRegister() {
                const el = document.getElementById('etudiantRegisterOverlay');
                if (!el) return;
                el.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeEtudiantRegister();
            });
            @if ($openRegister && old('_form') === 'etudiant_register')
                openEtudiantRegister();
            @endif
        </script>
    @endif
</section>
