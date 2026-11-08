
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('admin-menu-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('admin-sidebar-backdrop');

    if (!toggle || !sidebar || !backdrop) {
        return;
    }

    const mobileQuery = window.matchMedia('(max-width: 991px)');
    let previousFocus = null;

    function setOpen(open, restoreFocus = true) {
        const shouldOpen = open && mobileQuery.matches;

        sidebar.classList.toggle('is-open', shouldOpen);
        backdrop.hidden = !shouldOpen;

        document.body.classList.toggle(
            'daken-admin-menu-open',
            shouldOpen
        );

        toggle.setAttribute(
            'aria-expanded',
            String(shouldOpen)
        );

        toggle.setAttribute(
            'aria-label',
            shouldOpen
                ? (document.documentElement.lang === 'ar'
                    ? 'إغلاق القائمة'
                    : 'Close menu')
                : (document.documentElement.lang === 'ar'
                    ? 'فتح القائمة'
                    : 'Open menu')
        );

        if (shouldOpen) {
            previousFocus = document.activeElement;
            sidebar.querySelector('a')?.focus();
        } else if (restoreFocus && previousFocus) {
            previousFocus.focus();
            previousFocus = null;
        }
    }

    toggle.addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('is-open'));
    });

    backdrop.addEventListener('click', () => {
        setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            sidebar.classList.contains('is-open')
        ) {
            setOpen(false);
        }

        if (
            event.key === 'Tab' &&
            sidebar.classList.contains('is-open')
        ) {
            const focusable = Array.from(
                sidebar.querySelectorAll(
                    'a[href], button:not([disabled])'
                )
            );

            if (focusable.length === 0) {
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (
                !event.shiftKey &&
                document.activeElement === last
            ) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    mobileQuery.addEventListener('change', () => {
        setOpen(false, false);
    });
});
