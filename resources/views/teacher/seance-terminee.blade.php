@extends('teacher.layout')

@section('title', 'Séance terminée')
@section('heading', 'Séance terminée')
@section('subtitle', 'Contenu pédagogique archivé')

@section('content')
<article class="mx-auto max-w-lg rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-600">Archivage automatique</p>
    <h2 class="mt-2 text-2xl font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">{{ $resume['matiere'] }}</h2>
    <p class="mt-1 text-slate-500">{{ $resume['salle'] }} · {{ $resume['horaire'] }}</p>
    <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-slate-500">Présents</dt><dd class="text-lg font-bold">{{ $resume['presents'] }}</dd></div>
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-slate-500">Absents</dt><dd class="text-lg font-bold">{{ $resume['absents'] }}</dd></div>
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-slate-500">Documents</dt><dd class="text-lg font-bold">{{ $resume['documents'] }}</dd></div>
        <div class="rounded-xl bg-slate-50 p-3"><dt class="text-slate-500">Exercices</dt><dd class="text-lg font-bold">{{ $resume['exercices'] }}</dd></div>
    </dl>
    <p class="mt-4 text-sm text-slate-600">Tableau : {{ $resume['tableau'] }} · Enregistrement : {{ $resume['enregistrement'] }}</p>
    <a href="{{ route('teacher.archives') }}" class="mt-6 inline-flex rounded-xl bg-slate-900 px-5 py-3 text-sm font-extrabold text-white">CONSULTER L’ARCHIVE</a>
</article>
@endsection
