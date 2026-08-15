@extends('admin.layout')

@section('title', $item['label'])
@section('heading', $item['label'])
@section('subtitle', $group)

@section('content')
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-emerald-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
            </span>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $item['label'] }}</h2>
                <p class="text-sm text-slate-500">Section « {{ $group }} »</p>
            </div>
        </div>
        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nouveau
        </button>
    </div>

    <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
        @for ($i = 1; $i <= 6; $i++)
            <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Élément {{ $i }}</span>
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $item['label'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Contenu à venir.</p>
            </div>
        @endfor
    </div>
</div>
@endsection
