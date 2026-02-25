document.addEventListener('DOMContentLoaded', function () {
        console.log('SPA script loaded');
    const links = Array.from(document.querySelectorAll('aside a[data-page]'));
        console.log('SPA nav links found:', links.length);
    const sections = Array.from(document.querySelectorAll('.spa-section'));
    // Remove PHP-set active classes on all sidebar links
    links.forEach(a => {
        a.classList.remove('bg-white/20', 'shadow-lg', 'hover:bg-white/10');
    });

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
            console.log('SPA nav clicked:', page);
            showPage(page);
            sessionStorage.setItem('spa_selected_page', page);
            console.log('sessionStorage updated:', sessionStorage.getItem('spa_selected_page'));
        });
    });

    // Restore selected page from sessionStorage or use default
    const savedPage = sessionStorage.getItem('spa_selected_page');
    const initial = savedPage || window.__INITIAL_SPA_PAGE || 'dashboard';
    showPage(initial);

    // Fetch logged-in student/profile info and populate booking form
    function populateStudent() {
        fetch('/IDSystem/api/student', { credentials: 'same-origin' })
            .then(r => {
                if (!r.ok) throw new Error('Not authenticated');
                return r.json();
            })
            .then(data => {
                const set = (id, value) => {
                    const el = document.getElementById(id);
                    if (el && (value !== undefined && value !== null)) el.value = value;
                };

                set('full_name', data.full_name || '');
                set('student_id', data.student_id || '');
                set('email', data.email || '');
                set('department', data.department || '');
                set('course_grade_strand', data.course_grade_strand || '');
                set('year', data.year || '');
            })
            .catch(() => {
                // silently ignore if not authenticated or API missing
            });
    }

    populateStudent();
});
