@extends('student.layout')
@section('title', 'Notifications')
@section('heading', 'Mes notifications')
@section('subtitle', 'Cours, devoirs et nouveaux documents')
@section('content')
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    @foreach($notifications as $notification)
        <article class="flex gap-4 border-b border-slate-100 p-5 last:border-0 {{ $notification['unread'] ? 'bg-indigo-50/50' : '' }}"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification['unread'] ? 'bg-indigo-500' : 'bg-slate-300' }}"></span><div class="min-w-0 flex-1"><h2 class="text-sm font-bold text-slate-900">{{ $notification['title'] }}</h2><p class="mt-1 text-sm text-slate-600">{{ $notification['detail'] }}</p><p class="mt-2 text-[11px] font-semibold text-slate-400">{{ $notification['time'] }}</p></div></article>
    @endforeach
</section>
@endsection
