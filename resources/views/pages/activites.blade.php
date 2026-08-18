@extends('layouts.app')

@section('title', 'Activités')

@section('content')
    @include('partials.page-header', [
        'title' => 'Activités Parascolaires',
        'subtitle' => "Sport, arts, sciences et culture pour un épanouissement complet.",
    ])

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        @include('partials.site-video-frame')
    </section>
@endsection
