@extends('teacher.layout')

@section('title', 'Mes Séances')
@section('heading', 'Mes Séances')
@section('subtitle', 'Planning pédagogique')

@section('content')
@php
    $status = [
        'en_direct' => ['label' => 'En direct', 'chip' => 'bg-emerald-50 text-emerald-700'],
        'a_venir' => ['label' => 'À venir', 'chip' => 'bg-amber-50 text-amber-700'],
        'terminee' => ['label' => 'Terminée', 'chip' => 'bg-blue-50 text-blue-700'],
        'annulee' => ['label' => 'Annulée', 'chip' => 'bg-rose-50 text-rose-700'],
    ];
@endphp
<div class="mb-5 flex flex-wrap gap-2">
    @foreach (['aujourdhui' => 'Aujourd’hui', 'semaine' => 'Cette semaine', 'mois' => 'Ce mois', 'toutes' => 'Toutes'] as $k => $label)
        <a href="{{ route('teacher.seances', ['filtre' => $k]) }}"
           class="rounded-xl px-3 py-1.5 text-sm font-semibold {{ $filtre === $k ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-600' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white md:block">
    <table class="ep-table w-full text-sm">
        <thead>
            <tr>
                <th>Heure</th>
                <th>Classe</th>
                <th>Matière</th>
                <th>Élèves</th>
                <th>Professeur</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($seances as $s)
                @php $st = $status[$s['statut']]; @endphp
                <tr>
                    <td>{{ $s['date'] }}<br><span class="text-xs text-slate-500">{{ $s['heure'] }}</span></td>
                    <td>{{ $s['classe'] }}</td>
                    <td title="{{ $s['matiere'] }}">{{ \App\Support\SubjectAbbreviation::display($s['matiere']) }}</td>
                    <td>{{ $s['eleves'] }}</td>
                    <td>{{ $s['professeur'] }}</td>
                    <td><span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $st['chip'] }}">{{ $st['label'] }}</span></td>
                    <td>
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('teacher.classes') }}" class="text-xs font-semibold text-blue-600">Voir</a>
                            @if (! empty($s['joinable']) && in_array($s['statut'], ['a_venir', 'en_direct'], true))
                                <a href="{{ route('teacher.salle.show', $s['id']) }}" class="text-xs font-semibold text-emerald-600">{{ $s['statut'] === 'en_direct' ? 'Rejoindre' : 'Entrer' }}</a>
                            @endif
                            @if ($s['statut'] === 'terminee')
                                <a href="{{ route('teacher.archives') }}" class="text-xs font-semibold text-slate-600">Archive</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="grid gap-3 md:hidden">
    @foreach ($seances as $s)
        @php $st = $status[$s['statut']]; @endphp
        <article class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <p class="font-bold text-slate-900">{{ $s['matiere'] }}</p>
                <span class="rounded-full px-2 py-1 text-[11px] font-semibold {{ $st['chip'] }}">{{ $st['label'] }}</span>
            </div>
            <p class="mt-1 text-sm text-slate-500">{{ $s['classe'] }} · {{ $s['date'] }} · {{ $s['heure'] }}</p>
            <p class="text-sm text-slate-500">{{ $s['eleves'] }} élèves · Salle {{ $s['salle'] }}</p>
            @if (! empty($s['joinable']) && in_array($s['statut'], ['a_venir', 'en_direct'], true))
                <a href="{{ route('teacher.salle.show', $s['id']) }}" class="mt-3 inline-flex rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">{{ $s['statut'] === 'en_direct' ? 'Rejoindre la salle' : 'Entrer dans la salle' }}</a>
            @endif
        </article>
    @endforeach
</div>
@endsection
