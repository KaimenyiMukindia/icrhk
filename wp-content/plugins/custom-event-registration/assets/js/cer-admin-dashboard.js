(function () {
	'use strict';

	const trigger = document.querySelector('.cer-mobile-radial-trigger');
	const menu = document.getElementById('cer-mobile-radial-menu');
	const triggerIcon = trigger ? trigger.querySelector('.dashicons') : null;

	if (!trigger || !menu) {
		return;
	}

	const closeMenu = (returnFocus) => {
		menu.hidden = true;
		trigger.setAttribute('aria-expanded', 'false');
		trigger.setAttribute('aria-label', trigger.dataset.openLabel);
		trigger.title = trigger.dataset.openLabel;
		if (triggerIcon) {
			triggerIcon.classList.remove('dashicons-no-alt');
			triggerIcon.classList.add('dashicons-plus');
		}
		if (returnFocus) {
			trigger.focus();
		}
	};

	const openMenu = () => {
		menu.hidden = false;
		trigger.setAttribute('aria-expanded', 'true');
		trigger.setAttribute('aria-label', trigger.dataset.closeLabel);
		trigger.title = trigger.dataset.closeLabel;
		if (triggerIcon) {
			triggerIcon.classList.remove('dashicons-plus');
			triggerIcon.classList.add('dashicons-no-alt');
		}
		const firstLink = menu.querySelector('a');
		if (firstLink) {
			firstLink.focus();
		}
	};

	trigger.dataset.openLabel = trigger.getAttribute('aria-label');
	trigger.dataset.closeLabel = trigger.dataset.closeLabel || 'Close dashboard quick actions';

	trigger.addEventListener('click', () => {
		if (menu.hidden) {
			openMenu();
		} else {
			closeMenu(false);
		}
	});

	menu.addEventListener('click', (event) => {
		if (event.target.closest('a')) {
			closeMenu(false);
		}
	});

	document.addEventListener('click', (event) => {
		if (!menu.hidden && !menu.contains(event.target) && !trigger.contains(event.target)) {
			closeMenu(false);
		}
	});

	document.addEventListener('keydown', (event) => {
		if ('Escape' === event.key && !menu.hidden) {
			closeMenu(true);
		}
	});
})();
