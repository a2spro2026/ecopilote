@php
    $customRoute = $item['route'] ?? null;
    $href = $customRoute
        ? route($customRoute)
        : route('admin.page.'.$item['key']);
    $active = $customRoute
        ? request()->routeIs($customRoute)
        : request()->routeIs('admin.page.'.$item['key']);

    if (($item['key'] ?? '') === 'classes' && request()->routeIs('admin.classes.show', 'admin.classes.create')) {
        $active = true;
    }
    if (($item['key'] ?? '') === 'groupes' && request()->routeIs('admin.page.groupes', 'admin.groups.*')) {
        $active = true;
    }
    if (($item['key'] ?? '') === 'cours-classes' && (request()->routeIs('admin.page.classes') || request()->routeIs('admin.classes.show', 'admin.classes.create'))) {
        $active = true;
    }
    if (($item['key'] ?? '') === 'fiche-technique-eleve' && request()->routeIs('admin.students.technical')) {
        $active = true;
    }
    if (($item['key'] ?? '') === 'fiche-technique-professeur' && request()->routeIs('admin.teachers.technical')) {
        $active = true;
    }
    if (($item['key'] ?? '') === 'demandes-eleves' && request()->routeIs('admin.page.demandes-eleves')) {
        $active = true;
    }

    $badge = $item['badge'] ?? null;
    if (($item['key'] ?? '') === 'demandes-eleves') {
        $badge = $pendingStudentDemandes > 0 ? $pendingStudentDemandes : null;
    }
    if (($item['key'] ?? '') === 'candidatures-profs') {
        $badge = $pendingTeacherDemandes > 0 ? $pendingTeacherDemandes : null;
    }

    $tone = $tone ?? 'slate';
@endphp

<a href="{{ $href }}" data-window-title="{{ $item['label'] }}"
   class="admin-nav-sublink group {{ $active ? 'is-active' : '' }}">
    <span class="admin-nav-subicon admin-nav-subicon--{{ $tone }}">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
        </svg>
    </span>
    <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
    @if ($badge)
        <span class="min-w-[1.25rem] rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-bold text-white shadow-sm shadow-rose-500/40">{{ $badge }}</span>
    @endif
</a>
