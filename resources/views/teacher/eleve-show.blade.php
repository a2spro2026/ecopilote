@extends('teacher.layout')

@section('title', $eleve['nom'])
@section('heading', $eleve['nom'])
@section('subtitle', $eleve['niveau'].' · '.$eleve['classe'])

@section('content')
<a href="{{ route('teacher.eleves') }}" class="mb-5 inline-flex text-sm font-semibold text-blue-600">← Mes élèves</a>

<div class="grid gap-4 lg:grid-cols-3">
    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-bold text-slate-900">Informations</h3>
        <dl class="mt-3 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Classe</dt><dd>{{ $eleve['classe'] }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Niveau</dt><dd>{{ $eleve['niveau'] }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Présence</dt><dd>{{ $eleve['presence'] }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Séances</dt><dd>{{ $eleve['seances'] }}</dd></div>
        </dl>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-bold text-slate-900">Progression</h3>
        <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $eleve['progression'] }} %</p>
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
            <div class="h-full rounded-full bg-emerald-500" style="width: {{ $eleve['progression'] }}%"></div>
        </div>
        <p class="mt-2 text-xs text-slate-500">Exercices {{ $eleve['exercices'] }}</p>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-bold text-slate-900">Dernière activité</h3>
        <p class="mt-3 text-sm text-slate-700">{{ $eleve['activite'] }}</p>
        <p class="mt-2 text-xs text-slate-500">Présences, séances, notes et documents sont conservés dans les archives de classe.</p>
    </section>
</div>
@endsection
