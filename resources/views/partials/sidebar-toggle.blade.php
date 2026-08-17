@php
    $tone = $tone ?? 'admin';
    $onclick = $onclick ?? 'toggleSidebar()';
    $controls = $controls ?? 'adminSidebar';
@endphp
<button type="button"
        id="sidebarToggleBtn"
        onclick="{{ $onclick }}"
        class="sidebar-rail-btn sidebar-rail-btn--{{ $tone }}"
        aria-label="Afficher ou masquer le menu"
        aria-controls="{{ $controls }}"
        aria-expanded="true"
        title="Afficher ou masquer le menu">
    <span class="sidebar-rail-glyph" aria-hidden="true">
        <svg id="sidebarIconOpen" class="sidebar-rail-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3.5" y="4" width="17" height="16" rx="3"/>
            <path stroke-linecap="round" d="M9.5 4v16"/>
            <path stroke-linecap="round" d="M12.5 9h5M12.5 12.5h5M12.5 16h3.5"/>
        </svg>
        <svg id="sidebarIconClosed" class="sidebar-rail-icon hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3.5" y="4" width="17" height="16" rx="3"/>
            <path stroke-linecap="round" d="M8 4v16"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 9.5 16.5 12 13 14.5"/>
        </svg>
    </span>
</button>
