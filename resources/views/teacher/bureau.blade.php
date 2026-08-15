@extends('teacher.layout')

@section('title', 'Mon Bureau')
@section('heading', 'Mon Bureau')
@section('subtitle', now()->format('d/m/Y'))

@section('content')
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
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-slate-500">{{ $kpi['label'] }}</p>
            <p class="mt-1 text-2xl font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">{{ $kpi['value'] }}</p>
        </div>
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
