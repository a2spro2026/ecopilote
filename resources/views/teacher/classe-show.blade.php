@extends('teacher.layout')

@section('title', $classe['salle'])
@section('heading', $classe['salle'])
@section('subtitle', $classe['matiere'].' · '.$classe['niveau'])

@section('content')
<a href="{{ route('teacher.classes') }}" class="mb-5 inline-flex text-sm font-semibold text-blue-600">← Mes classes</a>

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-sm text-slate-500">{{ $classe['effectif'] }} élèves · {{ $classe['type'] }}</p>
        <p class="text-sm font-medium text-slate-700">Prochaine séance : {{ $classe['prochaine'] }}</p>
    </div>
    <a href="{{ route('teacher.salle') }}" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-2.5 text-sm font-semibold text-white">Ouvrir la classe</a>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
    <table class="ep-table w-full text-sm">
        <thead>
            <tr>
                <th>Élève</th>
                <th>Niveau</th>
                <th>Présence</th>
                <th>Progression</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($eleves as $e)
                <tr>
                    <td class="font-medium">{{ $e['nom'] }}</td>
                    <td>{{ $e['niveau'] }}</td>
                    <td>{{ $e['presence'] }}</td>
                    <td>{{ $e['progression'] }} %</td>
                    <td><a href="{{ route('teacher.eleves.show', $e['id']) }}" class="text-xs font-semibold text-blue-600">Fiche</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
