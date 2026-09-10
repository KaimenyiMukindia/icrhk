(function (document, window) {
    var form = document.getElementById('cer-registration-form');
    var messageBox = document.getElementById('cer-form-message');
    var ticketType = document.getElementById('cer-ticket-type');
    var ticketTypeId = document.getElementById('cer-ticket-type-id');
    var amountInput = document.getElementById('cer-amount');
    var summaryTicket = document.getElementById('cer-summary-ticket');
    var summaryAmount = document.getElementById('cer-summary-amount');
    var summaryMethod = document.getElementById('cer-summary-method');
    var paymentMethodHidden = document.getElementById('payment_method_hidden');
    var fullNameField = document.getElementById('cer-full-name');
    var nameFieldContainer = fullNameField ? fullNameField.closest('.cer-field-group') : null;
    var btnMpesa = document.getElementById('btn-mpesa');
    var btnCard = document.getElementById('btn-card');
    var submitButton = form ? form.querySelector('button[type="submit"]') : null;
    var requestInFlight = false;
    var paystackHandler = null;
    var supportsViewTransitions = typeof document.startViewTransition === 'function';

    var currency = (window.cerRegistrationSettings && window.cerRegistrationSettings.currency) || 'KES';

    function runUiTransition(callback) {
        if (supportsViewTransitions) {
            document.startViewTransition(callback);
            return;
        }
        callback();
    }

    function getPaystackHandler() {
        if (typeof window.PaystackPop !== 'function') {
            return null;
        }
        if (!paystackHandler) {
            paystackHandler = new window.PaystackPop();
        }
        return paystackHandler;
    }

    function safeText(text) {
        return String(text || '').trim();
    }

    function formatCurrency(amountValue) {
        var parsed = parseFloat(amountValue || 0);
        if (Number.isNaN(parsed)) {
            parsed = 0;
        }

        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: currency
        }).format(parsed);
    }

    function setMessage(text, state) {
        if (!messageBox) {
            return;
        }

        messageBox.textContent = text;
        messageBox.className = 'cer-form-message cer-form-message-' + state;
    }

    function setPaymentMethod(method) {
        var isCard = method === 'card';
        if (form) {
            form.classList.toggle('cer-card-selected', isCard);
            form.setAttribute('data-payment-method', isCard ? 'card' : 'mpesa');
        }
        if (paymentMethodHidden) {
            paymentMethodHidden.value = method;
        }

        if (btnMpesa && btnCard) {
            btnMpesa.classList.toggle('is-active', method === 'mpesa');
            btnCard.classList.toggle('is-active', method === 'card');
        }

        // Card never collects a name: Paystack supplies it (if any) from the charge itself.
        if (fullNameField && nameFieldContainer) {
            fullNameField.required = !isCard;
            nameFieldContainer.style.display = isCard ? 'none' : '';
            if (isCard) {
                fullNameField.value = '';
            }
        }

        if (submitButton) {
            submitButton.textContent = isCard ? 'Pay with Card' : 'Register & Pay';
        }
        updateSubmitState();
        updateSummary();
    }

    window.togglePaymentMethod = function (method) {
        setPaymentMethod(method === 'card' ? 'card' : 'mpesa');
    };

    function updateSummary() {
        var activeTicketType = ticketType;
        var ticket = safeText(activeTicketType && activeTicketType.value);
        var amountValue = '0.00';

        if ( activeTicketType && activeTicketType.selectedIndex >= 0 ) {
            var selected = activeTicketType.options[activeTicketType.selectedIndex];
            if ( selected && selected.dataset.price ) {
                amountValue = parseFloat(selected.dataset.price).toFixed(2);
            }
            if ( selected && selected.dataset.id && ticketTypeId ) {
                ticketTypeId.value = selected.dataset.id;
            } else if ( ticketTypeId ) {
                ticketTypeId.value = '0';
            }
        } else if ( ticketTypeId ) {
            ticketTypeId.value = '0';
        }

        if (amountInput) {
            amountInput.value = amountValue;
        }
        if (summaryTicket) {
            summaryTicket.textContent = ticket || 'Select a ticket';
        }
        if (summaryAmount) {
            summaryAmount.textContent = formatCurrency(amountValue);
        }
        if (summaryMethod) {
            var methodLabel = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'Credit/Debit Card' : 'M-Pesa';
            summaryMethod.textContent = methodLabel;
        }

    }

    function updateSubmitState() {
        if (!submitButton) {
            return;
        }
        var method = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'card' : 'mpesa';
        var emailField = document.getElementById('cer-email');
        var phoneField = document.getElementById('cer-phone');
        var valid = !!ticketType && !!ticketType.value && !!emailField && emailField.checkValidity();
        valid = valid && !!phoneField && phoneField.value.trim() !== '';
        valid = valid && (method === 'card' || (!!fullNameField && fullNameField.checkValidity()));
        submitButton.disabled = !valid || requestInFlight;
    }

    function pollPaymentStatus(reference) {
        var attempts = 0;
        var verificationInFlight = false;
        var timer = window.setInterval(function () {
            if (verificationInFlight) {
                return;
            }

            attempts += 1;
            verificationInFlight = true;
            fetch((window.cerRegistrationSettings || {}).payment_verify_url, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reference: reference }), credentials: 'same-origin'
            }).then(function (response) { return response.json(); }).then(function (verification) {
                if (verification.status === 'success' && verification.registration_status === 'paid') {
                    window.clearInterval(timer);
                    setMessage('Payment confirmed. Your ticket will be emailed shortly.', 'success');
                }
            }).catch(function () {}).finally(function () {
                verificationInFlight = false;
            });
            if (attempts >= 40) {
                window.clearInterval(timer);
            }
        }, 3000);
    }

    if (ticketType) {
        ticketType.addEventListener('change', function () {
            updateSummary();
            updateSubmitState();
        });
    }

    if (btnMpesa) {
        btnMpesa.addEventListener('click', function () { setPaymentMethod('mpesa'); });
    }
    if (btnCard) {
        btnCard.addEventListener('click', function () { setPaymentMethod('card'); });
    }

    Array.prototype.forEach.call(form ? form.querySelectorAll('input') : [], function (field) {
        field.addEventListener('input', updateSubmitState);
    });

    setPaymentMethod(paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'card' : 'mpesa');

    updateSummary();

    if (!form) {
        return;
    }

    function submitRegistration(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        var method = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'card' : 'mpesa';

        if (!ticketType || !ticketType.value) {
            setMessage('Please select a ticket type.', 'error');
            return;
        }

        requestInFlight = true;
        updateSubmitState();
        runUiTransition(function () {
            setMessage(method === 'card' ? 'Preparing secure card checkout...' : 'Submitting registration...', 'info');
        });

        var formData = new FormData(form);
        formData.set('security', window.cerRegistrationSettings.nonce);
        // Keep the existing WordPress AJAX contract as the default transport to preserve
        // the original plugin/payment integration, while the REST endpoint remains as a
        // compatibility fallback for future optimization work.
        var endpoint = (window.cerRegistrationSettings && window.cerRegistrationSettings.ajax_url) || '/wp-admin/admin-ajax.php';

        fetch(endpoint, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (!result.success) {
                    var message = (result.data && result.data.message) ? result.data.message : 'Registration failed.';
                    setMessage(message, 'error');
                    return;
                }

                var reference = result.data.reference || '';
                if (!reference) {
                    throw new Error('Paystack checkout is not available.');
                }

                if (method === 'mpesa') {
                    runUiTransition(function () {
                        setMessage(result.data.display_text || 'Approve the M-Pesa payment request on your phone.', 'info');
                    });
                    pollPaymentStatus(reference);
                    return;
                }

                var accessCode = result.data.access_code || '';
                var popup = getPaystackHandler();
                if (!accessCode || !popup) {
                    throw new Error('Paystack card checkout is not available.');
                }
                runUiTransition(function () {
                    setMessage('Complete your card payment in the secure checkout.', 'info');
                });
                popup.resumeTransaction(accessCode);
                pollPaymentStatus(reference);
            })
            .catch(function (error) {
                setMessage(error.message || 'Unable to submit registration, please try again.', 'error');
            })
            .finally(function () {
                requestInFlight = false;
                updateSubmitState();
            });
    }

    form.addEventListener('submit', submitRegistration);

})(document, window);

