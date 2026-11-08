

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
        const mainImage = gallery.querySelector('[data-gallery-main]');
        const thumbnails = gallery.querySelectorAll('[data-gallery-thumb]');

        if (!mainImage || thumbnails.length === 0) {
            return;
        }

        thumbnails.forEach((thumbnail) => {
            thumbnail.addEventListener('click', () => {
                const imageUrl = thumbnail.dataset.imageUrl;

                if (!imageUrl) {
                    return;
                }

                mainImage.src = imageUrl;

                thumbnails.forEach((button) => {
                    button.setAttribute('aria-pressed', 'false');
                });

                thumbnail.setAttribute('aria-pressed', 'true');
            });
        });
    });
});
