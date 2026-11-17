
import './admin.js';
import './product-gallery.js';
import './admin-sidebar';

/**
 * DAKEN — Shared homepage carousel.
 * Supports LTR, RTL, keyboard, touch and reduced motion.
 */
function initDakenCarousel(
    selector,
    trackSelector,
    prevSelector,
    nextSelector,
    cardSelector
) {
    document.querySelectorAll(selector).forEach((carousel) => {
        const track = carousel.querySelector(trackSelector);
        const prev = carousel.querySelector(prevSelector);
        const next = carousel.querySelector(nextSelector);

        if (!track || !prev || !next) {
            return;
        }

        const cards = Array.from(
            track.querySelectorAll(cardSelector)
        );

        if (cards.length < 2) {
            prev.disabled = true;
            next.disabled = true;
            return;
        }

        const isRTL = () =>
            getComputedStyle(track).direction === 'rtl';

        const getStep = () => {
            const first = cards[0].getBoundingClientRect();
            const second = cards[1].getBoundingClientRect();

            return (
                Math.abs(second.left - first.left)
                || track.clientWidth
            );
        };

        const getMaxScroll = () =>
            Math.max(
                0,
                track.scrollWidth - track.clientWidth
            );

        const getPosition = () =>
            isRTL()
                ? -track.scrollLeft
                : track.scrollLeft;

        const updateControls = () => {
            const position = Math.max(0, getPosition());
            const max = getMaxScroll();
            const tolerance = 3;

            prev.disabled = position <= tolerance;
            next.disabled = position >= max - tolerance;
        };

        const scrollByCard = (direction) => {
            const step = getStep();

            const delta = direction
                * step
                * (isRTL() ? -1 : 1);

            const reducedMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;

            track.scrollBy({
                left: delta,
                behavior: reducedMotion ? 'auto' : 'smooth'
            });
        };

        prev.addEventListener('click', () => {
            scrollByCard(-1);
        });

        next.addEventListener('click', () => {
            scrollByCard(1);
        });

        track.addEventListener(
            'scroll',
            updateControls,
            { passive: true }
        );

        window.addEventListener(
            'resize',
            updateControls
        );

        updateControls();
    });
}

/**
 * Initialize homepage carousels.
 */
function initDakenCarousels() {
    initDakenCarousel(
        '[data-daken-featured-carousel]',
        '[data-daken-featured-track]',
        '[data-daken-featured-prev]',
        '[data-daken-featured-next]',
        '.product-card'
    );

    initDakenCarousel(
        '[data-daken-collections-carousel]',
        '[data-daken-collections-track]',
        '[data-daken-collections-prev]',
        '[data-daken-collections-next]',
        '.daken-layered-frame'
    );

    initDakenCarousel(
        '[data-daken-gallery-carousel]',
        '[data-daken-gallery-track]',
        '[data-daken-gallery-prev]',
        '[data-daken-gallery-next]',
        '.daken-layered-frame'
    );
}

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initDakenCarousels,
        { once: true }
    );
} else {
    initDakenCarousels();
}
