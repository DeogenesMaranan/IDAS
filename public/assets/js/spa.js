document.addEventListener('DOMContentLoaded', function () {
    const links = Array.from(document.querySelectorAll('aside a[data-page]'));
    const sections = Array.from(document.querySelectorAll('.spa-section'));

    function showPage(page) {
        sections.forEach(s => {
            if (s.id === page) {
                s.classList.remove('hidden');
            } else {
                s.classList.add('hidden');
            }
        });

        // Update active classes on sidebar links
        links.forEach(a => {
            const p = a.getAttribute('data-page');
            // Remove any previous active classes
            a.classList.remove('bg-white/20', 'shadow-lg');
            // Ensure hover state present for non-active
            a.classList.remove('hover:bg-white/10');

            if (p === page) {
                a.classList.add('bg-white/20', 'shadow-lg');
            } else {
                a.classList.add('hover:bg-white/10');
            }
        });
    }

    links.forEach(a => {
        a.addEventListener('click', function (ev) {
            // Only intercept SPA links
            const page = this.getAttribute('data-page');
            if (!page) return;
            ev.preventDefault();
            showPage(page);
        });
    });

    // initial page
    const initial = window.__INITIAL_SPA_PAGE || 'dashboard';
    showPage(initial);
});
