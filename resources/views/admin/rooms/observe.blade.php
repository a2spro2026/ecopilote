@extends('admin.layout')

@section('title', $mode === 'ecouter' ? 'Écouter la salle' : 'Voir la salle')
@section('heading', $roomLabel)
@section('subtitle', $mode === 'ecouter' ? 'Mode écoute' : 'Mode observation')

@section('content')
@php
    $isListen = $mode === 'ecouter';
@endphp

<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-blue-900 to-emerald-800 px-6 py-5 text-white">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300">{{ $isListen ? 'Écoute en direct' : 'Observation visuelle' }}</p>
            <h2 class="text-xl font-extrabold" style="font-family:'Poppins',sans-serif;">{{ $roomLabel }} · {{ $matiereLabel }}</h2>
            <p class="mt-1 text-sm text-blue-100">{{ $teacher?->nom_complet ?: '—' }} · {{ $session->heureDebutDisplay() }} — {{ $session->heureFinDisplay() }}</p>
        </div>
        <span class="inline-flex items-center gap-2 rounded-full bg-rose-500/20 px-3 py-1.5 text-xs font-bold text-rose-200 ring-1 ring-rose-400/30">
            <span class="h-2 w-2 animate-pulse rounded-full bg-rose-400"></span>
            EN DIRECT
        </span>
    </div>

    <div class="grid gap-6 p-6 lg:grid-cols-[minmax(0,1fr)_280px]">
        <section class="min-w-0">
            @if ($isListen)
                <div class="flex min-h-[320px] flex-col items-center justify-center rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-center text-white">
                    <span class="flex h-24 w-24 items-center justify-center rounded-full bg-emerald-500/15 ring-1 ring-emerald-400/30">
                        <svg class="h-12 w-12 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9.75v6m6-6v6m-3-9.75V4.5A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m6 0V6.75A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m0 0H6.75A2.25 2.25 0 0 0 4.5 11.25v1.5A2.25 2.25 0 0 0 6.75 15H9m6 0h2.25A2.25 2.25 0 0 0 18 13.5v-1.5A2.25 2.25 0 0 0 15.75 9H15"/>
                        </svg>
                    </span>
                    <h3 class="mt-6 text-lg font-bold" style="font-family:'Poppins',sans-serif;">Écoute de la salle</h3>
                    <p class="mt-2 max-w-md text-sm text-slate-300">Vous entendez le professeur et les élèves sans activer votre caméra. Le son de la séance est diffusé ici.</p>
                    <div class="mt-8 flex h-16 w-full max-w-lg items-end justify-center gap-1.5">
                        @foreach ([40, 72, 56, 88, 48, 76, 52, 92, 44, 68, 58, 84] as $height)
                            <span class="w-2 rounded-full bg-emerald-400/80 animate-pulse" style="height: {{ $height }}%; animation-delay: {{ $loop->index * 80 }}ms;"></span>
                        @endforeach
                    </div>
                    <p class="mt-6 text-xs font-semibold uppercase tracking-wide text-emerald-300">Micros actifs · {{ $students->count() + 1 }} participant(s)</p>
                </div>
            @else
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-950">
                    <div class="grid gap-3 border-b border-white/10 p-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-slate-900 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Professeur</p>
                            <div class="mt-3 flex aspect-video items-center justify-center rounded-xl bg-slate-800">
                                <svg class="h-10 w-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/>
                                </svg>
                            </div>
                            <p class="mt-2 truncate text-sm font-semibold text-white">{{ $teacher?->nom_complet ?: '—' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-900 p-3 sm:col-span-2">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tableau · {{ $matiereLabel }}</p>
                            <div class="mt-3 flex aspect-video items-center justify-center rounded-xl bg-gradient-to-br from-emerald-950 to-slate-900">
                                <div class="text-center">
                                    <svg class="mx-auto h-10 w-10 text-emerald-400/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75 5.159 5.159a2.25 2.25 0 0 1 1.742-1.742l9.6-2.4a2.25 2.25 0 0 1 2.818 2.818l-2.4 9.6a2.25 2.25 0 0 1-1.742 1.742L2.25 15.75Z"/>
                                    </svg>
                                    <p class="mt-2 text-xs text-slate-400">Cours en cours · vue en lecture seule</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid gap-3 p-3 sm:grid-cols-4">
                        @forelse ($students as $student)
                            <div class="rounded-2xl border border-white/10 bg-slate-900 p-2">
                                <div class="flex aspect-video items-center justify-center rounded-lg bg-slate-800">
                                    <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                                    </svg>
                                </div>
                                <p class="mt-2 truncate text-[11px] font-medium text-slate-200">{{ $student->nom_complet }}</p>
                            </div>
                        @empty
                            <p class="col-span-full px-2 py-4 text-sm text-slate-400">Aucun élève inscrit dans ce groupe.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/40">
                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Séance</p>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">N°/Sé</dt><dd class="font-semibold text-slate-800 dark:text-slate-100">{{ $session->displayId() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Groupe</dt><dd class="font-semibold text-slate-800 dark:text-slate-100">{{ $group->displayId() }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Date</dt><dd class="font-semibold text-slate-800 dark:text-slate-100">{{ $session->dateDisplay() }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Participants</p>
                <ul class="mt-3 space-y-2">
                    <li class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-sm dark:bg-emerald-950/30">
                        <span class="font-medium text-slate-800 dark:text-slate-100">{{ $teacher?->nom_complet ?: 'Professeur' }}</span>
                        <span class="text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-300">Prof</span>
                    </li>
                    @foreach ($students as $student)
                        <li class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm dark:bg-slate-800/60">
                            <span class="text-slate-700 dark:text-slate-200">{{ $student->nom_complet }}</span>
                            <span class="text-[10px] font-semibold text-slate-400">Élève</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex flex-col gap-2">
                @if (! $isListen)
                    <a href="{{ route('admin.rooms.listen', $session) }}" data-window-title="Écouter · {{ $roomLabel }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9.75v6m6-6v6m-3-9.75V4.5A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m6 0V6.75A2.25 2.25 0 0 0 12 2.25S9.75 4.5 9.75 6.75V9m0 0H6.75A2.25 2.25 0 0 0 4.5 11.25v1.5A2.25 2.25 0 0 0 6.75 15H9m6 0h2.25A2.25 2.25 0 0 0 18 13.5v-1.5A2.25 2.25 0 0 0 15.75 9H15"/></svg>
                        Écouter
                    </a>
                @else
                    <a href="{{ route('admin.rooms.watch', $session) }}" data-window-title="Voir · {{ $roomLabel }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        Voir
                    </a>
                @endif
                <a href="{{ route('admin.page.salles-actives') }}" data-window-close
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    Retour aux salles
                </a>
            </div>
        </aside>
    </div>
</div>
@endsection
