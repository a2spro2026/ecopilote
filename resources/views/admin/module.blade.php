@extends('admin.layout')

@section('title', $module['label'])
@section('heading', $module['label'])

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-emerald-600">Pôle {{ $module['group'] }}</p>
            <h2 class="text-2xl font-extrabold text-blue-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $module['label'] }}</h2>
        </div>
        <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-700 to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:-translate-y-0.5">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nouveau
        </button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-800/60">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-900 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $module['icon'] }}" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Module {{ $module['label'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Module {{ $module['label'] }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-16 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $module['icon'] }}" />
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-blue-900 dark:text-slate-100">Aucune donnée pour le moment</h3>
            <p class="mx-auto mt-1 max-w-md text-sm text-slate-500 dark:text-slate-400">
                L'interface « {{ $module['label'] }} » est en place. Les enregistrements (création, consultation, gestion) seront affichés ici.
            </p>
        </div>
    </div>
@endsection
