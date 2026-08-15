@extends('admin.layout')

@section('title', 'Vue générale')
@section('heading', 'Vue générale')
@section('subtitle', 'Centre de contrôle plateforme')

@section('content')
@php
    $statusMap = [
        'active' => ['label' => 'En direct', 'dot' => 'bg-emerald-500', 'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30'],
        'programmee' => ['label' => 'Programmée', 'dot' => 'bg-amber-400', 'chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30'],
        'annulee' => ['label' => 'Annulée', 'dot' => 'bg-rose-500', 'chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30'],
        'terminee' => ['label' => 'Terminée', 'dot' => 'bg-slate-400', 'chip' => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600'],
    ];
    $typeMap = [
        'individuelle' => ['label' => 'Individuelle', 'chip' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-500/15 dark:text-violet-300 dark:ring-violet-500/30'],
        'groupe' => ['label' => 'Groupe', 'chip' => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30'],
    ];
    $toneBg = [
        'blue' => 'from-blue-500 to-indigo-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'indigo' => 'from-indigo-500 to-blue-600',
        'green' => 'from-emerald-400 to-green-600',
        'amber' => 'from-amber-400 to-orange-500',
        'violet' => 'from-violet-500 to-purple-600',
    ];
@endphp

{{-- Stats figées + contenu scrollable --}}
<div class="classes-lock-page">
    <div class="classes-lock-toolbar">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            @foreach ($data['stats'] as $stat)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-[11px] font-medium {{ $stat['up'] ? 'text-emerald-600' : 'text-amber-600' }}">{{ $stat['hint'] }}</p>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br {{ $toneBg[$stat['tone']] }} text-white shadow">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                @if ($stat['icon'] === 'users')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766"/>
                                @elseif ($stat['icon'] === 'live')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/>
                                @elseif ($stat['icon'] === 'money')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/>
                                @endif
                            </svg>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="classes-lock-scroll space-y-6">
<div class="grid gap-6 xl:grid-cols-3">
    {{-- Séances du jour --}}
    <section class="xl:col-span-2 space-y-4">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Séances du jour</h2>
                <p class="text-sm text-slate-500">{{ ucfirst($data['today']) }}</p>
            </div>
            <a href="{{ route('admin.page.seances') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">Tout voir →</a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($data['sessions_today'] as $s)
                @php $st = $statusMap[$s['statut']]; $tp = $typeMap[$s['type']]; @endphp
                <article class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $tp['chip'] }}">
                            {{ $tp['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $st['chip'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $st['dot'] }} {{ $s['statut'] === 'active' ? 'animate-pulse' : '' }}"></span>
                            {{ $st['label'] }}
                        </span>
                    </div>
                    <h3 class="mt-3 text-base font-bold text-slate-900 dark:text-white">{{ $s['matiere'] }}</h3>
                    <dl class="mt-2 space-y-1 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex justify-between gap-2"><dt>Professeur</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['prof'] }}</dd></div>
                        <div class="flex justify-between gap-2"><dt>Élève / Groupe</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['cible'] }}</dd></div>
                        <div class="flex justify-between gap-2"><dt>Niveau</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['niveau'] }}</dd></div>
                        <div class="flex justify-between gap-2"><dt>Horaire</dt><dd class="font-medium text-slate-700 dark:text-slate-200">{{ $s['debut'] }} – {{ $s['fin'] }} ({{ $s['duree'] }})</dd></div>
                    </dl>
                    <a href="{{ route('admin.page.seances') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">Voir</a>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Activité temps réel --}}
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Activité en temps réel</h2>
        <p class="text-sm text-slate-500">Activité récente</p>
        <ul class="mt-4 space-y-3">
            @foreach ($data['activity'] as $a)
                @php
                    $dot = match($a['tone']) {
                        'green' => 'bg-emerald-500',
                        'amber' => 'bg-amber-400',
                        'red' => 'bg-rose-500',
                        'violet' => 'bg-violet-500',
                        default => 'bg-blue-500',
                    };
                @endphp
                <li class="flex gap-3 rounded-xl border border-slate-100 p-3 dark:border-slate-800">
                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $dot }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $a['title'] }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $a['text'] }}</p>
                    </div>
                    <span class="shrink-0 text-[10px] font-medium text-slate-400">{{ $a['time'] }}</span>
                </li>
            @endforeach
        </ul>
    </section>
</div>

{{-- Calendrier semaine --}}
<section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Calendrier</h2>
            <p class="text-sm text-slate-500">Vue semaine</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-xs font-semibold dark:border-slate-700 dark:bg-slate-800">
                <button type="button" class="rounded-lg px-3 py-1.5 text-slate-500 hover:text-slate-800 dark:hover:text-white">Jour</button>
                <button type="button" class="rounded-lg bg-white px-3 py-1.5 text-slate-900 shadow dark:bg-slate-700 dark:text-white">Semaine</button>
                <button type="button" class="rounded-lg px-3 py-1.5 text-slate-500 hover:text-slate-800 dark:hover:text-white">Mois</button>
            </div>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['Professeur', 'Élève', 'Matière', 'Niveau', 'Type', 'Statut'] as $filter)
            <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <option>{{ $filter }} · Tous</option>
            </select>
        @endforeach
    </div>

    <div class="mt-5 overflow-x-auto">
        <div class="min-w-[780px]">
            <div class="grid grid-cols-8 gap-2">
                <div></div>
                @foreach ($data['week_days'] as $day)
                    <div class="rounded-lg bg-slate-50 px-2 py-2 text-center text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $day }}</div>
                @endforeach
            </div>
            @foreach ($data['week_slots'] as $si => $slot)
                <div class="mt-2 grid grid-cols-8 gap-2">
                    <div class="flex items-center text-xs font-semibold text-slate-400">{{ $slot }}</div>
                    @for ($di = 0; $di < 7; $di++)
                        @php
                            $event = collect($data['week_events'])->first(fn ($e) => $e['d'] === $di && $e['s'] === $si);
                        @endphp
                        <div class="min-h-[56px] rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-1 dark:border-slate-800 dark:bg-slate-950/40">
                            @if ($event)
                                @php
                                    $bg = match($event['statut']) {
                                        'active' => 'bg-emerald-500 text-white',
                                        'annulee' => 'bg-rose-100 text-rose-700 line-through dark:bg-rose-500/20 dark:text-rose-300',
                                        'terminee' => 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                        default => $event['type'] === 'individuelle' ? 'bg-violet-100 text-violet-800 dark:bg-violet-500/20 dark:text-violet-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-200',
                                    };
                                @endphp
                                <div class="h-full rounded-lg px-1.5 py-1 text-[10px] font-semibold leading-tight {{ $bg }}">
                                    {{ $event['label'] }}
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Archives --}}
<section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Archives</h2>
            <p class="text-sm text-slate-500">Filtres Matière → Niveau → Professeur → Élève → Date</p>
        </div>
        <a href="{{ route('admin.page.archives-videos') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">Ouvrir les archives →</a>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['Matière', 'Niveau', 'Professeur', 'Élève', 'Date'] as $filter)
            <select class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <option>{{ $filter }} · Tous</option>
            </select>
        @endforeach
    </div>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($data['archives'] as $arch)
            @php
                $chip = match($arch['tone']) {
                    'violet' => 'bg-violet-50 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300',
                    'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300',
                    'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300',
                    'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300',
                    default => 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300',
                };
            @endphp
            <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $chip }}">{{ $arch['kind'] }}</span>
                <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">{{ $arch['title'] }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $arch['meta'] }}</p>
            </article>
        @endforeach
    </div>
</section>
    </div>
</div>
@endsection

