@extends('admin.layout')

@section('title', 'Tableau de bord')
@section('heading', 'Tableau de bord')

@section('content')

@if ($data)
    {{-- ============ DASHBOARD SUPERADMIN ============ --}}

    {{-- Cartes KPI (fixées en haut au défilement) --}}
    <div class="sticky top-20 z-10 -mx-4 -mt-8 mb-2 border-b border-slate-200/70 bg-slate-100/85 px-4 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-950/85 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($data['cards'] as $card)
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-gradient-to-br {{ $card['color'] }} opacity-10 blur-xl transition group-hover:opacity-20"></div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-extrabold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">{{ $card['value'] }}</p>
                        <p class="text-xs font-medium text-slate-400">{{ $card['unit'] }}</p>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $card['color'] }} text-white shadow-lg transition group-hover:scale-105">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                        </svg>
                    </span>
                </div>
                <div class="relative mt-3 flex items-center gap-1 text-xs font-semibold {{ $card['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
                    <span class="inline-flex items-center gap-1 rounded-full {{ $card['up'] ? 'bg-emerald-50' : 'bg-rose-50' }} px-2 py-0.5">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            @if ($card['up'])
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
                            @endif
                        </svg>
                        {{ $card['trend'] }}
                    </span>
                    <span class="font-normal text-slate-400">vs mois dernier</span>
                </div>
            </div>
        @endforeach
    </div>
    </div>

    {{-- Graphique + Niveaux --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">Évolution annuelle</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Revenus, charges et effectifs par mois</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="annualChart"></canvas>
            </div>
        </div>

        {{-- Étudiants par niveau --}}
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 shadow-sm">
            <h3 class="text-lg font-bold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">Étudiants par niveau</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Répartition scolaire</p>
            @php $maxNiv = max(array_column($data['niveaux'], 'etudiants')); @endphp
            <div class="mt-5 space-y-4">
                @foreach ($data['niveaux'] as $n)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $n['niveau'] }}</span>
                            <span class="font-semibold text-blue-950 dark:text-white">{{ $n['etudiants'] }}</span>
                        </div>
                        <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="{{ $n['color'] }} h-full rounded-full" style="width: {{ round($n['etudiants'] / $maxNiv * 100) }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">{{ $n['classes'] }} classes</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Emploi du temps --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
            <h3 class="text-lg font-bold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">Emploi du temps</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Semaine type</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                        <th class="px-6 py-3 font-semibold">Horaire</th>
                        @foreach ($data['emploi']['jours'] as $jour)
                            <th class="px-4 py-3 font-semibold">{{ $jour }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($data['emploi']['slots'] as $i => $slot)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
                            <td class="whitespace-nowrap px-6 py-3 font-semibold text-blue-900 dark:text-slate-100">{{ $slot }}</td>
                            @foreach ($data['emploi']['grille'][$i] as $cours)
                                <td class="px-4 py-3">
                                    @if ($cours === '—')
                                        <span class="text-slate-300">—</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">{{ $cours }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Activités + Vacances --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Activités scolaires --}}
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">Activités scolaires</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Planning des activités</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left dark:bg-slate-800/60 text-slate-500 dark:text-slate-400">
                            <th class="px-6 py-3 font-semibold">Activité</th>
                            <th class="px-4 py-3 font-semibold">Jour</th>
                            <th class="px-4 py-3 font-semibold">Horaire</th>
                            <th class="px-4 py-3 font-semibold">Responsable</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($data['activites'] as $a)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-3 font-medium text-blue-900 dark:text-slate-100">{{ $a['activite'] }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $a['jour'] }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $a['horaire'] }}</td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $a['responsable'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vacances --}}
        <div class="rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <h3 class="text-lg font-bold text-blue-950 dark:text-white" style="font-family:'Poppins',sans-serif;">Vacances de l'année</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Calendrier scolaire</p>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($data['vacances'] as $v)
                    <div class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-slate-50/60 dark:hover:bg-slate-800/50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-blue-900 dark:text-slate-100">{{ $v['nom'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $v['debut'] }} → {{ $v['fin'] }}</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-500/15 dark:text-blue-300">{{ $v['jours'] }} j</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@else
    {{-- ============ DASHBOARD AUTRES RÔLES ============ --}}
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-blue-700 via-blue-800 to-emerald-600 p-8 text-white shadow-lg">
        <p class="text-sm font-medium text-blue-100">Bienvenue,</p>
        <h2 class="mt-1 text-2xl font-extrabold" style="font-family:'Poppins',sans-serif;">{{ auth()->user()->name }}</h2>
        <p class="mt-2 text-sm text-blue-100">
            Vous êtes connecté en tant que <span class="font-semibold text-white">{{ auth()->user()->roleLabel() }}</span>. Voici vos modules.
        </p>
    </div>

    @if (count($modules))
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($modules as $key => $module)
                <a href="{{ route("admin.module.$key") }}"
                   class="group rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br {{ $module['color'] ?? 'from-blue-600 to-emerald-400' }} text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $module['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-blue-900 dark:text-slate-100">{{ $module['label'] }}</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pôle {{ $module['group'] }}</p>
                    <span class="mt-4 inline-block text-sm font-semibold text-emerald-600">Ouvrir →</span>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-slate-500 dark:text-slate-400">Aucun module disponible pour votre rôle.</p>
    @endif
@endif

@endsection

@if ($data)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('annualChart');
    if (ctx) {
        const isDark = document.documentElement.classList.contains('dark');
        Chart.defaults.color = isDark ? '#cbd5e1' : '#475569';
        const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';
        new Chart(ctx, {
            data: {
                labels: @json($data['months']),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Revenus (k MAD)',
                        data: @json($data['revenue']),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        type: 'bar',
                        label: 'Charges (k MAD)',
                        data: @json($data['charges']),
                        backgroundColor: 'rgba(244, 63, 94, 0.75)',
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Effectifs',
                        data: @json($data['effectifs']),
                        borderColor: 'rgba(37, 99, 235, 1)',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } },
                },
                scales: {
                    y:  { position: 'left',  beginAtZero: true, grid: { color: gridColor } },
                    y1: { position: 'right', grid: { drawOnChartArea: false } },
                    x:  { grid: { display: false } },
                },
            },
        });
    }
</script>
@endpush
@endif
