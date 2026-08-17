@extends('teacher.layout')

@section('title', 'Mon Bureau')
@section('heading', 'Mon Bureau')
@section('subtitle', now()->format('d/m/Y'))

@section('content')
@php
    $toneBg = [
        'blue' => 'from-blue-500 to-indigo-500',
        'emerald' => 'from-emerald-500 to-teal-500',
        'indigo' => 'from-indigo-500 to-blue-600',
        'amber' => 'from-amber-400 to-orange-500',
        'violet' => 'from-violet-500 to-fuchsia-500',
    ];
    $cardSkin = [
        'blue' => ['wash' => 'from-blue-600/12 via-sky-400/5 to-transparent', 'ring' => 'ring-blue-500/15', 'bar' => 'from-blue-500 to-indigo-400', 'glow' => 'shadow-blue-500/20'],
        'emerald' => ['wash' => 'from-emerald-500/12 via-teal-400/5 to-transparent', 'ring' => 'ring-emerald-500/15', 'bar' => 'from-emerald-500 to-teal-400', 'glow' => 'shadow-emerald-500/20'],
        'indigo' => ['wash' => 'from-indigo-500/12 via-blue-400/5 to-transparent', 'ring' => 'ring-indigo-500/15', 'bar' => 'from-indigo-500 to-blue-400', 'glow' => 'shadow-indigo-500/20'],
        'amber' => ['wash' => 'from-amber-500/15 via-orange-400/5 to-transparent', 'ring' => 'ring-amber-500/20', 'bar' => 'from-amber-500 to-orange-400', 'glow' => 'shadow-amber-500/20'],
        'violet' => ['wash' => 'from-violet-500/12 via-fuchsia-400/5 to-transparent', 'ring' => 'ring-violet-500/15', 'bar' => 'from-violet-500 to-fuchsia-400', 'glow' => 'shadow-violet-500/20'],
    ];
@endphp
<div class="mb-6">
    <p class="text-sm text-slate-500">{{ now()->format('d/m/Y') }} · {{ $currentTeacher->matiere ?: 'Mathématiques' }}</p>
    <h2 class="mt-1 text-2xl font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">Bonjour, Professeur {{ $currentTeacher->nom_complet }}</h2>
    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Professeur validé
    </span>
</div>

<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
    @foreach ($kpis as $kpi)
        @php $skin = $cardSkin[$kpi['tone']] ?? $cardSkin['blue']; @endphp
        <article class="group relative overflow-hidden rounded-2xl border border-white/70 bg-white p-4 shadow-lg {{ $skin['glow'] }} ring-1 {{ $skin['ring'] }} transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br {{ $skin['wash'] }}"></div>
            <div class="pointer-events-none absolute -right-6 -top-8 h-24 w-24 rounded-full bg-gradient-to-br {{ $toneBg[$kpi['tone']] }} opacity-20 blur-2xl transition group-hover:opacity-40"></div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">{{ $kpi['label'] }}</p>
                    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900" style="font-family:'Poppins',sans-serif;">
                        {{ $kpi['value'] }}
                        @if (! empty($kpi['suffix']))
                            <span class="text-sm font-bold text-slate-400">{{ $kpi['suffix'] }}</span>
                        @endif
                    </p>
                    <p class="mt-2 inline-flex items-center gap-1 rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-bold {{ $kpi['up'] ? 'text-emerald-700' : 'text-amber-700' }} ring-1 ring-slate-200">
                        <span>{{ $kpi['up'] ? '▲' : '●' }}</span>
                        {{ $kpi['hint'] }}
                    </p>
                </div>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $toneBg[$kpi['tone']] }} text-white shadow-lg {{ $skin['glow'] }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        @if ($kpi['icon'] === 'groups')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72M6.348 15.66A3 3 0 0 0 3 18.24c.76.1 1.54.16 2.34.16 1.08 0 2.12-.1 3.12-.3M15 11.25a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5ZM6.75 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z"/>
                        @elseif ($kpi['icon'] === 'users')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766"/>
                        @elseif ($kpi['icon'] === 'calendar')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/>
                        @elseif ($kpi['icon'] === 'money')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"/>
                        @endif
                    </svg>
                </span>
            </div>
            <div class="relative mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <span class="absolute inset-y-0 left-0 w-3/4 rounded-full bg-gradient-to-r {{ $skin['bar'] }}"></span>
            </div>
        </article>
    @endforeach
</div>

<article class="mt-6 rounded-3xl border border-emerald-200 bg-gradient-to-r from-slate-900 via-blue-900 to-emerald-800 p-6 text-white shadow-sm sm:p-8">
    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-300">Prochaine séance</p>
    <div class="mt-3 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h3 class="text-3xl font-extrabold" style="font-family:'Poppins',sans-serif;">{{ $next['matiere'] }}</h3>
            <p class="mt-2 text-lg text-blue-100">{{ $next['salle'] }} · {{ $next['debut'] }} — {{ $next['fin'] }}</p>
            <p class="mt-1 text-sm text-blue-200">{{ $next['effectif'] }} élèves</p>
            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-400/15 px-3 py-1.5 text-sm font-semibold text-emerald-200 ring-1 ring-emerald-400/30">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                {{ $next['statut_label'] }}
            </span>
        </div>
        <a href="{{ route('teacher.salle') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3.5 text-sm font-extrabold tracking-wide text-slate-900 transition hover:bg-emerald-50">
            ENTRER DANS LA SALLE
        </a>
    </div>
</article>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="xl:col-span-2">
        <div class="mb-3 flex items-end justify-between">
            <h3 class="text-base font-bold text-slate-900" style="font-family:'Poppins',sans-serif;">Séances du jour</h3>
            <a href="{{ route('teacher.seances') }}" class="text-sm font-semibold text-blue-600">Tout voir →</a>
        </div>
        <div class="space-y-3">
            @foreach ($sessionsToday as $s)
                <article class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                    <div>
                        <p class="font-bold text-slate-900">{{ $s['matiere'] }} · {{ $s['classe'] }}</p>
                        <p class="text-sm text-slate-500">{{ $s['heure'] }} · {{ $s['eleves'] }} élèves</p>
                    </div>
                    @if ($s['statut'] === 'a_venir')
                        <a href="{{ route('teacher.salle') }}" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Entrer</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
    <section>
        <div class="mb-3 flex items-end justify-between">
            <h3 class="text-base font-bold text-slate-900" style="font-family:'Poppins',sans-serif;">Mes classes</h3>
            <a href="{{ route('teacher.classes') }}" class="text-sm font-semibold text-blue-600">Voir →</a>
        </div>
        <div class="space-y-3">
            @foreach ($classes as $c)
                <a href="{{ route('teacher.classes.show', $c['id']) }}" class="block rounded-2xl border border-slate-200 bg-white p-4 hover:border-emerald-300">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $c['salle'] }}</p>
                    <p class="font-bold text-slate-900">{{ $c['matiere'] }}</p>
                    <p class="text-sm text-slate-500">{{ $c['niveau'] }} · {{ $c['effectif'] }} élèves</p>
                </a>
            @endforeach
        </div>
    </section>
</div>
@endsection
