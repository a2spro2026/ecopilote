@php
    // Paramètres attendus : $storageKey, $initialTitle, $initialUrl, $accent
    $accentColor = [
        'blue' => '#2563eb',
        'emerald' => '#059669',
        'indigo' => '#4f46e5',
    ][$accent ?? 'blue'] ?? '#2563eb';
@endphp
<div id="workspaceStack" class="flex h-full flex-col gap-3 overflow-y-auto p-3 sm:p-4"></div>

<template id="workspaceWindowTemplate">
    <section class="workspace-window flex min-h-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <header class="flex h-11 shrink-0 items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 dark:border-slate-800 dark:bg-slate-950/40">
            <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background:{{ $accentColor }}"></span>
            <p class="window-title min-w-0 flex-1 truncate text-xs font-bold text-slate-700 dark:text-slate-200"></p>
            <button type="button" data-action="reduce"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-200 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white"
                    title="Réduire" aria-label="Réduire la fenêtre">
                <svg class="icon-reduce h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" d="M5 12h14"/>
                </svg>
                <svg class="icon-expand hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                </svg>
            </button>
            <button type="button" data-action="close"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-500/15"
                    title="Fermer" aria-label="Fermer la fenêtre">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </header>
        <div class="window-body min-h-0 flex-1">
            <iframe class="h-full w-full border-0 bg-white dark:bg-slate-900" title="Page"></iframe>
        </div>
    </section>
</template>

<script>
(() => {
    const STORAGE_KEY = @json($storageKey);
    const stack = document.getElementById('workspaceStack');
    const template = document.getElementById('workspaceWindowTemplate');
    const initial = { title: @json($initialTitle), url: @json($initialUrl) };
    let windows = [];

    const cleanUrl = (raw) => {
        const url = new URL(raw, window.location.origin);
        url.searchParams.delete('embed');
        return url.pathname + url.search + url.hash;
    };
    const framedUrl = (raw) => {
        const url = new URL(raw, window.location.origin);
        url.searchParams.set('embed', '1');
        return url.toString();
    };
    const save = () => sessionStorage.setItem(STORAGE_KEY, JSON.stringify(
        windows.map(({ id, title, url, reduced }) => ({ id, title, url, reduced }))
    ));

    const applyLayout = () => {
        const expanded = windows.filter((item) => !item.reduced);
        windows.forEach((item) => {
            if (item.reduced) {
                item.element.style.flex = '0 0 auto';
                item.element.querySelector('.window-body').classList.add('hidden');
                item.element.querySelector('.icon-reduce').classList.add('hidden');
                item.element.querySelector('.icon-expand').classList.remove('hidden');
            } else {
                item.element.style.flex = '1 1 0%';
                item.element.style.minHeight = expanded.length > 1 ? '280px' : '0';
                item.element.querySelector('.window-body').classList.remove('hidden');
                item.element.querySelector('.icon-reduce').classList.remove('hidden');
                item.element.querySelector('.icon-expand').classList.add('hidden');
            }
        });
        save();
    };

    const closeWindow = (id) => {
        const index = windows.findIndex((item) => item.id === id);
        if (index < 0) return;
        windows[index].element.remove();
        windows.splice(index, 1);
        if (!windows.length) openWindow(initial.title, initial.url);
        else applyLayout();
    };

    const createWindow = ({ id, title, url, reduced = false }) => {
        const element = template.content.firstElementChild.cloneNode(true);
        element.dataset.windowId = id;
        element.querySelector('.window-title').textContent = title;
        const frame = element.querySelector('iframe');
        frame.title = title;
        frame.src = framedUrl(url);

        const item = { id, title, url, reduced, element };
        element.querySelector('[data-action="reduce"]').addEventListener('click', () => {
            item.reduced = !item.reduced;
            applyLayout();
        });
        element.querySelector('[data-action="close"]').addEventListener('click', () => closeWindow(id));

        stack.appendChild(element);
        windows.push(item);
        return item;
    };

    function openWindow(title, rawUrl) {
        const url = cleanUrl(rawUrl);
        const existing = windows.find((item) => item.url === url);
        if (existing) {
            existing.reduced = false;
            existing.element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            applyLayout();
            return existing;
        }
        const item = createWindow({ id: 'win-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7), title: title || 'Page', url });
        applyLayout();
        item.element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return item;
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-workspace-link]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        openWindow(link.dataset.windowTitle || link.textContent.trim(), link.href);
        document.querySelectorAll('[id$="Menu"]').forEach((menu) => menu.classList.add('hidden'));
        if (window.innerWidth < 1024) {
            document.dispatchEvent(new CustomEvent('ecopilote:close-sidebar'));
        }
    });

    window.addEventListener('message', (event) => {
        if (event.origin !== window.location.origin) return;
        const item = windows.find((entry) => entry.element.querySelector('iframe').contentWindow === event.source);
        if (!item) return;

        if (event.data?.type === 'ecopilote:window-close') {
            closeWindow(item.id);
            return;
        }

        if (event.data?.type === 'ecopilote:window-navigate') {
            item.url = cleanUrl(event.data.url);
            item.title = event.data.title || item.title;
            item.element.querySelector('.window-title').textContent = item.title;
            save();
        }
    });

    let restored = [];
    try {
        restored = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]');
    } catch (_) {}

    if (restored.length) {
        restored.forEach(createWindow);
        if (!windows.some((item) => item.url === cleanUrl(initial.url))) {
            createWindow({ id: 'win-initial-' + Date.now(), title: initial.title, url: cleanUrl(initial.url) });
        }
        applyLayout();
    } else {
        openWindow(initial.title, initial.url);
    }
})();
</script>
