'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

class Element {
    constructor() {
        this.value = '';
        this.hidden = false;
        this.textContent = '';
        this.listeners = {};
        this.options = [];
    }
    addEventListener(name, callback) { this.listeners[name] = callback; }
    replaceChildren(...options) {
        this.options = options;
        this.value = options.length ? options[0].value : '';
    }
    add(option) { this.options.push(option); }
}

const event = new Element();
const ticket = new Element();
const field = new Element();
const status = new Element();
const elements = {
    '#cer-assign-event': event,
    '#cer-assign-ticket': ticket,
    '#cer-assign-ticket-field': field,
    '#cer-assign-availability': status
};
const form = { dataset: { availabilityUrl: '/wp-admin/admin-ajax.php' }, querySelector: key => elements[key] };
const requests = [];
let timer;
const strings = {
    assignSelectEvent: 'Select an event', assignLoading: 'Loading', assignFailed: 'Failed',
    assignSelectTicket: 'Select ticket', assignNoTickets: 'No tickets', assignUnlimited: 'Unlimited',
    assignLeft: 'left', assignMaintenance: 'Maintenance', assignSoldOut: 'Sold out'
};
const document = {
    hidden: false,
    getElementById: id => id === 'cer-assignment-form' ? form : id === 'cer-assign-inventory' ? {
        textContent: JSON.stringify({ 11: { availability: { tickets: [{ id: 110, name: 'Delegate', remaining: 7, maintenance: false, price: '500' }], remaining: 7 } } })
    } : null,
    querySelectorAll: () => []
};
const window = {
    cerAdminListStrings: strings,
    location: { href: 'http://localhost/wp-admin/admin.php' },
    fetch: url => new Promise(resolve => requests.push({ url, resolve })),
    setInterval: fn => { timer = fn; }
};
function Option(text, value) { this.textContent = text; this.value = value; this.disabled = false; }
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-admin-list-controls.js'), 'utf8'), {
    window, document, fetch: window.fetch, URL, Option, console
});
const flush = () => new Promise(resolve => setImmediate(resolve));
const respond = (index, tickets, remaining) => requests[index].resolve({
    ok: true, json: () => Promise.resolve({ success: true, data: { tickets, remaining } })
});

(async () => {
    event.value = '10';
    event.listeners.change();
    assert.match(requests[0].url, /event_id=10/);
    event.value = '11';
    event.listeners.change();
    assert.strictEqual(ticket.value, '110', 'Preloaded event details must be selectable immediately, before the live request returns.');
    respond(0, [{ id: 100, name: 'Wrong event', remaining: 5 }], 5);
    await flush();
    assert.strictEqual(ticket.options[1].value, '110', 'An out-of-order response must not expose another event’s tickets.');
    respond(1, [{ id: 110, name: 'Delegate', remaining: 7, maintenance: false }], 7);
    await flush();
    assert.strictEqual(ticket.value, '110');
    assert.strictEqual(field.hidden, true, 'An available sole ticket should be selected without a selector.');
    assert.match(status.textContent, /Delegate.*7 left/);
    timer();
    respond(2, [{ id: 110, name: 'Delegate', remaining: 0, maintenance: false }], 0);
    await flush();
    assert.strictEqual(field.hidden, false);
    assert.strictEqual(ticket.value, '');
    assert.strictEqual(ticket.options[1].disabled, true, 'Sold-out inventory must not be selectable.');
    event.value = '12';
    event.listeners.change();
    respond(3, [
        { id: 120, name: 'Student', remaining: 2, maintenance: false },
        { id: 121, name: 'Standard', remaining: null, maintenance: false }
    ], 2);
    await flush();
    assert.strictEqual(field.hidden, false);
    assert.strictEqual(ticket.options.length, 3);
    assert.match(ticket.options[1].textContent, /Student.*2 left/);
    console.log('Admin assignment inventory tests passed.');
})().catch(error => { console.error(error); process.exitCode = 1; });
