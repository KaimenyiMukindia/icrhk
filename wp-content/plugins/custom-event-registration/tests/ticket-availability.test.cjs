const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const script = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-registration.js'), 'utf8');
const source = script.slice(script.lastIndexOf('(function (document, window) {'));

function render({ tickets, capacity, remaining, single = false, configuredCount = tickets.length,
    paymentRequired = false, soldOut = false, initial = true }) {
    tickets = tickets.map(item => ({ maintenance: false, ...item }));
    function element() {
        return {
            dataset: {}, textContent: '', children: [],
            replaceChildren(...children) { this.children = children; },
            querySelector() { return null; },
            addEventListener() {},
        };
    }
    const data = { capacity, remaining, tickets };
    const form = element();
    form.dataset = {
        ticketCount: String(configuredCount),
        availability: initial ? JSON.stringify(data) : '',
        availabilityReady: initial ? 'true' : 'false',
        paymentRequired: paymentRequired ? 'true' : 'false',
        paymentMethod: 'mpesa',
    };
    form.classList = {
        contains: name => name === 'cer-sold-out' && soldOut,
        toggle(name, enabled) { if (name === 'cer-sold-out') soldOut = enabled; },
    };
    const ticket = element();
    if (single) {
        ticket.value = tickets[0].name;
        ticket.dataset = { id: String(tickets[0].id), price: '100' };
    } else {
        ticket.options = [{ value: '', dataset: {} }, ...tickets.map(item => ({
            value: item.name, disabled: !!item.disabled,
            dataset: { id: String(item.id), baseLabel: item.label || item.name, price: '100' },
            textContent: item.label || item.name,
        }))];
        ticket.selectedIndex = 0;
        ticket.value = '';
    }
    const count = element();
    const inline = element();
    const error = element();
    const eventId = { value: '7' };
    const elements = {
        'cer-registration-form': form,
        'cer-ticket-type': ticket,
        'cer-ticket-availability': count,
        'cer-inline-ticket-availability': single && configuredCount > 1 ? inline : null,
        'cer-availability-error': error,
        'cer-event-id': eventId,
    };
    const document = {
        hidden: false, handlers: {}, getElementById: id => elements[id],
        createElement: element, addEventListener(name, callback) { this.handlers[name] = callback; },
    };
    let response = { ok: true, json: async () => ({ success: true, data }) };
    const calls = [];
    const errors = [];
    let tick;
    const window = {
        location: { href: 'https://example.test/event' },
        cerRegistrationSettings: { ajax_url: 'https://example.test/wp-admin/admin-ajax.php' },
        console: { error: (...args) => errors.push(args) },
        setInterval(callback, milliseconds) { assert.equal(milliseconds, 30000); tick = callback; },
    };
    vm.runInNewContext(source, {
        document, window, URL,
        fetch: async (...args) => { calls.push(args); return response; },
    });
    return {
        form, ticket, count, inline, error, document, calls, errors, data,
        settle: () => new Promise(resolve => setImmediate(resolve)),
        tick: () => tick(),
        respond: value => { response = value; },
    };
}

for (const paymentRequired of [false, true]) {
    test(`one ${paymentRequired ? 'paid' : 'free'} ticket updates the existing summary count`, async () => {
        const ui = render({
            tickets: [{ id: 1, name: 'Delegate', capacity: 100, remaining: 70 }],
            capacity: 100, remaining: 70, single: true, paymentRequired,
        });
        await ui.settle();
        assert.equal(ui.count.children[0].textContent, 'Available');
        assert.equal(ui.count.children[1].textContent, 'out of 100 capacity');
        assert.equal(ui.form.dataset.paymentRequired, String(paymentRequired));
        assert.equal(ui.ticket.value, 'Delegate');
        assert.match(ui.calls[0][0], /action=cer_ticket_availability&event_id=7/);
        assert.equal(ui.calls[0][1].cache, 'no-store');

        ui.form.dataset.paymentMethod = 'card';
        ui.respond({ ok: true, json: async () => ({ success: true, data: {
            ...ui.data, remaining: 69, tickets: [{ ...ui.data.tickets[0], remaining: 69 }],
        } }) });
        await ui.tick();
        assert.equal(ui.count.children[0].textContent, 'Available');
        assert.equal(ui.form.dataset.paymentMethod, 'card');
    });
}

test('multiple ticket types show counts in the original options without changing selection or payment', async () => {
    const ui = render({
        tickets: [
            { id: 1, name: 'Regular', label: 'Regular (KES 2,000.00)', capacity: 80, remaining: 50 },
            { id: 2, name: 'VIP', capacity: 20, remaining: 20 },
            { id: 3, name: 'Early', capacity: 10, remaining: 0, disabled: true },
        ],
        capacity: 110, remaining: 70, paymentRequired: true,
    });
    await ui.settle();
    assert.equal(ui.ticket.options[1].textContent, 'Regular (KES 2,000.00)');
    assert.equal(ui.ticket.options[2].textContent, 'VIP');
    assert.equal(ui.ticket.options[3].textContent, 'Early - Sold out');
    assert.equal(ui.count.children.length, 0);

    ui.ticket.selectedIndex = 2;
    ui.ticket.value = 'VIP';
    ui.form.dataset.paymentMethod = 'card';
    ui.respond({ ok: true, json: async () => ({ success: true, data: {
        ...ui.data, remaining: 50,
        tickets: [ui.data.tickets[0], { ...ui.data.tickets[1], remaining: 0 }, ui.data.tickets[2]],
    } }) });
    await ui.tick();
    assert.equal(ui.ticket.options[2].textContent, 'VIP - Sold out');
    assert.equal(ui.ticket.options[2].disabled, true);
    assert.equal(ui.ticket.value, 'VIP');
    assert.equal(ui.form.dataset.paymentMethod, 'card');
});

