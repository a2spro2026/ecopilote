@extends('admin.layout')

@section('title', $classe['numero'])
@section('heading', $classe['numero'])
@section('subtitle', 'Fiche classe')

@section('content')
@php
    $statusMeta = [
        'active' => ['label' => 'Active', 'dot' => 'bg-emerald-500', 'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30'],
        'suspendue' => ['label' => 'Suspendue', 'dot' => 'bg-amber-400', 'chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30'],
        'terminee' => ['label' => 'Terminée', 'dot' => 'bg-slate-400', 'chip' => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600'],
    ];
    $seanceMeta = [
        'active' => ['label' => 'En direct', 'dot' => 'bg-emerald-500', 'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30'],
        'programmee' => ['label' => 'Programmée', 'dot' => 'bg-amber-400', 'chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30'],
        'annulee' => ['label' => 'Annulée', 'dot' => 'bg-rose-500', 'chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30'],
        'terminee' => ['label' => 'Terminée', 'dot' => 'bg-slate-400', 'chip' => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:ring-slate-600'],
        'individuelle' => ['label' => 'Individuelle', 'dot' => 'bg-violet-500', 'chip' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-500/15 dark:text-violet-300 dark:ring-violet-500/30'],
    ];
    $st = $statusMeta[$classe['statut']] ?? $statusMeta['terminee'];
    $typeLabel = $classe['type'] === 'individuelle' ? 'Individuelle' : 'Groupe';
    $eleveCount = count($classe['eleves'] ?? []);
    $periode = $classe['date_debut'];
    if (! empty($classe['date_fin'])) {
        $periode .= ' → '.$classe['date_fin'];
    } else {
        $periode .= ' → Sans date de fin';
    }
@endphp

<div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <a href="{{ route('admin.page.classes') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/></svg>
        Retour aux classes
    </a>
    <div class="flex flex-wrap gap-2">
        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $st['chip'] }}">
            <span class="h-1.5 w-1.5 rounded-full {{ $st['dot'] }}"></span>
            {{ $st['label'] }}
        </span>
        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30">
            {{ $typeLabel }}
        </span>
    </div>
</div>

<div class="grid gap-4 xl:grid-cols-3">
    {{-- Informations générales --}}
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6"/></svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Informations générales</h2>
                <p class="text-[11px] text-slate-500">Identité de la classe</p>
            </div>
        </div>
        <dl class="space-y-2.5 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Numéro</dt><dd class="font-semibold text-slate-900 dark:text-white">{{ $classe['numero'] }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Matière</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $classe['matiere'] }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Niveau</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $classe['niveau'] }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Type</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $typeLabel }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Statut</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $st['label'] }}</dd></div>
        </dl>
    </section>

    {{-- Professeur --}}
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/></svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Professeur</h2>
                <p class="text-[11px] text-slate-500">Affectation pédagogique</p>
            </div>
        </div>
        <p class="text-base font-bold text-slate-900 dark:text-white">{{ $classe['professeur']['nom'] }}</p>
        <p class="mt-2 text-xs text-slate-500">Matières</p>
        <div class="mt-1.5 flex flex-wrap gap-1.5">
            @foreach ($classe['professeur']['matieres'] as $m)
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $m }}</span>
            @endforeach
        </div>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
            Statut :
            <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ ucfirst($classe['professeur']['statut'] ?? 'validé') }}</span>
        </p>
    </section>

    {{-- Planning --}}
    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25"/></svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Planning</h2>
                <p class="text-[11px] text-slate-500">Jours et horaires</p>
            </div>
        </div>
        <dl class="space-y-2.5 text-sm">
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Jours</dt><dd class="text-right font-medium text-slate-800 dark:text-slate-100">{{ implode(' · ', $classe['jours']) }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Horaires</dt><dd class="font-medium text-slate-800 dark:text-slate-100">{{ $classe['heure_debut'] }} → {{ $classe['heure_fin'] }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-500">Période</dt><dd class="text-right font-medium text-slate-800 dark:text-slate-100">{{ $periode }}</dd></div>
        </dl>
    </section>
</div>

{{-- Élèves --}}
<section class="mt-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07"/></svg>
            </span>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Élèves</h2>
                <p class="text-[11px] text-slate-500">{{ $eleveCount }} élève{{ $eleveCount > 1 ? 's' : '' }} inscrit{{ $eleveCount > 1 ? 's' : '' }}</p>
            </div>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($classe['eleves'] as $eleve)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 px-3 py-3 dark:border-slate-800">
                <div>
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $eleve['nom'] }}</p>
                    <p class="text-xs text-slate-500">{{ $eleve['niveau'] }}</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30">
                    {{ ucfirst($eleve['statut'] ?? 'actif') }}
                </span>
            </div>
        @endforeach
    </div>
</section>

{{-- Séances --}}
<section class="mt-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-emerald-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
        </span>
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Séances</h2>
            <p class="text-[11px] text-slate-500">Dernières séances de la classe</p>
        </div>
    </div>

    @if (empty($classe['seances']))
        <p class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-500 dark:border-slate-700">Aucune séance réalisée</p>
    @else
        <div class="overflow-x-auto">
            <table class="ep-table min-w-full text-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Horaire</th>
                        <th>Statut</th>
                        <th>Présents</th>
                        <th>Absents</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($classe['seances'] as $seance)
                        @php $sm = $seanceMeta[$seance['statut']] ?? $seanceMeta['terminee']; @endphp
                        <tr>
                            <td class="font-medium text-slate-800 dark:text-slate-100">{{ $seance['date'] }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $seance['horaire'] }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $sm['chip'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $sm['dot'] }} {{ $seance['statut'] === 'active' ? 'animate-pulse' : '' }}"></span>
                                    {{ $sm['label'] }}
                                </span>
                            </td>
                            <td class="text-emerald-600 dark:text-emerald-400">{{ $seance['presents'] }}</td>
                            <td class="text-rose-600 dark:text-rose-400">{{ $seance['absents'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{-- Archives (préparation visuelle) --}}
<section class="mt-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center gap-2.5">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5"/></svg>
        </span>
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Archives</h2>
            <p class="text-[11px] text-slate-500">Accès prévu aux ressources de la classe</p>
        </div>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Enregistrements', 'desc' => 'Replays des séances', 'icon' => 'M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z'],
            ['label' => 'Documents', 'desc' => 'Fichiers partagés', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25'],
            ['label' => 'Cours', 'desc' => 'Supports pédagogiques', 'icon' => 'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292'],
            ['label' => 'Historique', 'desc' => 'Séances passées', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ] as $arch)
            <button type="button" disabled
                    class="rounded-xl border border-slate-200 px-4 py-4 text-left opacity-80 dark:border-slate-800">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $arch['icon'] }}"/></svg>
                </span>
                <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $arch['label'] }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ $arch['desc'] }}</p>
                <p class="mt-2 text-[11px] font-medium text-slate-400">Bientôt disponible</p>
            </button>
        @endforeach
    </div>
</section>
@endsection
