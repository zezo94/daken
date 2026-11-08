
document.addEventListener('DOMContentLoaded', () => {
    // Close flash messages.
    document.querySelectorAll('[data-flash-close]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-admin-flash]')?.remove();
        });
    });

    // Reusable confirmation dialog.
    const dialog = document.getElementById('admin-confirm-dialog');

    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    const titleElement = dialog.querySelector('#admin-confirm-title');
    const messageElement = dialog.querySelector('#admin-confirm-message');
    const acceptButton = dialog.querySelector('[data-confirm-accept]');
    const cancelButton = dialog.querySelector('[data-confirm-cancel]');

    let pendingForm = null;

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmApproved === 'true') {
                return;
            }

            event.preventDefault();

            pendingForm = form;

            titleElement.textContent =
                form.dataset.confirmTitle || 'تأكيد العملية';

            messageElement.textContent =
                form.dataset.confirmMessage ||
                'هل أنت متأكد من المتابعة؟';

            dialog.showModal();
        });
    });

    cancelButton?.addEventListener('click', () => {
        pendingForm = null;
        dialog.close();
    });

    acceptButton?.addEventListener('click', () => {
        if (!pendingForm) {
            return;
        }

        const form = pendingForm;
        pendingForm = null;

        dialog.close();

        form.dataset.confirmApproved = 'true';

        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            HTMLFormElement.prototype.submit.call(form);
        }
    });

    dialog.addEventListener('close', () => {
        pendingForm = null;
    });
});
