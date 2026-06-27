@extends('layouts.app')

@section('title', 'Catégories')

@section('content')
    @include('partials.page-header', [
        'title' => 'Nos Catégories & Formations',
        'subtitle' => "Un parcours complet, de la maternelle au lycée.",
    ])

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Maternelle', 'Éveil et premiers apprentissages', 'from-pink-500 to-rose-500'],
                ['Primaire', 'Fondamentaux et autonomie', 'from-amber-500 to-orange-500'],
                ['Collège', 'Approfondissement des savoirs', 'from-emerald-500 to-teal-500'],
                ['Lycée', 'Préparation au baccalauréat', 'from-blue-600 to-indigo-600'],
            ] as [$title, $desc, $gradient])
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="h-28 bg-gradient-to-br {{ $gradient }}"></div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-blue-900">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
