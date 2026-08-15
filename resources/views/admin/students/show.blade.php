@extends('admin.layout')

@section('title', $eleve->displayId())
@section('heading', $eleve->displayId())
@section('subtitle', 'Fiche élève')

@section('content')
@php
    $etatMeta = [
        'actif' => ['chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30', 'dot' => 'bg-emerald-500'],
        'en_attente' => ['chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30', 'dot' => 'bg-amber-400'],
        'suspendu' => ['chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30', 'dot' => 'bg-rose-500'],
    ];
    $em = $etatMeta[$eleve->etat] ?? $etatMeta['en_attente'];
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('admin.page.eleves') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
        ← Retour à la liste
    </a>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.students.edit', $eleve) }}"
           class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Modifier</a>
        <form method="POST" action="{{ route('admin.students.suspend', $eleve) }}">
            @csrf
            <button type="submit" @disabled($eleve->etat === 'suspendu')
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-40">
                Suspendre
            </button>
        </form>
    </div>
</div>

<section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-slate-500">{{ $eleve->displayId() }}</p>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $eleve->nom_complet }}</h2>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $em['chip'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $em['dot'] }}"></span>
            {{ $eleve->etatLabel() }}
        </span>
    </div>

    <dl class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Identifiant</dt><dd class="break-all text-sm font-medium text-blue-700 dark:text-blue-300">{{ $eleve->login }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Mot de passe d’accès</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->access_password }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Contact</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->contact }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Contact Tuteur</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->contact_tuteur }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Ville</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->ville }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Niveau</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->niveau_scolaire }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Matière</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->matiere }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Type Cours</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->typeCoursLabel() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Paiement</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->paiementDisplay() }}</dd></div>
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800"><dt class="text-xs text-slate-500">Échéance</dt><dd class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $eleve->echeanceDisplay() }}</dd></div>
    </dl>
</section>
@endsection
