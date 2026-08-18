@extends('student.layout')
@section('title', 'Mon profil')
@section('heading', 'Mon profil')
@section('subtitle', 'Informations du compte élève')
@section('content')
<div class="grid gap-6 lg:grid-cols-[.7fr_1.3fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
        <div class="mx-auto flex aspect-square w-full max-w-[220px] items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-indigo-200 bg-gradient-to-br from-indigo-50 via-white to-cyan-50 shadow-inner">
            @if (! empty($student->photo_url ?? null))
                <img src="{{ $student->photo_url }}" alt="Photo de {{ $student->nom_complet }}" class="h-full w-full object-cover">
            @else
                <div class="flex flex-col items-center gap-2 px-4 text-indigo-400">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/>
                        </svg>
                    </span>
                    <p class="text-xs font-bold text-indigo-600">Photo de profil</p>
                    <p class="text-[11px] leading-snug text-indigo-400">Ajoutée par l’administration</p>
                </div>
            @endif
        </div>
        <h2 class="mt-5 text-xl font-bold text-slate-900">{{ $student->nom_complet }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ $student->niveau_scolaire }}</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="mb-5 font-bold text-slate-900">Informations personnelles</h2>
        <dl class="grid gap-4 sm:grid-cols-2">
            @foreach([
                'Identifiant '.config('app.brand') => $student->login,
                'Code élève' => $student->displayId(),
                'Contact' => $student->contact,
                'Contact tuteur' => $student->contact_tuteur,
                'Ville' => $student->ville,
                'Matière principale' => $student->matiere,
                'Type de cours' => $student->typeCoursLabel(),
                'Niveau scolaire' => $student->niveau_scolaire,
            ] as $label => $value)
                <div class="rounded-xl bg-slate-50 p-3">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
        <p class="mt-5 text-xs text-slate-500">Pour corriger une information, contactez l’administration {{ config('app.brand') }}.</p>
    </section>
</div>
@endsection
