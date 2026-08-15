@extends('admin.layout')

@section('title', 'Niveaux')
@section('heading', 'Niveaux')
@section('subtitle', 'Enseignement')

@section('content')
@php
    $toneBg = [
        'blue' => 'from-blue-500 to-indigo-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'amber' => 'from-amber-400 to-orange-500',
        'violet' => 'from-violet-500 to-purple-600',
        'indigo' => 'from-indigo-500 to-blue-600',
        'teal' => 'from-teal-500 to-cyan-600',
        'green' => 'from-emerald-400 to-green-600',
        'rose' => 'from-rose-500 to-pink-600',
    ];
@endphp

<div class="mb-5">
    <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Vue des niveaux</h2>
    <p class="text-sm text-slate-500">Effectifs, revenus mensuels et évolution</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach ($niveaux as $n)
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $n['nom'] }}</h3>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-wide text-slate-400">Niveau</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br {{ $toneBg[$n['tone']] ?? $toneBg['blue'] }} text-white shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                    </svg>
                </span>
            </div>

            <dl class="mt-5 space-y-3">
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Effectif</dt>
                    <dd class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $n['effectif'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Revenus / mois</dt>
                    <dd class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $n['revenus'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Évolution</dt>
                    <dd class="text-sm font-extrabold {{ $n['up'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $n['evolution'] }}
                    </dd>
                </div>
            </dl>
        </article>
    @endforeach
</div>
@endsection
