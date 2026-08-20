@php
    $icon = $m['icon'] ?? null;
    $flag = $m['flag'] ?? null;
    $toneBg = $toneBg ?? [];
    $bg = $toneBg[$m['tone'] ?? 'blue'] ?? 'from-blue-500 to-indigo-500';
@endphp

@if ($flag)
    <span class="subject-mark subject-mark--flag" title="{{ $m['nom'] }}">
        @include('admin.subjects.flag', ['code' => $flag])
    </span>
@else
    <span class="subject-mark bg-gradient-to-br {{ $bg }} text-white" title="{{ $m['nom'] }}">
        @if ($icon === 'math')
            <span class="subject-mark-glyph">√x</span>
        @elseif ($icon === 'science')
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5m-9 16.5h13.5A1.5 1.5 0 0 0 19.5 18.75v-.87a2.25 2.25 0 0 0-.659-1.591L14.25 11.7V8.25m-4.5 0V11.7L5.16 16.289A2.25 2.25 0 0 0 4.5 17.88v.87A1.5 1.5 0 0 0 6 20.25ZM12 3.75V8.25"/>
            </svg>
        @elseif ($icon === 'leaf')
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4.2-1.8-7.5-6-7.5-11.25C4.5 5.5 8.25 3 12 3s7.5 2.5 7.5 6.75C19.5 15 16.2 19.2 12 21Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9"/>
            </svg>
        @elseif ($icon === 'globe')
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3c2.5 2.7 3.75 6 3.75 9S14.5 18.3 12 21c-2.5-2.7-3.75-6-3.75-9S9.5 5.7 12 3Z"/>
            </svg>
        @elseif ($icon === 'code')
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 8.25 4.5 12l3.75 3.75M15.75 8.25 19.5 12l-3.75 3.75M13.5 6.75 10.5 17.25"/>
            </svg>
        @else
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
            </svg>
        @endif
    </span>
@endif
