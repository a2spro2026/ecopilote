@php
    $tone = $tone ?? 'student';
@endphp
<form method="POST" action="{{ $action }}" class="shrink-0">
    @csrf
    <button type="submit" class="school-logout school-logout--{{ $tone }}" title="Quitter l’espace de travail">
        <span class="school-logout-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25 12 4.5l8.25 3.75L12 12 3.75 8.25Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 10.2v4.05c0 .9 2.35 1.95 5.25 1.95s5.25-1.05 5.25-1.95V10.2"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75V15"/>
            </svg>
        </span>
        <span class="school-logout-copy">
            <span class="school-logout-kicker">{{ $tone === 'teacher' ? 'Fin de cours' : 'Fin de séance' }}</span>
            <span class="school-logout-label">Déconnexion</span>
        </span>
        <span class="school-logout-door" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M21 12H9m12 0-3-3m3 3-3 3"/>
            </svg>
        </span>
    </button>
</form>
