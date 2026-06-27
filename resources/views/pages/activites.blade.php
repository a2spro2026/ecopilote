@extends('layouts.app')

@section('title', 'Activités')

@section('content')
    @include('partials.page-header', [
        'title' => 'Activités Parascolaires',
        'subtitle' => "Sport, arts, sciences et culture pour un épanouissement complet.",
    ])

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Football & Basket', 'Équipes sportives encadrées par des coachs.'],
                ['Arts & Théâtre', 'Expression artistique et représentations.'],
                ['Club Sciences', 'Expériences, robotique et innovation.'],
                ['Musique', 'Initiation aux instruments et chorale.'],
                ['Langues', 'Anglais, espagnol et clubs de conversation.'],
                ['Sorties éducatives', 'Excursions et voyages pédagogiques.'],
            ] as [$title, $desc])
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <h3 class="text-lg font-semibold text-blue-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
