document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const titleEl = document.getElementById('confirmTitle');
    const messageEl = document.getElementById('confirmMessage');
    const okBtn = document.getElementById('confirmOk');
    let targetForm = null;

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            targetForm = form;
            titleEl.textContent = form.dataset.confirmTitle || 'Are you sure?';
            messageEl.textContent = form.dataset.confirm;
            modal.show();
        });
    });

    okBtn.addEventListener('click', function () {
        if (!targetForm) return;
        modal.hide();
        targetForm.submit();
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        targetForm = null;
    });
});
