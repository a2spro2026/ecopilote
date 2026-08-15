@extends('admin.layout')

@section('title', 'Candidatures professeurs')
@section('heading', 'Candidatures professeurs')
@section('subtitle', 'Demandes')

@section('content')
@php
    $etatMeta = [
        'en_attente' => ['chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30', 'dot' => 'bg-amber-400'],
        'validee' => ['chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30', 'dot' => 'bg-emerald-500'],
        'suspendue' => ['chip' => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-500/15 dark:text-orange-300 dark:ring-orange-500/30', 'dot' => 'bg-orange-400'],
        'refusee' => ['chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30', 'dot' => 'bg-rose-500'],
    ];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif

@if ($candidatures->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Aucune candidature pour le moment</p>
        <p class="mt-1 text-xs text-slate-500">Les inscriptions du portail professeurs apparaîtront ici.</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($candidatures as $c)
            @php
                $meta = $etatMeta[$c->etat] ?? $etatMeta['en_attente'];
                $isValidee = $c->etat === 'validee';
            @endphp
            <article class="flex flex-col rounded-2xl border p-4 shadow-sm transition
                {{ $isValidee
                    ? 'border-slate-200 bg-slate-100 opacity-60 grayscale dark:border-slate-700 dark:bg-slate-800/70'
                    : 'border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900' }}">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-xs font-medium text-slate-500">Candidature #{{ $c->id }}</p>
                        <h3 class="mt-0.5 text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $c->nom_complet }}</h3>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $meta['chip'] }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
                        {{ $c->etatLabel() }}
                    </span>
                </div>

                <dl class="mt-3 space-y-1.5 text-sm text-slate-600 dark:text-slate-300">
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Contact</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->contact }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Ville</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->ville }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Matière</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->matiere }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Niveau</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->niveauLabel() }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Statut</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->statutLabel() }}</dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500">Disponibilité</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $c->disponibiliteLabel() }}</dd></div>
                </dl>

                @unless ($isValidee)
                    <div class="mt-4 flex flex-col gap-2">
                        <form method="POST" action="{{ route('admin.teachers.applications.validate', $c) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition
                                        {{ $c->etat === 'validee'
                                            ? 'bg-emerald-600 ring-2 ring-emerald-300 ring-offset-1 dark:ring-offset-slate-900'
                                            : 'bg-emerald-500 hover:bg-emerald-600' }}">
                                Valider
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.teachers.applications.pending', $c) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition
                                        {{ $c->etat === 'en_attente'
                                            ? 'bg-amber-500 ring-2 ring-amber-300 ring-offset-1 dark:ring-offset-slate-900'
                                            : 'bg-amber-400 hover:bg-amber-500' }}">
                                En Attente
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.teachers.applications.suspend', $c) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2.5 text-sm font-semibold text-white transition
                                        {{ $c->etat === 'suspendue'
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
