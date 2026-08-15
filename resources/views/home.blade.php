@extends('layouts.app')

@section('title', 'Accueil')

@section('content')

    {{-- HERO --}}
    <section class="relative flex min-h-[calc(100vh-6rem)] items-center overflow-hidden h-[calc(100vh-6rem)]">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1920&q=80"
                 alt="Campus" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-950/90 via-blue-900/80 to-blue-800/60"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-2xl">
                <span class="inline-block h-1.5 w-20 rounded-full bg-gradient-to-r from-emerald-400 to-blue-400"></span>
                <h1 class="mt-6 text-5xl font-black leading-[1.05] tracking-tight text-white sm:text-6xl lg:text-7xl" style="font-family:'Poppins',sans-serif;">
                    Ici où
                    <span class="relative inline-block">
                        <span class="bg-gradient-to-r from-emerald-300 via-emerald-400 to-blue-300 bg-clip-text text-transparent drop-shadow-[0_2px_12px_rgba(16,185,129,0.45)]">L'AVENIR</span>
                        <span class="absolute -bottom-1 left-0 h-1 w-full rounded-full bg-gradient-to-r from-emerald-400/80 to-transparent"></span>
                    </span>
                    <br class="hidden sm:block">Commence
                </h1>
                <p class="mt-6 max-w-xl text-lg text-blue-100">
                    Un enseignement de qualité pour un avenir prometteur. Une éducation
                    d'excellence pour un monde en évolution.
                </p>
                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    <a href="{{ route('categories') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-emerald-400 px-7 py-4 text-base font-semibold text-blue-950 shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-300 hover:-translate-y-0.5">
                        Découvrir nos formations
                    </a>
                    <a href="{{ route('portail.etudiant') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-white/40 bg-white/10 px-7 py-4 text-base font-semibold text-white backdrop-blur transition hover:bg-white/20">
                        S'inscrire en ligne
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
