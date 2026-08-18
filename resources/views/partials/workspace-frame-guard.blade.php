<script>
(() => {
    if (window.self === window.top) return;
    const prefix = @json($workspacePrefix);
    try {
        const parentPath = (window.top.location.pathname || '/').replace(/\/+$/, '') || '/';
        const expected = String(prefix).replace(/\/+$/, '') || '/';
        if (parentPath !== expected && !parentPath.startsWith(expected + '/')) {
            window.parent.postMessage({ type: 'ecopilote:window-close', origin: window.location.origin }, window.location.origin);
            return;
        }
    } catch (_) {}
    const url = new URL(window.location.href);
    if (url.searchParams.get('embed') !== '1') {
        url.searchParams.set('embed', '1');
        window.location.replace(url.toString());
    }
})();
</script>
