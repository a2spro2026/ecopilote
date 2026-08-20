@extends('admin.layout')

@section('title', 'Vue générale')
@section('heading', 'Vue générale')
@section('subtitle', 'Centre de contrôle plateforme')

@section('content')
@php
    $toneBg = [
        'blue' => 'from-blue-500 to-indigo-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'indigo' => 'from-indigo-500 to-blue-600',
        'green' => 'from-emerald-400 to-green-600',
        'amber' => 'from-amber-400 to-orange-500',
        'violet' => 'from-violet-500 to-purple-600',
    ];
    $cardSkin = [
        'blue' => ['wash' => 'from-blue-600/12 via-sky-400/5 to-transparent', 'ring' => 'ring-blue-500/15', 'bar' => 'from-blue-500 to-indigo-400', 'glow' => 'shadow-blue-500/20'],
        'emerald' => ['wash' => 'from-emerald-500/12 via-teal-400/5 to-transparent', 'ring' => 'ring-emerald-500/15', 'bar' => 'from-emerald-500 to-teal-400', 'glow' => 'shadow-emerald-500/20'],
        'indigo' => ['wash' => 'from-indigo-500/12 via-blue-400/5 to-transparent', 'ring' => 'ring-indigo-500/15', 'bar' => 'from-indigo-500 to-blue-400', 'glow' => 'shadow-indigo-500/20'],
        'green' => ['wash' => 'from-green-500/12 via-emerald-400/5 to-transparent', 'ring' => 'ring-green-500/20', 'bar' => 'from-green-500 to-emerald-400', 'glow' => 'shadow-green-500/25'],
        'amber' => ['wash' => 'from-amber-500/15 via-orange-400/5 to-transparent', 'ring' => 'ring-amber-500/20', 'bar' => 'from-amber-500 to-orange-400', 'glow' => 'shadow-amber-500/20'],
        'violet' => ['wash' => 'from-violet-500/12 via-fuchsia-400/5 to-transparent', 'ring' => 'ring-violet-500/15', 'bar' => 'from-violet-500 to-fuchsia-400', 'glow' => 'shadow-violet-500/20'],
    ];
@endphp

{{-- Stats figées + contenu scrollable --}}
<div class="classes-lock-page">
    <div class="classes-lock-toolbar">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            @foreach ($data['stats'] as $stat)
                @php $skin = $cardSkin[$stat['tone']] ?? $cardSkin['blue']; @endphp
                <article class="group relative overflow-hidden rounded-2xl border border-white/70 bg-white p-4 shadow-lg {{ $skin['glow'] }} ring-1 {{ $skin['ring'] }} transition duration-300 hover:-translate-y-0.5 hover:shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br {{ $skin['wash'] }}"></div>
                    <div class="pointer-events-none absolute -right-6 -top-8 h-24 w-24 rounded-full bg-gradient-to-br {{ $toneBg[$stat['tone']] }} opacity-20 blur-2xl transition group-hover:opacity-40"></div>
                    <div class="relative flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $stat['value'] }}</p>
                            <p class="mt-2 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold {{ $stat['up'] ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">
                                <span>{{ $stat['up'] ? '▲' : '●' }}</span>
                                {{ $stat['hint'] }}
                            </p>
                        </div>
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $toneBg[$stat['tone']] }} text-white shadow-lg {{ $skin['glow'] }}">
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
                    <div class="relative mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <span class="absolute inset-y-0 left-0 w-3/4 rounded-full bg-gradient-to-r {{ $skin['bar'] }}"></span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <div class="classes-lock-scroll space-y-6">
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

