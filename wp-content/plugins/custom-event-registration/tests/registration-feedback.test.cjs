const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-registration.js'), 'utf8')
    .split('})(document, window);')[0] + '})(document, window);';

function render({ result, method = 'mpesa', requestError, verification, verificationError, popup = true,
    tickets = [{ name: 'Delegate', price: '100', id: '1' }], single = false, selectedIndex = 0,
    initialSoldOut = false, initialSummaryTicket = '', initialAvailability = '' } = {}) {
    const elements = new Map();
    function element(id, value = '') {
        const handlers = new Map();
        const attributes = new Map();
        const classes = new Set();
        const node = {
            value, textContent: '', innerHTML: '', className: '', disabled: false, handlers, attributes, dataset: {},
            addEventListener: (event, callback) => handlers.set(event, callback),
            setAttribute: (name, value) => attributes.set(name, value),
            checkValidity: () => true,
            closest: () => ({ style: {} }),
            classList: { toggle: (name, enabled) => enabled ? classes.add(name) : classes.delete(name), add: (name) => classes.add(name), contains: (name) => classes.has(name) },
            classes,
        };
        elements.set(id, node);
        return node;
    }
    const button = element('submit');
    const form = element('cer-registration-form');
    form.dataset = {
        paymentRequired: tickets.some(ticket => Number(ticket.price) > 0) ? 'true' : 'false',
        paymentNote: 'Your registration is secure. Payment is processed through our secure gateway.',
        contactNote: 'Your contact details will be saved securely. No payment is required to submit this form.',
        soldOutNote: 'Tickets for this event are sold out. Please contact the event team about future availability.',
        ticketCount: String(tickets.length),
    };
    if (initialSoldOut) form.classes.add('cer-sold-out');
    form.querySelector = () => button;
    form.querySelectorAll = () => [];
    const message = element('cer-form-message');
    const ticket = element('cer-ticket-type', tickets[selectedIndex] ? tickets[selectedIndex].name : '');
    if (single) {
        ticket.dataset = { price: tickets[0].price, id: tickets[0].id };
    } else {
        ticket.selectedIndex = selectedIndex;
        ticket.options = tickets.map(option => ({ value: option.name, textContent: option.display || option.name, disabled: !!option.soldOut, dataset: { price: option.price, id: option.id, baseLabel: option.baseLabel } }));
    }
    const ticketId = element('cer-ticket-type-id');
    const amount = element('cer-amount');
    const summaryTicket = initialSummaryTicket ? element('cer-summary-ticket') : null;
    if (summaryTicket) summaryTicket.textContent = initialSummaryTicket;
    const availabilityLabel = initialAvailability ? { textContent: initialAvailability } : null;
    if (availabilityLabel) element('cer-ticket-availability').querySelector = () => availabilityLabel;
    element('payment_method_hidden', method);
    element('cer-full-name', 'Test Visitor');
    element('cer-email', 'test@example.com');
    element('cer-phone', '0712345678');
    const timers = new Map();
    const calls = [];
    const errors = [];
    const window = {
        cerRegistrationSettings: { nonce: 'test', ajax_url: 'register', payment_verify_url: 'verify' },
        setInterval: (callback) => { timers.set(1, callback); return 1; },
        clearInterval: (id) => timers.delete(id),
    };
    if (popup) {
        window.PaystackPop = class { resumeTransaction(code) { calls.push(code); } };
    }
    vm.runInNewContext(source, {
        document: { getElementById: (id) => elements.get(id) || null },
        window,
        FormData: class { set() {} },
        console: { error: (...args) => errors.push(args) },
        fetch: async (endpoint) => {
            calls.push(endpoint);
            if (endpoint === 'register') {
                if (requestError) throw requestError;
                return { ok: true, json: async () => result };
            }
            if (verificationError) throw verificationError;
            return { ok: true, json: async () => verification };
        },
    });
    const settle = () => new Promise((resolve) => setImmediate(resolve));
    return {
        message, button, calls, timers, errors, form, ticketId, amount, elements, ticket, summaryTicket, availabilityLabel,
        select: (index) => {
            ticket.selectedIndex = index;
            ticket.value = tickets[index].name;
            ticket.handlers.get('change')();
        },
        submit: async () => { form.handlers.get('submit')({ preventDefault() {} }); await settle(); },
        tick: async () => { timers.get(1)(); await settle(); },
    };
}

