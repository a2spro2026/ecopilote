@extends('student.layout')
@section('title', 'Mes classes')
@section('heading', 'Mes classes')
@section('subtitle', 'Vos groupes, professeurs et progression')
@section('content')
<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
    @foreach($classes as $class)
        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="h-2 bg-gradient-to-r from-indigo-600 to-cyan-500"></div>
            <div class="p-5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">{{ $class['level'] }}</span>
                <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $class['name'] }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $class['teacher'] }}</p>
                <div class="mt-5"><div class="mb-1.5 flex justify-between text-xs"><span class="font-semibold text-slate-500">Progression</span><strong>{{ $class['progress'] }} %</strong></div><div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-gradient-to-r from-indigo-600 to-cyan-500" style="width:{{ $class['progress'] }}%"></div></div></div>
                <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="text-xs text-slate-500">Prochain cours<br><strong class="text-slate-800">{{ $class['next'] }}</strong></span><a href="{{ route('student.documents') }}" class="rounded-xl bg-indigo-50 px-3 py-2 text-xs font-bold text-indigo-700">Ressources</a></div>
            </div>
        </article>
    @endforeach
</div>
@endsection
