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

    $formatMad = fn (int $amount) => number_format($amount, 0, ',', ' ').' MAD';
@endphp

<div class="w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Tableau des matières</h2>
            <p class="text-sm text-slate-500">{{ count($matieres) }} matière(s) · effectifs, heures et finances</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.subjects.print') }}" target="_blank"
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
        <table class="ep-table min-w-[1180px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[22%]">
                <col class="w-[10%]">
                <col class="w-[11%]">
                <col class="w-[11%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
                <col class="w-[13%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Nbrs Profs</th>
                    <th>Nbrs Étudiant</th>
                    <th>Nbrs H/mois</th>
                    <th>Revenue</th>
                    <th>Paiement</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($matieres as $m)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                        <td>
                            <div class="flex items-center gap-3">
                                @include('admin.subjects.icon', ['m' => $m, 'toneBg' => $toneBg])
                                <span class="truncate font-semibold text-slate-800 dark:text-slate-100" title="{{ $m['nom'] }}">{{ $m['nom'] }}</span>
                            </div>
                        </td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $m['profs'] }}</td>
                        <td class="font-semibold text-slate-800 dark:text-slate-100">{{ $m['etudiants'] }}</td>
                        <td class="font-semibold text-blue-700 dark:text-blue-300">{{ number_format($m['heures_mois'], 0, ',', ' ') }} h</td>
                        <td class="font-semibold text-emerald-700 dark:text-emerald-300">{{ $formatMad($m['revenue']) }}</td>
                        <td class="font-semibold text-amber-700 dark:text-amber-300">{{ $formatMad($m['paiement']) }}</td>
                        <td class="font-extrabold text-violet-700 dark:text-violet-300">{{ $formatMad($m['benefice']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
