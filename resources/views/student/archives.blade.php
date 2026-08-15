@extends('student.layout')
@section('title', 'Archives')
@section('heading', 'Mes archives')
@section('subtitle', 'Anciens cours, corrections et enregistrements')
@section('content')
<div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800"><strong>Archives en lecture seule.</strong> Elles sont conservées par l’administration et ne peuvent pas être supprimées par un élève.</div>
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @foreach($archives as $archive)
        <article class="flex flex-col gap-3 border-b border-slate-100 p-5 last:border-0 sm:flex-row sm:items-center"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-lg">🗂️</span><div class="flex-1"><h2 class="text-sm font-bold text-slate-900">{{ $archive['title'] }}</h2><p class="mt-1 text-xs text-slate-500">{{ $archive['type'] }} · {{ $archive['date'] }}</p></div><button class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600">Consulter</button></article>
    @endforeach
</section>
@endsection
