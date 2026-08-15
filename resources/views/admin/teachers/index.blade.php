@extends('admin.layout')

@section('title', 'Professeurs')
@section('heading', 'Professeurs')
@section('subtitle', 'Utilisateurs')

@section('content')
@php
    $etatMeta = [
        'actif' => ['chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30', 'dot' => 'bg-emerald-500'],
        'en_attente' => ['chip' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30', 'dot' => 'bg-amber-400'],
        'suspendu' => ['chip' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/15 dark:text-rose-300 dark:ring-rose-500/30', 'dot' => 'bg-rose-500'],
    ];
@endphp

@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">
        {{ session('status') }}
    </div>
@endif

<div class="w-full min-h-[calc(100vh-10rem)] overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-800 sm:px-6">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Liste des professeurs</h2>
            <p class="text-sm text-slate-500">{{ $professeurs->count() }} professeur(s)</p>
        </div>
        <a href="{{ route('admin.page.candidatures-profs') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
            Voir les candidatures →
        </a>
    </div>

    @if ($professeurs->isEmpty())
        <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-6 py-14 text-center">
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Aucun professeur validé</p>
                <p class="mt-1 text-xs text-slate-500">Validez une candidature pour l’ajouter ici.</p>
            </div>
        </div>
    @else
        <div class="w-full overflow-x-auto">
            <table class="ep-table min-w-[1500px] text-sm sm:text-[15px]">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom Complet</th>
                        <th>Login</th>
                        <th>Mot de passe</th>
                        <th>Contact</th>
                        <th>Ville</th>
                        <th>Statut</th>
                        <th>Matière</th>
                        <th>Disponibilité</th>
                        <th>État</th>
                        <th>Mode</th>
                        <th>Montant</th>
                        <th>Type Paiement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($professeurs as $p)
                        @php $em = $etatMeta[$p->etat] ?? $etatMeta['en_attente']; @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                            <td class="font-semibold text-slate-900 dark:text-white">{{ $p->displayId() }}</td>
                            <td class="font-medium text-slate-800 dark:text-slate-100">{{ $p->nom_complet }}</td>
                            <td class="font-semibold text-blue-700 dark:text-blue-300">{{ $p->login ?: \App\Support\EcopiloteIdentity::loginFromName($p->nom_complet) }}</td>
                            <td>
                                <code class="rounded-lg bg-slate-100 px-2 py-1 font-mono text-xs font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-100">
                                    {{ $p->access_password ?: '—' }}
                                </code>
                            </td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->contact }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->ville }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->statutLabel() }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->matiere }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->disponibiliteLabel() }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $em['chip'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $em['dot'] }}"></span>
                                    {{ $p->etatLabel() }}
                                </span>
                            </td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->paiementLabel() }}</td>
                            <td class="font-medium text-slate-800 dark:text-slate-100">{{ $p->montantDisplay() }}</td>
                            <td class="text-slate-600 dark:text-slate-300">{{ $p->typePaiementLabel() }}</td>
                            <td>
                                <div class="flex flex-wrap items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.teachers.show', $p) }}" title="Voir" aria-label="Voir"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.teachers.edit', $p) }}" title="Modifier" aria-label="Modifier"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white transition hover:bg-blue-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.teachers.suspend', $p) }}">
                                        @csrf
                                        <button type="submit" title="Suspendre" aria-label="Suspendre" @disabled($p->etat === 'suspendu')
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-600 text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-40">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