test('the existing auto-selected last ticket shows its count beside its label', async () => {
    const ui = render({
        tickets: [
            { id: 1, name: 'Regular', capacity: 20, remaining: 5 },
            { id: 2, name: 'Sold out', capacity: 10, remaining: 0 },
        ],
        capacity: 30, remaining: 5, single: true, configuredCount: 2,
    });
    await ui.settle();
    assert.equal(ui.inline.textContent, ' - 5 left');
    assert.equal(ui.count.children.length, 0);
});

test('a previously disabled ticket that becomes available is selectable again', async () => {
    const ui = render({
        tickets: [
            { id: 1, name: 'Regular', capacity: 10, remaining: 3 },
            { id: 2, name: 'VIP', capacity: 10, remaining: 2, disabled: true },
        ],
        capacity: 20, remaining: 5,
    });
    await ui.settle();
    assert.equal(ui.ticket.options[2].textContent, 'VIP - 2 left');
    assert.equal(ui.ticket.options[2].disabled, false);
});

test('refresh failures warn about stale counts and recover, including when returning to the tab', async () => {
    const ui = render({
        tickets: [{ id: 1, name: 'Open admission', capacity: null, remaining: null }],
        capacity: null, remaining: null, single: true,
    });
    await ui.settle();
    assert.equal(ui.count.children[0].textContent, 'Available');
    assert.equal(ui.count.children[1].textContent, 'Capacity not specified');
    ui.respond({ ok: false, status: 503 });
    await ui.tick();
    assert.match(ui.error.textContent, /out of date/);
    assert.equal(ui.errors.length, 1);
    assert.equal(ui.count.children[0].textContent, 'Available');
    ui.respond({ ok: true, json: async () => ({ success: true, data: ui.data }) });
    await ui.tick();
    assert.equal(ui.error.textContent, '');
    ui.document.hidden = true;
    const count = ui.calls.length;
    await ui.tick();
    assert.equal(ui.calls.length, count);
    ui.document.hidden = false;
    ui.document.handlers.visibilitychange();
    await ui.settle();
    assert.equal(ui.calls.length, count + 1);
});

test('changed ticket types and reopened sold-out forms require a refresh without altering the form', async () => {
    const ticket = { id: 1, name: 'Delegate', capacity: 10, remaining: 5, maintenance: false };
    const ui = render({ tickets: [ticket], capacity: 10, remaining: 5, single: true });
    await ui.settle();
    ui.respond({ ok: true, json: async () => ({ success: true, data: {
        capacity: 20, remaining: 15, tickets: [ticket, { id: 2, name: 'New', capacity: 10, remaining: 10, maintenance: false }],
    } }) });
    await ui.tick();
    assert.match(ui.error.textContent, /Refresh this page/);
    assert.equal(ui.form.dataset.availabilityReady, 'false');
    assert.equal(ui.ticket.value, 'Delegate');

    const reopened = render({ tickets: [ticket], capacity: 10, remaining: 5, single: true, soldOut: true });
    await reopened.settle();
    assert.match(reopened.error.textContent, /Refresh this page/);
    assert.equal(reopened.form.classList.contains('cer-sold-out'), true);
});

test('invalid live data does not replace the last valid counts', async () => {
    const ui = render({
        tickets: [{ id: 1, name: 'Delegate', capacity: 10, remaining: 5 }],
        capacity: 10, remaining: 5, single: true,
    });
    await ui.settle();
    ui.respond({ ok: true, json: async () => ({ success: true, data: { ...ui.data, remaining: 'unknown' } }) });
    await ui.tick();
    assert.match(ui.error.textContent, /out of date/);
    assert.equal(ui.count.children[0].textContent, 'Available');
});

test('remaining counts appear at or below 25% and hide again after inventory recovers', async () => {
    for (const [capacity, remaining, label] of [
        [100, 26, 'Available'], [100, 25, '25 left'], [100, 24, '24 left'],
        [100, 0, 'Sold out'], [10, 3, 'Available'], [10, 2, '2 left'],
        [null, 5, 'Available'],
    ]) {
        const ui = render({
            tickets: [{ id: 1, name: 'Delegate', capacity, remaining }],
            capacity, remaining, single: true,
        });
        await ui.settle();
        assert.equal(ui.count.children[0].textContent, label);
    }
    const ui = render({
        tickets: [{ id: 1, name: 'Delegate', capacity: 100, remaining: 26 }],
        capacity: 100, remaining: 26, single: true,
    });
    await ui.settle();
    for (const remaining of [25, 24, 26]) {
        ui.respond({ ok: true, json: async () => ({ success: true, data: {
            ...ui.data, remaining, tickets: [{ ...ui.data.tickets[0], remaining }],
        } }) });
        await ui.tick();
        assert.equal(ui.count.children[0].textContent, remaining <= 25 ? `${remaining} left` : 'Available');
    }
});

test('each option uses its own capacity, not the combined event capacity', async () => {
    const ui = render({
        tickets: [
            { id: 1, name: 'Regular', capacity: 100, remaining: 25 },
            { id: 2, name: 'VIP', capacity: 20, remaining: 6 },
            { id: 3, name: 'Early', capacity: 20, remaining: 5 },
            { id: 4, name: 'Reopened', capacity: 100, remaining: 26, disabled: true },
        ],
        capacity: 240, remaining: 62,
    });
    await ui.settle();
    assert.equal(ui.ticket.options[1].textContent, 'Regular - 25 left');
    assert.equal(ui.ticket.options[2].textContent, 'VIP');
    assert.equal(ui.ticket.options[3].textContent, 'Early - 5 left');
    assert.equal(ui.ticket.options[4].textContent, 'Reopened');
});