(function (document) {
    var toggle = document.getElementById('cer-map-toggle');
    var panel = document.getElementById('cer-map-panel');
    var closeBtn = document.getElementById('cer-map-close');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', function () {
        panel.hidden = !panel.hidden;
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            panel.hidden = true;
        });
    }

    document.addEventListener('click', function (event) {
        var widget = document.getElementById('cer-map-widget');
        if (widget && !widget.contains(event.target)) {
            panel.hidden = true;
        }
    });
})(document);

/* ==========================================================================
   CER MOTION — pillar expand + scroll reveal
   --------------------------------------------------------------------------
   Deliberately dependency-free. This page already loads four idle animation
   libraries via the theme (TweenMax/GSAP 2, wow.js — enqueued but never
   initialised, appear.js, and three separate copies of animate.css) on top of
   71 scripts and 81 stylesheets. Adding a fifth was not defensible, and the
   primary benchmark (summit.health.go.ke) ships no animation library at all —
   just hand-rolled keyframes on cubic-bezier(.4,0,.2,1).
   ========================================================================== */
(function (document, window) {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Opt in to the reveal only once JS is confirmed running AND the observer
       exists. The CSS that hides .cer-reveal is gated on this class, so if this
       line never executes the content simply stays visible. */
    var canReveal = !reduceMotion && ('IntersectionObserver' in window);
    if (canReveal) {
        document.documentElement.classList.add('cer-js');
    }

    /* --- Pillar cards: click + keyboard expand ---------------------------
       Replaces the previous CSS-:hover-only 3D flip, which left the pillar
       descriptions unreachable on any touch device.
       Only one pillar popover may be visible at a time: opening a card closes
       every other card, and entering a card drops the click-opened state of
       the others so a sticky popover never overlaps a hover tooltip. */
    var pillarButtons = document.querySelectorAll('.cer-kamgc-pillars-front');

    /* --- Small-screen popover portal --------------------------------------
       Below 1025px the description used to expand inline, which grew the card
       and pushed the rest of the page down. It now opens as a centred popup in
       the brand orange with its own cancel button: the panel is moved out of
       its card into a body-level fixed host, so nothing in the document flow
       moves and the page cannot scroll behind it. A portal is required rather
       than `position: fixed` in place, because the pillars wrapper carries
       backdrop-filter and so becomes the containing block for any fixed
       descendant, and the rail's overflow-x would clip an absolute one. */
    var smallScreen = window.matchMedia ? window.matchMedia('(max-width: 1024px)') : null;
    var pop = null;
    var popBody = null;
    var backdrop = null;
    var portalCard = null;
    var unmountTimer = 0;

    function isSmallScreen() {
        return !smallScreen || smallScreen.matches;
    }

    function buildPop() {
        if (pop) {
            return;
        }

        backdrop = document.createElement('div');
        backdrop.className = 'cer-pillar-pop-backdrop';
        backdrop.hidden = true;

        pop = document.createElement('div');
        pop.className = 'cer-pillar-pop';
        pop.setAttribute('role', 'dialog');
        pop.setAttribute('aria-modal', 'true');
        pop.hidden = true;

        var cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'cer-pillar-pop-cancel';
        cancel.setAttribute('aria-label', 'Close');
        cancel.innerHTML = '<span class="material-symbols-outlined">close</span>';

        popBody = document.createElement('div');
        popBody.className = 'cer-pillar-pop-body';

        pop.appendChild(cancel);
        pop.appendChild(popBody);

        document.body.appendChild(backdrop);
        document.body.appendChild(pop);

        cancel.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            closeAllPillars();
        });

        backdrop.addEventListener('click', function () {
            closeAllPillars();
        });
    }

    function portalIn(card) {
        var panel = card.querySelector('.cer-kamgc-pillars-back');
        if (!panel) {
            return;
        }

        buildPop();
        returnPanel();

        window.clearTimeout(unmountTimer);
        unmountTimer = 0;

        var heading = card.querySelector('.cer-kamgc-pillars-front h3');
        popBody.innerHTML = '';
        if (heading) {
            var title = document.createElement('h3');
            title.className = 'cer-pillar-pop-title';
            title.textContent = heading.textContent;
            popBody.appendChild(title);
        }
        popBody.appendChild(panel);
        portalCard = card;

        backdrop.hidden = false;
        pop.hidden = false;
        document.body.classList.add('cer-pillar-pop-lock');
        /* Next frame, so the entry transition has a start value to run from. */
        window.requestAnimationFrame(function () {
            backdrop.classList.add('is-visible');
            pop.classList.add('is-visible');
        });
    }

    /* Move the panel back to the card it came from, leaving the host's own
       visibility alone. */
    function returnPanel() {
        if (!portalCard) {
            return;
        }
        var panel = pop.querySelector('.cer-kamgc-pillars-back');
        if (panel) {
            portalCard.appendChild(panel);
        }
        portalCard = null;
    }

    function portalOut() {
        if (!portalCard) {
            return;
        }

        returnPanel();
        pop.classList.remove('is-visible');
        backdrop.classList.remove('is-visible');
        document.body.classList.remove('cer-pillar-pop-lock');
        window.clearTimeout(unmountTimer);
        unmountTimer = window.setTimeout(function () {
            pop.hidden = true;
            backdrop.hidden = true;
        }, 260);
    }

    /* Single-open pillar popovers. Opening one card closes every other card;
       the open popover can be dismissed by re-clicking its trigger, pressing
       its own close button, pressing Escape, or clicking anywhere outside it. */
    function setPillarOpen(card, isOpen) {
        if (!card) {
            return;
        }
        card.classList.toggle('is-open', isOpen);
        var btn = card.querySelector('.cer-kamgc-pillars-front');
        if (btn) {
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        if (!isSmallScreen()) {
            return;
        }

        if (isOpen) {
            portalIn(card);
        } else if (card === portalCard) {
            portalOut();
        }
    }

    function closeOtherPillars(exceptCard) {
        Array.prototype.forEach.call(document.querySelectorAll('.cer-kamgc-pillars-card.is-open'), function (card) {
            if (card !== exceptCard) {
                setPillarOpen(card, false);
            }
        });
    }

    function closeAllPillars() {
        closeOtherPillars(null);
    }

    Array.prototype.forEach.call(pillarButtons, function (button) {
        var card = button.closest('.cer-kamgc-pillars-card');
        if (!card) {
            return;
        }

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var willOpen = !card.classList.contains('is-open');
            closeOtherPillars(card);
            setPillarOpen(card, willOpen);
        });

        button.addEventListener('pointerenter', function () {
            if (isSmallScreen()) {
                return;
            }
            closeOtherPillars(card);
        });
    });

    /* Delegated close for the popover's own x button (rendered per card). The
       panel may be portalled into the sheet, so fall back to the card that
       currently owns it. */
    document.addEventListener('click', function (event) {
        var closeBtn = event.target.closest('.cer-kamgc-pillars-close');
        if (!closeBtn) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        setPillarOpen(closeBtn.closest('.cer-kamgc-pillars-card') || portalCard, false);
    });

    /* Click outside any pillar card (or the portalled popup) closes it. */
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.cer-kamgc-pillars-card, .cer-pillar-pop')) {
            closeAllPillars();
        }
    });

    /* Escape closes the open popover and returns focus to its trigger. */
    document.addEventListener('keydown', function (event) {
        if ('Escape' !== event.key && 27 !== event.keyCode) {
            return;
        }
        var openCard = document.querySelector('.cer-kamgc-pillars-card.is-open');
        if (openCard) {
            setPillarOpen(openCard, false);
            var btn = openCard.querySelector('.cer-kamgc-pillars-front');
            if (btn) {
                btn.focus();
            }
        }
    });

    /* Crossing the breakpoint with the popup open would strand the panel in
       the body host, where the desktop tooltip rules do not reach it. */
    if (smallScreen) {
        var onBreakpoint = function () {
            closeAllPillars();
            portalOut();
        };
        if (smallScreen.addEventListener) {
            smallScreen.addEventListener('change', onBreakpoint);
        } else if (smallScreen.addListener) {
            smallScreen.addListener(onBreakpoint);
        }
    }

    /* --- Scroll reveal ---------------------------------------------------
       Applied to card CONTENTS only, never to the glass containers: animating
       transform/opacity on an element that also carries backdrop-filter forces
       the blur to recomposite every frame and Safari drops it outright. */
    var revealTargets = document.querySelectorAll('.cer-reveal');

    if (!revealTargets.length) {
        return;
    }

    if (!canReveal) {
        Array.prototype.forEach.call(revealTargets, function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    Array.prototype.forEach.call(revealTargets, function (el) {
        observer.observe(el);
    });
})(document, window);

