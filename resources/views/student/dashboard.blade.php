@extends('student.layout')
@section('title', 'Mon accueil')
@section('heading', 'Bonjour, '.explode(' ', $student->nom_complet)[0].' 👋')
@section('subtitle', 'Voici l’essentiel de votre parcours aujourd’hui')
@section('content')
<section class="mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-cyan-500 p-6 text-white shadow-xl shadow-indigo-200 sm:p-8">
    <div class="max-w-2xl">
        @if (! empty($sessions[0]))
            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">Prochaine séance</span>
            <h2 class="mt-4 text-2xl font-extrabold sm:text-3xl" style="font-family:Poppins,sans-serif">{{ $sessions[0]['title'] }}</h2>
            <p class="mt-2 text-sm text-indigo-100">{{ $sessions[0]['subject'] }} avec {{ $sessions[0]['teacher'] }} · {{ $sessions[0]['date'] }} à {{ $sessions[0]['time'] }}</p>
            <a href="{{ route('student.room') }}" class="mt-5 inline-flex rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-indigo-700 shadow-sm">Rejoindre la salle</a>
        @else
            <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold">Accueil</span>
            <h2 class="mt-4 text-2xl font-extrabold sm:text-3xl" style="font-family:Poppins,sans-serif">Aucune séance prévue</h2>
            <p class="mt-2 text-sm text-indigo-100">Vos cours et devoirs apparaîtront ici.</p>
        @endif
    </div>
</section>
<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach([
        ['value' => count($classes), 'label' => 'Classes actives', 'color' => 'text-indigo-600 bg-indigo-50'],
        ['value' => collect($assignments)->where('status', 'À rendre')->count(), 'label' => 'Devoirs à rendre', 'color' => 'text-amber-600 bg-amber-50'],
        ['value' => '—', 'label' => 'Moyenne générale', 'color' => 'text-emerald-600 bg-emerald-50'],
        ['value' => '—', 'label' => 'Assiduité', 'color' => 'text-cyan-600 bg-cyan-50'],
    ] as $stat)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><span class="inline-flex rounded-xl px-3 py-1 text-xl font-extrabold {{ $stat['color'] }}">{{ $stat['value'] }}</span><p class="mt-3 text-sm font-semibold text-slate-600">{{ $stat['label'] }}</p></article>
    @endforeach
</div>
<div class="grid gap-6 xl:grid-cols-[1.35fr_.65fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between"><h2 class="font-bold text-slate-900">Mes prochains rendez-vous</h2><a href="{{ route('student.sessions') }}" class="text-xs font-bold text-indigo-600">Tout voir →</a></div>
        <div class="space-y-3">
            @foreach(array_slice($sessions, 0, 2) as $session)
                <div class="flex items-center gap-4 rounded-xl border border-slate-100 p-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-sm font-extrabold text-indigo-600">{{ $session['time'] }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-bold">{{ $session['title'] }}</p><p class="text-xs text-slate-500">{{ $session['subject'] }} · {{ $session['date'] }}</p></div><span class="hidden rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 sm:inline">{{ $session['status'] }}</span></div>
            @endforeach
        </div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between"><h2 class="font-bold text-slate-900">À faire</h2><a href="{{ route('student.assignments') }}" class="text-xs font-bold text-indigo-600">Mes devoirs →</a></div>
        @foreach(array_slice($assignments, 0, 2) as $assignment)
            <div class="mb-3 rounded-xl bg-slate-50 p-3"><p class="text-sm font-bold">{{ $assignment['title'] }}</p><p class="mt-1 text-xs text-slate-500">Avant le {{ $assignment['due'] }}</p></div>
        @endforeach
    </section>
</div>
@endsection
