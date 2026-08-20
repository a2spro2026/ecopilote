@extends('admin.layout')

@section('title', 'Calendrier')
@section('heading', 'Calendrier')
@section('subtitle', 'Planning')

@section('content')
@php
    $joursFr = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $joursShort = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    $moisFr = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
    $dayIndex = ((int) $date->dayOfWeekIso) - 1;
    $titleHeure = ($joursFr[$dayIndex] ?? '').' '.$date->format('d').' '.($moisFr[(int) $date->month] ?? '').' '.$date->format('Y');
    $titleSemaine = 'Semaine du '.$weekStart->format('d/m').' au '.$weekEnd->format('d/m/Y');
    $heading = $vue === 'semaine' ? $titleSemaine : $titleHeure;
    $buttonClass = [
        'actif' => 'bg-emerald-500 text-white hover:bg-emerald-600',
        'reportee' => 'bg-amber-400 text-amber-950 hover:bg-amber-500',
        'annulee' => 'bg-rose-500 text-white hover:bg-rose-600',
    ];
    $dayKey = $date->format('Y-m-d');
    $sessionsByDateHour = collect($sessions)->groupBy('date')->map(fn ($items) => collect($items)->groupBy(fn ($item) => (string) $item['hour']));
@endphp

<div id="calendarBoard" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Calendrier</h2>
            <p class="text-sm font-semibold text-black">{{ $heading }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="inline-flex overflow-hidden rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                @foreach (['heure' => 'Heure', 'jour' => 'Jour', 'semaine' => 'Semaine'] as $key => $label)
                    <a href="{{ route('admin.page.calendrier', ['vue' => $key, 'date' => $date->format('Y-m-d')]) }}"
                       class="px-3 py-2 text-xs font-bold {{ $vue === $key ? 'bg-gradient-to-r from-blue-600 to-emerald-500 text-white' : 'text-slate-600 hover:bg-white dark:text-slate-200 dark:hover:bg-slate-700' }}">{{ $label }}</a>
                @endforeach
            </div>
            <a href="{{ route('admin.page.calendrier', ['vue' => $vue, 'date' => $prevDate]) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">←</a>
            <a href="{{ route('admin.page.calendrier', ['vue' => $vue, 'date' => $today]) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Aujourd’hui</a>
            <a href="{{ route('admin.page.calendrier', ['vue' => $vue, 'date' => $nextDate]) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">→</a>
            <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</a>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 border-b border-slate-100 px-5 py-3 text-[11px] font-semibold dark:border-slate-800">
        <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-emerald-500"></span> Actif</span>
        <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-amber-400"></span> Reportée</span>
        <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-md bg-rose-500"></span> Annulée</span>
    </div>

    @if ($vue === 'semaine')
        <div class="overflow-x-auto">
            <table class="ep-calendar-table min-w-[1100px] w-full table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-white">
                        <th class="w-16 border-b border-r border-slate-200 bg-white px-2 py-3 text-xs font-bold text-black">Heure</th>
                        @foreach ($weekDays as $i => $day)
                            @php $isToday = $day->isSameDay(now()); @endphp
                            <th class="border-b border-slate-200 bg-white px-2 py-3 text-center {{ $isToday ? 'bg-blue-50' : '' }}">
                                <span class="block text-[11px] font-bold uppercase tracking-wide text-black">{{ $joursShort[$i] }}</span>
                                <span class="text-sm font-extrabold text-black">{{ $day->format('d/m') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hours as $hour)
                        <tr class="align-top">
                            <td class="border-r border-t border-slate-100 px-2 py-2 text-xs font-bold tabular-nums text-black">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                            @foreach ($weekDays as $day)
                                @php
                                    $dayMap = $sessionsByDateHour->get($day->format('Y-m-d'), collect());
                                    $items = $dayMap->get((string) $hour, collect());
                                @endphp
                                <td class="border-t border-slate-100 p-1.5 dark:border-slate-800 {{ $day->isSameDay(now()) ? 'bg-blue-50/40 dark:bg-blue-500/5' : '' }}">
                                    <div class="flex min-h-[3.2rem] flex-col gap-1">
                                        @foreach ($items as $item)
                                            <button type="button" data-open-session="{{ $item['id'] }}"
                                                    class="w-full rounded-lg px-2 py-1.5 text-left text-[11px] font-bold shadow-sm {{ $buttonClass[$item['statut']] ?? $buttonClass['actif'] }}">
                                                <span class="block truncate">{{ $item['start'] }} · {{ $item['matiereLabel'] }}</span>
                                                <span class="block truncate text-[10px] font-semibold opacity-90">{{ $item['group'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($vue === 'heure')
        <div class="overflow-x-auto">
            <table class="ep-calendar-table min-w-[720px] w-full table-fixed border-collapse text-sm">
                <thead>
                    <tr class="bg-white">
                        <th class="w-24 border-b border-r border-slate-200 bg-white px-3 py-3 text-xs font-bold text-black">Heure</th>
                        <th class="border-b border-slate-200 bg-white px-3 py-3 text-left text-xs font-bold text-black">Séances</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hours as $hour)
                        @php
                            $dayMap = $sessionsByDateHour->get($dayKey, collect());
                            $items = $dayMap->get((string) $hour, collect());
                        @endphp
                        <tr class="align-top">
                            <td class="border-r border-t border-slate-100 px-3 py-3 text-sm font-bold tabular-nums text-black">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                            <td class="border-t border-slate-100 p-2 dark:border-slate-800">
                                <div class="flex min-h-[3.5rem] flex-wrap gap-2">
                                    @forelse ($items as $item)
                                        <button type="button" data-open-session="{{ $item['id'] }}"
                                                class="min-w-[11rem] rounded-xl px-3 py-2 text-left text-xs font-bold shadow-sm {{ $buttonClass[$item['statut']] ?? $buttonClass['actif'] }}">
                                            <span class="block">{{ $item['start'] }} – {{ $item['end'] }}</span>
                                            <span class="mt-0.5 block truncate text-[11px]">{{ $item['matiereLabel'] }} · {{ $item['group'] }}</span>
                                            <span class="block truncate text-[10px] font-semibold opacity-90">{{ $item['teacher'] }}</span>
                                        </button>
                                    @empty
                                        <span class="self-center text-[11px] text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        @php $daySessions = collect($sessions)->where('date', $dayKey)->values(); @endphp
        <div class="min-h-[28rem] p-5 sm:p-6">
            @if ($daySessions->isEmpty())
                <p class="rounded-2xl border border-dashed border-slate-200 px-4 py-16 text-center text-sm text-slate-500 dark:border-slate-700">Aucune séance ce jour.</p>
            @else
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($daySessions as $item)
                        <button type="button" data-open-session="{{ $item['id'] }}"
                                class="rounded-2xl px-4 py-4 text-left shadow-md {{ $buttonClass[$item['statut']] ?? $buttonClass['actif'] }}">
                            <span class="text-[11px] font-bold uppercase tracking-wide opacity-90">{{ $item['start'] }} – {{ $item['end'] }}</span>
                            <span class="mt-1 block text-base font-extrabold">{{ $item['matiereLabel'] }}</span>
                            <span class="mt-1 block text-xs font-semibold opacity-90">{{ $item['group'] }} · {{ $item['teacher'] }}</span>
                            <span class="mt-2 inline-flex rounded-lg bg-white/20 px-2 py-0.5 text-[11px] font-bold">{{ $item['statutLabel'] }} · Salle {{ $item['room'] }}</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<section id="sessionInfoPanel" class="hidden min-h-[calc(100vh-8rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-blue-600 to-emerald-500 px-6 py-5 text-white">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-white/80">Fiche séance</p>
            <h2 id="sessionInfoTitle" class="text-lg font-extrabold" style="font-family:'Poppins',sans-serif;">Séance</h2>
        </div>
        <span id="sessionInfoStatut" class="rounded-xl bg-white px-3 py-1.5 text-xs font-extrabold text-slate-800"></span>
    </div>
    <dl id="sessionInfoFields" class="grid gap-3 p-6 sm:grid-cols-2 sm:p-8"></dl>
    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <button type="button" id="sessionInfoClose" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">Fermer</button>
    </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const sessions = @json($sessions);
    const board = document.getElementById('calendarBoard');
    const panel = document.getElementById('sessionInfoPanel');
    const fields = document.getElementById('sessionInfoFields');
    const title = document.getElementById('sessionInfoTitle');
    const statutEl = document.getElementById('sessionInfoStatut');

    const escapeHtml = value => String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    const field = (label, value) => `
        <div class="rounded-xl border border-slate-100 px-3 py-2.5 dark:border-slate-800">
            <dt class="text-xs text-slate-500">${escapeHtml(label)}</dt>
            <dd class="text-sm font-medium text-slate-800 dark:text-slate-100">${escapeHtml(value || '—')}</dd>
        </div>`;

    const openInfo = session => {
        title.textContent = session.code;
        statutEl.textContent = session.statutLabel;
        fields.innerHTML = [
            field('Date', session.dateLabel),
            field('N°/Sé', session.code),
            field('Groupe', session.group),
            field('Matière', session.matiereLabel),
            field('Niveau', session.niveau),
            field('Prof', session.teacher),
            field('Élèves', (session.eleves || []).join(', ')),
            field('Effectif', String(session.effectif ?? '0')),
            field('Hr Début', session.start),
            field('Hr Fin', session.end),
            field('N° Salle', session.room),
            field('Statut', session.statutLabel),
            field('Remarque', session.statut === 'actif' ? '' : session.remarque),
        ].join('');
        board.classList.add('hidden');
        panel.classList.remove('hidden');
    };

    const closeInfo = () => {
        panel.classList.add('hidden');
        board.classList.remove('hidden');
    };

    document.querySelectorAll('[data-open-session]').forEach(button => {
        button.addEventListener('click', () => {
            const session = sessions.find(item => item.id === Number(button.dataset.openSession));
            if (session) openInfo(session);
        });
    });

    document.getElementById('sessionInfoClose')?.addEventListener('click', closeInfo);
})();
</script>
@endpush
