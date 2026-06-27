@php
    // Paramètres attendus : $title, $subtitle, $action, $passwordId, $iconPath
    $iconPath = $iconPath ?? 'M12 14l9-5-9-5-9 5 9 5z';
    $showRegister = $showRegister ?? false;
@endphp

<section class="relative flex min-h-[calc(100vh-6rem)] items-center justify-center overflow-hidden bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800 px-4 py-16">

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
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">E-mail</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="vous@ecopilote.ma"
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                        </div>
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
                            <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-700">S'inscrire en ligne →</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <p class="mt-6 text-center text-sm text-blue-100">
            <a href="{{ route('home') }}" class="font-semibold text-white hover:underline">← Retour à l'accueil</a>
        </p>
    </div>
</section>
