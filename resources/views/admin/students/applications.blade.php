@extends('admin.layout')

@section('title', 'Demandes élèves')
@section('heading', 'Demandes élèves')
@section('subtitle', 'Demandes')

@section('content')
@php
    $etatMeta = [
        'en_attente' => ['chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30', 'dot' => 'bg-amber-400'],
        'validee' => ['chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30', 'dot' => 'bg-emerald-500'],
        'suspendue' => ['chip' => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-500/15 dark:text-orange-300 dark:ring-orange-500/30', 'dot' => 'bg-orange-400'],
    ];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif

@if ($demandes->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Aucune demande pour le moment</p>
        <p class="mt-1 text-xs text-slate-500">Les inscriptions du portail étudiant apparaîtront ici.</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($demandes as $d)
            @php
                $meta = $etatMeta[$d->etat] ?? $etatMeta['en_attente'];
                $isValidee = $d->etat === 'validee';
            @endphp
            <article class="flex flex-col rounded-2xl border p-4 shadow-sm transition
                {{ $isValidee
                    ? 'border-slate-200 bg-slate-100 opacity-60 grayscale dark:border-slate-700 dark:bg-slate-800/70'
                    : 'border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900' }}">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-3">
                        @if ($d->photo_url)
                            <img src="{{ $d->photo_url }}" alt="Photo de {{ $d->nom_complet }}" class="h-12 w-12 shrink-0 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700">
                        @endif
                        <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">Demande #{{ $d->id }}</p>
                        <h3 class="mt-0.5 truncate text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $d->nom_complet }}</h3>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $meta['chip'] }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
                        {{ $d->etatLabel() }}
                    </span>
                </div>

                <dl class="mt-3 space-y-1.5 text-sm text-slate-600 dark:text-slate-300">
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Contact</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->contact }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Contact Tuteur</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->contact_tuteur }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Ville</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->ville }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Niveau Scolaire</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->niveau_scolaire }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Matière</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->matiere }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Type Cours</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $d->typeCoursLabel() }}</dd></div>
                </dl>

                @unless ($isValidee)
                    <div class="mt-4 flex flex-col gap-2">
                        <form method="POST" action="{{ route('admin.students.applications.validate', $d) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-500 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                Valider
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.students.applications.pending', $d) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition
                                        {{ $d->etat === 'en_attente'
                                            ? 'bg-amber-500 ring-2 ring-amber-300 ring-offset-1 dark:ring-offset-slate-900'
                                            : 'bg-amber-400 hover:bg-amber-500' }}">
                                En Attente
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.students.applications.suspend', $d) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition
                                        {{ $d->etat === 'suspendue'
                                            ? 'bg-rose-600 ring-2 ring-rose-300 ring-offset-1 dark:ring-offset-slate-900'
                                            : 'bg-rose-500 hover:bg-rose-600' }}">
                                Suspendre
                            </button>
                        </form>
                    </div>
                @endunless
            </article>
        @endforeach
    </div>
@endif
@endsection
