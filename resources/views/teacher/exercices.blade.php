@extends('teacher.layout')

@section('title', 'Exercices & Devoirs')
@section('heading', 'Exercices & Devoirs')
@section('subtitle', 'Créer, affecter, corriger')

@section('content')
@php
    $st = [
        'corrige' => ['label' => 'Corrigé', 'chip' => 'bg-emerald-50 text-emerald-700'],
        'a_corriger' => ['label' => 'À corriger', 'chip' => 'bg-amber-50 text-amber-700'],
        'non_remis' => ['label' => 'Non remis', 'chip' => 'bg-rose-50 text-rose-700'],
    ];
@endphp

<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5">
    <h3 class="text-sm font-bold text-slate-900">Créer un exercice</h3>
    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <input class="rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Titre / consigne">
        <select class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><option>Affecter à une classe</option><option>Salle 01</option><option>Salle 04</option></select>
        <select class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><option>Affecter à un élève</option><option>Tous</option></select>
        <input type="date" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
    </div>
    <div class="mt-3 flex gap-2">
        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold">Joindre un document</button>
        <button type="button" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Publier</button>
    </div>
</div>

<div class="grid gap-3">
    @foreach ($exercices as $ex)
        <article class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div>
                <p class="font-bold text-slate-900">{{ $ex['titre'] }}</p>
                <p class="text-sm text-slate-500">{{ $ex['classe'] }} · Limite {{ $ex['limite'] }} · Remis {{ $ex['remis'] }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $st[$ex['statut']]['chip'] }}">{{ $st[$ex['statut']]['label'] }}</span>
                <button type="button" class="text-xs font-semibold text-blue-600">Consulter</button>
                <button type="button" class="text-xs font-semibold text-slate-700">Corriger</button>
                <button type="button" class="text-xs font-semibold text-slate-700">Noter</button>
            </div>
        </article>
    @endforeach
</div>
@endsection
