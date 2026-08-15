@extends('teacher.layout')

@section('title', 'Mon Profil')
@section('heading', 'Mon Profil')
@section('subtitle', 'Espace enseignant')

@section('content')
<section class="max-w-xl rounded-2xl border border-slate-200 bg-white p-6">
    <div class="flex items-center gap-4">
        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-500 text-lg font-bold text-white">
            {{ strtoupper(mb_substr($currentTeacher->nom_complet, 0, 1)) }}
        </span>
        <div>
            <p class="text-lg font-extrabold text-slate-900" style="font-family:'Poppins',sans-serif;">{{ $currentTeacher->nom_complet }}</p>
            <p class="text-sm text-slate-500">{{ $currentTeacher->displayId() }} · {{ $currentTeacher->matiere }}</p>
            <span class="mt-1 inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Professeur validé
            </span>
        </div>
    </div>
    <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
        <div><dt class="text-slate-500">Login</dt><dd class="font-semibold">{{ $currentTeacher->login }}</dd></div>
        <div><dt class="text-slate-500">Contact</dt><dd class="font-semibold">{{ $currentTeacher->contact }}</dd></div>
        <div><dt class="text-slate-500">Ville</dt><dd class="font-semibold">{{ $currentTeacher->ville }}</dd></div>
        <div><dt class="text-slate-500">Niveau</dt><dd class="font-semibold">{{ $currentTeacher->niveau ?: '—' }}</dd></div>
    </dl>
</section>
@endsection
