@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <style>
        .home-title-kicker {
            display: block;
            color: #ffffff !important;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.35rem, 2.4vw, 1.9rem);
            font-weight: 600;
            letter-spacing: 0.18em;
            text-shadow: 0 2px 12px rgba(2, 18, 40, 0.7), 0 0 18px rgba(125, 211, 252, 0.55);
        }
        .home-title-avenir {
            display: block;
            margin-top: 0.55rem;
            color: #ffffff !important;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(3.4rem, 8.5vw, 7rem);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: 0.04em;
            text-shadow:
                0 3px 14px rgba(2, 18, 40, 0.55),
                0 0 18px rgba(255, 255, 255, 0.55),
                0 0 36px rgba(110, 231, 183, 0.7);
        }
        .home-title-line {
            display: block;
            margin-top: 0.7rem;
            height: 3px;
            width: min(22rem, 80%);
            border-radius: 9999px;
            background: linear-gradient(90deg, #ffffff, #6ee7b7, transparent);
        }
    </style>

    <section class="relative flex min-h-[calc(100vh-6rem)] items-start overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-background.png') }}"
                 alt="Élèves en cours à domicile" class="h-full w-full object-cover object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-950/75 via-blue-900/40 to-transparent"></div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 pt-10 pb-16 sm:px-6 lg:px-8 lg:pt-14">
            <div class="max-w-3xl">
                <h1>
                    <span class="home-title-kicker" style="color:#ffffff">Ici où commence</span>
                    <span class="home-title-avenir" style="color:#ffffff">L'AVENIR</span>
                    <span class="home-title-line"></span>
                </h1>
                <div class="mt-12">
                    <a href="{{ route('portail.etudiant') }}"
                       class="inline-flex items-center justify-center rounded-full bg-white px-8 py-4 text-base font-bold text-slate-900 shadow-lg shadow-emerald-400/30 transition hover:-translate-y-0.5 hover:shadow-xl">
                        S'inscrire en ligne
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
