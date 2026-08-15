@extends('teacher.layout')

@section('title', 'Suivi pédagogique')
@section('heading', 'Suivi pédagogique')
@section('subtitle', 'Vue d’ensemble de vos classes')

@section('content')
<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Taux de présence</p><p class="mt-1 text-2xl font-extrabold">{{ $pedagogy['presence'] }}</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Nombre de séances</p><p class="mt-1 text-2xl font-extrabold">{{ $pedagogy['seances'] }}</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Exercices réalisés</p><p class="mt-1 text-2xl font-extrabold">{{ $pedagogy['exercices'] }}</p></div>
    <div class="rounded-2xl border border-slate-200 bg-white p-4"><p class="text-xs text-slate-500">Progression moyenne</p><p class="mt-1 text-2xl font-extrabold">{{ $pedagogy['progression'] }}</p></div>
</div>

<div class="mt-6 grid gap-4 lg:grid-cols-2">
    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <h3 class="text-sm font-bold text-slate-900">Progression par élève</h3>
        <div class="mt-4 space-y-3">
            @foreach (array_slice($eleves, 0, 6) as $e)
                <div>
                    <div class="mb-1 flex justify-between text-xs"><span>{{ $e['nom'] }}</span><span>{{ $e['progression'] }} %</span></div>
                    <div class="h-2 rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-600" style="width: {{ $e['progression'] }}%"></div></div>
                </div>
            @endforeach
        </div>
    </section>
    <div class="grid gap-4">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-bold text-slate-900">Élèves en difficulté</h3>
            <ul class="mt-3 space-y-1 text-sm text-slate-700">
                @foreach ($pedagogy['difficulte'] as $n)<li>{{ $n }}</li>@endforeach
            </ul>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h3 class="text-sm font-bold text-slate-900">Élèves les plus réguliers</h3>
            <ul class="mt-3 space-y-1 text-sm text-slate-700">
                @foreach ($pedagogy['reguliers'] as $n)<li>{{ $n }}</li>@endforeach
            </ul>
        </section>
    </div>
</div>
@endsection