test('a sold-out single ticket keeps its name in the ticket information summary', () => {
    const ui = render({
        tickets: [{ name: 'Delegate', price: '0', id: '1', soldOut: true }],
        selectedIndex: -1, initialSoldOut: true, initialSummaryTicket: 'Delegate',
    });
    assert.equal(ui.summaryTicket.textContent, 'Delegate');
});

test('no-cost ticket confirms admission without checkout or polling', async () => {
    const ui = render({ tickets: [{ name: 'Delegate', price: '0', id: '1' }], single: true, result: { success: true, data: {
        payment_status: 'not_required', ticket_sent: true, message: 'Thank you for registering! Your ticket has been emailed to you.',
    } } });
    await ui.submit();
    assert.match(ui.message.textContent, /Thank you/);
    assert.match(ui.message.className, /success/);
    assert.equal(ui.message.attributes.get('aria-busy'), 'false');
    assert.deepEqual(ui.calls, ['register']);
    assert.equal(ui.timers.size, 0);
    assert.equal(ui.button.disabled, false);
});

test('no-cost ticket email failure is a warning without checkout or a second registration', async () => {
    const ui = render({ single: true, tickets: [{ name: 'Delegate', price: '0', id: '1' }], result: {
        success: true, data: { payment_status: 'not_required', ticket_sent: false,
            message: 'Thank you for registering! We will retry automatically; please do not register again.' },
    } });
    await ui.submit();
    assert.match(ui.message.className, /warning/);
    assert.match(ui.message.textContent, /retry automatically/);
    assert.deepEqual(ui.calls, ['register']);
    assert.equal(ui.timers.size, 0);
});

test('duplicate no-cost registration displays the server guidance', async () => {
    const message = 'You are already registered for this ticket. Please check your email for your ticket or contact the event team for help.';
    const ui = render({ single: true, tickets: [{ name: 'Delegate', price: '0', id: '1' }],
        result: { success: false, data: { message } } });
    await ui.submit();
    assert.equal(ui.message.textContent, message);
    assert.match(ui.message.className, /error/);
    assert.deepEqual(ui.calls, ['register']);
});

for (const price of ['', '0', null]) {
    test(`single ticket with ${JSON.stringify(price)} price auto-selects without payment UI`, async () => {
        const ui = render({ single: true, tickets: [{ name: 'Delegate', price, id: '9' }], method: 'card' });
        assert.equal(ui.ticketId.value, '9');
        assert.equal(ui.amount.value, '0.00');
        assert.equal(ui.form.dataset.paymentRequired, 'false');
        assert.equal(ui.button.textContent, 'Register');
        assert.equal(ui.elements.get('payment_method_hidden').value, 'mpesa');
        assert.equal(ui.elements.get('cer-full-name').required, true);
        assert.match(ui.message.innerHTML, /No payment is required/);
        assert.equal(ui.button.disabled, false);
    });
}

for (const method of ['mpesa', 'card']) {
    test(`single priced ticket retains ${method} checkout without needing a select element`, async () => {
        const ui = render({ single: true, method,
            result: { success: true, data: { reference: 'ref', access_code: 'code' } } });
        assert.equal(ui.ticketId.value, '1');
        assert.equal(ui.amount.value, '100.00');
        assert.equal(ui.form.dataset.paymentRequired, 'true');
        assert.equal(ui.button.textContent, method === 'card' ? 'Pay with Card' : 'Register & Pay');
        await ui.submit();
        assert.equal(ui.timers.size, 1);
        assert.equal(ui.calls.includes('code'), method === 'card');
    });
}

