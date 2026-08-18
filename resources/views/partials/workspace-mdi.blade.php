@php
    $accentColor = [
        'blue' => '#2563eb',
        'emerald' => '#059669',
        'indigo' => '#4f46e5',
    ][$accent ?? 'blue'] ?? '#2563eb';
@endphp
<div id="mdiDesktop" class="mdi-desktop relative min-h-0 flex-1 overflow-hidden">
    <p id="mdiHint" class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm font-semibold text-white/40">
        Cliquez un menu pour ouvrir une page. Les fenêtres restent ouvertes pour travailler en même temps.
    </p>
    <div id="mdiTaskbar" class="absolute inset-x-0 bottom-0 z-[1000] flex h-10 items-center gap-1 overflow-x-auto border-t border-black/20 bg-slate-800/95 px-2"></div>
</div>

<template id="mdiWindowTemplate">
    <section class="mdi-window absolute flex flex-col overflow-hidden rounded-md border border-slate-400 bg-white shadow-2xl dark:border-slate-600 dark:bg-slate-900">
        <header class="mdi-titlebar flex h-8 shrink-0 cursor-move items-center gap-2 bg-slate-700 px-2 text-white select-none">
            <span class="h-2 w-2 shrink-0 rounded-full" style="background:{{ $accentColor }}"></span>
            <p class="mdi-title min-w-0 flex-1 truncate text-[12px] font-bold"></p>
            <button type="button" data-mdi="min" class="mdi-ctrl" title="Réduire" aria-label="Réduire">─</button>
            <button type="button" data-mdi="max" class="mdi-ctrl" title="Agrandir" aria-label="Agrandir">□</button>
            <button type="button" data-mdi="close" class="mdi-ctrl mdi-ctrl-close" title="Fermer" aria-label="Fermer">✕</button>
        </header>
        <div class="mdi-body min-h-0 flex-1 bg-white dark:bg-slate-900">
            <iframe class="h-full w-full border-0" title="Page"></iframe>
        </div>
        <div class="mdi-resize" title="Redimensionner"></div>
    </section>
</template>

