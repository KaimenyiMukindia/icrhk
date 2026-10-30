(function () {
    'use strict';

    document.querySelectorAll('.cer-admin-shell form[method="post"]').forEach(function (form) {
        var action = form.querySelector('[name="bulk_action"]');
        var submit = form.querySelector('input[type="submit"], button[type="submit"]');
        var rows = form.querySelectorAll('.cer-event-checkbox, .cer-registration-checkbox');
        var selectAll = form.querySelector('#cer-select-all-events, #cer-select-all-registrations');
        if (!action || !submit || !selectAll) {
            return;
        }
        var status = document.createElement('span');
        status.className = 'cer-bulk-selection-status';
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        submit.after(status);

        function updateSelection() {
            var selected = Array.from(rows).filter(function (row) { return row.checked; }).length;
            submit.disabled = !action.value || selected === 0;
            selectAll.checked = rows.length > 0 && selected === rows.length;
            selectAll.indeterminate = selected > 0 && selected < rows.length;
            selectAll.disabled = rows.length === 0;
            status.textContent = String(selected) + ' ' + cerAdminListStrings.selected;
        }

        form.addEventListener('change', updateSelection);
        updateSelection();
    });
})();