test('mixed tickets alternate payment UI, contact fields and idle copy with selection', () => {
    const ui = render({ method: 'card', tickets: [
        { name: 'Paid', price: '100', id: '1' },
        { name: 'Contact registration', price: '0', id: '2' },
    ] });
    ui.select(1);
    assert.equal(ui.form.dataset.paymentRequired, 'false');
    assert.equal(ui.button.textContent, 'Register');
    assert.equal(ui.elements.get('cer-full-name').required, true);
    assert.equal(ui.ticketId.value, '2');
    assert.match(ui.message.innerHTML, /No payment is required/);
    ui.select(0);
    assert.equal(ui.form.dataset.paymentRequired, 'true');
    assert.equal(ui.button.textContent, 'Register & Pay');
    assert.equal(ui.amount.value, '100.00');
    assert.match(ui.message.innerHTML, /secure gateway/);
});

test('registration-only network errors do not mention payments or charges', async () => {
    const ui = render({ single: true, tickets: [{ name: 'Delegate', price: '0', id: '1' }],
        requestError: new Error('Failed to fetch') });
    await ui.submit();
    assert.match(ui.message.textContent, /could not confirm your registration/);
    assert.doesNotMatch(ui.message.textContent, /payment|charged|checkout/);
});

test('technical registration errors never reach the visitor', async () => {
    const ui = render({ result: { success: false, data: {
        message: 'Payment initiation failed: Laravel bridge payment call returned HTTP 404: Not Found',
    } } });
    await ui.submit();
    assert.doesNotMatch(ui.message.textContent, /Laravel|HTTP|404/);
    assert.match(ui.message.textContent, /contact the event team/);
    assert.equal(ui.errors.length, 1);
});

test('validation errors remain actionable', async () => {
    const message = 'Your session has expired. Please refresh this page and submit again.';
    const ui = render({ result: { success: false, data: { message } } });
    await ui.submit();
    assert.equal(ui.message.textContent, message);
});

test('failed contact saves and unavailable tickets provide a next step', async () => {
    for (const [message, expected] of [
        ['The registration could not be saved.', /try again in a moment/],
        ['The selected ticket is no longer available.', /refresh the page/],
    ]) {
        const ui = render({ result: { success: false, data: { message } } });
        await ui.submit();
        assert.match(ui.message.textContent, expected);
    }
});

for (const method of ['mpesa', 'card']) {
    test(`${method} progress alternates to confirmed payment in the same region`, async () => {
        const ui = render({ method, result: { success: true, data: { reference: 'ref', access_code: 'code' } },
            verification: { status: 'success', registration_status: 'paid' } });
        await ui.submit();
        assert.equal(ui.button.disabled, true);
        assert.equal(ui.message.classes.has('cer-form-message-pending'), true);
        assert.equal(ui.message.attributes.get('aria-busy'), 'true');
        await ui.tick();
        assert.match(ui.message.textContent, /Payment confirmed/);
        assert.equal(ui.message.classes.has('cer-form-message-pending'), false);
        assert.equal(ui.button.disabled, false);
        assert.equal(ui.timers.size, 0);
    });
}

for (const status of ['failed', 'abandoned', 'cancelled', 'timeout', 'reversed']) {
    test(`${status} payment stops the spinner with safe retry guidance`, async () => {
        const ui = render({ result: { success: true, data: { reference: 'ref' } }, verification: { status } });
        await ui.submit();
        await ui.tick();
        assert.match(ui.message.textContent, /not completed/);
        assert.match(ui.message.textContent, /do not pay again/);
        assert.equal(ui.timers.size, 0);
        assert.equal(ui.button.disabled, false);
    });
}

test('pending payment times out without claiming failure or encouraging double payment', async () => {
    const ui = render({ result: { success: true, data: { reference: 'ref' } }, verification: { status: 'pending' } });
    await ui.submit();
    for (let i = 0; i < 40; i++) await ui.tick();
    assert.match(ui.message.textContent, /still awaiting confirmation/);
    assert.match(ui.message.className, /warning/);
    assert.equal(ui.message.attributes.get('aria-busy'), 'false');
    assert.equal(ui.timers.size, 0);
});

test('verification outages are logged and end with explicit advice', async () => {
    const ui = render({ result: { success: true, data: { reference: 'ref' } },
        verificationError: new Error('Unexpected token <') });
    await ui.submit();
    for (let i = 0; i < 40; i++) await ui.tick();
    assert.match(ui.message.textContent, /cannot confirm your payment/);
    assert.doesNotMatch(ui.message.textContent, /Unexpected token/);
    assert.equal(ui.errors.length, 40);
    assert.equal(ui.timers.size, 0);
});

