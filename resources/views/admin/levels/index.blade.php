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
    ];
@endphp

<div class="w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Tableau des niveaux</h2>
            <p class="text-sm text-slate-500">{{ count($niveaux) }} niveau(x) · Primaire, Collège, Lycée, Coran</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.levels.print') }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829h10.56M6.72 17.443h10.56M6.72 21h10.56A1.72 1.72 0 0 0 19 19.28V9.5H5v9.78A1.72 1.72 0 0 0 6.72 21ZM7 5V3h10v2m2 0H5a3 3 0 0 0-3 3v5h3V9.5h14V13h3V8a3 3 0 0 0-3-3Z"/>
                </svg>
                Imprimer
            </a>
            <a href="{{ route('admin.dashboard') }}" data-window-close
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                Fermer
            </a>
        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[720px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[46%]">
                <col class="w-[27%]">
                <col class="w-[27%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Niveaux</th>
                    <th>Nbrs Étudiant</th>
                    <th>Nbrs Profs</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($niveaux as $n)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $toneBg[$n['tone']] ?? $toneBg['blue'] }} text-white shadow-sm">
                                    @if ($n['key'] === 'primaire')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"/>
                                        </svg>
                                    @elseif ($n['key'] === 'college')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21"/>
                                        </svg>
                                    @elseif ($n['key'] === 'lycee')
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                                        </svg>
                                    @endif
                                </span>
                                <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $n['nom'] }}</span>
                            </div>
                        </td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $n['etudiants'] }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $n['profs'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
