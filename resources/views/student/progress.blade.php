@extends('student.layout')
@section('title', 'Mon suivi')
@section('heading', 'Mon suivi pédagogique')
@section('subtitle', 'Progression, résultats et assiduité')
@section('content')
<div class="mb-6 grid gap-4 sm:grid-cols-3">
    <article class="rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-5 text-white shadow-lg"><p class="text-xs font-semibold text-indigo-100">Moyenne générale</p><strong class="mt-2 block text-3xl">15,5/20</strong><span class="text-xs text-emerald-200">+1,2 ce mois</span></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Assiduité</p><strong class="mt-2 block text-3xl text-slate-900">92 %</strong><span class="text-xs text-emerald-600">Très régulière</span></article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold text-slate-500">Devoirs rendus</p><strong class="mt-2 block text-3xl text-slate-900">18/20</strong><span class="text-xs text-amber-600">2 à compléter</span></article>
</div>
<section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="mb-5 font-bold text-slate-900">Progression par matière</h2><div class="space-y-5">
    @foreach($progress as $item)
        <div><div class="mb-2 flex flex-wrap items-end justify-between gap-2"><div><p class="text-sm font-bold">{{ $item['subject'] }}</p><p class="text-xs text-slate-500">Moyenne : {{ $item['average'] }}</p></div><span class="text-xs font-bold text-emerald-600">{{ $item['trend'] }}</span></div><div class="h-2.5 rounded-full bg-slate-100"><div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-600 to-cyan-500" style="width:{{ $item['value'] }}%"></div></div></div>
    @endforeach
</div></section>
@endsection