test('missing card SDK preserves saved-registration guidance', async () => {
    const ui = render({ method: 'card', popup: false,
        result: { success: true, data: { reference: 'ref', access_code: 'code' } } });
    await ui.submit();
    assert.match(ui.message.textContent, /registration details are saved/);
    assert.doesNotMatch(ui.message.textContent, /Paystack/);
    assert.equal(ui.button.disabled, false);
});

test('network and malformed-response errors do not falsely claim contacts were saved', async () => {
    for (const options of [{ requestError: new Error('Failed to fetch') }, { result: null }]) {
        const ui = render(options);
        await ui.submit();
        assert.match(ui.message.textContent, /could not confirm your submission/);
        assert.doesNotMatch(ui.message.textContent, /details are saved|Failed to fetch/);
        assert.equal(ui.button.disabled, false);
    }
});

test('a ticket selling out mid-checkout is disabled while other tickets stay available', async () => {
    const ui = render({ tickets: [{ name: 'Early', price: '100', id: '1' }, { name: 'Regular', price: '200', id: '2' }],
        result: { success: false, data: { message: 'This ticket type is sold out.' } } });
    await ui.submit();
    assert.equal(ui.ticket.options[0].disabled, true);
    assert.match(ui.ticket.options[0].textContent, /Sold out/);
    assert.equal(ui.ticket.value, '');
    assert.match(ui.message.textContent, /just sold out.*choose another/);
    assert.match(ui.message.className, /warning/);
    assert.equal(ui.form.classes.has('cer-sold-out'), false);
    assert.equal(ui.button.disabled, true);
    ui.select(1);
    assert.equal(ui.button.disabled, false);
});

test('a ticket selling out replaces its stale remaining count', async () => {
    const ui = render({
        tickets: [
            { name: 'Early', price: '100', id: '1', baseLabel: 'Early (KES 100.00)', display: 'Early (KES 100.00) - 1 left' },
            { name: 'Regular', price: '200', id: '2' },
        ],
        result: { success: false, data: { message: 'This ticket type is sold out.' } },
    });
    await ui.submit();
    assert.equal(ui.ticket.options[0].textContent, 'Early (KES 100.00) - Sold out');
});

test('a single ticket selling out immediately updates its availability summary', async () => {
    const ui = render({
        single: true, initialAvailability: '1 left',
        result: { success: false, data: { message: 'This ticket type is sold out.' } },
    });
    await ui.submit();
    assert.equal(ui.availabilityLabel.textContent, 'Sold out');
});

for (const [label, options] of [
    ['last dropdown ticket', { tickets: [{ name: 'Early', price: '100', id: '1' }, { name: 'Regular', price: '200', id: '2', soldOut: true }] }],
    ['single ticket', { single: true }],
]) {
    test(`${label} selling out locks the form without payment UI`, async () => {
        const ui = render({ ...options, result: { success: false, data: { message: 'This ticket type is sold out.' } } });
        await ui.submit();
        assert.equal(ui.form.classes.has('cer-sold-out'), true);
        assert.equal(ui.form.dataset.paymentRequired, 'false');
        assert.equal(ui.button.textContent, 'Sold Out');
        assert.equal(ui.button.disabled, true);
        assert.match(ui.message.textContent, /sold out/);
        assert.doesNotMatch(ui.message.textContent, /choose another/);
    });
}

test('reaching event capacity locks the form as fully booked', async () => {
    const ui = render({ tickets: [{ name: 'Early', price: '100', id: '1' }, { name: 'Regular', price: '0', id: '2' }],
        result: { success: false, data: { message: 'This event has reached its attendance capacity.' } } });
    await ui.submit();
    assert.equal(ui.form.classes.has('cer-sold-out'), true);
    assert.match(ui.message.textContent, /fully booked/);
    assert.equal(ui.button.disabled, true);
    ui.select(1);
    assert.equal(ui.button.disabled, true);
    assert.equal(ui.button.textContent, 'Sold Out');
});