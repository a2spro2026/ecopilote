<div class="hero-video-stage mx-auto w-full max-w-3xl">
    <span class="hero-video-orb hero-video-orb-a"></span>
    <span class="hero-video-orb hero-video-orb-b"></span>
    <span class="hero-video-spark" style="top:8%; left:6%;"></span>
    <span class="hero-video-spark" style="top:18%; right:10%; animation-delay:.8s;"></span>
    <span class="hero-video-spark" style="bottom:14%; left:12%; animation-delay:1.4s;"></span>
    <span class="hero-video-spark" style="bottom:22%; right:8%; animation-delay:2s;"></span>
    <div class="hero-video-frame">
        <span class="hero-video-corner hero-video-corner-tl"></span>
        <span class="hero-video-corner hero-video-corner-tr"></span>
        <span class="hero-video-corner hero-video-corner-bl"></span>
        <span class="hero-video-corner hero-video-corner-br"></span>
        <div class="hero-video-screen">
            @if (! empty($heroVideoUrl) && ! empty($heroHasPicture))
                <video src="{{ $heroVideoUrl }}" playsinline controls preload="metadata"></video>
            @elseif (! empty($heroVideoUrl))
                <div class="hero-video-placeholder">
                    <p class="text-sm font-bold text-white">Fichier sans image</p>
                    <p class="px-6 text-center text-xs text-blue-200">Le fichier ajouté est un audio. Choisissez une vraie vidéo MP4 avec image.</p>
                </div>
            @else
                <div class="hero-video-placeholder">
                    <svg class="h-12 w-12 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <p class="text-sm font-bold text-white">Cadre de diffusion</p>
                    <p class="text-xs text-blue-200">Lancez la vidéo ici quand vous souhaitez la diffuser.</p>
                </div>
            @endif
        </div>
        <div class="hero-video-live">
            <span class="hero-video-live-dot"></span>
            {{ ! empty($heroVideoUrl) && ! empty($heroHasPicture) ? 'Prêt à diffuser' : 'Prêt' }}
        </div>
    </div>
</div>
