@extends('student.layout')
@section('title', 'Mes devoirs')
@section('heading', 'Mes devoirs')
@section('subtitle', 'Consultez les consignes et rendez votre travail')
@section('content')
@if(session('status'))<div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('status') }}</div>@endif
@if($errors->any())<div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
<div class="grid gap-5 lg:grid-cols-2">
    @foreach($assignments as $assignment)
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wider text-indigo-600">{{ $assignment['subject'] }}</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $assignment['title'] }}</h2></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $assignment['status'] === 'Corrigé' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $assignment['status'] }}</span></div>
            <p class="mt-3 text-sm text-slate-500">À rendre avant le <strong class="text-slate-700">{{ $assignment['due'] }}</strong></p>
            @if($assignment['score'])
                <div class="mt-5 flex items-center justify-between rounded-xl bg-emerald-50 p-4"><span class="text-sm font-semibold text-emerald-800">Correction disponible</span><strong class="text-xl text-emerald-700">{{ $assignment['score'] }}</strong></div>
            @else
                <form method="POST" enctype="multipart/form-data" action="{{ route('student.assignments.submit', $assignment['id']) }}" class="mt-5 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 p-4">
                    @csrf
                    <label class="block text-xs font-bold text-indigo-900">Ajouter votre fichier</label>
                    <input type="file" name="submission" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-2 block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:font-bold file:text-white">
                    <p class="mt-2 text-[11px] text-slate-500">PDF, Word ou image · 10 Mo maximum</p>
                    <button type="submit" class="mt-3 w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white">Rendre le devoir</button>
                </form>
            @endif
        </article>
    @endforeach
</div>
@endsection
