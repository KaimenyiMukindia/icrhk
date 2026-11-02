(function () {
    'use strict';

    var strings = window.cerAdminListStrings;

    function initializeBulkSelection(root) {
        root.querySelectorAll('form[method="post"]').forEach(function (form) {
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
                status.textContent = String(selected) + ' ' + strings.selected;
            }

            selectAll.addEventListener('change', function () {
                rows.forEach(function (row) { row.checked = selectAll.checked; });
            });
            form.addEventListener('change', updateSelection);
            updateSelection();
        });
    }

    initializeBulkSelection(document);

    if (!window.fetch || !window.AbortController || !window.DOMParser || !window.history.replaceState) {
        return;
    }

    document.querySelectorAll('[data-cer-live-list]').forEach(function (root) {
        var filters = root.querySelector('[data-cer-live-filters]');
        var results = root.querySelector('[data-cer-live-results]');
        if (!filters || !results) {
            return;
        }
        root.setAttribute('data-cer-live-ready', '');
        var status = document.createElement('p');
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        status.hidden = true;
        filters.after(status);
        var reset = document.createElement('button');
        reset.type = 'button';
        reset.className = 'button';
        reset.textContent = strings.reset;
        filters.append(reset);
        var controller;
        var timer;
        var sequence = 0;
        var pending = false;
        var exports = root.querySelectorAll('[data-cer-filter-export]');
        var controls = Array.from(filters.querySelectorAll('input[name], select[name]'));
        var editable = controls.filter(function (control) { return control.type !== 'hidden'; });

        function showStatus(message, failed) {
            status.textContent = message;
            status.hidden = !message;
            status.className = failed ? 'notice notice-error' : '';
        }

        function lockResults(value) {
            pending = value;
            results.inert = value;
            results.setAttribute('aria-busy', value ? 'true' : 'false');
            exports.forEach(function (link) {
                link.setAttribute('aria-disabled', value ? 'true' : 'false');
            });
        }

        function filterUrl() {
            var url = new URL(filters.action || filters.dataset.cerFilterUrl, window.location.href);
            controls.forEach(function (control) {
                if (control.value) {
                    url.searchParams.set(control.name, control.value);
                } else {
                    url.searchParams.delete(control.name);
                }
            });
            url.searchParams.delete('paged');
            url.searchParams.delete('item_paged');
            return url;
        }

        function cancelRequest() {
            ++sequence;
            window.clearTimeout(timer);
            if (controller) {
                controller.abort();
            }
        }

        function load(url, pushHistory) {
            cancelRequest();
            var request = sequence;
            controller = new AbortController();
            var timeout = window.setTimeout(function () { controller.abort(); }, 30000);
            lockResults(true);
            showStatus(strings.loading, false);
            fetch(url.toString(), {
                credentials: 'same-origin',
                cache: 'no-store',
                signal: controller.signal
            }).then(function (response) {
                if (!response.ok || response.redirected) {
                    throw new Error('Filter request failed.');
                }
                return response.text();
            }).then(function (html) {
                if (request !== sequence) {
                    return;
                }
                var parsed = new DOMParser().parseFromString(html, 'text/html');
                var source = Array.from(parsed.querySelectorAll('[data-cer-live-list]')).find(function (node) {
                    return node.dataset.cerLiveList === root.dataset.cerLiveList;
                });
                var updated = source && source.querySelector('[data-cer-live-results]');
                var sourceFilters = source && source.querySelector('[data-cer-live-filters]');
                if (!updated || !sourceFilters || !updated.querySelector('table')) {
                    throw new Error('Filtered results were not returned.');
                }
                root.dispatchEvent(new CustomEvent('cer:list-before-update', { bubbles: true }));
                results.replaceChildren.apply(results, Array.from(updated.childNodes));
                exports.forEach(function (link) {
                    var replacement = source.querySelector('[data-cer-filter-export]');
                    if (replacement) {
                        link.href = replacement.href;
                    }
                });
                initializeBulkSelection(results);
                root.dispatchEvent(new CustomEvent('cer:list-updated', { bubbles: true }));
                if (pushHistory) {
                    window.history.pushState(null, '', url.toString());
                } else {
                    window.history.replaceState(null, '', url.toString());
                }
                lockResults(false);
                showStatus(strings.updated, false);
            }).catch(function (error) {
                if (request !== sequence) {
                    return;
                }
                console.error('CER live filtering failed', error);
                results.setAttribute('aria-busy', 'false');
                showStatus(strings.error, true);
            }).finally(function () {
                window.clearTimeout(timeout);
            });
        }

        function schedule() {
            cancelRequest();
            lockResults(true);
            showStatus(strings.loading, false);
            timer = window.setTimeout(function () { load(filterUrl(), false); }, 250);
        }

        filters.addEventListener('input', function (event) {
            if (editable.indexOf(event.target) !== -1 && event.target.type !== 'date' && !event.isComposing) {
                schedule();
            }
        });
        filters.addEventListener('change', function (event) {
            if (editable.indexOf(event.target) !== -1) {
                load(filterUrl(), false);
            }
        });
        filters.addEventListener('submit', function (event) {
            event.preventDefault();
            load(filterUrl(), false);
        });
        filters.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && editable.indexOf(event.target) !== -1) {
                event.preventDefault();
                load(filterUrl(), false);
            }
        });
        filters.addEventListener('click', function (event) {
            if (event.target.closest('.cer-item-trash-search-submit')) {
                load(filterUrl(), false);
            }
        });
        reset.addEventListener('click', function () {
            editable.forEach(function (control) { control.value = ''; });
            load(filterUrl(), false);
        });
        root.addEventListener('click', function (event) {
            var link = event.target.closest('a');
            if (!link) {
                return;
            }
            if (pending && (results.contains(link) || link.hasAttribute('data-cer-filter-export'))) {
                event.preventDefault();
                return;
            }
            if (!link.closest('.cer-list-pagination') || event.defaultPrevented || event.button !== 0 ||
                event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }
            var url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) {
                return;
            }
            event.preventDefault();
            load(url, true);
        });
        root.addEventListener('submit', function (event) {
            if (pending && results.contains(event.target)) {
                event.preventDefault();
            }
        }, true);
        window.addEventListener('popstate', function () {
            var url = new URL(window.location.href);
            controls.forEach(function (control) {
                control.value = url.searchParams.get(control.name) || '';
            });
            load(url, false);
        });
    });
})();