/* ==========================================================================
   ANIMATE ON SCROLL
   --------------------------------------------------------------------------
   Tags section contents with .cer-reveal and staggers them in as they enter
   the viewport. Done from JS rather than the template so no markup changes,
   and so nothing is ever hidden when JS is unavailable — the CSS that hides
   .cer-reveal is gated on html.cer-js, which is only set below.
   ========================================================================== */
(function (document, window) {
    'use strict';

    var reduce = window.matchMedia &&
                 window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce || !('IntersectionObserver' in window)) {
        return; // leave everything visible and untouched
    }

    /* Contents only — never a glass container (.cer-kamgc-hero, -main, -card,
       -speakers-card, -pillars-card-wrapper), whose backdrop-filter would be
       recomposited every frame and dropped by Safari. */
    var groups = [
        '.cer-kamgc-speaker-grid > *',
        '.cer-kamgc-pillar-grid > *',
        '.cer-kamgc-compact-grid > *',
        '.cer-kamgc-partner-grid > *',
        '.cer-kamgc-stats-grid > *',
        '.cer-kamgc-faq-item',
        '.cer-kamgc-section-title',
        '.cer-kamgc-pillar-header'
    ];

    var tagged = [];

    groups.forEach(function (selector) {
        var nodes = document.querySelectorAll(selector);
        Array.prototype.forEach.call(nodes, function (el, i) {
            if (el.classList.contains('cer-reveal')) {
                return;
            }
            el.classList.add('cer-reveal');
            el.style.setProperty('--cer-reveal-delay', Math.min(i, 6) * 60 + 'ms');
            tagged.push(el);
        });
    });

    if (!tagged.length) {
        return;
    }

    document.documentElement.classList.add('cer-js');

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    tagged.forEach(function (el) {
        // Anything already on screen at load reveals immediately, so the page
        // at rest is never a set of blank panels.
        var r = el.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });

    /* Safety net.
       An entrance animation must never be the reason someone cannot read the
       page. If the observer misses an element — fast scrolling, a throttled
       background tab, a browser quirk — reveal whatever is still hidden after
       a few seconds and stop observing. Measured without this: 16 of 19
       elements were still at opacity 0 after scrolling the whole page. */
    function revealAll() {
        tagged.forEach(function (el) {
            if (!el.classList.contains('is-visible')) {
                el.classList.add('is-visible');
            }
            observer.unobserve(el);
        });
    }

    window.setTimeout(revealAll, 3000);

    // Also reveal anything the reader has already scrolled past.
    window.addEventListener('scroll', function () {
        tagged.forEach(function (el) {
            if (el.classList.contains('is-visible')) {
                return;
            }
            if (el.getBoundingClientRect().top < window.innerHeight) {
                el.classList.add('is-visible');
                observer.unobserve(el);
            }
        });
    }, { passive: true });
})(document, window);

