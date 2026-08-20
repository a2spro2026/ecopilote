@extends('admin.layout')

@section('title', 'Demandes')
@section('heading', 'Demandes')
@section('subtitle', 'Département élèves')

@section('content')
@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif

<div class="w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Demandes</h2>
            <p class="text-sm text-slate-500">{{ $demandes->count() }} demande(s)</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" data-window-close class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            Fermer
        </a>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="ep-table min-w-[1180px] w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[9%]">
                <col class="w-[9%]">
                <col class="w-[16%]">
                <col class="w-[12%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[10%]">
                <col class="w-[14%]">
            </colgroup>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>N° Demande</th>
                    <th>Nom Complet</th>
                    <th>Contact</th>
                    <th>Ville</th>
                    <th>Niveau</th>
                    <th>Matière</th>
                    <th>Type Cour</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($demandes as $d)
                    @php $isValidee = $d->etat === 'validee'; @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 {{ $isValidee ? 'opacity-60' : '' }}">
                        <td class="whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $d->created_at?->format('d/m/Y') ?: '—' }}</td>
                        <td class="font-semibold text-slate-900 dark:text-white">{{ $d->displayId() }}</td>
                        <td class="truncate font-medium text-slate-800 dark:text-slate-100" title="{{ $d->nom_complet }}">{{ $d->nom_complet }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->contact }}">{{ $d->contact }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->ville }}">{{ $d->ville }}</td>
                        <td class="truncate text-slate-600 dark:text-slate-300" title="{{ $d->niveau_scolaire }}">{{ $d->niveau_scolaire }}</td>
                        <td class="text-[12px] font-semibold text-slate-700 dark:text-slate-200" title="{{ $d->matiere }}">{{ \App\Support\SubjectAbbreviation::display($d->matiere) }}</td>
                        <td class="text-slate-600 dark:text-slate-300">{{ $d->typeCoursLabel() }}</td>
                        <td>
                            @if ($isValidee)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30">Validée</span>
                            @else
                                <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                    <form method="POST" action="{{ route('admin.students.applications.validate', $d) }}">
                                        @csrf
                                        <button type="submit" title="Valider" aria-label="Valider"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500 text-white transition hover:bg-emerald-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.applications.pending', $d) }}">
                                        @csrf
                                        <button type="submit" title="En attente" aria-label="En attente"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-white transition
                                                    {{ $d->etat === 'en_attente'
                                                        ? 'bg-amber-500 ring-2 ring-amber-300 ring-offset-1 dark:ring-offset-slate-900'
                                                        : 'bg-amber-400 hover:bg-amber-500' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students.applications.suspend', $d) }}">
                                        @csrf
                                        <button type="submit" title="Suspendre" aria-label="Suspendre"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-white transition
                                                    {{ $d->etat === 'suspendue'
                                                        ? 'bg-rose-600 ring-2 ring-rose-300 ring-offset-1 dark:ring-offset-slate-900'
                                                        : 'bg-rose-500 hover:bg-rose-600' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="!py-14 text-center text-sm text-slate-500">Aucune demande pour le moment. Les inscriptions du portail étudiant apparaîtront ici.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
