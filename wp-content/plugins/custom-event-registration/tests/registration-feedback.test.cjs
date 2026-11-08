const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-registration.js'), 'utf8')
    .split('})(document, window);')[0] + '})(document, window);';

function render({ result, method = 'mpesa', requestError, verification, verificationError, popup = true } = {}) {
    const elements = new Map();
    function element(id, value = '') {
        const handlers = new Map();
        const attributes = new Map();
        const classes = new Set();
        const node = {
            value, textContent: '', className: '', disabled: false, handlers, attributes,
            addEventListener: (event, callback) => handlers.set(event, callback),
            setAttribute: (name, value) => attributes.set(name, value),
            checkValidity: () => true,
            closest: () => ({ style: {} }),
            classList: { toggle: (name, enabled) => enabled ? classes.add(name) : classes.delete(name) },
            classes,
        };
        elements.set(id, node);
        return node;
    }
    const button = element('submit');
    const form = element('cer-registration-form');
    form.querySelector = () => button;
    form.querySelectorAll = () => [];
    const message = element('cer-form-message');
    const ticket = element('cer-ticket-type', 'Delegate');
    ticket.selectedIndex = 0;
    ticket.options = [{ dataset: { price: '100', id: '1' } }];
    element('cer-ticket-type-id');
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
        message, button, calls, timers, errors,
        submit: async () => { form.handlers.get('submit')({ preventDefault() {} }); await settle(); },
        tick: async () => { timers.get(1)(); await settle(); },
    };
}

test('unpriced ticket confirms contact capture without checkout or polling', async () => {
    const ui = render({ result: { success: true, data: {
        payment_status: 'not_on_sale', message: 'Tickets for this event are not on sale yet. We have your contact details and will get in touch.',
    } } });
    await ui.submit();
    assert.match(ui.message.textContent, /not on sale yet/);
    assert.match(ui.message.className, /success/);
    assert.equal(ui.message.attributes.get('aria-busy'), 'false');
    assert.deepEqual(ui.calls, ['register']);
    assert.equal(ui.timers.size, 0);
    assert.equal(ui.button.disabled, false);
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

test('sold-out tickets and failed contact saves provide a next step', async () => {
    for (const [message, expected] of [
        ['This ticket type is sold out.', /choose another available ticket/],
        ['This event has reached its attendance capacity.', /contact the event team/],
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
