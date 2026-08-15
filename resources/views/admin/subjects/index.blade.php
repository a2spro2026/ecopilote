@extends('admin.layout')

@section('title', 'Matières')
@section('heading', 'Matières')
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
    <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Vue des matières</h2>
    <p class="text-sm text-slate-500">Effectifs, revenus mensuels et évolution</p>
</div>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
    @foreach ($matieres as $m)
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $m['nom'] }}</h3>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-wide text-slate-400">Matière</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br {{ $toneBg[$m['tone']] ?? $toneBg['blue'] }} text-white shadow">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </span>
            </div>

            <dl class="mt-5 space-y-3">
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Effectif</dt>
                    <dd class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $m['effectif'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Revenus / mois</dt>
                    <dd class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $m['revenus'] }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 dark:bg-slate-800/60">
                    <dt class="text-xs font-medium text-slate-500">Évolution</dt>
                    <dd class="text-sm font-extrabold {{ $m['up'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $m['evolution'] }}
                    </dd>
                </div>
            </dl>
        </article>
    @endforeach
</div>
@endsection
