@extends('teacher.layout')

@section('title', 'Mes Classes')
@section('heading', 'Mes Classes')
@section('subtitle', 'Environnements pédagogiques')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($classes as $c)
        <article class="flex flex-col rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-600">{{ $c['salle'] }}</p>
            <h3 class="mt-1 text-lg font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">{{ $c['matiere'] }}</h3>
            <p class="text-sm text-slate-500">{{ $c['niveau'] }}</p>
            <p class="mt-4 text-sm font-semibold text-slate-800">{{ $c['effectif'] }} élèves</p>
            <p class="mt-1 text-xs text-slate-500">Prochaine séance : {{ $c['prochaine'] }}</p>
            <div class="mt-5 flex gap-2">
                <a href="{{ route('teacher.salle') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-slate-900 px-3 py-2.5 text-xs font-semibold text-white">Ouvrir la classe</a>
                <a href="{{ route('teacher.classes.show', $c['id']) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Voir les élèves</a>
            </div>
        </article>
    @endforeach
</div>
@endsection
