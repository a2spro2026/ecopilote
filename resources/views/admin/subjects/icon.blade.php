@php
    $icon = $m['icon'] ?? null;
    $flag = $m['flag'] ?? null;
    $toneBg = $toneBg ?? [];
    $bg = $toneBg[$m['tone'] ?? 'blue'] ?? 'from-blue-500 to-indigo-500';
@endphp

@if ($flag)
    <span class="flex h-10 w-10 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700" title="{{ $m['nom'] }}">
        @include('admin.subjects.flag', ['code' => $flag])
    </span>
@else
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $bg }} text-white shadow-sm" title="{{ $m['nom'] }}">
        @if ($icon === 'math')
            <span class="text-[15px] font-black leading-none tracking-tight">√x</span>
        @elseif ($icon === 'science')
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H8.25A2.25 2.25 0 0 0 6 6v.75m12 0V6a2.25 2.25 0 0 0-2.25-2.25H15M9 3.75V6m6-2.25V6M9 6h6m-7.5 12.75h9A2.25 2.25 0 0 0 18.75 16.5v-1.06a2.25 2.25 0 0 0-.659-1.591L14.25 10.5V8.25m-4.5 0V10.5L5.91 13.849a2.25 2.25 0 0 0-.66 1.591v1.06A2.25 2.25 0 0 0 7.5 18.75Z"/>
            </svg>
        @elseif ($icon === 'leaf')
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.2-1.8-7.5-6-7.5-11.25C4.5 5.5 8.25 3 12 3s7.5 2.5 7.5 6.75C19.5 15 16.2 19.2 12 21Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9"/>
            </svg>
        @elseif ($icon === 'globe')
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.5 2.7 3.75 6 3.75 9S14.5 18.3 12 21c-2.5-2.7-3.75-6-3.75-9S9.5 5.7 12 3Z"/>
            </svg>
        @elseif ($icon === 'code')
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25 4.5 12l3.75 3.75M15.75 8.25 19.5 12l-3.75 3.75M13.5 6.75 10.5 17.25"/>
            </svg>
        @else
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
            </svg>
        @endif
    </span>
@endif
