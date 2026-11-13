const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-registration.js'), 'utf8');

function render({ event = true, header = true, elements = [], resizeObserver = true, hash = '', target = null } = {}) {
    const properties = new Map();
    const events = new Map();
    const observed = [];
    const frames = [];
    const eventPage = {
        style: { setProperty: (name, value) => properties.set(name, value) },
        contains: (element) => element === target,
    };
    const headerElement = { querySelectorAll: () => elements };
    const document = {
        getElementById: (id) => id === hash.slice(1) ? target : null,
        querySelector: (selector) => {
            if (selector === '.cer-event-page') return event ? eventPage : null;
            if (selector === '.elementor-location-header') return header ? headerElement : null;
            return null;
        },
        querySelectorAll: () => [],
        addEventListener: () => {},
    };
    const window = {
        location: { hash },
        innerHeight: 900,
        getComputedStyle: (element) => element.style,
        addEventListener: (name, callback) => events.set(name, callback),
        removeEventListener: (name, callback) => {
            if (events.get(name) === callback) events.delete(name);
        },
        requestAnimationFrame: (callback) => {
            frames.push(callback);
            return frames.length;
        },
    };
    if (resizeObserver) {
        window.ResizeObserver = class {
            constructor(callback) { events.set('observe', callback); }
            observe(element) { observed.push(element); }
        };
    }
    vm.runInNewContext(source, { document, window });
    return {
        properties,
        events,
        observed,
        runFrames: () => {
            while (frames.length) frames.shift()();
        },
    };
}

function element(position, bottom, { visibility = 'visible', display = 'block', height = 50 } = {}) {
    return {
        style: { position, visibility, display },
        getBoundingClientRect: () => ({ bottom, height }),
    };
}

test('uses the rounded-up bottom of the visible fixed header', () => {
    for (const [bottom, expected] of [[98.27, '99px'], [89.47, '90px'], [76, '76px']]) {
        const result = render({ elements: [element('fixed', bottom)] });
        assert.equal(result.properties.get('--cer-header-offset'), expected);
    }
});

test('ignores hidden, collapsed and non-sticky elements', () => {
    const result = render({ elements: [
        element('fixed', 98.27),
        element('fixed', 400, { visibility: 'hidden' }),
        element('fixed', 500, { display: 'none' }),
        element('fixed', 600, { height: 0 }),
        element('relative', 700),
    ] });
    assert.equal(result.properties.get('--cer-header-offset'), '99px');
});

test('remeasures after load, scroll, resize and header resize', () => {
    const sticky = element('fixed', 130);
    const result = render({ elements: [sticky] });
    sticky.getBoundingClientRect = () => ({ bottom: 89.47, height: 50 });
    result.events.get('load')();
    assert.equal(result.properties.get('--cer-header-offset'), '90px');
    for (const event of ['scroll', 'resize', 'observe']) {
        sticky.getBoundingClientRect = () => ({ bottom: 75.1, height: 50 });
        result.events.get(event)();
        result.runFrames();
        assert.equal(result.properties.get('--cer-header-offset'), '76px');
        sticky.getBoundingClientRect = () => ({ bottom: 89.47, height: 50 });
        result.events.get(event)();
        result.runFrames();
        assert.equal(result.properties.get('--cer-header-offset'), '90px');
    }
    assert.equal(result.observed.length, 2);
});

test('works without ResizeObserver and clamps offscreen headers', () => {
    const result = render({ elements: [element('sticky', -50)], resizeObserver: false });
    assert.equal(result.properties.get('--cer-header-offset'), '0px');
    assert.ok(result.events.has('scroll'));
});

test('realigns a section fragment initially hidden by the fixed header', () => {
    const calls = [];
    const target = {
        tagName: 'SECTION',
        getBoundingClientRect: () => ({ top: 16 }),
        scrollIntoView: (options) => calls.push(options),
    };
    const result = render({ elements: [element('fixed', 98.27)], hash: '#registration', target });
    result.events.get('load')();
    assert.equal(calls.length, 1);
    assert.equal(calls[0].block, 'start');
    assert.equal(calls[0].behavior, 'instant');
    assert.equal(result.events.has('wheel'), false);
});

test('does not realign a fragment after user interaction', () => {
    let calls = 0;
    const target = {
        tagName: 'SECTION',
        getBoundingClientRect: () => ({ top: 16 }),
        scrollIntoView: () => { calls++; },
    };
    const result = render({ elements: [element('fixed', 98.27)], hash: '#registration', target });
    result.events.get('wheel')();
    result.events.get('load')();
    assert.equal(calls, 0);
});

test('brings an initially offscreen section fragment into view', () => {
    let calls = 0;
    const target = {
        tagName: 'SECTION',
        getBoundingClientRect: () => ({ top: 3000 }),
        scrollIntoView: () => { calls++; },
    };
    const result = render({ elements: [element('fixed', 98.27)], hash: '#registration', target });
    result.events.get('load')();
    assert.equal(calls, 1);
});

test('realigns a section even while native smooth scrolling is still in progress', () => {
    let calls = 0;
    const target = {
        tagName: 'SECTION',
        getBoundingClientRect: () => ({ top: 200 }),
        scrollIntoView: () => { calls++; },
    };
    const result = render({ elements: [element('fixed', 98.27)], hash: '#registration', target });
    result.events.get('load')();
    assert.equal(calls, 1);
});

test('does not run without an event page and Elementor header', () => {
    for (const options of [{ event: false }, { header: false }]) {
        const result = render(options);
        assert.equal(result.properties.size, 0);
        assert.equal(result.events.size, 0);
    }
});
