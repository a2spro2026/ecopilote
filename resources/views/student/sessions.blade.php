@extends('student.layout')
@section('title', 'Mes séances')
@section('heading', 'Mes séances')
@section('subtitle', 'Agenda des cours en direct et séances terminées')
@section('content')
<div class="mb-5 flex flex-wrap gap-2">
    <button class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white">Toutes</button>
    <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600">À venir</button>
    <button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600">Terminées</button>
</div>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @foreach($sessions as $session)
        <article class="flex flex-col gap-4 border-b border-slate-100 p-5 last:border-0 sm:flex-row sm:items-center">
            <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl {{ $session['status'] === 'À venir' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500' }}"><strong class="text-base">{{ $session['time'] }}</strong><span class="text-[9px] uppercase">heure</span></div>
            <div class="min-w-0 flex-1"><p class="text-xs font-bold uppercase tracking-wider text-indigo-600">{{ $session['subject'] }}</p><h2 class="mt-1 font-bold text-slate-900">{{ $session['title'] }}</h2><p class="mt-1 text-xs text-slate-500">{{ $session['teacher'] }} · {{ $session['date'] }}</p></div>
            @if($session['live'])
                <a href="{{ route('student.room') }}" class="rounded-xl bg-emerald-500 px-4 py-2.5 text-center text-xs font-bold text-white shadow-sm"><span class="mr-1 inline-block h-2 w-2 animate-pulse rounded-full bg-white"></span> Rejoindre</a>
            @elseif($session['status'] === 'Terminée')
                <a href="{{ route('student.archives') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-bold text-slate-600">Voir l’archive</a>
            @else
                <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">À venir</span>
            @endif
        </article>
    @endforeach
</section>
@endsection
