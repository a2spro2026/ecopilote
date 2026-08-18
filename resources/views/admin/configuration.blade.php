@extends('admin.layout')

@section('title', 'Configuration')
@section('heading', 'Configuration')
@section('subtitle', 'Système')

@section('content')
@if (session('status'))
    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
        <ul class="list-disc space-y-1 pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white dark:bg-emerald-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87" />
                </svg>
            </span>
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white" style="font-family:'Poppins',sans-serif;">Configuration</h2>
                <p class="text-sm text-slate-500">Section « Système »</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800 sm:col-span-2 lg:col-span-3">
            <div class="flex items-center justify-between">
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Élément 1</span>
                <span class="h-2 w-2 rounded-full {{ $heroVideoUrl ? 'bg-emerald-400' : 'bg-slate-300' }}"></span>
            </div>
            <p class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Vidéo des activités</p>
            <p class="mt-1 text-xs text-slate-500">Cette vidéo s’affiche dans la page Activités, prête à être diffusée.</p>

            <form method="POST" action="{{ route('admin.configuration.video.store') }}" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/60 sm:flex-row sm:items-center">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Ajouter une vidéo</p>
                        <p id="heroVideoName" class="truncate text-[11px] text-slate-500">Aucun fichier choisi — tous les fichiers du bureau sont visibles</p>
                    </div>
                    <input id="siteHeroVideoFile" type="file" name="video" required class="sr-only">
                    <button type="button" id="heroVideoBrowse" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                        Parcourir
                    </button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-blue-600 to-emerald-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20">
                        Enregistrer
                    </button>
                </div>
                <p class="mt-2 text-[11px] text-slate-500">Si Windows masque encore les vidéos, en bas à droite de la fenêtre choisissez « Tous les fichiers ».</p>
            </form>

            @if ($heroVideoUrl)
                @if (! empty($heroHasPicture))
                    <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-black dark:border-slate-700">
                        <video class="aspect-video w-full bg-black object-cover" src="{{ $heroVideoUrl }}" controls playsinline></video>
                    </div>
                @else
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        Le fichier actuel n’a pas d’image (audio uniquement). Ajoutez une vraie vidéo MP4 avec image.
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.configuration.video.destroy') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300">
                        Retirer la vidéo
                    </button>
                </form>
            @endif
        </div>

        @for ($i = 2; $i <= 6; $i++)
            <div class="rounded-2xl border border-slate-200 p-4 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">Élément {{ $i }}</span>
                    <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                </div>
                <p class="mt-3 text-sm font-semibold text-slate-800 dark:text-slate-100">Configuration</p>
                <p class="mt-1 text-xs text-slate-500">Contenu à venir.</p>
            </div>
        @endfor
    </div>
</div>
<script>
    const videoInput = document.getElementById('siteHeroVideoFile');
    const videoName = document.getElementById('heroVideoName');
    const browseButton = document.getElementById('heroVideoBrowse');

    const showChosenFile = () => {
        const file = videoInput?.files?.[0];
        if (videoName) {
            videoName.textContent = file
                ? file.name
                : 'Aucun fichier choisi — tous les fichiers du bureau sont visibles';
        }
    };

    browseButton?.addEventListener('click', () => {
        const picker = document.createElement('input');
        picker.type = 'file';
        picker.style.display = 'none';
        document.body.appendChild(picker);
        picker.addEventListener('change', () => {
            if (picker.files?.length && videoInput) {
                const transfer = new DataTransfer();
                transfer.items.add(picker.files[0]);
                videoInput.files = transfer.files;
                showChosenFile();
            }
            picker.remove();
        });
        picker.click();
    });

    videoInput?.addEventListener('change', showChosenFile);
</script>
@endsection
