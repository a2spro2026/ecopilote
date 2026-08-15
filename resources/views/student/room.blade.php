<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salle de classe · ECOPILOTE</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:500,600,700,800|instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white antialiased">
<header class="flex min-h-16 flex-wrap items-center gap-3 border-b border-white/10 bg-slate-900 px-4 py-3">
    <a href="{{ route('student.dashboard') }}" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold text-slate-200">← Mon espace</a>
    <div class="min-w-0 flex-1"><h1 class="truncate text-sm font-bold sm:text-base" style="font-family:Poppins,sans-serif">Mathématiques · Équations du premier degré</h1><p class="text-[11px] text-slate-400">Mme Nadia El Amrani · Salle en direct</p></div>
    <div class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-3 py-2 text-xs font-bold text-slate-300" title="L’enregistrement est automatique"><span class="h-2.5 w-2.5 animate-pulse rounded-full bg-emerald-400 shadow-[0_0_10px_#34d399]"></span><span class="hidden sm:inline">Enregistrement automatique</span></div>
    <div class="inline-flex items-center gap-2 rounded-xl bg-emerald-600/80 px-3 py-2 text-xs font-bold"><span class="h-2 w-2 animate-pulse rounded-full bg-emerald-200"></span><span id="roomChrono" class="font-mono tabular-nums">00:00</span></div>
</header>
<main class="grid min-h-[calc(100vh-64px)] lg:grid-cols-[1fr_340px]">
    <section class="relative flex min-h-[60vh] flex-col bg-slate-200 p-3 sm:p-5">
        <div class="mb-3 flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800"><span class="mr-1 inline-block h-2 w-2 rounded-full bg-emerald-500"></span> Professeur en direct</span>
            <span id="writingStatus" class="rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm">Tableau en lecture seule</span>
            <button id="requestWritingButton" type="button" class="rounded-xl bg-amber-500 px-3 py-1.5 text-xs font-extrabold text-slate-950">Demander à écrire</button>
        </div>
        <div id="studentToolbar" class="pointer-events-none mb-3 flex flex-wrap items-center gap-2 rounded-2xl border border-white/10 bg-slate-900 p-2 opacity-40 grayscale" aria-disabled="true">
            <span id="toolbarLockBadge" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500/20 px-2.5 py-1.5 text-[11px] font-bold text-amber-200">🔒 Outils verrouillés</span>
            <button type="button" data-student-tool="pen" disabled class="student-tool rounded-lg bg-indigo-600 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Stylo</button>
            <button type="button" data-student-tool="text" disabled class="student-tool rounded-lg bg-white/10 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Clavier</button>
            <button type="button" data-student-tool="eraser" disabled class="student-tool rounded-lg bg-white/10 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Gomme</button>
            <select id="studentShape" disabled class="student-control rounded-lg border-0 bg-slate-800 px-2 py-1.5 text-[11px] font-bold text-white disabled:cursor-not-allowed">
                <option value="">Formes</option><option value="line">Ligne</option><option value="rect">Rectangle</option><option value="circle">Cercle</option><option value="triangle">Triangle</option>
            </select>
            <button id="studentRuling" type="button" disabled class="student-control rounded-lg bg-white/10 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Lignes de cahier</button>
            <div class="flex items-center gap-1 border-l border-white/10 pl-2">
                @foreach (['#0f172a', '#2563eb', '#dc2626', '#16a34a', '#9333ea'] as $color)
                    <button type="button" data-student-color="{{ $color }}" disabled class="student-color h-6 w-6 rounded-full border-2 border-white/30 disabled:cursor-not-allowed" style="background:{{ $color }}" aria-label="Couleur {{ $color }}"></button>
                @endforeach
            </div>
            <div class="flex items-center gap-1 border-l border-white/10 pl-2">
                @foreach (['+', '−', '×', '÷', '=', '√', 'π'] as $symbol)
                    <button type="button" data-student-symbol="{{ $symbol }}" disabled class="student-symbol rounded-lg bg-violet-600/80 px-2 py-1 text-xs font-bold disabled:cursor-not-allowed">{{ $symbol }}</button>
                @endforeach
            </div>
            <button id="studentUndo" type="button" disabled class="student-control ml-auto rounded-lg bg-white/10 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Annuler</button>
            <button id="studentRedo" type="button" disabled class="student-control rounded-lg bg-white/10 px-2.5 py-1.5 text-[11px] font-bold disabled:cursor-not-allowed">Rétablir</button>
        </div>
        <div class="relative flex-1 overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div id="studentRulingLayer" class="pointer-events-none absolute inset-0 opacity-50" style="background-image:linear-gradient(#dbeafe 1px,transparent 1px);background-size:100% 32px"></div>
            <div class="relative z-10 p-7 text-slate-800 sm:p-12">
                <p class="text-sm font-bold text-indigo-600">ÉQUATIONS DU PREMIER DEGRÉ</p>
                <h2 class="mt-5 text-2xl font-bold sm:text-4xl">3x + 5 = 20</h2>
                <div class="mt-8 space-y-4 text-lg sm:text-2xl"><p>3x = 20 − 5</p><p>3x = 15</p><p class="font-bold text-emerald-600">x = 5 ✓</p></div>
                <p class="mt-10 max-w-xl rounded-xl bg-amber-50 p-4 text-sm text-amber-900">On effectue toujours la même opération dans les deux membres de l’équation.</p>
            </div>
            <canvas id="studentBoard" tabindex="-1" class="pointer-events-none absolute inset-0 z-20 h-full w-full touch-none" aria-label="Zone d’écriture de l’élève"></canvas>
            <div id="boardLockedOverlay" class="absolute inset-0 z-30 flex items-center justify-center bg-white/15">
                <div class="rounded-2xl bg-slate-900/85 px-5 py-3 text-center text-xs font-bold text-white shadow-xl">🔒 Le professeur doit autoriser l’écriture</div>
            </div>
            <div id="permissionBanner" class="absolute bottom-4 left-1/2 z-40 hidden -translate-x-1/2 rounded-xl bg-amber-500 px-4 py-2 text-center text-xs font-bold text-white shadow-lg">Demande envoyée au professeur</div>
        </div>
        <p class="mt-3 text-center text-[11px] text-slate-500">L’élève ne peut pas modifier le tableau principal. Toute contribution doit être autorisée par le professeur.</p>
    </section>
    <aside class="border-l border-white/10 bg-slate-900 p-4">
        <div class="mb-4 grid grid-cols-2 gap-2">
            <button id="micButton" type="button" aria-pressed="false" class="rounded-xl bg-white/10 px-3 py-3 text-xs font-bold"><span class="media-dot mr-2 inline-block h-2 w-2 rounded-full bg-slate-500"></span><span class="media-label">Micro</span></button>
            <button id="cameraButton" type="button" aria-pressed="false" class="rounded-xl bg-white/10 px-3 py-3 text-xs font-bold"><span class="media-dot mr-2 inline-block h-2 w-2 rounded-full bg-slate-500"></span><span class="media-label">Caméra</span></button>
        </div>
        <video id="cameraPreview" autoplay muted playsinline class="mb-4 hidden aspect-video w-full rounded-xl bg-black object-cover"></video>
        <button id="raiseHandButton" type="button" class="mb-4 w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-extrabold text-slate-950">✋ Lever la main</button>
        <section class="mb-4 rounded-2xl border border-white/10 bg-white/5 p-4">
            <h2 class="text-sm font-bold">Poser une question</h2>
            <textarea id="questionText" rows="3" maxlength="300" placeholder="Écrivez votre question…" class="mt-3 w-full resize-none rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-sm text-white outline-none focus:border-indigo-400"></textarea>
            <button id="sendQuestionButton" type="button" class="mt-2 w-full rounded-xl bg-indigo-600 px-3 py-2.5 text-xs font-bold">Envoyer au professeur</button>
        </section>
        <section class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <h2 class="text-sm font-bold">Proposer une contribution</h2>
            <p class="mt-1 text-[11px] text-slate-400">Texte, calcul ou fichier : le professeur doit accepter avant l’affichage.</p>
            <textarea id="contributionText" rows="3" maxlength="500" placeholder="Mon calcul ou ma réponse…" class="mt-3 w-full resize-none rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-sm text-white outline-none focus:border-cyan-400"></textarea>
            <input id="contributionFile" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-3 block w-full text-[11px] text-slate-400 file:mr-2 file:rounded-lg file:border-0 file:bg-slate-700 file:px-3 file:py-2 file:font-bold file:text-white">
            <button id="requestContributionButton" type="button" class="mt-3 w-full rounded-xl bg-cyan-500 px-3 py-2.5 text-xs font-bold text-slate-950">Demander l’autorisation</button>
        </section>
        <div id="roomMessage" role="status" class="mt-4 hidden rounded-xl bg-emerald-500/15 px-3 py-2 text-xs font-semibold text-emerald-300"></div>
    </aside>
</main>
<script>
(() => {
    let micStream = null, cameraStream = null;
    const message = document.getElementById('roomMessage');
    const showMessage = text => { message.textContent = text; message.classList.remove('hidden'); setTimeout(() => message.classList.add('hidden'), 4000); };
    const setControl = (button, active) => {
        button.setAttribute('aria-pressed', String(active));
        button.querySelector('.media-dot').className = `media-dot mr-2 inline-block h-2 w-2 rounded-full ${active ? 'bg-emerald-400' : 'bg-slate-500'}`;
    };
    document.getElementById('raiseHandButton').addEventListener('click', e => {
        const raised = e.currentTarget.dataset.raised !== 'true';
        e.currentTarget.dataset.raised = String(raised);
        e.currentTarget.textContent = raised ? '✋ Main levée · annuler' : '✋ Lever la main';
        showMessage(raised ? 'Le professeur voit votre main levée.' : 'Demande annulée.');
    });
    document.getElementById('sendQuestionButton').addEventListener('click', () => {
        const input = document.getElementById('questionText');
        if (!input.value.trim()) return showMessage('Écrivez votre question avant de l’envoyer.');
        input.value = ''; showMessage('Question envoyée au professeur.');
    });
    document.getElementById('requestContributionButton').addEventListener('click', () => {
        const text = document.getElementById('contributionText').value.trim();
        const file = document.getElementById('contributionFile').files[0];
        if (!text && !file) return showMessage('Ajoutez un texte ou un fichier.');
        document.getElementById('permissionBanner').classList.remove('hidden');
        showMessage('Contribution en attente de l’autorisation du professeur.');
    });

    const board = document.getElementById('studentBoard');
    const boardContext = board.getContext('2d');
    const toolbar = document.getElementById('studentToolbar');
    const lockedOverlay = document.getElementById('boardLockedOverlay');
    const writingStatus = document.getElementById('writingStatus');
    const requestWritingButton = document.getElementById('requestWritingButton');
    const permissionKey = 'ecopilote.student.writing.allowed';
    const requestKey = 'ecopilote.student.writing.request';
    let writingAllowed = localStorage.getItem(permissionKey) === '1';
    let boardTool = 'pen';
    let boardColor = '#0f172a';
    let drawing = false;
    let startPoint = null;
    let lastPoint = null;
    let shapeBase = null;
    let textPoint = null;
    const undoStack = [];
    const redoStack = [];

    const fitBoard = () => {
        const snapshot = board.width && board.height ? board.toDataURL() : null;
        const bounds = board.parentElement.getBoundingClientRect();
        board.width = Math.max(1, Math.round(bounds.width));
        board.height = Math.max(1, Math.round(bounds.height));
        if (snapshot) {
            const image = new Image();
            image.onload = () => boardContext.drawImage(image, 0, 0, board.width, board.height);
            image.src = snapshot;
        }
    };
    fitBoard();
    window.addEventListener('resize', fitBoard);

    const restoreBoard = (snapshot, afterRestore = null) => {
        boardContext.clearRect(0, 0, board.width, board.height);
        if (!snapshot) {
            afterRestore?.();
            return;
        }
        const image = new Image();
        image.onload = () => {
            boardContext.drawImage(image, 0, 0, board.width, board.height);
            afterRestore?.();
        };
        image.src = snapshot;
    };
    const saveBoard = () => {
        undoStack.push(board.toDataURL());
        if (undoStack.length > 30) undoStack.shift();
        redoStack.length = 0;
    };
    const pointFromEvent = event => {
        const bounds = board.getBoundingClientRect();
        return { x: event.clientX - bounds.left, y: event.clientY - bounds.top };
    };
    const controls = toolbar.querySelectorAll('button[data-student-tool], button[data-student-color], button[data-student-symbol], .student-control');

    const setWritingPermission = allowed => {
        writingAllowed = allowed;
        controls.forEach(control => { control.disabled = ! allowed; });
        toolbar.classList.toggle('pointer-events-none', ! allowed);
        toolbar.classList.toggle('opacity-40', ! allowed);
        toolbar.classList.toggle('grayscale', ! allowed);
        toolbar.setAttribute('aria-disabled', String(! allowed));
        document.getElementById('toolbarLockBadge').classList.toggle('hidden', allowed);
        board.classList.toggle('pointer-events-none', ! allowed);
        board.classList.toggle('cursor-crosshair', allowed);
        board.tabIndex = allowed ? 0 : -1;
        if (! allowed) {
            board.blur();
            textPoint = null;
            drawing = false;
        }
        lockedOverlay.classList.toggle('hidden', allowed);
        writingStatus.textContent = allowed ? 'Écriture autorisée' : 'Tableau en lecture seule';
        writingStatus.className = allowed
            ? 'rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800 shadow-sm'
            : 'rounded-full bg-white px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm';
        requestWritingButton.classList.toggle('hidden', allowed);
        if (allowed) {
            document.getElementById('permissionBanner').classList.add('hidden');
            showMessage('Le professeur vous autorise à écrire.');
        } else {
            requestWritingButton.disabled = false;
            requestWritingButton.textContent = 'Demander à écrire';
        }
    };
    setWritingPermission(writingAllowed);

    requestWritingButton.addEventListener('click', () => {
        localStorage.setItem(requestKey, JSON.stringify({ at: Date.now(), student: @json($currentStudent->nom_complet ?? 'Élève') }));
        document.getElementById('permissionBanner').classList.remove('hidden');
        requestWritingButton.textContent = 'Demande envoyée';
        requestWritingButton.disabled = true;
        showMessage('Demande d’écriture envoyée au professeur.');
    });
    window.addEventListener('storage', event => {
        if (event.key === permissionKey) setWritingPermission(event.newValue === '1');
    });

    const selectTool = tool => {
        if (!writingAllowed) return showMessage('Demandez d’abord l’autorisation au professeur.');
        boardTool = tool;
        document.querySelectorAll('.student-tool').forEach(button => {
            button.classList.toggle('bg-indigo-600', button.dataset.studentTool === tool);
            button.classList.toggle('bg-white/10', button.dataset.studentTool !== tool);
        });
        if (tool === 'text') board.focus();
    };
    document.querySelectorAll('.student-tool').forEach(button => button.addEventListener('click', () => selectTool(button.dataset.studentTool)));
    document.querySelectorAll('.student-color').forEach(button => button.addEventListener('click', () => {
        if (!writingAllowed) return showMessage('Demandez d’abord l’autorisation au professeur.');
        boardColor = button.dataset.studentColor;
        document.querySelectorAll('.student-color').forEach(item => item.classList.toggle('ring-2', item === button));
    }));
    document.getElementById('studentShape').addEventListener('change', event => {
        if (event.target.value) selectTool(event.target.value);
    });
    document.getElementById('studentRuling').addEventListener('click', event => {
        if (!writingAllowed) return showMessage('Demandez d’abord l’autorisation au professeur.');
        const layer = document.getElementById('studentRulingLayer');
        layer.classList.toggle('hidden');
        event.currentTarget.textContent = layer.classList.contains('hidden') ? 'Afficher les lignes' : 'Masquer les lignes';
    });

    const drawShape = (tool, from, to) => {
        const width = to.x - from.x;
        const height = to.y - from.y;
        boardContext.beginPath();
        if (tool === 'line') {
            boardContext.moveTo(from.x, from.y); boardContext.lineTo(to.x, to.y);
        } else if (tool === 'rect') {
            boardContext.rect(from.x, from.y, width, height);
        } else if (tool === 'circle') {
            boardContext.ellipse(from.x + width / 2, from.y + height / 2, Math.abs(width / 2), Math.abs(height / 2), 0, 0, Math.PI * 2);
        } else if (tool === 'triangle') {
            boardContext.moveTo(from.x + width / 2, from.y);
            boardContext.lineTo(to.x, to.y);
            boardContext.lineTo(from.x, to.y);
            boardContext.closePath();
        }
        boardContext.stroke();
    };
    board.addEventListener('pointerdown', event => {
        if (!writingAllowed) return;
        event.preventDefault();
        board.setPointerCapture?.(event.pointerId);
        const point = pointFromEvent(event);
        if (boardTool === 'text') {
            textPoint = point;
            board.focus();
            return;
        }
        saveBoard();
        drawing = true;
        startPoint = lastPoint = point;
        boardContext.lineCap = 'round';
        boardContext.lineJoin = 'round';
        boardContext.lineWidth = boardTool === 'eraser' ? 18 : 3;
        boardContext.strokeStyle = boardColor;
        boardContext.globalCompositeOperation = boardTool === 'eraser' ? 'destination-out' : 'source-over';
        if (['line', 'rect', 'circle', 'triangle'].includes(boardTool)) shapeBase = board.toDataURL();
    });
    board.addEventListener('pointermove', event => {
        if (!drawing || !writingAllowed) return;
        const point = pointFromEvent(event);
        if (['line', 'rect', 'circle', 'triangle'].includes(boardTool)) {
            restoreBoard(shapeBase, () => {
                boardContext.globalCompositeOperation = 'source-over';
                boardContext.strokeStyle = boardColor;
                boardContext.lineWidth = 3;
                drawShape(boardTool, startPoint, point);
            });
        } else {
            boardContext.beginPath();
            boardContext.moveTo(lastPoint.x, lastPoint.y);
            boardContext.lineTo(point.x, point.y);
            boardContext.stroke();
        }
        lastPoint = point;
    });
    const stopDrawing = () => { drawing = false; shapeBase = null; boardContext.globalCompositeOperation = 'source-over'; };
    board.addEventListener('pointerup', stopDrawing);
    board.addEventListener('pointercancel', stopDrawing);

    board.addEventListener('keydown', event => {
        if (!writingAllowed || boardTool !== 'text') return;
        if (!textPoint) textPoint = { x: 30, y: 50 };
        if (event.key === 'Enter') { textPoint = { x: 30, y: textPoint.y + 28 }; return; }
        if (event.key.length !== 1) return;
        event.preventDefault();
        saveBoard();
        boardContext.globalCompositeOperation = 'source-over';
        boardContext.fillStyle = boardColor;
        boardContext.font = '600 22px "Instrument Sans", sans-serif';
        boardContext.fillText(event.key, textPoint.x, textPoint.y);
        textPoint.x += boardContext.measureText(event.key).width;
    });
    document.querySelectorAll('.student-symbol').forEach(button => button.addEventListener('click', () => {
        if (!writingAllowed) return showMessage('Demandez d’abord l’autorisation au professeur.');
        selectTool('text');
        if (!textPoint) textPoint = { x: 30, y: 50 };
        saveBoard();
        boardContext.fillStyle = boardColor;
        boardContext.font = '600 24px "Instrument Sans", sans-serif';
        boardContext.fillText(button.dataset.studentSymbol, textPoint.x, textPoint.y);
        textPoint.x += boardContext.measureText(button.dataset.studentSymbol).width + 3;
        board.focus();
    }));
    document.getElementById('studentUndo').addEventListener('click', () => {
        if (!writingAllowed || !undoStack.length) return;
        redoStack.push(board.toDataURL());
        restoreBoard(undoStack.pop());
    });
    document.getElementById('studentRedo').addEventListener('click', () => {
        if (!writingAllowed || !redoStack.length) return;
        undoStack.push(board.toDataURL());
        restoreBoard(redoStack.pop());
    });

    document.getElementById('micButton').addEventListener('click', async e => {
        try {
            if (micStream) { micStream.getTracks().forEach(t => t.stop()); micStream = null; setControl(e.currentTarget, false); return; }
            micStream = await navigator.mediaDevices.getUserMedia({audio:true}); setControl(e.currentTarget, true);
        } catch { showMessage('Autorisation du microphone refusée ou indisponible.'); }
    });
    document.getElementById('cameraButton').addEventListener('click', async e => {
        const preview = document.getElementById('cameraPreview');
        try {
            if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; preview.srcObject = null; preview.classList.add('hidden'); setControl(e.currentTarget, false); return; }
            cameraStream = await navigator.mediaDevices.getUserMedia({video:true}); preview.srcObject = cameraStream; preview.classList.remove('hidden'); setControl(e.currentTarget, true);
        } catch { showMessage('Autorisation de la caméra refusée ou indisponible.'); }
    });
    const startedAt = Date.now();
    setInterval(() => {
        const seconds = Math.floor((Date.now() - startedAt) / 1000);
        document.getElementById('roomChrono').textContent = `${String(Math.floor(seconds/60)).padStart(2,'0')}:${String(seconds%60).padStart(2,'0')}`;
    }, 1000);
})();
</script>
</body>
</html>
