'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

class Element {
    constructor() {
        this.children = [];
        this.listeners = {};
        this.value = '';
        this.textContent = '';
        this.hidden = false;
        this.disabled = false;
        this.checked = false;
        this.options = [];
        this.dataset = {};
    }
    addEventListener(name, callback) { this.listeners[name] = callback; }
    setAttribute() {}
    replaceChildren(...children) { this.children = children; }
    append(...children) { this.children.push(...children); }
    after(child) { this.afterElement = child; }
    removeAttribute(name) { delete this[name]; }
    focus() { this.focused = true; }
}
const ids = {};
[
    'cer-mail-picker', 'cer-mail-recipients', 'cer-mail-all', 'cer-mail-picker-enhanced',
    'cer-mail-recipient-json', 'cer-mail-search', 'cer-mail-search-results',
    'cer-mail-selected', 'cer-mail-selected-count', 'cer-mail-added-addresses',
    'cer-mail-add-addresses', 'cer-mail-address-error', 'cer-event-mail-form'
].forEach(id => { ids[id] = new Element(); });
ids['cer-mail-added-addresses'].name = 'cer_mail_added_addresses';
ids['cer-mail-address-error'].dataset.invalid = 'Invalid email';
ids['cer-mail-recipients'].options = [
    { value: '1', textContent: '#1 Alice <alice@example.test> (confirmed)', selected: false },
    { value: '2', textContent: '#2 Bob <bob@example.test> (paid)', selected: false }
];
const document = {
    getElementById: id => ids[id] || null,
    querySelectorAll: () => [],
    createElement: () => new Element()
};
const window = { cerAdminListStrings: {
    mailSearchHint: 'Type to find', mailSearchNone: 'No matches', mailSearchMore: 'More matches',
    mailAdd: 'Add', mailRemove: 'Remove', mailSelected: 'selected', mailEmpty: 'None selected'
} };
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'cer-admin-list-controls.js'), 'utf8'), {
    window, document, console
});
const search = ids['cer-mail-search'];
const results = ids['cer-mail-search-results'];
const selected = ids['cer-mail-selected'];
const json = ids['cer-mail-recipient-json'];
assert.strictEqual(ids['cer-mail-recipients'].disabled, true);
assert.strictEqual(json.value, '[]');
search.value = 'ALICE';
search.listeners.input();
assert.strictEqual(results.children.length, 1);
results.children[0].children[1].listeners.click();
assert.strictEqual(json.value, '[1]');
assert.strictEqual(selected.children[0].children[0].textContent, 'alice@example.test');
assert.strictEqual(selected.children[0].children[1].textContent, '\u00d7');
search.value = 'bob@';
search.listeners.input();
results.children[0].children[1].listeners.click();
assert.strictEqual(json.value, '[1,2]');
selected.children[0].children[1].listeners.click();
assert.strictEqual(json.value, '[2]');
ids['cer-mail-all'].checked = true;
ids['cer-mail-all'].listeners.change();
assert.strictEqual(json.disabled, true);
assert.strictEqual(ids['cer-mail-picker-enhanced'].hidden, true);
assert.strictEqual(selected.children.length, 2, 'Send-to-all must show the same unified recipient chips.');
ids['cer-mail-all'].checked = false;
ids['cer-mail-all'].listeners.change();
assert.strictEqual(json.disabled, false);
assert.strictEqual(json.value, '[2]', 'Selection must survive toggling all recipients.');
const added = ids['cer-mail-added-addresses'];
added.value = 'one@example.test\nTWO@example.test\nONE@example.test\nBOB@example.test';
ids['cer-mail-add-addresses'].listeners.click();
assert.strictEqual(selected.children.length, 3, 'Registered and added addresses must render in one case-insensitively deduplicated list.');
assert.strictEqual(added.afterElement.value, 'one@example.test\nTWO@example.test\nBOB@example.test');
selected.children[0].children[1].listeners.click();
assert.strictEqual(json.value, '[]', 'Removing a deduplicated chip removes its existing registration selection.');
assert.strictEqual(added.afterElement.value, 'one@example.test\nTWO@example.test', 'Removing a chip also removes its duplicate added address.');
selected.children[0].children[1].listeners.click();
assert.strictEqual(added.afterElement.value, 'TWO@example.test');
ids['cer-mail-all'].checked = true;
ids['cer-mail-all'].listeners.change();
selected.children[0].children[1].listeners.click();
assert.strictEqual(ids['cer-mail-all'].checked, false, 'Removing a chip must switch all recipients to an explicit selection.');
assert.strictEqual(json.disabled, false);
assert.strictEqual(json.value, '[2]', 'All other registered recipients remain selected.');
added.value = 'bad';
let prevented = false;
let editorSaved = 0;
window.tinymce = {
    get(id) {
        assert.strictEqual(id, 'cer_mail_body', 'Only the mail editor should be saved.');
        return { save() { editorSaved++; } };
    }
};
ids['cer-event-mail-form'].listeners.submit({ preventDefault() { prevented = true; } });
assert.strictEqual(editorSaved, 1, 'Submitting the form must sync the editor without relying on a button click.');
assert.strictEqual(prevented, true);
assert.strictEqual(ids['cer-mail-address-error'].textContent, 'Invalid email');
added.value = '';
prevented = false;
ids['cer-event-mail-form'].listeners.submit({ preventDefault() { prevented = true; } });
assert.strictEqual(editorSaved, 2);
assert.strictEqual(prevented, false, 'Valid submission must retain the composed message.');
delete window.tinymce;
ids['cer-event-mail-form'].listeners.submit({ preventDefault() { throw new Error('Text-mode submission must work without TinyMCE.'); } });
console.log('Admin mail recipient picker tests passed.');
