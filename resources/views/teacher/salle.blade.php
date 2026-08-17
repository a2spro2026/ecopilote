<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salle · {{ $session['matiere'] }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="teacher-salle antialiased">
@php
    $online = collect($eleves)->where('etat', 'en_ligne')->count();
    $present = collect($eleves)->whereIn('etat', ['en_ligne', 'present'])->count();
@endphp

<header class="flex h-14 shrink-0 items-center gap-3 border-b border-white/10 px-4">
    <a href="{{ route('teacher.bureau') }}" class="rounded-lg px-2 py-1 text-xs font-semibold text-slate-300 hover:bg-white/10">← Bureau</a>
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-bold text-white" style="font-family:'Poppins',sans-serif;">{{ $session['matiere'] }} · {{ $session['salle'] }}</p>
        <p class="truncate text-[11px] text-slate-400">{{ $currentTeacher->nom_complet }} · {{ $session['debut'] }} — {{ $session['fin'] }}</p>
    </div>
    <span class="hidden text-xs text-slate-300 sm:inline">{{ $online }}/{{ count($eleves) }} connectés</span>
    <span id="sessionTimer" class="rounded-lg bg-white/10 px-2 py-1 font-mono text-xs text-white">00:00</span>
    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/20 px-2.5 py-1 text-[11px] font-bold text-rose-300">
        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-rose-400"></span>
        EN DIRECT
    </span>
</header>

<div class="flex min-h-0 flex-1">
    <aside class="hidden w-56 shrink-0 flex-col border-r border-white/10 bg-slate-900/60 xl:flex">
        <div class="border-b border-white/10 px-4 py-3">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Élèves — {{ $present }}/{{ count($eleves) }}</p>
                <span id="questionBadge" class="hidden rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-950">0 demande</span>
            </div>
        </div>
        <ul class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($eleves as $e)
                @php
                    $dot = match($e['etat']) {
                        'en_ligne' => 'bg-emerald-400',
                        'present' => 'bg-emerald-600',
                        'absent' => 'bg-rose-500',
                        default => 'bg-amber-400',
                    };
                    $label = match($e['etat']) {
                        'en_ligne' => 'En ligne',
                        'present' => 'Présent',
                        'absent' => 'Absent',
                        default => 'Déconnecté',
                    };
                @endphp
                <li class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm">
                    <span class="flex items-center gap-2 text-slate-200">
                        <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
                        {{ $e['nom'] }}
                    </span>
                    <span class="text-[10px] text-slate-500">{{ $label }}</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-white/10 p-3">
            <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-500">Demandes des élèves</p>
            <div id="studentRequests" class="max-h-40 space-y-2 overflow-y-auto">
                <p id="emptyRequests" class="text-[11px] text-slate-500">Aucune demande en attente.</p>
            </div>
        </div>
    </aside>

    <section class="flex min-w-0 flex-1 flex-col p-3">
        <div class="mb-2 flex flex-wrap items-center gap-1.5">
            <button type="button" data-tool="select" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Sélection</button>
            <button type="button" data-tool="pen" class="board-tool rounded-lg bg-emerald-500 px-2 py-1 text-[11px] font-semibold text-white">Stylo</button>
            <button type="button" data-tool="highlight" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Surligneur</button>
            <button type="button" data-tool="eraser" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Gomme</button>
            <button type="button" data-tool="text" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Clavier</button>
            <button type="button" data-tool="line" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Ligne</button>
            <button type="button" data-tool="rect" class="board-tool rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Formes</button>
            <button type="button" id="openShapeLibrary" class="rounded-lg bg-blue-600 px-2 py-1 text-[11px] font-semibold text-white">Bibliothèque de formes</button>
            <div class="relative">
                <button type="button" id="boardRulingButton" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Lignes : aucune</button>
                <div id="boardRulingMenu" class="absolute left-0 top-full z-40 mt-1 hidden w-56 overflow-hidden rounded-xl border border-white/10 bg-slate-900 shadow-2xl">
                    @foreach ([
                        'none' => ['Page blanche', 'Aucun repère'],
                        'lines' => ['Lignes simples', 'Écriture courante'],
                        'seyes' => ['Lignes de cahier', 'Apprentissage de l’écriture'],
                        'grid' => ['Carreaux', 'Maths et géométrie'],
                    ] as $key => $option)
                        <button type="button" data-ruling="{{ $key }}" class="ruling-option block w-full px-3 py-2 text-left hover:bg-white/10">
                            <span class="block text-[12px] font-semibold text-white">{{ $option[0] }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $option[1] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <button type="button" id="boardSymbolsButton" class="rounded-lg bg-violet-600 px-2 py-1 text-[11px] font-semibold text-white">Signes mathématiques</button>
                <div id="boardSymbolsMenu" class="absolute left-0 top-full z-40 mt-1 hidden w-72 rounded-xl border border-white/10 bg-slate-900 p-3 shadow-2xl">
                    <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-400">Opérations et relations</p>
                    <div class="grid grid-cols-6 gap-1.5">
                        @foreach ([
                            '+' => 'Addition',
                            '−' => 'Soustraction',
                            '×' => 'Multiplication',
                            '÷' => 'Division',
                            '=' => 'Égal',
                            '≠' => 'Différent',
                            '<' => 'Inférieur',
                            '>' => 'Supérieur',
                            '≤' => 'Inférieur ou égal',
                            '≥' => 'Supérieur ou égal',
                            '±' => 'Plus ou moins',
                            '%' => 'Pourcentage',
                            '√' => 'Racine carrée',
                            'π' => 'Pi',
                            '∞' => 'Infini',
                            '°' => 'Degré',
                            '²' => 'Au carré',
                            '³' => 'Au cube',
                        ] as $symbol => $label)
                            <button type="button" data-math-symbol="{{ $symbol }}" title="{{ $label }}" aria-label="{{ $label }}"
                                    class="math-symbol flex h-9 items-center justify-center rounded-lg bg-white/10 text-lg font-semibold text-white transition hover:bg-violet-500">
                                {{ $symbol }}
                            </button>
                        @endforeach
                    </div>
                    <p class="mt-2 text-[10px] text-slate-400">Le signe est inséré directement au curseur.</p>
                </div>
            </div>
            <button type="button" id="boardShapeAssist" class="rounded-lg bg-emerald-500 px-2 py-1 text-[11px] font-semibold text-white">Formes auto : activé</button>
            <div id="boardPalette" class="flex items-center gap-1 rounded-lg bg-white/10 p-1" aria-label="Couleurs du tableau">
                @foreach ([
                    '#0f172a' => 'Noir',
                    '#2563eb' => 'Bleu',
                    '#dc2626' => 'Rouge',
                    '#16a34a' => 'Vert',
                    '#f59e0b' => 'Orange',
                    '#7c3aed' => 'Violet',
                    '#db2777' => 'Rose',
                    '#ffffff' => 'Blanc',
                ] as $color => $label)
                    <button type="button"
                            data-board-color="{{ $color }}"
                            title="{{ $label }}"
                            aria-label="{{ $label }}"
                            class="board-color h-5 w-5 rounded-full border border-white/40 transition hover:scale-110 {{ $loop->first ? 'ring-2 ring-emerald-400 ring-offset-1 ring-offset-slate-900' : '' }}"
                            style="background-color: {{ $color }}"></button>
                @endforeach
                <input id="boardColor" type="color" value="#0f172a" title="Couleur personnalisée" aria-label="Couleur personnalisée" class="h-5 w-6 cursor-pointer rounded border-0 bg-transparent p-0">
            </div>
            <input id="boardSize" type="range" min="1" max="24" value="3" class="w-20">
            <button type="button" id="boardUndo" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Annuler</button>
            <button type="button" id="boardRedo" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Rétablir</button>
            <button type="button" id="boardClear" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">Effacer</button>
            <button type="button" id="boardZoomIn" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">+</button>
            <button type="button" id="boardZoomOut" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">−</button>
            <label class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">
                Importer
                <input id="boardImport" type="file" accept="image/*,.pdf" class="hidden">
            </label>
            <button type="button" id="boardLock" class="ml-auto rounded-lg bg-amber-500/20 px-2 py-1 text-[11px] font-semibold text-amber-200">Tableau verrouillé</button>
            <span class="hidden items-center gap-1 text-[11px] text-emerald-300 sm:inline-flex">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                Tableau partagé · sync
            </span>
        </div>

        <div class="teacher-board min-h-0 flex-1">
            <canvas id="boardBackground" class="teacher-board-layer" aria-hidden="true"></canvas>
            <canvas id="boardCanvas" tabindex="0" aria-label="Tableau blanc interactif"></canvas>
            <p id="keyboardHint" class="teacher-board-hint">Tapez au clavier pour écrire · dessinez une forme, elle est corrigée automatiquement.</p>
            <div id="mediaPreviewWrap" class="absolute right-3 top-3 z-20 hidden w-48 overflow-hidden rounded-2xl border border-slate-700 bg-slate-950 shadow-2xl">
                <div class="flex items-center justify-between border-b border-white/10 px-2 py-1">
                    <span id="mediaPreviewLabel" class="text-[10px] font-semibold text-slate-300">Caméra</span>
                    <button type="button" id="closeMediaPreview" class="text-xs text-slate-400 hover:text-white" aria-label="Masquer l’aperçu">×</button>
                </div>
                <video id="mediaPreview" class="aspect-video w-full bg-black object-cover" autoplay muted playsinline></video>
            </div>
        </div>

        <div class="mt-2 flex items-center gap-2">
            <div id="boardPages" class="flex gap-1"></div>
            <button type="button" id="boardNewPage" class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white">+ Nouvelle page</button>
        </div>
    </section>

    <aside id="docsPanel" class="hidden w-64 shrink-0 flex-col border-l border-white/10 bg-slate-900/60 lg:flex">
        <div class="border-b border-white/10 px-4 py-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Contributions élèves</p>
                <span id="contributionCount" class="rounded-full bg-white/10 px-2 py-0.5 text-[10px] text-slate-300">0</span>
            </div>
            <div id="studentContributions" class="mt-2 max-h-48 space-y-2 overflow-y-auto">
                <p id="emptyContributions" class="text-[11px] text-slate-500">Les textes et fichiers envoyés apparaîtront ici.</p>
            </div>
        </div>
        <div class="border-b border-white/10 px-4 py-3">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Documents du cours</p>
        </div>
        <ul class="flex-1 space-y-2 overflow-y-auto p-3">
            @foreach ($documents as $d)
                <li class="rounded-xl border border-white/10 bg-white/5 p-3">
                    <p class="text-sm font-semibold text-white">{{ $d['nom'] }}</p>
                    <p class="text-[11px] text-slate-400">{{ $d['type'] }}</p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <button type="button" class="rounded-md bg-white/10 px-2 py-1 text-[10px] font-semibold text-white">Ouvrir</button>
                        <button type="button" class="rounded-md bg-white/10 px-2 py-1 text-[10px] font-semibold text-white">Tableau</button>
                        <button type="button" class="rounded-md bg-white/10 px-2 py-1 text-[10px] font-semibold text-white">Partager</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </aside>
</div>

<footer class="flex h-16 shrink-0 items-center justify-center gap-2 overflow-x-auto border-t border-white/10 px-3">
    <button type="button" id="microphoneButton" aria-pressed="false" class="media-control inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white">
        <span class="media-dot h-2 w-2 rounded-full bg-slate-500"></span>
        <span class="media-label">Activer le micro</span>
        <span id="micEqualizer" class="mic-equalizer hidden" aria-hidden="true">
            <span class="mic-eq-bar" style="--eq-h: 20%"></span>
            <span class="mic-eq-bar" style="--eq-h: 45%"></span>
            <span class="mic-eq-bar" style="--eq-h: 70%"></span>
            <span class="mic-eq-bar" style="--eq-h: 35%"></span>
            <span class="mic-eq-bar" style="--eq-h: 55%"></span>
        </span>
    </button>
    <button type="button" id="cameraButton" aria-pressed="false" class="media-control inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white">
        <span class="media-dot h-2 w-2 rounded-full bg-slate-500"></span>
        <span class="media-label">Activer la caméra</span>
    </button>
    <button type="button" id="screenShareButton" aria-pressed="false" class="media-control inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white">
        <span class="media-dot h-2 w-2 rounded-full bg-slate-500"></span>
        <span class="media-label">Partager l’écran</span>
    </button>
    <button type="button" id="documentsButton" class="rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white">Documents</button>
    <button type="button" id="openStudentPanel" class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Vue élève</button>
    <a href="{{ route('teacher.exercices') }}" class="rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white">Exercices</a>
    <button type="button" id="automaticRecordingButton" disabled aria-disabled="true"
            title="L’enregistrement est automatique et ne peut pas être interrompu par le professeur."
            class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-slate-700 px-3 py-2 text-xs font-semibold text-slate-300">
        <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-400 shadow-[0_0_10px_#34d399]"></span>
        <span>Enregistrement automatique</span>
    </button>
    <button type="button" id="chronoBtn" disabled aria-disabled="true"
            title="Le chronomètre démarre automatiquement avec la séance."
            class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-emerald-600/80 px-3 py-2 text-xs font-semibold text-white">
        <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-200"></span>
        <span>Chronomètre</span>
        <span id="chronoDisplay" class="font-mono tabular-nums">00:00</span>
    </button>
    <a href="{{ route('teacher.seance.terminee') }}" id="endSessionLink" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white">Terminer le cours</a>
</footer>

<div id="shapeLibraryModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/70 p-4">
    <section class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white text-slate-800 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="shapeLibraryTitle">
        <header class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h2 id="shapeLibraryTitle" class="font-bold text-slate-900">Bibliothèque de formes</h2>
                <p class="text-xs text-slate-500">Choisissez une forme, puis cliquez-glissez sur le tableau.</p>
            </div>
            <button type="button" id="closeShapeLibrary" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold">Fermer</button>
        </header>
        <div class="grid max-h-[70vh] grid-cols-2 gap-3 overflow-y-auto p-5 sm:grid-cols-3 md:grid-cols-4">
            @php
                $libraryShapes = [
                    ['key' => 'line', 'label' => 'Droite', 'icon' => '<line x1="4" y1="20" x2="28" y2="4"/>'],
                    ['key' => 'arrow', 'label' => 'Flèche', 'icon' => '<path d="M3 16h25M20 7l9 9-9 9"/>'],
                    ['key' => 'square', 'label' => 'Carré', 'icon' => '<rect x="5" y="5" width="22" height="22"/>'],
                    ['key' => 'rectangle', 'label' => 'Rectangle', 'icon' => '<rect x="3" y="8" width="26" height="16"/>'],
                    ['key' => 'circle', 'label' => 'Cercle', 'icon' => '<circle cx="16" cy="16" r="12"/>'],
                    ['key' => 'ellipse', 'label' => 'Ellipse', 'icon' => '<ellipse cx="16" cy="16" rx="14" ry="9"/>'],
                    ['key' => 'triangle', 'label' => 'Triangle', 'icon' => '<path d="M16 3l14 26H2L16 3z"/>'],
                    ['key' => 'right-triangle', 'label' => 'Triangle rectangle', 'icon' => '<path d="M4 4v24h24L4 4z"/>'],
                    ['key' => 'diamond', 'label' => 'Losange', 'icon' => '<path d="M16 2l14 14-14 14L2 16 16 2z"/>'],
                    ['key' => 'pentagon', 'label' => 'Pentagone', 'icon' => '<path d="M16 2l14 10-5 17H7L2 12 16 2z"/>'],
                    ['key' => 'hexagon', 'label' => 'Hexagone', 'icon' => '<path d="M9 3h14l7 13-7 13H9L2 16 9 3z"/>'],
                    ['key' => 'star', 'label' => 'Étoile', 'icon' => '<path d="M16 2l4 10h11l-9 7 4 11-10-7-10 7 4-11-9-7h11L16 2z"/>'],
                ];
            @endphp
            @foreach ($libraryShapes as $shape)
                <button type="button" data-library-shape="{{ $shape['key'] }}"
                        class="library-shape flex min-h-28 flex-col items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-700 transition hover:border-blue-400 hover:bg-blue-50 hover:text-blue-700">
                    <svg class="h-12 w-12" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $shape['icon'] !!}</svg>
                    {{ $shape['label'] }}
                </button>
            @endforeach
        </div>
    </section>
</div>

<div id="studentPanelBackdrop" class="fixed inset-0 z-40 hidden bg-slate-950/70"></div>
<aside id="studentPanel" class="fixed inset-y-0 right-0 z-50 flex w-full max-w-sm translate-x-full flex-col bg-white text-slate-800 shadow-2xl transition-transform duration-300" aria-hidden="true">
    <div class="flex items-center justify-between border-b border-slate-200 p-4">
        <div>
            <p class="font-bold text-slate-900">Aperçu élève</p>
            <p class="text-xs text-slate-500">Simulation locale, sans temps réel</p>
        </div>
        <button type="button" id="closeStudentPanel" class="rounded-lg border border-slate-200 px-2 py-1 text-sm">Fermer</button>
    </div>
    <div class="flex-1 space-y-5 overflow-y-auto p-5">
        <label class="block text-xs font-semibold text-slate-600">
            Élève connecté
            <select id="studentName" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                @foreach ($eleves as $e)
                    @if (in_array($e['etat'], ['en_ligne', 'present'], true))
                        <option>{{ $e['nom'] }}</option>
                    @endif
                @endforeach
            </select>
        </label>

        <section class="rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-200">
            <p class="text-sm font-bold text-amber-900">Poser une question</p>
            <p class="mt-1 text-xs text-amber-700">Le professeur reçoit une demande et choisit quand donner la parole.</p>
            <button type="button" id="raiseHand" class="mt-3 rounded-xl bg-amber-500 px-4 py-2 text-sm font-bold text-white">✋ Lever la main</button>
        </section>

        <section class="rounded-2xl border border-slate-200 p-4">
            <p class="text-sm font-bold text-slate-900">Écrire quelque chose</p>
            <textarea id="studentMessage" rows="4" class="mt-2 w-full resize-none rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Réponse, question ou calcul…"></textarea>
            <button type="button" id="sendStudentMessage" class="mt-2 w-full rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white">Envoyer au professeur</button>
        </section>

        <section class="rounded-2xl border border-slate-200 p-4">
            <p class="text-sm font-bold text-slate-900">Importer un fichier</p>
            <p class="mt-1 text-xs text-slate-500">Images et documents sont soumis au professeur avant partage.</p>
            <input id="studentFile" type="file" accept="image/*,.pdf,.doc,.docx,.ppt,.pptx" class="mt-3 block w-full text-xs">
            <button type="button" id="sendStudentFile" class="mt-3 w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white">Envoyer le fichier</button>
        </section>
    </div>
</aside>

<div id="roomToast" class="pointer-events-none fixed bottom-20 left-1/2 z-[60] hidden -translate-x-1/2 rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-xl"></div>

<script>
(() => {
    const canvas = document.getElementById('boardCanvas');
    const ctx = canvas.getContext('2d');
    const wrap = canvas.parentElement;
    const background = document.getElementById('boardBackground');
    const bg = background.getContext('2d');
    let ruling = 'none';
    let tool = 'pen';
    let drawing = false;
    let last = null;
    let zoom = 1;
    const studentWritingPermissionKey = 'ecopilote.student.writing.allowed';
    const studentWritingRequestKey = 'ecopilote.student.writing.request';
    let locked = localStorage.getItem(studentWritingPermissionKey) !== '1'; // Le professeur peut toujours écrire.
    let pages = [null];
    let page = 0;
    const undo = [];
    const redo = [];
    let typing = null;
    let caretVisible = true;
    let caretTimer = null;
    let strokePoints = [];
    let shapeAssist = true;
    let selectedLibraryShape = null;
    let libraryStart = null;
    let libraryEnd = null;
    let libraryBase = null;
    let libraryBaseImage = null;
    const requests = [];
    const contributions = [];

    // Le réglage vit sur un calque distinct : écrire, gommer ou effacer le tableau
    // ne touche jamais aux lignes.
    function drawRuling() {
        const width = background.width;
        const height = background.height;
        bg.clearRect(0, 0, width, height);
        if (ruling === 'none') return;

        const horizontal = (y, color, lineWidth = 1) => {
            bg.strokeStyle = color;
            bg.lineWidth = lineWidth;
            bg.beginPath();
            bg.moveTo(0, Math.round(y) + 0.5);
            bg.lineTo(width, Math.round(y) + 0.5);
            bg.stroke();
        };
        const vertical = (x, color, lineWidth = 1) => {
            bg.strokeStyle = color;
            bg.lineWidth = lineWidth;
            bg.beginPath();
            bg.moveTo(Math.round(x) + 0.5, 0);
            bg.lineTo(Math.round(x) + 0.5, height);
            bg.stroke();
        };

        if (ruling === 'lines') {
            for (let y = 48; y < height; y += 48) horizontal(y, '#cbd5e1');
            return;
        }

        if (ruling === 'seyes') {
            const block = 56;
            const interline = block / 4;
            for (let y = block; y < height; y += block) {
                for (let step = 1; step <= 3; step += 1) horizontal(y - (step * interline), '#dbeafe');
                horizontal(y, '#93c5fd', 1.4);
            }
            vertical(96, '#fca5a5', 1.4);
            return;
        }

        const step = 40;
        for (let x = step; x < width; x += step) {
            vertical(x, (x / step) % 5 === 0 ? '#cbd5e1' : '#e8eef6');
        }
        for (let y = step; y < height; y += step) {
            horizontal(y, (y / step) % 5 === 0 ? '#cbd5e1' : '#e8eef6');
        }
    }

    function resize() {
        const snap = canvas.toDataURL();
        canvas.width = wrap.clientWidth;
        canvas.height = wrap.clientHeight;
        background.width = wrap.clientWidth;
        background.height = wrap.clientHeight;
        drawRuling();
        const img = new Image();
        img.onload = () => ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        img.src = snap;
        if (typing) {
            typing.base = snap;
            renderTyping(true);
        }
    }
    resize();
    window.addEventListener('resize', resize);

    function snapshot() {
        undo.push(canvas.toDataURL());
        if (undo.length > 30) undo.shift();
        redo.length = 0;
    }
    function restore(src, done) {
        const img = new Image();
        img.onload = () => {
            ctx.clearRect(0,0,canvas.width,canvas.height);
            ctx.drawImage(img,0,0,canvas.width,canvas.height);
            done?.();
        };
        img.src = src;
    }

    function paintBaseSync() {
        if (!typing?.baseImage?.complete) return false;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(typing.baseImage, 0, 0, canvas.width, canvas.height);
        return true;
    }

    function toast(message) {
        const element = document.getElementById('roomToast');
        element.textContent = message;
        element.classList.remove('hidden');
        window.clearTimeout(toast.timer);
        toast.timer = window.setTimeout(() => element.classList.add('hidden'), 2600);
    }

    function setTool(nextTool) {
        if (nextTool !== 'text') endTyping();
        tool = nextTool;
        document.querySelectorAll('.board-tool').forEach(button => {
            button.classList.remove('bg-emerald-500');
            button.classList.add('bg-white/10');
            if (button.dataset.tool === nextTool) {
                button.classList.remove('bg-white/10');
                button.classList.add('bg-emerald-500');
            }
        });
    }

    function fontSize() {
        return 14 + Number(document.getElementById('boardSize').value);
    }

    function measureLine(line) {
        ctx.font = `${fontSize()}px Poppins, sans-serif`;
        return ctx.measureText(line).width;
    }

    // Écart entre deux lignes du réglage actif, pour que la frappe suive le cahier.
    function rulingSpacing() {
        if (ruling === 'lines') return 48;
        if (ruling === 'seyes') return 56;
        if (ruling === 'grid') return 40;
        return null;
    }

    function lineStep() {
        return rulingSpacing() ?? (fontSize() + 5);
    }

    // Le texte doit reposer sur la ligne, pas flotter entre deux.
    function snapBaseline(y) {
        const spacing = rulingSpacing();
        if (!spacing) return y;
        const snapped = Math.round(y / spacing) * spacing;
        return Math.min(Math.max(snapped, spacing), Math.max(spacing, canvas.height - 6)) - 2;
    }

    function drawText(text, x, y, color = document.getElementById('boardColor').value) {
        const lines = String(text).toLocaleUpperCase('fr-FR').split(/\r?\n/);
        if (!lines.length || (lines.length === 1 && !lines[0])) return;
        snapshot();
        const size = fontSize();
        ctx.globalCompositeOperation = 'source-over';
        ctx.fillStyle = color;
        ctx.font = `${size}px Poppins, sans-serif`;
        const step = lineStep();
        lines.forEach((line, index) => ctx.fillText(line, x, y + (index * step)));
    }

    function paintTypingOverlay(showCaret = caretVisible) {
        if (!typing) return;
        const size = fontSize();
        const color = typing.color;
        const lines = typing.text.split(/\r?\n/);
        ctx.globalCompositeOperation = 'source-over';
        ctx.fillStyle = color;
        ctx.font = `${size}px Poppins, sans-serif`;
        const step = lineStep();
        lines.forEach((line, index) => {
            ctx.fillText(line, typing.x, typing.y + (index * step));
        });
        if (showCaret) {
            const lastLine = lines[lines.length - 1] || '';
            const caretX = typing.x + measureLine(lastLine);
            const caretY = typing.y + ((lines.length - 1) * step);
            ctx.strokeStyle = color;
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(caretX + 1, caretY - size + 2);
            ctx.lineTo(caretX + 1, caretY + 2);
            ctx.stroke();
        }
    }

    function renderTyping(showCaret = caretVisible) {
        if (!typing) return;
        if (paintBaseSync()) {
            paintTypingOverlay(showCaret);
            return;
        }
        restore(typing.base, () => paintTypingOverlay(showCaret));
    }

    function startTyping(x, y, initial = '', snap = true) {
        endTyping();
        setTool('text');
        const base = canvas.toDataURL();
        const baseImage = new Image();
        baseImage.src = base;
        typing = {
            x,
            y: snap ? snapBaseline(y) : y,
            text: initial,
            base,
            baseImage,
            color: document.getElementById('boardColor').value,
        };
        caretVisible = true;
        window.clearInterval(caretTimer);
        caretTimer = window.setInterval(() => {
            caretVisible = !caretVisible;
            renderTyping(caretVisible);
        }, 530);
        canvas.focus();
        baseImage.onload = () => renderTyping(true);
        if (baseImage.complete) renderTyping(true);
        document.getElementById('keyboardHint')?.classList.add('hidden');
    }

    function cancelTyping(repaintBase = false) {
        if (!typing) return;
        window.clearInterval(caretTimer);
        caretTimer = null;
        const session = typing;
        typing = null;
        if (repaintBase) restore(session.base);
    }

    function endTyping(done) {
        if (!typing) {
            done?.();
            return;
        }
        window.clearInterval(caretTimer);
        caretTimer = null;
        const session = typing;
        typing = null;
        const finish = () => {
            if (session.text.trim()) {
                // La couleur retenue est celle du début de la saisie : changer de
                // couleur ne doit pas repeindre le texte déjà écrit.
                drawText(session.text, session.x, session.y, session.color);
            }
            done?.();
        };
        if (session.baseImage?.complete) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(session.baseImage, 0, 0, canvas.width, canvas.height);
            finish();
            return;
        }
        restore(session.base, finish);
    }

    const distance = (a, b) => Math.hypot(a.x - b.x, a.y - b.y);

    function pathLength(points) {
        let total = 0;
        for (let i = 1; i < points.length; i += 1) total += distance(points[i - 1], points[i]);
        return total;
    }

    function pointToSegment(point, a, b) {
        const dx = b.x - a.x;
        const dy = b.y - a.y;
        const lengthSquared = (dx * dx) + (dy * dy);
        if (!lengthSquared) return distance(point, a);
        let t = (((point.x - a.x) * dx) + ((point.y - a.y) * dy)) / lengthSquared;
        t = Math.max(0, Math.min(1, t));
        return Math.hypot(point.x - (a.x + (t * dx)), point.y - (a.y + (t * dy)));
    }

    function resamplePath(points, count) {
        const total = pathLength(points);
        if (!total) return points.slice();
        const interval = total / (count - 1);
        const result = [points[0]];
        let previous = points[0];
        let accumulated = 0;
        for (let i = 1; i < points.length; i += 1) {
            let segment = distance(previous, points[i]);
            while (segment > 0 && accumulated + segment >= interval) {
                const ratio = (interval - accumulated) / segment;
                const inserted = {
                    x: previous.x + ((points[i].x - previous.x) * ratio),
                    y: previous.y + ((points[i].y - previous.y) * ratio),
                };
                result.push(inserted);
                previous = inserted;
                segment = distance(previous, points[i]);
                accumulated = 0;
            }
            accumulated += segment;
            previous = points[i];
        }
        while (result.length < count) result.push({ ...points[points.length - 1] });
        return result.slice(0, count);
    }

    function smoothPath(points, window) {
        if (points.length < 3) return points.slice();
        return points.map((point, index) => {
            let sumX = 0;
            let sumY = 0;
            let count = 0;
            for (let offset = -window; offset <= window; offset += 1) {
                const neighbour = points[Math.min(points.length - 1, Math.max(0, index + offset))];
                sumX += neighbour.x;
                sumY += neighbour.y;
                count += 1;
            }
            return { x: sumX / count, y: sumY / count };
        });
    }

    function edgeBulge(path, fromIndex, toIndex) {
        const a = path[fromIndex];
        const b = path[toIndex];
        const edgeLength = distance(a, b);
        if (edgeLength < 1) return 0;

        const between = [];
        let index = fromIndex;
        let guard = 0;
        while (index !== toIndex && guard < path.length) {
            index = (index + 1) % path.length;
            guard += 1;
            if (index === toIndex) break;
            between.push(path[index]);
        }
        if (between.length < 3) return 0;

        // Les sommets tracés à la main sont arrondis : on ignore leurs abords.
        const margin = Math.floor(between.length * 0.15);
        const core = between.slice(margin, between.length - margin);
        const sampled = core.length ? core : between;
        return sampled.reduce((maximum, point) =>
            Math.max(maximum, pointToSegment(point, a, b)), 0) / edgeLength;
    }

    function snapPolygon(vertices, tolerance) {
        const snapped = vertices.map(vertex => ({ ...vertex }));
        for (let i = 0; i < snapped.length; i += 1) {
            const current = snapped[i];
            const next = snapped[(i + 1) % snapped.length];
            if (Math.abs(current.y - next.y) < tolerance) {
                const y = (current.y + next.y) / 2;
                current.y = y;
                next.y = y;
            }
            if (Math.abs(current.x - next.x) < tolerance) {
                const x = (current.x + next.x) / 2;
                current.x = x;
                next.x = x;
            }
        }
        return snapped;
    }

    // Un polygone concentre ses changements de direction sur quelques points, alors
    // qu'un cercle les répartit régulièrement : c'est ce qui les distingue de façon
    // fiable, même sur un tracé tremblé.
    function turningProfile(path, span) {
        const count = path.length;
        return path.map((point, index) => {
            const previous = path[(index - span + count) % count];
            const next = path[(index + span) % count];
            const incoming = Math.atan2(point.y - previous.y, point.x - previous.x);
            const outgoing = Math.atan2(next.y - point.y, next.x - point.x);
            let delta = outgoing - incoming;
            while (delta > Math.PI) delta -= Math.PI * 2;
            while (delta < -Math.PI) delta += Math.PI * 2;
            return delta * (180 / Math.PI);
        });
    }

    function detectCorners(path) {
        const count = path.length;
        // Rotation par pas unitaire : la somme sur tout le contour vaut 360°.
        const turns = turningProfile(path, 1);
        const window = 3;
        const windowed = turns.map((_, index) => {
            let sum = 0;
            for (let offset = -window; offset <= window; offset += 1) {
                sum += turns[(index + offset + count) % count];
            }
            return Math.abs(sum);
        });

        const suppression = window * 3;
        const peaks = [];
        for (let index = 0; index < count; index += 1) {
            const value = windowed[index];
            if (value < 32) continue;
            let isPeak = true;
            for (let offset = -suppression; offset <= suppression; offset += 1) {
                if (!offset) continue;
                const neighbour = windowed[(index + offset + count) % count];
                if (neighbour > value || (neighbour === value && offset < 0)) {
                    isPeak = false;
                    break;
                }
            }
            if (isPeak) peaks.push(index);
        }

        return peaks;
    }

    function ellipseFrom(minX, minY, width, height) {
        const radiusX = width / 2;
        const radiusY = height / 2;
        const isCircle = Math.abs(radiusX - radiusY) < Math.max(radiusX, radiusY) * 0.22;
        const radius = (radiusX + radiusY) / 2;
        return {
            type: 'ellipse',
            cx: minX + radiusX,
            cy: minY + radiusY,
            rx: isCircle ? radius : radiusX,
            ry: isCircle ? radius : radiusY,
            label: isCircle ? 'Cercle' : 'Ellipse',
        };
    }

    function recognizeShape(points, forceShape) {
        if (points.length < 5) return null;

        // Deux niveaux de lissage : le tracé fortement lissé sert à localiser les
        // sommets, le tracé peu lissé conserve la géométrie réelle des côtés.
        const sampled = resamplePath(points, 72);
        const analysis = smoothPath(sampled, 3);
        const path = smoothPath(sampled, 1);
        const xs = path.map(p => p.x);
        const ys = path.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);
        const width = maxX - minX;
        const height = maxY - minY;
        const diagonal = Math.hypot(width, height);
        if (diagonal < 30) return null;

        const start = path[0];
        const end = path[path.length - 1];
        const chord = distance(start, end);
        const isClosed = chord < Math.max(30, diagonal * 0.32) && pathLength(path) > diagonal * 1.2;

        if (!isClosed) {
            const straightLine = { type: 'line', a: points[0], b: points[points.length - 1], label: 'Droite' };
            const deviation = path.reduce((maximum, point) =>
                Math.max(maximum, pointToSegment(point, start, end)), 0);
            if (chord > 0 && deviation / chord < 0.08) return straightLine;
            return forceShape ? straightLine : null;
        }

        const cornerIndices = detectCorners(analysis);
        if (cornerIndices.length < 3 || cornerIndices.length > 6) {
            return ellipseFrom(minX, minY, width, height);
        }

        const straightSided = cornerIndices.every((fromIndex, position) => {
            const toIndex = cornerIndices[(position + 1) % cornerIndices.length];
            return edgeBulge(path, fromIndex, toIndex) < 0.12;
        });
        if (!straightSided) return ellipseFrom(minX, minY, width, height);

        const corners = cornerIndices.map(index => path[index]);

        if (corners.length === 4) {
            const boxCorners = [
                { x: minX, y: minY },
                { x: maxX, y: minY },
                { x: maxX, y: maxY },
                { x: minX, y: maxY },
            ];
            const axisAligned = boxCorners.every(boxCorner =>
                corners.some(corner => distance(corner, boxCorner) < diagonal * 0.22));
            if (axisAligned) {
                const isSquare = Math.abs(width - height) < Math.max(width, height) * 0.2;
                const side = (width + height) / 2;
                const rectWidth = isSquare ? side : width;
                const rectHeight = isSquare ? side : height;
                return {
                    type: 'rect',
                    x: minX + ((width - rectWidth) / 2),
                    y: minY + ((height - rectHeight) / 2),
                    width: rectWidth,
                    height: rectHeight,
                    label: isSquare ? 'Carré' : 'Rectangle',
                };
            }
        }

        const labels = { 3: 'Triangle', 4: 'Quadrilatère', 5: 'Pentagone', 6: 'Hexagone' };
        return {
            type: 'polygon',
            points: snapPolygon(corners, diagonal * 0.06),
            label: labels[corners.length],
        };
    }

    function drawShape(shape) {
        const size = Number(document.getElementById('boardSize').value);
        ctx.globalCompositeOperation = 'source-over';
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = tool === 'highlight'
            ? document.getElementById('boardColor').value + '55'
            : document.getElementById('boardColor').value;
        ctx.lineWidth = tool === 'highlight' ? size * 4 : Math.max(2, size);
        ctx.beginPath();
        if (shape.type === 'line') {
            ctx.moveTo(shape.a.x, shape.a.y);
            ctx.lineTo(shape.b.x, shape.b.y);
        } else if (shape.type === 'arrow') {
            const angle = Math.atan2(shape.b.y - shape.a.y, shape.b.x - shape.a.x);
            const head = Math.min(24, Math.max(10, distance(shape.a, shape.b) * 0.18));
            ctx.moveTo(shape.a.x, shape.a.y);
            ctx.lineTo(shape.b.x, shape.b.y);
            ctx.moveTo(shape.b.x, shape.b.y);
            ctx.lineTo(
                shape.b.x - (head * Math.cos(angle - Math.PI / 6)),
                shape.b.y - (head * Math.sin(angle - Math.PI / 6)),
            );
            ctx.moveTo(shape.b.x, shape.b.y);
            ctx.lineTo(
                shape.b.x - (head * Math.cos(angle + Math.PI / 6)),
                shape.b.y - (head * Math.sin(angle + Math.PI / 6)),
            );
        } else if (shape.type === 'rect') {
            ctx.rect(shape.x, shape.y, shape.width, shape.height);
        } else if (shape.type === 'ellipse') {
            ctx.ellipse(shape.cx, shape.cy, shape.rx, shape.ry, 0, 0, Math.PI * 2);
        } else if (shape.type === 'polygon') {
            shape.points.forEach((point, index) => {
                if (index === 0) ctx.moveTo(point.x, point.y);
                else ctx.lineTo(point.x, point.y);
            });
            ctx.closePath();
        }
        ctx.stroke();
    }

    function regularPolygon(cx, cy, rx, ry, sides, rotation = -Math.PI / 2) {
        return Array.from({ length: sides }, (_, index) => {
            const angle = rotation + ((index / sides) * Math.PI * 2);
            return { x: cx + (Math.cos(angle) * rx), y: cy + (Math.sin(angle) * ry) };
        });
    }

    function createLibraryShape(kind, start, end) {
        const minX = Math.min(start.x, end.x);
        const maxX = Math.max(start.x, end.x);
        const minY = Math.min(start.y, end.y);
        const maxY = Math.max(start.y, end.y);
        const width = Math.max(12, maxX - minX);
        const height = Math.max(12, maxY - minY);
        const cx = minX + (width / 2);
        const cy = minY + (height / 2);

        if (kind === 'line' || kind === 'arrow') {
            return { type: kind, a: start, b: end };
        }
        if (kind === 'square') {
            const side = Math.max(width, height);
            return {
                type: 'rect',
                x: end.x >= start.x ? start.x : start.x - side,
                y: end.y >= start.y ? start.y : start.y - side,
                width: side,
                height: side,
            };
        }
        if (kind === 'rectangle') return { type: 'rect', x: minX, y: minY, width, height };
        if (kind === 'circle') {
            const radius = Math.max(width, height) / 2;
            return { type: 'ellipse', cx, cy, rx: radius, ry: radius };
        }
        if (kind === 'ellipse') return { type: 'ellipse', cx, cy, rx: width / 2, ry: height / 2 };
        if (kind === 'triangle') {
            return {
                type: 'polygon',
                points: [
                    { x: cx, y: minY },
                    { x: maxX, y: maxY },
                    { x: minX, y: maxY },
                ],
            };
        }
        if (kind === 'right-triangle') {
            return {
                type: 'polygon',
                points: [
                    { x: minX, y: minY },
                    { x: minX, y: maxY },
                    { x: maxX, y: maxY },
                ],
            };
        }
        if (kind === 'diamond') {
            return {
                type: 'polygon',
                points: [
                    { x: cx, y: minY },
                    { x: maxX, y: cy },
                    { x: cx, y: maxY },
                    { x: minX, y: cy },
                ],
            };
        }
        if (kind === 'star') {
            const points = [];
            for (let index = 0; index < 10; index += 1) {
                const radiusScale = index % 2 === 0 ? 1 : 0.42;
                const angle = -Math.PI / 2 + ((index / 10) * Math.PI * 2);
                points.push({
                    x: cx + (Math.cos(angle) * (width / 2) * radiusScale),
                    y: cy + (Math.sin(angle) * (height / 2) * radiusScale),
                });
            }
            return { type: 'polygon', points };
        }
        const sides = kind === 'pentagon' ? 5 : 6;
        return { type: 'polygon', points: regularPolygon(cx, cy, width / 2, height / 2, sides) };
    }

    function renderLibraryShape(end) {
        if (!libraryStart || !libraryBaseImage?.complete || !selectedLibraryShape) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(libraryBaseImage, 0, 0, canvas.width, canvas.height);
        drawShape(createLibraryShape(selectedLibraryShape, libraryStart, end));
    }

    function openShapeLibrary() {
        const modal = document.getElementById('shapeLibraryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeShapeLibrary() {
        const modal = document.getElementById('shapeLibraryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function finishStroke() {
        const points = strokePoints;
        strokePoints = [];
        if (!points.length || tool === 'eraser' || tool === 'select') return;
        if (tool !== 'rect' && tool !== 'line' && !shapeAssist) return;

        const shape = tool === 'line'
            ? recognizeShape(points, true)
            : recognizeShape(points, tool === 'rect');
        if (!shape) return;

        const base = undo[undo.length - 1];
        if (!base) return;
        restore(base, () => {
            drawShape(shape);
            toast(`${shape.label} corrigé automatiquement.`);
        });
    }

    document.querySelectorAll('.board-tool').forEach(btn => {
        btn.addEventListener('click', () => {
            setTool(btn.dataset.tool);
            if (tool === 'text') canvas.focus();
        });
    });

    document.getElementById('boardShapeAssist').onclick = function () {
        shapeAssist = !shapeAssist;
        this.textContent = shapeAssist ? 'Formes auto : activé' : 'Formes auto : désactivé';
        this.className = shapeAssist
            ? 'rounded-lg bg-emerald-500 px-2 py-1 text-[11px] font-semibold text-white'
            : 'rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white';
        toast(shapeAssist ? 'Les formes tracées seront corrigées.' : 'Tracé libre sans correction.');
    };

    const rulingButton = document.getElementById('boardRulingButton');
    const rulingMenu = document.getElementById('boardRulingMenu');
    const symbolsButton = document.getElementById('boardSymbolsButton');
    const symbolsMenu = document.getElementById('boardSymbolsMenu');
    const rulingLabels = {
        none: 'aucune',
        lines: 'simples',
        seyes: 'cahier',
        grid: 'carreaux',
    };

    rulingButton.onclick = event => {
        event.stopPropagation();
        symbolsMenu.classList.add('hidden');
        rulingMenu.classList.toggle('hidden');
    };
    symbolsButton.onclick = event => {
        event.stopPropagation();
        rulingMenu.classList.add('hidden');
        symbolsMenu.classList.toggle('hidden');
    };
    document.addEventListener('click', event => {
        if (!rulingMenu.contains(event.target) && event.target !== rulingButton) {
            rulingMenu.classList.add('hidden');
        }
        if (!symbolsMenu.contains(event.target) && event.target !== symbolsButton) {
            symbolsMenu.classList.add('hidden');
        }
    });
    document.querySelectorAll('.ruling-option').forEach(option => {
        option.addEventListener('click', () => {
            ruling = option.dataset.ruling;
            drawRuling();
            rulingMenu.classList.add('hidden');
            rulingButton.textContent = `Lignes : ${rulingLabels[ruling]}`;
            rulingButton.className = ruling === 'none'
                ? 'rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white'
                : 'rounded-lg bg-emerald-500 px-2 py-1 text-[11px] font-semibold text-white';
            toast(ruling === 'none' ? 'Réglage retiré.' : 'Réglage appliqué au fond du tableau.');
            canvas.focus();
        });
    });

    document.querySelectorAll('.math-symbol').forEach(button => {
        button.addEventListener('click', () => {
            const symbol = button.dataset.mathSymbol;
            if (typing) {
                typing.text += symbol;
                caretVisible = true;
                renderTyping(true);
            } else {
                startTyping(canvas.width / (2 * zoom), canvas.height / (2 * zoom), symbol);
            }
            symbolsMenu.classList.add('hidden');
            canvas.focus();
            toast(`${button.getAttribute('aria-label')} inséré.`);
        });
    });

    document.getElementById('openShapeLibrary').onclick = openShapeLibrary;
    document.getElementById('closeShapeLibrary').onclick = closeShapeLibrary;
    document.getElementById('shapeLibraryModal').addEventListener('click', event => {
        if (event.target.id === 'shapeLibraryModal') closeShapeLibrary();
    });
    document.querySelectorAll('.library-shape').forEach(button => {
        button.addEventListener('click', () => {
            selectedLibraryShape = button.dataset.libraryShape;
            setTool('library');
            closeShapeLibrary();
            canvas.focus();
            document.getElementById('keyboardHint').textContent =
                `${button.textContent.trim()} sélectionné : cliquez-glissez pour placer la forme.`;
            document.getElementById('keyboardHint').classList.remove('hidden');
            toast(`${button.textContent.trim()} sélectionné.`);
        });
    });

    function selectBoardColor(color) {
        document.getElementById('boardColor').value = color;
        document.querySelectorAll('.board-color').forEach(button => {
            const selected = button.dataset.boardColor.toLowerCase() === color.toLowerCase();
            button.classList.toggle('ring-2', selected);
            button.classList.toggle('ring-emerald-400', selected);
            button.classList.toggle('ring-offset-1', selected);
            button.classList.toggle('ring-offset-slate-900', selected);
        });

        if (typing) {
            // Le texte déjà tapé est validé dans sa couleur, la suite repart du
            // curseur avec la nouvelle couleur.
            const lines = typing.text.split(/\r?\n/);
            const caretX = typing.text ? typing.x + measureLine(lines[lines.length - 1] || '') : typing.x;
            const caretY = typing.y + ((lines.length - 1) * lineStep());
            endTyping(() => startTyping(caretX, caretY, '', false));
        }

        canvas.focus();
    }

    document.querySelectorAll('.board-color').forEach(button => {
        button.addEventListener('click', () => selectBoardColor(button.dataset.boardColor));
    });
    document.getElementById('boardColor').addEventListener('input', event => selectBoardColor(event.target.value));

    canvas.addEventListener('pointerdown', (e) => {
        const point = { x: e.offsetX / zoom, y: e.offsetY / zoom };
        if (tool === 'library' && selectedLibraryShape) {
            endTyping();
            libraryStart = point;
            libraryEnd = point;
            libraryBase = canvas.toDataURL();
            libraryBaseImage = new Image();
            libraryBaseImage.onload = () => renderLibraryShape(libraryEnd);
            libraryBaseImage.src = libraryBase;
            snapshot();
            drawing = true;
            canvas.setPointerCapture?.(e.pointerId);
            return;
        }
        if (tool === 'text' || typing) {
            startTyping(point.x, point.y);
            return;
        }
        if (tool === 'select') return;
        last = point;
        strokePoints = [point];
        snapshot();
        drawing = true;
    });
    canvas.addEventListener('pointermove', (e) => {
        if (!drawing || tool === 'select' || tool === 'text' || typing) return;
        const x = e.offsetX / zoom, y = e.offsetY / zoom;
        if (tool === 'library' && selectedLibraryShape) {
            libraryEnd = { x, y };
            renderLibraryShape(libraryEnd);
            return;
        }
        strokePoints.push({ x, y });
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        const size = Number(document.getElementById('boardSize').value);
        if (tool === 'eraser') {
            ctx.globalCompositeOperation = 'destination-out';
            ctx.strokeStyle = 'rgba(0,0,0,1)';
            ctx.lineWidth = size * 3;
        } else {
            ctx.globalCompositeOperation = 'source-over';
            ctx.strokeStyle = tool === 'highlight' ? document.getElementById('boardColor').value + '55' : document.getElementById('boardColor').value;
            ctx.lineWidth = tool === 'highlight' ? size * 4 : size;
        }
        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(x, y);
        ctx.stroke();
        last = { x, y };
    });
    window.addEventListener('pointerup', () => {
        if (!drawing) return;
        drawing = false;
        ctx.globalCompositeOperation = 'source-over';
        if (tool === 'library' && selectedLibraryShape) {
            const finalShape = createLibraryShape(
                selectedLibraryShape,
                libraryStart,
                libraryEnd || libraryStart,
            );
            const base = libraryBase;
            libraryStart = null;
            libraryEnd = null;
            libraryBase = null;
            libraryBaseImage = null;
            restore(base, () => {
                drawShape(finalShape);
                toast('Forme placée. Cliquez-glissez pour en ajouter une autre.');
            });
            return;
        }
        finishStroke();
    });

    document.addEventListener('keydown', event => {
        const target = event.target;
        if (event.key === 'Escape' && !document.getElementById('shapeLibraryModal').classList.contains('hidden')) {
            event.preventDefault();
            closeShapeLibrary();
            return;
        }
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName) || target.isContentEditable) return;
        if (event.ctrlKey || event.altKey || event.metaKey) return;

        const printable = event.key.length === 1;
        if (!typing && (printable || event.key === 'Enter' || event.key === 'Backspace')) {
            if (!printable) return;
            event.preventDefault();
            startTyping(canvas.width / (2 * zoom), canvas.height / (2 * zoom), event.key);
            return;
        }

        if (!typing) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            cancelTyping(true);
            return;
        }

        if (event.key === 'Enter') {
            event.preventDefault();
            typing.text += '\n';
            caretVisible = true;
            renderTyping(true);
            return;
        }

        if (event.key === 'Backspace') {
            event.preventDefault();
            typing.text = typing.text.slice(0, -1);
            caretVisible = true;
            renderTyping(true);
            return;
        }

        if (printable) {
            event.preventDefault();
            typing.text += event.key.toLocaleUpperCase('fr-FR');
            caretVisible = true;
            renderTyping(true);
        }
    });

    function clearBoard() {
        ctx.globalCompositeOperation = 'source-over';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    document.getElementById('boardUndo').onclick = () => { cancelTyping(); if (!undo.length) return; redo.push(canvas.toDataURL()); restore(undo.pop()); };
    document.getElementById('boardRedo').onclick = () => { cancelTyping(); if (!redo.length) return; undo.push(canvas.toDataURL()); restore(redo.pop()); };
    document.getElementById('boardClear').onclick = () => {
        cancelTyping();
        snapshot();
        clearBoard();
        document.getElementById('keyboardHint')?.classList.remove('hidden');
    };
    document.getElementById('boardZoomIn').onclick = () => { zoom = Math.min(2, zoom + 0.1); canvas.style.transform = `scale(${zoom})`; canvas.style.transformOrigin = '0 0'; };
    document.getElementById('boardZoomOut').onclick = () => { zoom = Math.max(0.6, zoom - 0.1); canvas.style.transform = `scale(${zoom})`; canvas.style.transformOrigin = '0 0'; };
    document.getElementById('boardLock').onclick = function () {
        locked = !locked;
        localStorage.setItem(studentWritingPermissionKey, locked ? '0' : '1');
        this.classList.remove('animate-pulse', 'ring-2', 'ring-amber-300');
        this.textContent = locked ? 'Tableau verrouillé' : 'Écriture élève autorisée';
        this.className = locked
            ? 'ml-auto rounded-lg bg-amber-500/20 px-2 py-1 text-[11px] font-semibold text-amber-200'
            : 'ml-auto rounded-lg bg-emerald-500/20 px-2 py-1 text-[11px] font-semibold text-emerald-200';
        toast(locked ? 'Écriture élève désactivée.' : 'L’élève peut maintenant écrire sur le tableau.');
    };
    const studentWritingButton = document.getElementById('boardLock');
    studentWritingButton.textContent = locked ? 'Tableau verrouillé' : 'Écriture élève autorisée';
    studentWritingButton.className = locked
        ? 'ml-auto rounded-lg bg-amber-500/20 px-2 py-1 text-[11px] font-semibold text-amber-200'
        : 'ml-auto rounded-lg bg-emerald-500/20 px-2 py-1 text-[11px] font-semibold text-emerald-200';
    window.addEventListener('storage', event => {
        if (event.key !== studentWritingRequestKey || !event.newValue) return;
        let student = 'Un élève';
        try { student = JSON.parse(event.newValue).student || student; } catch (_) {}
        toast(`${student} demande l’autorisation d’écrire. Cliquez sur « Tableau verrouillé » pour accepter.`);
        studentWritingButton.classList.add('animate-pulse', 'ring-2', 'ring-amber-300');
    });
    document.getElementById('boardImport').onchange = (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) {
            toast('Le document est ajouté aux ressources ; seules les images s’affichent sur le tableau.');
            return;
        }
        endTyping();
        snapshot();
        const img = new Image();
        img.onload = () => ctx.drawImage(img, 20, 20, Math.min(canvas.width-40, img.width), Math.min(canvas.height-40, img.height));
        img.src = URL.createObjectURL(file);
    };

    function placeImage(file) {
        if (!file.type.startsWith('image/')) {
            toast(`${file.name} est accepté comme document, mais ne peut pas être affiché sur le tableau.`);
            return;
        }
        endTyping();
        snapshot();
        const image = new Image();
        image.onload = () => {
            const ratio = Math.min((canvas.width - 80) / image.width, (canvas.height - 80) / image.height, 1);
            ctx.drawImage(image, 40, 40, image.width * ratio, image.height * ratio);
            URL.revokeObjectURL(image.src);
        };
        image.src = URL.createObjectURL(file);
    }

    function renderRequests() {
        const box = document.getElementById('studentRequests');
        const empty = document.getElementById('emptyRequests');
        box.querySelectorAll('[data-request]').forEach(element => element.remove());
        empty.classList.toggle('hidden', requests.length > 0);
        requests.forEach((request, index) => {
            const card = document.createElement('div');
            card.dataset.request = index;
            card.className = 'rounded-lg bg-amber-400/10 p-2 ring-1 ring-amber-400/20';
            const name = document.createElement('p');
            name.className = 'text-[11px] font-semibold text-amber-200';
            name.textContent = `✋ ${request.student}`;
            const action = document.createElement('button');
            action.type = 'button';
            action.className = 'mt-1 text-[10px] font-bold text-emerald-300';
            action.textContent = 'Donner la parole';
            action.onclick = () => {
                requests.splice(index, 1);
                renderRequests();
                toast(`Micro autorisé pour ${request.student}`);
            };
            card.append(name, action);
            box.appendChild(card);
        });
        const badge = document.getElementById('questionBadge');
        badge.textContent = `${requests.length} demande${requests.length > 1 ? 's' : ''}`;
        badge.classList.toggle('hidden', requests.length === 0);
    }

    function approveContribution(index) {
        const contribution = contributions[index];
        endTyping();
        if (contribution.type === 'text') {
            drawText(`${contribution.student} : ${contribution.value}`, 50, 80 + ((index % 8) * 34), '#1d4ed8');
        } else {
            placeImage(contribution.file);
        }
        contributions.splice(index, 1);
        renderContributions();
        toast('Contribution affichée sur le tableau.');
    }

    function renderContributions() {
        const box = document.getElementById('studentContributions');
        const empty = document.getElementById('emptyContributions');
        box.querySelectorAll('[data-contribution]').forEach(element => element.remove());
        empty.classList.toggle('hidden', contributions.length > 0);
        contributions.forEach((contribution, index) => {
            const card = document.createElement('div');
            card.dataset.contribution = index;
            card.className = 'rounded-lg bg-white/5 p-2 ring-1 ring-white/10';
            const author = document.createElement('p');
            author.className = 'text-[10px] font-bold text-blue-300';
            author.textContent = contribution.student;
            const content = document.createElement('p');
            content.className = 'mt-0.5 break-words text-[11px] text-slate-300';
            content.textContent = contribution.type === 'text' ? contribution.value : `Fichier : ${contribution.file.name}`;
            const actions = document.createElement('div');
            actions.className = 'mt-2 flex gap-2';
            const approve = document.createElement('button');
            approve.type = 'button';
            approve.className = 'text-[10px] font-bold text-emerald-300';
            approve.textContent = contribution.type === 'text' || contribution.file.type.startsWith('image/') ? 'Afficher au tableau' : 'Accepter';
            approve.onclick = () => approveContribution(index);
            const refuse = document.createElement('button');
            refuse.type = 'button';
            refuse.className = 'text-[10px] font-bold text-rose-300';
            refuse.textContent = 'Refuser';
            refuse.onclick = () => {
                contributions.splice(index, 1);
                renderContributions();
            };
            actions.append(approve, refuse);
            card.append(author, content, actions);
            box.appendChild(card);
        });
        document.getElementById('contributionCount').textContent = contributions.length;
    }

    const studentPanel = document.getElementById('studentPanel');
    const studentBackdrop = document.getElementById('studentPanelBackdrop');
    function toggleStudentPanel(show) {
        studentPanel.classList.toggle('translate-x-full', !show);
        studentBackdrop.classList.toggle('hidden', !show);
        studentPanel.setAttribute('aria-hidden', String(!show));
    }
    document.getElementById('openStudentPanel').onclick = () => toggleStudentPanel(true);
    document.getElementById('closeStudentPanel').onclick = () => toggleStudentPanel(false);
    studentBackdrop.onclick = () => toggleStudentPanel(false);
    document.getElementById('raiseHand').onclick = () => {
        const student = document.getElementById('studentName').value;
        if (requests.some(request => request.student === student)) {
            toast('La demande est déjà envoyée.');
            return;
        }
        requests.push({ student });
        renderRequests();
        toast('Main levée : le professeur a reçu la demande.');
    };
    document.getElementById('sendStudentMessage').onclick = () => {
        const field = document.getElementById('studentMessage');
        const value = field.value.trim();
        if (!value) {
            toast('Écrivez un message avant de l’envoyer.');
            return;
        }
        const contribution = { type: 'text', student: document.getElementById('studentName').value, value };
        if (locked) {
            contributions.push(contribution);
            renderContributions();
            toast('Texte envoyé au professeur pour validation.');
        } else {
            drawText(`${contribution.student} : ${value}`, 50, 80, '#1d4ed8');
            toast('Texte affiché directement sur le tableau.');
        }
        field.value = '';
    };
    document.getElementById('sendStudentFile').onclick = () => {
        const field = document.getElementById('studentFile');
        const file = field.files?.[0];
        if (!file) {
            toast('Choisissez un fichier à envoyer.');
            return;
        }
        const contribution = { type: 'file', student: document.getElementById('studentName').value, file };
        if (!locked && file.type.startsWith('image/')) {
            placeImage(file);
            toast('Image affichée directement sur le tableau.');
        } else {
            contributions.push(contribution);
            renderContributions();
            toast('Fichier envoyé au professeur pour validation.');
        }
        field.value = '';
    };

    function renderPages() {
        const box = document.getElementById('boardPages');
        box.innerHTML = pages.map((_, i) => `<button type="button" data-page="${i}" class="rounded-lg px-2 py-1 text-[11px] font-semibold ${i===page?'bg-emerald-500 text-white':'bg-white/10 text-white'}">Page ${i+1}</button>`).join('');
        box.querySelectorAll('button').forEach(b => b.onclick = () => {
            endTyping();
            pages[page] = canvas.toDataURL();
            page = Number(b.dataset.page);
            if (pages[page]) restore(pages[page]);
            else clearBoard();
            renderPages();
        });
    }
    document.getElementById('boardNewPage').onclick = () => {
        endTyping();
        pages[page] = canvas.toDataURL();
        pages.push(null);
        page = pages.length - 1;
        clearBoard();
        renderPages();
    };

    const roomStorageKey = 'ecopilote:teacher-room:{{ $currentTeacher->id }}';
    let roomEnded = false;

    function saveRoomState() {
        if (roomEnded) return;
        try {
            const current = canvas.toDataURL();
            pages[page] = current;
            sessionStorage.setItem(roomStorageKey, JSON.stringify({
                pages,
                page,
                current,
                ruling,
            }));
        } catch (error) {
            // Le tableau continue de fonctionner si le stockage du navigateur est plein.
        }
    }

    function restoreRoomState() {
        try {
            const saved = JSON.parse(sessionStorage.getItem(roomStorageKey) || 'null');
            if (!saved) return;
            pages = Array.isArray(saved.pages) && saved.pages.length ? saved.pages : [null];
            page = Math.min(Math.max(Number(saved.page) || 0, 0), pages.length - 1);
            ruling = ['none', 'lines', 'seyes', 'grid'].includes(saved.ruling) ? saved.ruling : 'none';
            drawRuling();
            rulingButton.textContent = `Lignes : ${rulingLabels[ruling]}`;
            rulingButton.className = ruling === 'none'
                ? 'rounded-lg bg-white/10 px-2 py-1 text-[11px] font-semibold text-white'
                : 'rounded-lg bg-emerald-500 px-2 py-1 text-[11px] font-semibold text-white';
            const image = saved.current || pages[page];
            if (image) restore(image);
        } catch (error) {
            sessionStorage.removeItem(roomStorageKey);
        }
    }

    window.addEventListener('beforeunload', saveRoomState);
    document.getElementById('endSessionLink').addEventListener('click', () => {
        roomEnded = true;
        sessionStorage.removeItem(roomStorageKey);
    });

    restoreRoomState();
    renderPages();

    let t0 = Date.now();
    const chronoDisplay = document.getElementById('chronoDisplay');
    const sessionTimer = document.getElementById('sessionTimer');

    function formatElapsed(totalSeconds) {
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        if (hours > 0) {
            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    }

    // Le chronomètre démarre dès l’entrée en salle : aucun contrôle manuel.
    setInterval(() => {
        const elapsed = Math.floor((Date.now() - t0) / 1000);
        const label = formatElapsed(elapsed);
        sessionTimer.textContent = label;
        if (chronoDisplay) chronoDisplay.textContent = label;
    }, 250);

    const mediaPreview = document.getElementById('mediaPreview');
    const mediaPreviewWrap = document.getElementById('mediaPreviewWrap');
    const mediaPreviewLabel = document.getElementById('mediaPreviewLabel');
    let microphoneStream = null;
    let cameraStream = null;
    let screenStream = null;
    let micAudioContext = null;
    let micAnalyser = null;
    let micEqFrame = null;
    const micEqualizer = document.getElementById('micEqualizer');
    const micEqBars = micEqualizer ? [...micEqualizer.querySelectorAll('.mic-eq-bar')] : [];

    function stopMicEqualizer() {
        if (micEqFrame) {
            cancelAnimationFrame(micEqFrame);
            micEqFrame = null;
        }
        if (micAudioContext) {
            micAudioContext.close().catch(() => {});
            micAudioContext = null;
        }
        micAnalyser = null;
        micEqualizer?.classList.add('hidden');
        micEqBars.forEach((bar, index) => {
            bar.style.setProperty('--eq-h', `${12 + (index * 8)}%`);
        });
    }

    function startMicEqualizer(stream) {
        stopMicEqualizer();
        if (!micEqualizer || (!window.AudioContext && !window.webkitAudioContext)) return;

        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        micAudioContext = new AudioCtx();
        const source = micAudioContext.createMediaStreamSource(stream);
        micAnalyser = micAudioContext.createAnalyser();
        micAnalyser.fftSize = 64;
        micAnalyser.smoothingTimeConstant = 0.72;
        source.connect(micAnalyser);
        micEqualizer.classList.remove('hidden');

        const data = new Uint8Array(micAnalyser.frequencyBinCount);
        const paint = () => {
            if (!micAnalyser) return;
            micAnalyser.getByteFrequencyData(data);
            const step = Math.max(1, Math.floor(data.length / micEqBars.length));
            micEqBars.forEach((bar, index) => {
                const sample = data[Math.min(data.length - 1, index * step)] || 0;
                // Accentue légèrement les médiums pour un rendu plus lisible.
                const boost = index === 2 || index === 3 ? 1.15 : 1;
                const height = Math.max(12, Math.min(100, (sample / 255) * 100 * boost));
                bar.style.setProperty('--eq-h', `${height}%`);
            });
            micEqFrame = requestAnimationFrame(paint);
        };
        paint();
    }

    function updateMediaButton(button, active, activeLabel, inactiveLabel) {
        button.setAttribute('aria-pressed', String(active));
        button.classList.toggle('bg-emerald-600', active);
        button.classList.toggle('bg-white/10', !active);
        button.querySelector('.media-dot').className =
            `media-dot h-2 w-2 rounded-full ${active ? 'bg-emerald-300 animate-pulse' : 'bg-slate-500'}`;
        button.querySelector('.media-label').textContent = active ? activeLabel : inactiveLabel;
    }

    function stopStream(stream) {
        stream?.getTracks().forEach(track => track.stop());
    }

    function refreshMediaPreview() {
        const stream = screenStream || cameraStream;
        if (!stream) {
            mediaPreview.srcObject = null;
            mediaPreviewWrap.classList.add('hidden');
            return;
        }
        mediaPreview.srcObject = stream;
        mediaPreviewLabel.textContent = screenStream ? 'Écran partagé' : 'Caméra du professeur';
        mediaPreviewWrap.classList.remove('hidden');
        mediaPreview.play().catch(() => {});
    }

    document.getElementById('microphoneButton').onclick = async function () {
        if (microphoneStream) {
            stopMicEqualizer();
            stopStream(microphoneStream);
            microphoneStream = null;
            updateMediaButton(this, false, '', 'Activer le micro');
            toast('Micro désactivé.');
            return;
        }
        try {
            microphoneStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            updateMediaButton(this, true, 'Micro activé', 'Activer le micro');
            startMicEqualizer(microphoneStream);
            toast('Micro activé.');
        } catch (error) {
            toast('Autorisation du microphone refusée ou indisponible.');
        }
    };

    document.getElementById('cameraButton').onclick = async function () {
        if (cameraStream) {
            stopStream(cameraStream);
            cameraStream = null;
            updateMediaButton(this, false, '', 'Activer la caméra');
            refreshMediaPreview();
            toast('Caméra désactivée.');
            return;
        }
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 1280 }, height: { ideal: 720 } },
            });
            updateMediaButton(this, true, 'Caméra activée', 'Activer la caméra');
            refreshMediaPreview();
            toast('Caméra activée.');
        } catch (error) {
            toast('Autorisation de la caméra refusée ou indisponible.');
        }
    };

    document.getElementById('screenShareButton').onclick = async function () {
        if (screenStream) {
            stopStream(screenStream);
            screenStream = null;
            updateMediaButton(this, false, '', 'Partager l’écran');
            refreshMediaPreview();
            toast('Partage d’écran arrêté.');
            return;
        }
        if (!navigator.mediaDevices?.getDisplayMedia) {
            toast('Le partage d’écran n’est pas pris en charge par ce navigateur.');
            return;
        }
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: true,
                audio: true,
            });
            const button = this;
            updateMediaButton(button, true, 'Écran partagé', 'Partager l’écran');
            screenStream.getVideoTracks()[0]?.addEventListener('ended', () => {
                screenStream = null;
                updateMediaButton(button, false, '', 'Partager l’écran');
                refreshMediaPreview();
                toast('Partage d’écran arrêté.');
            });
            refreshMediaPreview();
            toast('Partage d’écran démarré.');
        } catch (error) {
            toast('Partage d’écran annulé.');
        }
    };

    document.getElementById('closeMediaPreview').onclick = () => {
        mediaPreviewWrap.classList.add('hidden');
    };

    document.getElementById('documentsButton').onclick = () => {
        const panel = document.getElementById('docsPanel');
        const currentlyHidden = window.getComputedStyle(panel).display === 'none';
        if (currentlyHidden) {
            panel.classList.remove('hidden', '!hidden');
            panel.classList.add('flex');
        } else {
            panel.classList.add('!hidden');
            panel.classList.remove('flex');
        }
    };

    const automaticRecordingButton = document.getElementById('automaticRecordingButton');
    automaticRecordingButton.dataset.startedAt = new Date().toISOString();
    automaticRecordingButton.dataset.state = 'recording';

    window.addEventListener('beforeunload', () => {
        stopMicEqualizer();
        stopStream(microphoneStream);
        stopStream(cameraStream);
        stopStream(screenStream);
    });
})();
</script>
</body>
</html>
