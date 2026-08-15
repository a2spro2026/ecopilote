@extends('admin.layout')

@section('title', $professeur->displayId())
@section('heading', $professeur->displayId())
@section('subtitle', 'Fiche professeur')

@section('content')
@php
    $etatMeta = [
        'actif' => ['chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30', 'dot' => 'bg-emerald-500'],
        'en_attente' => ['chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30', 'dot' => 'bg-amber-400'],
        'suspendu' => ['chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30', 'dot' => 'bg-rose-500'],
    ];
    $em = $etatMeta[$professeur->etat] ?? $etatMeta['en_attente'];
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.page.professeurs') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
        ← Retour à la liste
    </a>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.teachers.edit', $professeur) }}"
           class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Modifier</a>
        <form method="POST" action="{{ route('admin.teachers.suspend', $professeur) }}">
            @csrf
            <button type="submit" @disabled($professeur->etat === 'suspendu')
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-40">
                Suspendre
            </button>
        </form>
    </div>
</div>

<section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-slate-500">{{ $professeur->displayId() }}</p>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $professeur->nom_complet }}</h2>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $em['chip'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $em['dot'] }}"></span>
            {{ $professeur->etatLabel() }}
        </span>
    </div>

    <dl class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Contact</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->contact }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Ville</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->ville }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Statut</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->statutLabel() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Matière</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->matiere }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Disponibilité</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->disponibiliteLabel() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Mode</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->paiementLabel() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Montant</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->montantDisplay() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Type Paiement</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $professeur->typePaiementLabel() }}</dd></div>
    </dl>
</section>
@endsection
