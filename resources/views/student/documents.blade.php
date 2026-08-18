@extends('student.layout')
@section('title', 'Mes documents')
@section('heading', 'Mes documents')
@section('subtitle', 'Ressources partagées par vos professeurs')
@section('content')
<div class="mb-5 flex flex-col gap-3 sm:flex-row"><input type="search" placeholder="Rechercher une ressource…" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500"><select class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm"><option>Toutes les matières</option><option>Mathématiques</option><option>Physique-Chimie</option><option>Français</option></select></div>
<section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach($documents as $document)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start gap-4"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $document['type'] === 'PDF' ? 'bg-rose-50 text-rose-600' : 'bg-indigo-50 text-indigo-600' }} text-[10px] font-extrabold">{{ $document['type'] }}</span><div class="min-w-0"><p class="text-xs font-bold uppercase text-indigo-600">{{ $document['subject'] }}</p><h2 class="mt-1 font-bold text-slate-900">{{ $document['title'] }}</h2><p class="mt-1 text-xs text-slate-500">{{ $document['size'] }}</p></div></div>
            <button type="button" class="mt-5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Ouvrir la ressource</button>
        </article>
    @endforeach
</section>
<div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">Les ressources sont conservées par {{ config('app.brand') }}. Vous pouvez les consulter et les télécharger, sans les supprimer ni les archiver.</div>
@endsection