/* --- Hero countdown ------------------------------------------------------
   PHP prints the current numbers and state, so the card is readable before
   this runs; the script only keeps them ticking and moves between states.
   It counts from the visitor's clock, not a server time in the markup: the
   page can be served from cache long after it was rendered. */
(function (document, window) {
    var roots = document.querySelectorAll('.cer-kamgc-countdown');
    var DAY_MS = 86400000;

    function pad(value) {
        return (value < 10 ? '0' : '') + value;
    }

    function setup(root) {
        var starts = Date.parse(root.getAttribute('data-starts'));
        var ends = Date.parse(root.getAttribute('data-ends'));
        if (isNaN(starts) || isNaN(ends)) {
            return;
        }

        var label = root.querySelector('[data-countdown-label]');
        var dayLabel = root.querySelector('[data-countdown-day]');
        var units = {};
        ['days', 'hours', 'minutes', 'seconds'].forEach(function (unit) {
            units[unit] = root.querySelector('[data-unit="' + unit + '"] b');
        });
        var segments = Array.prototype.map.call(root.querySelectorAll('.cer-kamgc-countdown-segment'), function (el) {
            return { open: Date.parse(el.getAttribute('data-open')), close: Date.parse(el.getAttribute('data-close')), bar: el.querySelector('i') };
        });

        /* "Today" and "tomorrow" are the event's calendar days, not the visitor's. */
        var dateKey;
        try {
            var format = new Intl.DateTimeFormat('en-CA', { timeZone: root.getAttribute('data-timezone'), year: 'numeric', month: '2-digit', day: '2-digit' });
            dateKey = function (time) { return format.format(new Date(time)); };
        } catch (error) {
            dateKey = function (time) { return new Date(time).toDateString(); };
        }

        var timer = 0;

        function render() {
            var now = Date.now();
            var state = now >= ends ? 'done' : now >= starts ? 'live' : now >= starts - DAY_MS ? 'final' : 'upcoming';
            root.setAttribute('data-state', state);

            if (state === 'upcoming' || state === 'final') {
                var total = Math.floor((starts - now) / 1000);
                var days = Math.floor(total / 86400);
                var hours = Math.floor((total % 86400) / 3600);
                var minutes = Math.floor((total % 3600) / 60);
                units.days.textContent = days;
                units.hours.textContent = pad(hours);
                units.minutes.textContent = pad(minutes);
                units.seconds.textContent = pad(total % 60);
                label.textContent = state === 'final'
                    ? (dateKey(now) === dateKey(starts) ? 'Opens today at ' : 'Opens tomorrow at ') + root.getAttribute('data-start-time')
                    : root.getAttribute('data-label');
                root.setAttribute('aria-label', days + ' days, ' + hours + ' hours and ' + minutes + ' minutes until the conference opens');
            } else if (state === 'live') {
                var day = 0;
                segments.forEach(function (segment) {
                    if (now >= segment.open) {
                        day++;
                    }
                    var fill = Math.min(1, Math.max(0, (now - segment.open) / Math.max(1, segment.close - segment.open)));
                    segment.bar.style.setProperty('--cer-fill', fill.toFixed(3));
                });
                dayLabel.textContent = 'Day ' + Math.max(1, day) + ' of ' + segments.length;
                root.setAttribute('aria-label', 'The conference is happening now, ' + dayLabel.textContent);
            } else {
                root.removeAttribute('aria-label');
                window.clearInterval(timer);
            }
        }

        render();
        timer = window.setInterval(render, 1000);
    }

    Array.prototype.forEach.call(roots, setup);
})(document, window);
