
<dialog
    id="admin-confirm-dialog"
    class="admin-confirm-dialog"
    aria-labelledby="admin-confirm-title"
    aria-describedby="admin-confirm-message"
>
    <div class="admin-confirm-dialog__content">

        <div class="admin-confirm-dialog__icon" aria-hidden="true">
            !
        </div>

        <h2 id="admin-confirm-title">
            تأكيد العملية
        </h2>

        <p id="admin-confirm-message">
            هل أنت متأكد من المتابعة؟
        </p>

        <div class="admin-confirm-dialog__actions">
            <button
                type="button"
                class="admin-ui-button admin-ui-button--secondary"
                data-confirm-cancel
            >
                إلغاء
            </button>

            <button
                type="button"
                class="admin-ui-button admin-ui-button--danger"
                data-confirm-accept
            >
                تأكيد
            </button>
        </div>

    </div>
</dialog>
