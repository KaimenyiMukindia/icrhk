(function () {
    'use strict';

    var strings = window.cerAdminListStrings;

    function initializeAssignment() {
        var form = document.getElementById('cer-assignment-form');
        if (!form || !window.fetch) {
            return;
        }
        var event = form.querySelector('#cer-assign-event');
        var ticket = form.querySelector('#cer-assign-ticket');
        var field = form.querySelector('#cer-assign-ticket-field');
        var status = form.querySelector('#cer-assign-availability');
        var basket = form.querySelector('#cer-assign-basket');
        var total = form.querySelector('#cer-assign-total');
        var preload = document.getElementById('cer-assign-inventory');
        var inventory = preload ? JSON.parse(preload.textContent) : {};
        var activeItems = [];
        var sequence = 0;
        var controller;
        if (basket) {
            ticket.disabled = true;
            ticket.required = false;
            form.querySelectorAll('[data-cer-assignment-legacy]').forEach(function (node) {
                node.hidden = true;
                node.querySelector('input').disabled = true;
            });
        }
        function optionLabel(item) {
            var availability = item.maintenance ? strings.assignMaintenance :
                (item.remaining === 0 ? strings.assignSoldOut :
                    (item.remaining === null ? strings.assignUnlimited : item.remaining.toLocaleString() + ' ' + strings.assignLeft));
            return item.name + ' — ' + availability;
        }
        function updateTotal() {
            if (!basket) { return; }
            var fields = Array.from(basket.querySelectorAll('[data-assign-quantity]'));
            var seats = fields.reduce(function (sum, node) { return sum + Number(node.value || 0); }, 0);
            var amount = fields.reduce(function (sum, node) {
                var price = node.closest('.cer-assignment-ticket-row').querySelector('[data-assign-price]');
                return sum + Math.round(Number(price.value || 0) * 100) * Number(node.value || 0);
            }, 0);
            total.textContent = seats + ' ' + strings.assignQuantity + ' · ' + strings.assignTotal + ': ' + (amount / 100).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            total.dataset.seats = String(seats);
        }
        function renderBasket(items, reset) {
            if (!basket) { return; }
            var sameTypes = activeItems.map(function (item) { return item.id; }).join(',') === items.map(function (item) { return item.id; }).join(',');
            if (reset || !sameTypes) {
                basket.replaceChildren();
                items.forEach(function (item) {
                    var row = document.createElement('div');
                    row.className = 'cer-assignment-ticket-row';
                    row.dataset.id = item.id;
                    var label = document.createElement('span');
                    label.dataset.assignLabel = '';
                    row.append(label);
                    [['quantity', strings.assignQuantity, items.length === 1 ? '1' : '0'],
                        ['amount', strings.assignPrice, item.price || '0']].forEach(function (definition) {
                        var wrap = document.createElement('label');
                        wrap.textContent = definition[1];
                        var control = document.createElement('input');
                        control.type = 'number';
                        control.name = 'tickets[' + item.id + '][' + definition[0] + ']';
                        control.min = '0';
                        control.max = definition[0] === 'quantity' ? '20' : '99999999.99';
                        control.step = definition[0] === 'quantity' ? '1' : '0.01';
                        control.value = definition[2];
                        control.required = true;
                        control.setAttribute(definition[0] === 'quantity' ? 'data-assign-quantity' : 'data-assign-price', '');
                        control.addEventListener('input', updateTotal);
                        wrap.append(control);
                        row.append(wrap);
                    });
                    basket.append(row);
                });
            }
            activeItems = items;
            items.forEach(function (item) {
                var row = basket.querySelector('[data-id="' + item.id + '"]');
                row.querySelector('[data-assign-label]').textContent = optionLabel(item);
                var quantity = row.querySelector('[data-assign-quantity]');
                quantity.max = String(Math.min(20, item.remaining === null ? 20 : item.remaining));
                quantity.disabled = item.maintenance || item.remaining === 0;
                row.querySelector('[data-assign-price]').disabled = quantity.disabled;
                if (quantity.disabled) { quantity.value = '0'; }
            });
            basket.hidden = !items.length;
            field.hidden = true;
            updateTotal();
        }
        function apply(data, reset) {
            var previous = reset ? '' : ticket.value;
            var items = data.tickets;
            ticket.replaceChildren(new Option(strings.assignSelectTicket, ''));
            items.forEach(function (item) {
                var choice = new Option(optionLabel(item), String(item.id));
                choice.disabled = item.maintenance || item.remaining === 0;
                ticket.add(choice);
            });
            field.hidden = items.length === 1 && !ticket.options[1].disabled;
            if (items.length === 1 && !ticket.options[1].disabled) {
                ticket.value = String(items[0].id);
            } else if (previous && Array.from(ticket.options).some(function (option) { return option.value === previous && !option.disabled; })) {
                ticket.value = previous;
            }
            status.textContent = items.length === 0 ? strings.assignNoTickets :
                (items.length === 1 && !basket ? optionLabel(items[0]) :
                    (data.remaining === null ? strings.assignUnlimited : data.remaining.toLocaleString() + ' ' + strings.assignLeft));
            renderBasket(items, reset);
        }
        function refresh() {
            var current = event.value;
            var request = ++sequence;
            if (!current) {
                ticket.replaceChildren(new Option(strings.assignSelectEvent, ''));
                field.hidden = false;
                status.textContent = '';
                if (basket) { basket.hidden = true; field.hidden = true; total.textContent = ''; }
                return;
            }
            var cached = inventory[current] && inventory[current].availability;
            if (cached) { apply(cached, false); }
            else { status.textContent = strings.assignLoading; }
            if (controller) { controller.abort(); }
            controller = typeof AbortController === 'function' ? new AbortController() : null;
            var url = new URL(form.dataset.availabilityUrl, window.location.href);
            url.searchParams.set('action', basket ? 'cer_admin_assignment_inventory' : 'cer_ticket_availability');
            if (basket) { url.searchParams.set('_ajax_nonce', form.dataset.inventoryNonce); }
            url.searchParams.set('event_id', current);
            fetch(url.toString(), { credentials: 'same-origin', cache: 'no-store', signal: controller ? controller.signal : undefined }).then(function (response) {
                if (!response.ok) { throw new Error('Availability request failed'); }
                return response.json();
            }).then(function (result) {
                if (request !== sequence || current !== event.value) { return; }
                if (!result.success || !result.data || !Array.isArray(result.data.tickets)) {
                    throw new Error('Invalid availability response');
                }
                result.data.tickets.forEach(function (item) {
                    var old = cached && cached.tickets.find(function (entry) { return entry.id === item.id; });
                    if (old && item.price === undefined) { item.price = old.price; }
                });
                inventory[current] = { availability: result.data };
                apply(result.data, false);
            }).catch(function (error) {
                if (request !== sequence || current !== event.value) { return; }
                console.error('CER assignment inventory refresh failed', error);
                ticket.replaceChildren(new Option(strings.assignSelectTicket, ''));
                field.hidden = false;
                status.textContent = strings.assignFailed;
            });
        }
        event.addEventListener('change', function () {
            ticket.replaceChildren(new Option(strings.assignSelectTicket, ''));
            activeItems = [];
            if (basket) { basket.replaceChildren(); }
            refresh();
        });
        if (basket) {
            form.addEventListener('submit', function (event) {
                var seats = Number(total.dataset.seats || 0);
                if (seats < 1 || seats > 20) {
                    event.preventDefault();
                    status.textContent = strings.assignQuantity + ': 1–20';
                }
            });
        }
        window.setInterval(function () {
            if (event.value && !document.hidden) { refresh(); }
        }, 30000);
        refresh();
    }

    initializeAssignment();

    function initializeMailPicker() {
        var picker = document.getElementById('cer-mail-picker');
        if (!picker) { return; }
        var select = document.getElementById('cer-mail-recipients');
        var all = document.getElementById('cer-mail-all');
        var enhanced = document.getElementById('cer-mail-picker-enhanced');
        var input = document.getElementById('cer-mail-recipient-json');
        var search = document.getElementById('cer-mail-search');
        var results = document.getElementById('cer-mail-search-results');
        var selectedList = document.getElementById('cer-mail-selected');
        var selectedCount = document.getElementById('cer-mail-selected-count');
        var addressText = document.getElementById('cer-mail-added-addresses');
        var addressButton = document.getElementById('cer-mail-add-addresses');
        var addressError = document.getElementById('cer-mail-address-error');
        var addresses = [];
        var choices = Array.from(select.options).map(function (option) {
            var match = option.textContent.match(/<([^>]+)>/);
            return { id: Number(option.value), label: option.textContent,
                email: option.dataset && option.dataset.email || (match ? match[1] : ''),
                selected: option.selected };
        });
        if (addressText && addressButton) {
            var addressPayload = document.createElement('input');
            addressPayload.type = 'hidden';
            addressPayload.name = addressText.name;
            addressPayload.setAttribute('form', 'cer-event-mail-form');
            addressText.removeAttribute('name');
            addressText.after(addressPayload);
            addressButton.hidden = false;
            function renderAddresses() {
                addressPayload.value = addresses.join('\n');
                renderSelected();
            }
            function addAddresses() {
                var added = addressText.value.split(/\r\n|\r|\n/).map(function (value) { return value.trim(); }).filter(Boolean);
                if (added.some(function (email) { return !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); })) {
                    addressError.textContent = addressError.dataset.invalid;
                    addressText.focus();
                    return false;
                }
                added.forEach(function (email) {
                    if (!addresses.some(function (value) { return value.toLowerCase() === email.toLowerCase(); })) { addresses.push(email); }
                });
                addressText.value = '';
                addressError.textContent = '';
                renderAddresses();
                return true;
            }
            addressButton.addEventListener('click', addAddresses);
            var mailForm = document.getElementById('cer-event-mail-form');
            if (mailForm) {
                mailForm.addEventListener('submit', function (event) {
                    var editor = window.tinymce && window.tinymce.get('cer_mail_body');
                    if (editor) { editor.save(); }
                    if (!addAddresses()) { event.preventDefault(); }
                });
            }
        }
        function makeButton(text, label, action) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'button button-small';
            button.textContent = text;
            button.setAttribute('aria-label', text + ' ' + label);
            button.addEventListener('click', action);
            return button;
        }
        function renderSelected() {
            var selected = choices.filter(function (choice) { return choice.selected; });
            input.value = JSON.stringify(selected.map(function (choice) { return choice.id; }));
            selectedList.replaceChildren();
            var recipients = new Map();
            (all.checked ? choices : selected).forEach(function (choice) {
                var key = choice.email.toLowerCase() || 'registration_' + choice.id;
                if (!recipients.has(key)) { recipients.set(key, { email: choice.email || choice.label, title: choice.label, registrations: [] }); }
                recipients.get(key).registrations.push(choice);
            });
            addresses.forEach(function (email) {
                var key = email.toLowerCase();
                if (!recipients.has(key)) { recipients.set(key, { email: email, title: email, registrations: [] }); }
            });
            selectedCount.textContent = recipients.size + ' ' + strings.mailSelected;
            if (!recipients.size) {
                var empty = document.createElement('li');
                empty.textContent = strings.mailEmpty;
                selectedList.append(empty);
                return;
            }
            recipients.forEach(function (recipient, key) {
                var item = document.createElement('li');
                var label = document.createElement('span');
                label.textContent = recipient.email;
                label.title = recipient.title;
                var remove = makeButton(strings.mailRemove, recipient.email, function () {
                    if (all.checked) {
                        all.checked = false;
                        choices.forEach(function (choice) { choice.selected = true; });
                        enhanced.hidden = false;
                        input.disabled = false;
                    }
                    recipient.registrations.forEach(function (choice) { choice.selected = false; });
                    addresses = addresses.filter(function (email) { return email.toLowerCase() !== key; });
                    if (addressText && addressButton) { addressPayload.value = addresses.join('\n'); }
                    renderSelected();
                    renderResults();
                });
                remove.textContent = '\u00d7';
                remove.className = 'cer-mail-recipient-remove';
                item.append(label, remove);
                selectedList.append(item);
            });
        }
        function renderResults() {
            results.replaceChildren();
            var query = search.value.trim().toLocaleLowerCase();
            if (!query) {
                results.textContent = strings.mailSearchHint;
                return;
            }
            var matches = choices.filter(function (choice) {
                return !choice.selected && choice.label.toLocaleLowerCase().indexOf(query) !== -1;
            });
            if (!matches.length) {
                results.textContent = strings.mailSearchNone;
                return;
            }
            matches.slice(0, 20).forEach(function (choice) {
                var row = document.createElement('div');
                var label = document.createElement('span');
                label.textContent = choice.label;
                row.append(label, makeButton(strings.mailAdd, choice.label, function () {
                    choice.selected = true;
                    renderSelected();
                    renderResults();
                }));
                results.append(row);
            });
            if (matches.length > 20) {
                var more = document.createElement('p');
                more.textContent = strings.mailSearchMore;
                results.append(more);
            }
        }
        function toggleAll() {
            enhanced.hidden = all.checked;
            input.disabled = all.checked;
            renderSelected();
        }
        var group = document.getElementById('cer-mail-group');
        var groupKey = document.getElementById('cer-mail-group-key');
        if (group && groupKey) {
            group.addEventListener('change', function () {
                groupKey.value = group.selectedIndex > 0 ? group.options[group.selectedIndex].dataset.key : '';
            });
        }
        select.hidden = true;
        select.disabled = true;
        input.disabled = false;
        search.addEventListener('input', renderResults);
        all.addEventListener('change', toggleAll);
        renderSelected();
        renderResults();
        toggleAll();
    }

    initializeMailPicker();

    function initializeBulkSelection(root) {
        root.querySelectorAll('form[method="post"]').forEach(function (form) {
            var action = form.querySelector('[name="bulk_action"]');
            var submit = form.querySelector('input[type="submit"]:not([form]), button[type="submit"]:not([form])');
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
        var currentListUrl = window.location.pathname + window.location.search;
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
                currentListUrl = url.pathname + url.search;
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
            if (url.pathname + url.search === currentListUrl) {
                return;
            }
            controls.forEach(function (control) {
                control.value = url.searchParams.get(control.name) || '';
            });
            load(url, false);
        });
    });
})();
