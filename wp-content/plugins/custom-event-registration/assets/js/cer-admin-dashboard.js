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

(function () {
	'use strict';

	const config = window.cerDashboardMetrics;
	const dashboard = document.querySelector('.cer-dashboard');
	const groups = dashboard && dashboard.querySelector('.cer-metric-groups');
	const status = dashboard && dashboard.querySelector('.cer-dashboard-metrics-status');
	const label = dashboard && dashboard.querySelector('.cer-metrics-event');
	if (!config || !groups || !status || !label || !window.fetch || !window.AbortController || !window.history.pushState) {
		return;
	}

	let controller;
	let sequence = 0;
	const metricNames = ['total_registrations', 'tickets_sold', 'tickets_available', 'revenue_collected', 'sponsorship_packages'];

	function setStatus(message) {
		status.textContent = message;
		status.hidden = !message;
	}

	function showMetrics(link, updateHistory) {
		const request = ++sequence;
		if (controller) {
			controller.abort();
		}
		controller = new AbortController();
		groups.setAttribute('aria-busy', 'true');
		setStatus(config.loading);

		const body = new URLSearchParams({
			action: 'cer_dashboard_event_metrics',
			nonce: config.nonce,
			event_id: link.dataset.eventId
		});
		fetch(config.url, {
			method: 'POST',
			credentials: 'same-origin',
			cache: 'no-store',
			body: body,
			signal: controller.signal
		}).then(function (response) {
			return response.json().then(function (data) {
				if (!response.ok || !data.success) {
					throw new Error(data.data && data.data.message ? data.data.message : config.error);
				}
				return data.data;
			});
		}).then(function (data) {
			if (request !== sequence) {
				return;
			}
			if (String(data.event_id) !== link.dataset.eventId || !data.metrics ||
				!metricNames.every(function (name) { return typeof data.metrics[name] === 'string'; })) {
				throw new Error(config.error);
			}
			metricNames.forEach(function (name) {
				groups.querySelector('[data-cer-metric="' + name + '"]').textContent = data.metrics[name];
			});
			label.textContent = data.event_label;
			dashboard.querySelectorAll('.cer-latest-event-select').forEach(function (item) {
				const selected = item === link;
				item.closest('li').classList.toggle('is-selected', selected);
				if (selected) {
					item.setAttribute('aria-current', 'true');
				} else {
					item.removeAttribute('aria-current');
				}
			});
			dashboard.querySelectorAll('.cer-recent-registrations .cer-list-pagination a').forEach(function (item) {
				const url = new URL(item.href, window.location.href);
				url.searchParams.set('event_id', data.event_id);
				item.href = url.toString();
			});
			if (updateHistory && window.location.href !== link.href) {
				window.history.pushState(null, '', link.href);
			}
			groups.setAttribute('aria-busy', 'false');
			setStatus(config.updated);
		}).catch(function (error) {
			if (request !== sequence || error.name === 'AbortError') {
				return;
			}
			groups.setAttribute('aria-busy', 'false');
			if (!updateHistory) {
				window.location.reload();
				return;
			}
			setStatus(error.message || config.error);
		});
	}

	dashboard.addEventListener('click', function (event) {
		const link = event.target.closest('.cer-latest-event-select');
		const current = new URL(window.location.href);
		if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey ||
			event.shiftKey || event.altKey || Number(current.searchParams.get('paged') || 1) > 1) {
			return;
		}
		event.preventDefault();
		showMetrics(link, true);
	});

	window.addEventListener('popstate', function () {
		const url = new URL(window.location.href);
		const id = url.searchParams.get('event_id');
		const link = Array.from(dashboard.querySelectorAll('.cer-latest-event-select')).find(function (item) {
			return item.dataset.eventId === id;
		});
		if (!link || Number(url.searchParams.get('paged') || 1) > 1) {
			window.location.reload();
			return;
		}
		showMetrics(link, false);
	});
})();
