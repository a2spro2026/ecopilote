@extends('teacher.layout')

@section('title', 'Notifications')
@section('heading', 'Notifications')
@section('subtitle', 'Activité de vos classes')

@section('content')
<ul class="space-y-3">
    @foreach ($notifications as $n)
        <li class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex justify-between gap-3">
                <div>
                    <p class="font-semibold text-slate-900">{{ $n['title'] }}</p>
                    <p class="text-sm text-slate-500">{{ $n['text'] }}</p>
                </div>
                <span class="text-[11px] text-slate-400">{{ $n['time'] }}</span>
            </div>
        </li>
    @endforeach
</ul>
@endsection
