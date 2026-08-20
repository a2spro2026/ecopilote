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
    $iconTone = match ($tone) {
        'violet' => $active ? 'bg-violet-500/35 text-violet-100' : 'bg-violet-500/20 text-violet-300',
        'emerald' => $active ? 'bg-emerald-500/35 text-emerald-100' : 'bg-emerald-500/20 text-emerald-300',
        'amber' => $active ? 'bg-amber-500/35 text-amber-100' : 'bg-amber-500/20 text-amber-300',
        'blue' => $active ? 'bg-blue-500/35 text-blue-100' : 'bg-blue-500/20 text-blue-300',
        'indigo' => $active ? 'bg-indigo-500/35 text-indigo-100' : 'bg-indigo-500/20 text-indigo-300',
        default => $active ? 'bg-slate-400/30 text-slate-100' : 'bg-slate-400/20 text-slate-300',
    };
@endphp

<a href="{{ $href }}" data-window-title="{{ $item['label'] }}"
   class="group flex items-center gap-2 rounded-xl px-2 py-1.5 text-[12px] font-semibold transition
          {{ $active ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
    <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $iconTone }}">
        <svg width="14" height="14" class="block h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
        </svg>
    </span>
    <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
    @if ($badge)
        <span class="min-w-[1.25rem] rounded-full bg-rose-500 px-1.5 py-0.5 text-center text-[10px] font-bold text-white shadow-sm shadow-rose-500/40">{{ $badge }}</span>
    @endif
</a>