<script>
(() => {
    const STORAGE_KEY = @json($storageKey);
    const URL_PREFIX = @json($urlPrefix ?? '/');
    const desktop = document.getElementById('mdiDesktop');
    const taskbar = document.getElementById('mdiTaskbar');
    const hint = document.getElementById('mdiHint');
    const template = document.getElementById('mdiWindowTemplate');
    const initial = { title: @json($initialTitle), url: @json($initialUrl) };
    const windows = [];
    let z = 20;
    let cascade = 0;

    const cleanUrl = (raw) => {
        const url = new URL(raw, window.location.origin);
        url.searchParams.delete('embed');
        return url.pathname + url.search + url.hash;
    };
    const isAllowedUrl = (raw) => {
        try {
            const url = new URL(raw, window.location.origin);
            if (url.origin !== window.location.origin) return false;
            const path = (url.pathname || '/').replace(/\/+$/, '') || '/';
            const prefix = String(URL_PREFIX).replace(/\/+$/, '') || '/';
            return path === prefix || path.startsWith(prefix + '/');
        } catch (_) {
            return false;
        }
    };
    const framedUrl = (raw) => {
        const url = new URL(raw, window.location.origin);
        url.searchParams.set('embed', '1');
        return url.toString();
    };
    const save = () => sessionStorage.setItem(STORAGE_KEY, JSON.stringify(windows.map((item) => ({
        id: item.id, title: item.title, url: item.url, x: item.x, y: item.y, w: item.w, h: item.h,
        minimized: item.minimized, maximized: item.maximized,
    }))));
    const deskSize = () => ({ w: desktop.clientWidth, h: desktop.clientHeight - 40 });
    const updateHint = () => hint.classList.toggle('hidden', windows.length > 0);

    const applyBox = (item) => {
        const el = item.element;
        if (item.minimized) {
            el.style.display = 'none';
            return;
        }
        el.style.display = 'flex';
        if (item.maximized) {
            el.style.left = '0px';
            el.style.top = '0px';
            el.style.width = deskSize().w + 'px';
            el.style.height = deskSize().h + 'px';
        } else {
            el.style.left = item.x + 'px';
            el.style.top = item.y + 'px';
            el.style.width = item.w + 'px';
            el.style.height = item.h + 'px';
        }
    };

    const renderTaskbar = () => {
        taskbar.innerHTML = '';
        windows.forEach((item) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'max-w-[180px] truncate rounded px-2 py-1 text-[11px] font-semibold ' + (item.minimized
                ? 'bg-slate-600 text-slate-200'
                : 'bg-white/15 text-white');
            btn.textContent = item.title;
            btn.addEventListener('click', () => {
                if (item.minimized) {
                    item.minimized = false;
                    focusWindow(item);
                } else if (item.element.style.zIndex === String(z)) {
                    minimizeWindow(item);
                } else {
                    focusWindow(item);
                }
            });
            taskbar.appendChild(btn);
        });
    };

    const focusWindow = (item) => {
        item.minimized = false;
        item.element.style.zIndex = String(++z);
        windows.forEach((entry) => entry.element.classList.toggle('mdi-window-active', entry === item));
        applyBox(item);
        renderTaskbar();
        save();
    };

    const minimizeWindow = (item) => {
        item.minimized = true;
        item.maximized = false;
        applyBox(item);
        renderTaskbar();
        save();
    };

    const closeWindow = (id) => {
        const index = windows.findIndex((item) => item.id === id);
        if (index < 0) return;
        windows[index].element.remove();
        windows.splice(index, 1);
        updateHint();
        renderTaskbar();
        save();
    };

    const bindChrome = (item) => {
        const el = item.element;
        const bar = el.querySelector('.mdi-titlebar');
        el.addEventListener('mousedown', () => focusWindow(item));
        el.querySelector('[data-mdi="min"]').addEventListener('click', (event) => {
            event.stopPropagation();
            minimizeWindow(item);
        });
        el.querySelector('[data-mdi="max"]').addEventListener('click', (event) => {
            event.stopPropagation();
            item.maximized = !item.maximized;
            item.minimized = false;
            applyBox(item);
            focusWindow(item);
        });
        el.querySelector('[data-mdi="close"]').addEventListener('click', (event) => {
            event.stopPropagation();
            closeWindow(item.id);
        });
        bar.addEventListener('dblclick', () => {
            item.maximized = !item.maximized;
            applyBox(item);
            save();
        });
        bar.addEventListener('pointerdown', (event) => {
            if (event.target.closest('button') || item.maximized) return;
            event.preventDefault();
            const startX = event.clientX - item.x;
            const startY = event.clientY - item.y;
            const onMove = (move) => {
                const size = deskSize();
                item.x = Math.max(0, Math.min(size.w - 160, move.clientX - startX));
                item.y = Math.max(0, Math.min(size.h - 40, move.clientY - startY));
                applyBox(item);
            };
            const onUp = () => {
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
                save();
            };
            window.addEventListener('pointermove', onMove);
            window.addEventListener('pointerup', onUp);
        });
        el.querySelector('.mdi-resize').addEventListener('pointerdown', (event) => {
            if (item.maximized) return;
            event.preventDefault();
            event.stopPropagation();
            const startX = event.clientX;
            const startY = event.clientY;
            const startW = item.w;
            const startH = item.h;
            const onMove = (move) => {
                item.w = Math.max(360, startW + (move.clientX - startX));
                item.h = Math.max(240, startH + (move.clientY - startY));
                applyBox(item);
            };
            const onUp = () => {
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onUp);
                save();
            };
            window.addEventListener('pointermove', onMove);
            window.addEventListener('pointerup', onUp);
        });
    };

    const createWindow = (state) => {
        if (!isAllowedUrl(state.url)) return null;
        const size = deskSize();
        const offset = (cascade++ % 8) * 28;
        const item = {
            id: state.id || ('mdi-' + Date.now() + '-' + Math.random().toString(36).slice(2, 6)),
            title: state.title || 'Page',
            url: cleanUrl(state.url),
            x: state.x ?? Math.min(24 + offset, Math.max(24, size.w - 400)),
            y: state.y ?? Math.min(24 + offset, Math.max(24, size.h - 280)),
            w: state.w ?? Math.min(920, Math.max(520, size.w * 0.72)),
            h: state.h ?? Math.min(620, Math.max(360, size.h * 0.7)),
            minimized: Boolean(state.minimized),
            maximized: Boolean(state.maximized),
            element: template.content.firstElementChild.cloneNode(true),
        };
        item.element.querySelector('.mdi-title').textContent = item.title;
        const frame = item.element.querySelector('iframe');
        frame.title = item.title;
        frame.src = framedUrl(item.url);
        desktop.appendChild(item.element);
        bindChrome(item);
        windows.push(item);
        applyBox(item);
        focusWindow(item);
        updateHint();
        return item;
    };

    const openWindow = (title, rawUrl) => {
        if (!isAllowedUrl(rawUrl)) return null;
        const url = cleanUrl(rawUrl);
        const existing = windows.find((item) => item.url === url);
        if (existing) {
            existing.minimized = false;
            focusWindow(existing);
            return existing;
        }
        return createWindow({ title, url });
    };

    document.addEventListener('click', (event) => {
        const skip = event.target.closest('[data-mdi-skip], a[target="_blank"], form button, form [type="submit"]');
        if (skip) return;
        const link = event.target.closest('a[href]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.origin && link.origin !== window.location.origin) return;
        if (!link.closest('aside, #notifMenu, #userMenu, #teacherUserMenu, #studentUserMenu')) return;
        if (link.getAttribute('href').startsWith('#')) return;
        event.preventDefault();
        if (!isAllowedUrl(link.href)) return;
        openWindow(link.dataset.windowTitle || link.textContent.trim().replace(/\s+/g, ' '), link.href);
        document.querySelectorAll('[id$="Menu"]').forEach((menu) => menu.classList.add('hidden'));
        if (window.innerWidth < 1024) document.dispatchEvent(new CustomEvent('ecopilote:close-sidebar'));
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
            if (!isAllowedUrl(event.data.url)) {
                closeWindow(item.id);
                return;
            }
            item.url = cleanUrl(event.data.url);
            item.title = event.data.title || item.title;
            item.element.querySelector('.mdi-title').textContent = item.title;
            renderTaskbar();
            save();
        }
    });

    let restored = [];
    try { restored = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || '[]'); } catch (_) {}
    restored = restored.filter((state) => state && isAllowedUrl(state.url));
    if (restored.length) {
        restored.forEach(createWindow);
        if (!windows.some((item) => item.url === cleanUrl(initial.url))) {
            createWindow({ title: initial.title, url: initial.url });
        }
    } else {
        openWindow(initial.title, initial.url);
    }
    renderTaskbar();
})();
</script>
