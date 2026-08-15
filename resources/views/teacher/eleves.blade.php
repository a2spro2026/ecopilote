@extends('teacher.layout')

@section('title', 'Mes Élèves')
@section('heading', 'Mes Élèves')
@section('subtitle', 'Uniquement les classes du professeur')

@section('content')
<div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white lg:block">
    <table class="ep-table w-full text-sm">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Niveau</th>
                <th>Classe</th>
                <th>Présence</th>
                <th>Séances</th>
                <th>Progression</th>
                <th>Exercices</th>
                <th>Dernière activité</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($eleves as $e)
                <tr>
                    <td><a href="{{ route('teacher.eleves.show', $e['id']) }}" class="font-semibold text-blue-700">{{ $e['nom'] }}</a></td>
                    <td>{{ $e['niveau'] }}</td>
                    <td>{{ $e['classe'] }}</td>
                    <td>{{ $e['presence'] }}</td>
                    <td>{{ $e['seances'] }}</td>
                    <td>{{ $e['progression'] }} %</td>
                    <td>{{ $e['exercices'] }}</td>
                    <td>{{ $e['activite'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="grid gap-3 lg:hidden">
    @foreach ($eleves as $e)
        <a href="{{ route('teacher.eleves.show', $e['id']) }}" class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="font-bold text-slate-900">{{ $e['nom'] }}</p>
            <p class="text-sm text-slate-500">{{ $e['niveau'] }} · {{ $e['classe'] }}</p>
            <p class="mt-2 text-xs text-slate-500">Présence {{ $e['presence'] }} · Progression {{ $e['progression'] }} %</p>
        </a>
    @endforeach
</div>
@endsection
