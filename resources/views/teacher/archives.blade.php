@extends('teacher.layout')

@section('title', 'Archives')
@section('heading', 'Archives')
@section('subtitle', 'Documents et cours conservés')

@section('content')
<div class="mb-5 flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
    <span class="mt-0.5 text-base">🔒</span>
    <div>
        <p class="font-semibold">Archives en lecture seule</p>
        <p class="text-xs text-blue-700">La restauration, la suppression et la gestion définitive de tout document sont exclusivement réservées à l’administration.</p>
    </div>
</div>

<div class="mb-5 flex flex-wrap gap-2">
    @foreach (['Matière', 'Classe', 'Type', 'Date', 'Enseignant', 'Élève'] as $f)
        <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
            <option>{{ $f }} · Tous</option>
        </select>
    @endforeach
</div>
<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($archives as $a)
        <article class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-semibold uppercase text-slate-400">{{ $a['type'] }}</p>
            <h3 class="mt-1 font-bold text-slate-900">{{ $a['titre'] }}</h3>
            <p class="text-xs text-slate-500">{{ $a['matiere'] }} · {{ $a['classe'] }} · {{ $a['date'] }}</p>
            <p class="text-xs text-slate-500">{{ $a['enseignant'] }}</p>
            <div class="mt-3 flex gap-2 text-xs font-semibold">
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Consulter</button>
            </div>
        </article>
    @endforeach
</div>
@endsection
