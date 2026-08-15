@extends('teacher.layout')

@section('title', 'Ma Bibliothèque')
@section('heading', 'Ma Bibliothèque')
@section('subtitle', 'Gestionnaire documentaire')

@section('content')
@php $sections = ['Cours','Exercices','Devoirs','Corrections','Vidéos','Documents','Supports']; @endphp
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('teacher.bibliotheque') }}" class="rounded-xl px-3 py-1.5 text-sm font-semibold {{ $section === '' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white' }}">Tous</a>
        @foreach ($sections as $s)
            <a href="{{ route('teacher.bibliotheque', ['section' => $s]) }}" class="rounded-xl px-3 py-1.5 text-sm font-semibold {{ $section === $s ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white' }}">{{ $s }}</a>
        @endforeach
        <a href="{{ route('teacher.archives') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold">Archives</a>
    </div>
    <button type="button" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-2 text-sm font-semibold text-white">+ Ajouter un document</button>
</div>

<div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
    Les documents pédagogiques sont conservés par l’administration. Un professeur peut les utiliser et les partager, mais ne peut ni les supprimer ni modifier leur archivage.
</div>

<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($documents as $d)
        <article class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-[11px] font-semibold uppercase text-emerald-600">{{ $d['section'] }}</p>
            <h3 class="mt-1 font-bold text-slate-900">{{ $d['nom'] }}</h3>
            <p class="text-xs text-slate-500">{{ $d['classe'] }} · {{ $d['date'] }}</p>
            <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Importer</button>
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Renommer</button>
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Déplacer</button>
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Partager</button>
                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1">Télécharger</button>
            </div>
        </article>
    @endforeach
</div>
@endsection
