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
        }
    });
})();
</script>
