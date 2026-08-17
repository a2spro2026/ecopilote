<script>
(() => {
    const send = (payload) => window.parent.postMessage({ ...payload, origin: window.location.origin }, window.location.origin);

    send({
        type: 'ecopilote:window-navigate',
        title: @json($pageHeading),
        url: @json($currentUrl),
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-window-close]')) {
            event.preventDefault();
            send({ type: 'ecopilote:window-close' });
            return;
        }
        const link = event.target.closest('a[href]');
        if (!link || event.ctrlKey || event.metaKey) return;
        const path = link.pathname || '';
        if (link.hasAttribute('data-mdi-skip') || path.endsWith('/salle') || path.includes('/imprimer')) {
            event.preventDefault();
            window.top.location.href = link.href;
        }
    });
})();
</script>
