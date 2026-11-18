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
    var formSteps = form ? Array.prototype.slice.call(form.querySelectorAll('[data-cer-step]')) : [];
    var progressSteps = form ? Array.prototype.slice.call(form.querySelectorAll('.cer-progress-step')) : [];
    var nextStepButton = form ? form.querySelector('[data-cer-next]') : null;
    var backStepButton = form ? form.querySelector('[data-cer-back]') : null;
    var accommodationDetails = document.getElementById('cer-accessibility-details');
    var accommodationDetailsWrap = accommodationDetails ? accommodationDetails.closest('.cer-accessibility-details') : null;
    var accommodationField = accommodationDetails ? accommodationDetails.closest('.cer-accommodation-field') : null;
    var btnMpesa = document.getElementById('btn-mpesa');
    var btnCard = document.getElementById('btn-card');
    var submitButton = form ? form.querySelector('button[type="submit"]') : null;
    var requestInFlight = false;
    var paystackHandler = null;
    var feedbackState = 'idle';
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
        feedbackState = state;
        if (!messageBox) {
            return;
        }

        runUiTransition(function () {
            messageBox.textContent = text;
            messageBox.className = 'cer-form-footnote cer-form-message cer-form-message-' + state;
            messageBox.setAttribute('aria-busy', state === 'info' ? 'true' : 'false');
            messageBox.classList.toggle('cer-form-message-pending', state === 'info');
        });
    }

    function selectedTicket() {
        if (!ticketType) {
            return null;
        }
        return ticketType.options ? ticketType.options[ticketType.selectedIndex] : ticketType;
    }

    function paymentRequired() {
        var selected = selectedTicket();
        if (selected && ticketType.value) {
            return parseFloat(selected.dataset.price || 0) > 0;
        }
        return !!form && form.dataset.paymentRequired === 'true';
    }

    function isSoldOut() {
        return !!form && form.classList.contains('cer-sold-out');
    }

    function showSoldOut(text) {
        form.classList.add('cer-sold-out');
        form.dataset.paymentRequired = 'false';
        if (submitButton) {
            submitButton.textContent = 'Sold Out';
            submitButton.disabled = true;
        }
        var availability = form.dataset.ticketCount === '1' ? document.getElementById('cer-ticket-availability') : null;
        var count = availability ? availability.querySelector('.cer-availability-count') : null;
        if (count) {
            count.textContent = 'Sold out';
            delete availability.dataset.availabilityState;
        }
        setMessage(text, 'warning');
    }

    // Reflects inventory that ran out after the page loaded; the server remains the source of truth.
    function handleSoldOut(eventFull) {
        var selected = selectedTicket();
        if (eventFull || !ticketType || !ticketType.options) {
            showSoldOut(eventFull
                ? 'This event is fully booked. Please contact the event team about future availability.'
                : form.dataset.soldOutNote);
            return;
        }
        if (selected && selected.value) {
            selected.disabled = true;
            selected.dataset.soldOut = 'true';
            selected.textContent = (selected.dataset.baseLabel || selected.textContent) + ' - Sold out';
        }
        ticketType.value = '';
        var remaining = Array.prototype.filter.call(ticketType.options, function (option) {
            return option.value && !option.disabled;
        });
        if (!remaining.length) {
            showSoldOut(form.dataset.soldOutNote);
            return;
        }
        setPaymentMethod(paymentMethodHidden ? paymentMethodHidden.value : 'mpesa');
        setMessage('That ticket has just sold out. Please choose another available ticket.', 'warning');
    }

    function registrationErrorMessage(message) {
        var messages = [
            'Your session has expired. Please refresh this page and submit again.',
            'Please check your email, Kenyan phone number and required fields, then try again.',
            'Please complete your contact and conference details, including any requested accessibility support.',
            'The selected event or ticket is no longer available.',
            'The selected ticket is no longer available.',
            'This event has reached its attendance capacity.',
            'This ticket type is sold out.',
            'The registration could not be saved.',
            'You are already registered for this ticket. Please check your email for your ticket or contact the event team for help.',
            'We have saved your registration details, but could not start your payment. Please try again later. If you approved a payment or were charged, do not pay again; contact the event team for help.'
        ];
        if (messages.indexOf(message) >= 0) {
            if (message === 'This ticket type is sold out.') {
                return 'This ticket type is sold out. Please choose another available ticket.';
            }
            if (message === 'This event has reached its attendance capacity.') {
                return 'This event is fully booked. Please contact the event team about future availability.';
            }
            if (message === 'The registration could not be saved.') {
                return 'We could not save your registration details. Please try again in a moment.';
            }
            if (message === 'The selected event or ticket is no longer available.' || message === 'The selected ticket is no longer available.') {
                return 'This event or ticket is no longer available. Please refresh the page or choose another ticket.';
            }
            return message;
        }
        console.error('CER registration failed:', message);
        if (!paymentRequired()) {
            return 'We could not complete your registration. Please try again later or contact the event team for help.';
        }
        return 'We could not complete your registration or start payment. Please try again later. If you were charged, do not pay again; contact the event team for help.';
    }

    function setPaymentMethod(method) {
        var needsPayment = paymentRequired();
        method = needsPayment && method === 'card' ? 'card' : 'mpesa';
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

        if (fullNameField) {
            fullNameField.required = true;
        }
        if (nameFieldContainer) {
            nameFieldContainer.style.display = '';
        }

        if (submitButton && !isSoldOut()) {
            submitButton.textContent = needsPayment ? (isCard ? 'Pay with Card' : 'Register & Pay') : 'Register';
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

        if ( activeTicketType && activeTicketType.value ) {
            var selected = selectedTicket();
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
        if (summaryTicket && (ticket || !isSoldOut() || form.dataset.ticketCount !== '1')) {
            summaryTicket.textContent = ticket || 'Select a ticket';
        }
        if (summaryAmount) {
            summaryAmount.textContent = formatCurrency(amountValue);
        }
        if (summaryMethod) {
            var methodLabel = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'Credit/Debit Card' : 'M-Pesa';
            summaryMethod.textContent = methodLabel;
        }

        if (form && !isSoldOut()) {
            var needsPayment = paymentRequired();
            form.dataset.paymentRequired = needsPayment ? 'true' : 'false';
            if (messageBox && feedbackState === 'idle') {
                messageBox.innerHTML = needsPayment ? form.dataset.paymentNote : form.dataset.contactNote;
            }
        }
    }

    function updateSubmitState() {
        if (!submitButton) {
            return;
        }
        var requiredFields = form ? Array.prototype.slice.call(form.querySelectorAll('[required]')) : [];
        var valid = !!ticketType && !!ticketType.value && requiredFields.every(function (field) {
            if (field.type === 'radio') {
                return !!form.querySelector('[name="' + field.name + '"]:checked');
            }
            return field.checkValidity();
        });
        submitButton.disabled = !valid || requestInFlight || isSoldOut();
    }

    function pollPaymentStatus(reference) {
        var attempts = 0;
        var verificationInFlight = false;
        var verificationUnavailable = false;
        function finish(text, state) {
            window.clearInterval(timer);
            setMessage(text, state);
            requestInFlight = false;
            updateSubmitState();
        }
        var timer = window.setInterval(function () {
            if (verificationInFlight) {
                return;
            }

            attempts += 1;
            verificationInFlight = true;
            fetch((window.cerRegistrationSettings || {}).payment_verify_url, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reference: reference }), credentials: 'same-origin'
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Payment verification returned HTTP ' + response.status);
                }
                return response.json();
            }).then(function (verification) {
                if (!verification || typeof verification.status !== 'string') {
                    throw new Error('Invalid payment verification response.');
                }
                if (verificationUnavailable) {
                    setMessage('Checking your payment status. Please keep this page open.', 'info');
                    verificationUnavailable = false;
                }
                if (verification.status === 'success' && verification.registration_status === 'paid') {
                    finish('Payment confirmed. Your ticket and receipt will be emailed shortly.', 'success');
                    return;
                }
                if (['failed', 'abandoned', 'cancelled', 'canceled', 'reversed', 'timeout'].indexOf(verification.status) >= 0) {
                    finish('Your payment was not completed. Your registration details are saved. Please try again when ready. If you were charged, do not pay again; contact the event team for help.', 'error');
                    return;
                }
                if (attempts >= 40) {
                    finish('Your payment is still awaiting confirmation. If you approved it or were charged, do not pay again. Your ticket and receipt will be emailed once confirmed; contact the event team if you need help.', 'warning');
                }
            }).catch(function (error) {
                console.error('CER payment verification failed:', error);
                verificationUnavailable = true;
                if (attempts >= 40) {
                    finish('We cannot confirm your payment right now. If you approved it or were charged, do not pay again. Please contact the event team to check your payment status.', 'warning');
                } else {
                    setMessage('We are having trouble checking your payment. Retrying automatically; please do not pay again.', 'info');
                }
            }).finally(function () {
                verificationInFlight = false;
            });
        }, 3000);
    }

    if (ticketType) {
        ticketType.addEventListener('change', function () {
            setPaymentMethod(paymentMethodHidden ? paymentMethodHidden.value : 'mpesa');
        });
    }

    if (btnMpesa) {
        btnMpesa.addEventListener('click', function () { setPaymentMethod('mpesa'); });
    }
    if (btnCard) {
        btnCard.addEventListener('click', function () { setPaymentMethod('card'); });
    }

    function showStep(index) {
        formSteps.forEach(function (step, stepIndex) {
            var active = stepIndex === index;
            step.hidden = !active;
            step.classList.toggle('is-active', active);
            if (progressSteps[stepIndex]) {
                progressSteps[stepIndex].classList.toggle('is-active', active || stepIndex < index);
            }
        });
        if (formSteps[index]) {
            var headingField = formSteps[index].querySelector('input, select, textarea');
            if (headingField) {
                window.setTimeout(function () { headingField.focus(); }, 50);
            }
        }
        updateSubmitState();
    }

    function updateAccommodationDetails() {
        var answer = form ? form.querySelector('[name="accessibility_support"]:checked') : null;
        var needsDetails = !!answer && answer.value === 'yes';
        if (accommodationField) {
            accommodationField.classList.toggle('has-details', needsDetails);
        }
        if (accommodationDetailsWrap) {
            accommodationDetailsWrap.hidden = false;
            accommodationDetailsWrap.classList.toggle('is-visible', needsDetails);
        }
        if (accommodationDetails) {
            accommodationDetails.disabled = !needsDetails;
            accommodationDetails.required = needsDetails;
            accommodationDetails.setAttribute('aria-disabled', needsDetails ? 'false' : 'true');
            if (!needsDetails) {
                accommodationDetails.value = '';
            }
        }
        updateSubmitState();
    }

    if (nextStepButton) {
        nextStepButton.addEventListener('click', function () {
            var firstStepFields = formSteps[0] ? Array.prototype.slice.call(formSteps[0].querySelectorAll('input, select, textarea')) : [];
            var invalid = firstStepFields.find(function (field) { return !field.checkValidity(); });
            if (invalid) {
                invalid.reportValidity();
                return;
            }
            showStep(1);
        });
    }
    if (backStepButton) {
        backStepButton.addEventListener('click', function () { showStep(0); });
    }

    Array.prototype.forEach.call(form ? form.querySelectorAll('[name="accessibility_support"]') : [], function (field) {
        field.addEventListener('change', updateAccommodationDetails);
    });
    Array.prototype.forEach.call(form ? form.querySelectorAll('input, select, textarea') : [], function (field) {
        field.addEventListener('input', updateSubmitState);
        field.addEventListener('change', updateSubmitState);
    });

    Array.prototype.forEach.call(form ? form.querySelectorAll('[data-cer-field-row]') : [], function (row) {
        var groups = Array.prototype.slice.call(row.querySelectorAll(':scope > .cer-field-group'));
        groups.forEach(function (group) {
            var activate = function () {
                row.classList.add('has-active-field');
                groups.forEach(function (item) { item.classList.toggle('is-active-field', item === group); });
            };
            var deactivate = function () {
                if (!row.querySelector(':focus-within')) {
                    row.classList.remove('has-active-field');
                    groups.forEach(function (item) { item.classList.remove('is-active-field'); });
                }
            };
            group.addEventListener('mouseenter', activate);
            group.addEventListener('mouseleave', deactivate);
            group.addEventListener('focusin', activate);
            group.addEventListener('focusout', function () {
                window.setTimeout(deactivate, 0);
            });
        });
    });

    updateAccommodationDetails();
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
            if (isSoldOut()) {
                return;
            }
            setMessage('Please select a ticket type.', 'error');
            showStep(0);
            return;
        }
        if (!form.checkValidity()) {
            var invalidField = form.querySelector(':invalid');
            if (invalidField) {
                var invalidStep = invalidField.closest('[data-cer-step]');
                showStep(invalidStep === formSteps[1] ? 1 : 0);
                window.setTimeout(function () { invalidField.reportValidity(); }, 75);
            }
            return;
        }

        requestInFlight = true;
        updateSubmitState();
        var needsPayment = paymentRequired();
        setMessage(needsPayment && method === 'card' ? 'Preparing secure card checkout...' : 'Submitting registration...', 'info');
        var paymentStarted = false;
        var registrationSaved = false;

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
                if (!response.ok) {
                    throw new Error('Registration returned HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function (result) {
                if (!result || typeof result.success !== 'boolean') {
                    throw new Error('Invalid registration response.');
                }
                if (!result.success) {
                    var serverMessage = result.data && result.data.message;
                    if (serverMessage === 'This ticket type is sold out.' || serverMessage === 'This event has reached its attendance capacity.') {
                        handleSoldOut(serverMessage === 'This event has reached its attendance capacity.');
                        return;
                    }
                    setMessage(registrationErrorMessage(serverMessage), 'error');
                    return;
                }

                registrationSaved = true;
                if (result.data && (result.data.payment_status === 'not_required' || result.data.payment_status === 'not_on_sale')) {
                    setMessage(result.data.message, result.data.ticket_sent === false ? 'warning' : 'success');
                    return;
                }
                if (!result.data) {
                    throw new Error('Missing payment checkout details.');
                }
                var reference = result.data.reference || '';
                if (!reference) {
                    throw new Error('Paystack checkout is not available.');
                }

                if (method === 'mpesa') {
                    setMessage('Approve the M-Pesa payment request on your phone. We will confirm your payment automatically.', 'info');
                    pollPaymentStatus(reference);
                    paymentStarted = true;
                    return;
                }

                var accessCode = result.data.access_code || '';
                var popup = getPaystackHandler();
                if (!accessCode || !popup) {
                    throw new Error('Paystack card checkout is not available.');
                }
                setMessage('Complete your card payment in the secure checkout. We will confirm your payment automatically.', 'info');
                popup.resumeTransaction(accessCode);
                pollPaymentStatus(reference);
                paymentStarted = true;
            })
            .catch(function (error) {
                console.error('CER registration checkout failed:', error);
                if (!needsPayment) {
                    setMessage('We could not confirm your registration. Please check your connection and try again later, or contact the event team for help.', 'error');
                    return;
                }
                setMessage(registrationSaved
                    ? 'Your registration details are saved, but we could not open payment checkout. Please try again later. If you approved a payment or were charged, do not pay again; contact the event team for help.'
                    : 'We could not confirm your submission. Please check your connection and try again later. If you approved a payment or were charged, do not pay again; contact the event team for help.', 'error');
            })
            .finally(function () {
                if (!paymentStarted) {
                    requestInFlight = false;
                }
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
   ANIMATE ON SCROLL (AOS)
   --------------------------------------------------------------------------
   AOS is enqueued from the CDN; the data-aos attributes are set here rather
   than in the template so the markup stays clean and nothing is ever hidden
   when the script does not run — AOS only hides elements that carry the
   attribute. Variations are per section so the page does not read as one
   repeated effect.
   ========================================================================== */
(function (document, window) {
    'use strict';

    var reduce = window.matchMedia &&
                 window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* The pillar cards carry .cer-reveal from the template, and the older CSS
       keeps those at opacity 0 until .is-visible is added. AOS owns the
       entrance now, so release them either way. */
    Array.prototype.forEach.call(document.querySelectorAll('.cer-reveal'), function (el) {
        el.classList.add('is-visible');
    });

    if (reduce || typeof window.AOS === 'undefined') {
        return;
    }

    /* Each group cycles through its own set, so neighbouring cards enter
       differently and a scroll down (or back up) never repeats one effect.
       The hero's own CSS load sequence is left alone; AOS starts below it. */
    var groups = [
        { selector: '.cer-kamgc-section-title, .cer-kamgc-pillar-header', animations: ['fade-down'], duration: 600 },
        { selector: '.cer-kamgc-divider-center', animations: ['zoom-in'], duration: 500 },
        { selector: '.cer-kamgc-hero-summary', animations: ['fade-left'], duration: 800 },
        { selector: '.cer-kamgc-organiser', animations: ['zoom-in', 'fade-down', 'zoom-in-up', 'fade-up'], stagger: 90, duration: 600 },
        { selector: '.cer-kamgc-feature-list li', animations: ['fade-up', 'zoom-in-up', 'fade-up-right', 'flip-up'], stagger: 70, duration: 600 },
        { selector: '.cer-kamgc-speaker-grid > *', animations: ['flip-left', 'zoom-in-up', 'flip-right'], stagger: 90, duration: 750 },
        { selector: '.cer-kamgc-pillar-grid > *', animations: ['zoom-in-up', 'flip-left', 'fade-down', 'flip-right', 'zoom-in-down'], stagger: 80, duration: 700 },
        { selector: '.cer-kamgc-objectives .cer-kamgc-compact-grid > *', animations: ['fade-up-right', 'zoom-in', 'fade-up-left', 'fade-right', 'zoom-in-up', 'fade-left'], stagger: 80, duration: 700 },
        { selector: '.cer-kamgc-summit-structure .cer-kamgc-compact-grid > *', animations: ['fade-right', 'zoom-in-down', 'fade-left', 'flip-up', 'fade-up', 'zoom-in', 'fade-down'], stagger: 80, duration: 700 },
        { selector: '.cer-kamgc-partner-grid > *', animations: ['zoom-in', 'flip-up'], stagger: 70, duration: 600 },
        { selector: '.cer-kamgc-stats-grid > *', animations: ['zoom-in-up', 'flip-up', 'zoom-in-down'], stagger: 90, duration: 650 },
        { selector: '.cer-kamgc-main-column > .cer-kamgc-card', animations: ['fade-up-right', 'zoom-in-up'], stagger: 120, duration: 800 },
        { selector: '.cer-kamgc-sidebar-column > *', animations: ['fade-left', 'zoom-in-left'], stagger: 120, duration: 800 },
        { selector: '.cer-kamgc-sponsor-list li', animations: ['fade-left', 'fade-right'], stagger: 90, duration: 550 },
        { selector: '.cer-kamgc-faq-column:first-child .cer-kamgc-faq-item', animations: ['fade-right', 'zoom-in-right'], stagger: 60, duration: 600 },
        { selector: '.cer-kamgc-faq-column:last-child .cer-kamgc-faq-item', animations: ['fade-left', 'zoom-in-left'], stagger: 60, duration: 600 }
    ];

    var easings = ['ease-out-cubic', 'ease-out-back', 'ease-out-quart'];

    groups.forEach(function (group) {
        Array.prototype.forEach.call(document.querySelectorAll(group.selector), function (el, i) {
            if (el.hasAttribute('data-aos')) {
                return;
            }
            el.setAttribute('data-aos', group.animations[i % group.animations.length]);
            el.setAttribute('data-aos-duration', String(group.duration));
            el.setAttribute('data-aos-easing', easings[i % easings.length]);
            if (group.stagger) {
                el.setAttribute('data-aos-delay', String(Math.min(i, 5) * group.stagger));
            }
        });
    });

    window.AOS.init({
        duration: 700,
        easing: 'ease-out-cubic',
        once: false,
        offset: 60,
        anchorPlacement: 'top-bottom',
        disableMutationObserver: false
    });

    /* The hero summary grows a countdown row every second, and the FAQ and
       pillar popovers change height; keep AOS positions in step. */
    window.addEventListener('load', function () { window.AOS.refresh(); });
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

(function (document, window) {
    var eventPage = document.querySelector('.cer-event-page');
    var header = document.querySelector('.elementor-location-header');
    if (!eventPage || !header) {
        return;
    }

    var lastOffset = -1;
    var frame = 0;
    var initialHash = window.location.hash;
    var interactionEvents = ['wheel', 'touchstart', 'keydown', 'pointerdown'];

    function measureHeader() {
        var bottom = 0;
        Array.prototype.forEach.call(header.querySelectorAll('.elementor-sticky'), function (element) {
            var style = window.getComputedStyle(element);
            var rect = element.getBoundingClientRect();
            if ((style.position === 'fixed' || style.position === 'sticky') &&
                style.visibility !== 'hidden' && style.display !== 'none' && rect.height > 0) {
                bottom = Math.max(bottom, rect.bottom);
            }
        });

        var offset = Math.max(0, Math.ceil(bottom));
        if (offset !== lastOffset) {
            eventPage.style.setProperty('--cer-header-offset', offset + 'px');
            lastOffset = offset;
        }
    }

    function scheduleMeasure() {
        if (!frame) {
            frame = window.requestAnimationFrame(function () {
                frame = 0;
                measureHeader();
            });
        }
    }

    function cancelInitialAnchor() {
        initialHash = '';
    }

    function finishInitialAnchor() {
        measureHeader();
        interactionEvents.forEach(function (name) {
            window.removeEventListener(name, cancelInitialAnchor);
        });

        // The browser can navigate to a fragment before Elementor makes the header sticky.
        var hash = initialHash;
        initialHash = '';
        if (!hash || hash !== window.location.hash || lastOffset <= 0) {
            return;
        }

        var target = document.getElementById(hash.slice(1));
        if (target && target.tagName === 'SECTION' && eventPage.contains(target)) {
            target.scrollIntoView({ block: 'start', behavior: 'instant' });
        }
    }

    if (initialHash) {
        interactionEvents.forEach(function (name) {
            window.addEventListener(name, cancelInitialAnchor, { passive: true });
        });
    }

    measureHeader();
    window.addEventListener('load', finishInitialAnchor);
    window.addEventListener('resize', scheduleMeasure, { passive: true });
    window.addEventListener('scroll', scheduleMeasure, { passive: true });

    if (window.ResizeObserver) {
        var observer = new window.ResizeObserver(scheduleMeasure);
        observer.observe(header);
        Array.prototype.forEach.call(header.querySelectorAll('.elementor-sticky'), function (element) {
            observer.observe(element);
        });
    }
})(document, window);

/* --- Blob layer offset ---------------------------------------------------
   The blobs are 80% orange, so they start below the section heading and the
   white title and intro never sit on one. Sections without a heading
   (registration) keep the full-height layer. */
(function (document, window) {
    var layers = document.querySelectorAll('.cer-kamgc-blobs');
    if (!layers.length) {
        return;
    }

    function place() {
        Array.prototype.forEach.call(layers, function (layer) {
            var host = layer.parentElement;
            // Keynote Speakers keeps its original full-height, low-opacity blobs.
            var title = host.classList.contains('cer-kamgc-speakers-card') ? null : host.querySelector(':scope > .cer-kamgc-section-title');
            layer.style.top = title ? (title.offsetTop + title.offsetHeight + 8) + 'px' : '';
            layer.classList.toggle('is-offset', !!title);
        });
    }

    var frame = 0;
    function schedule() {
        window.cancelAnimationFrame(frame);
        frame = window.requestAnimationFrame(place);
    }

    place();
    window.addEventListener('load', place);
    window.addEventListener('resize', schedule, { passive: true });
})(document, window);

(function (document, window) {
    var form = document.getElementById('cer-registration-form');
    if (!form || typeof form.dataset.availability === 'undefined') {
        return;
    }
    var ticket = document.getElementById('cer-ticket-type');
    var count = document.getElementById('cer-ticket-availability');
    var inlineCount = document.getElementById('cer-inline-ticket-availability');
    var error = document.getElementById('cer-availability-error');
    var message = document.getElementById('cer-form-message');
    var originalMessage = message ? message.innerHTML : '';
    var submitButton = form.querySelector('button[type="submit"]');
    var initiallyUnavailable = form.classList.contains('cer-sold-out');
    var eventId = document.getElementById('cer-event-id');
    var availability = null;
    var refreshing = false;

    function validCount(value) {
        return value === null || (Number.isSafeInteger(value) && value >= 0);
    }

    function validAvailability(value) {
        return value && validCount(value.capacity) && validCount(value.remaining) &&
            Array.isArray(value.tickets) && value.tickets.every(function (item) {
                return Number.isSafeInteger(item.id) && item.id > 0 && typeof item.name === 'string' &&
                    validCount(item.capacity) && validCount(item.remaining) && typeof item.maintenance === 'boolean';
            });
    }

    try {
        availability = form.dataset.availability ? JSON.parse(form.dataset.availability) : null;
        if (availability && !validAvailability(availability)) {
            throw new Error('Invalid initial availability.');
        }
    } catch (failure) {
        availability = null;
        error.textContent = 'Live availability unavailable. Counts may be out of date; retrying automatically.';
        window.console.error('CER availability data invalid:', failure);
    }

    function showCount(target, value) {
        var allMaintenance = value.tickets.length > 0 && value.tickets.every(function (item) { return item.maintenance; });
        var state = JSON.stringify([value.remaining, value.capacity, allMaintenance]);
        if (target.dataset.availabilityState === state) {
            return;
        }
        var remaining = document.createElement('strong');
        remaining.className = 'cer-availability-count';
        remaining.textContent = allMaintenance ? 'Scheduled for maintenance' : (
            value.remaining === null ? 'Available' : ( value.remaining === 0 ? 'Sold out' : value.remaining.toLocaleString('en-KE') + ' left' )
        );
        var total = document.createElement('small');
        total.textContent = allMaintenance ? 'Registration will reopen when maintenance is complete.' :
            ( value.capacity === null ? 'Capacity not specified' : 'out of ' + value.capacity.toLocaleString('en-KE') + ' capacity' );
        target.replaceChildren(remaining, total);
        target.dataset.availabilityState = state;
    }

    function optionSuffix(item, disabled) {
        if (item && item.maintenance) {
            return ' - Scheduled for maintenance';
        }
        if (!item || item.remaining === null) {
            return disabled ? ' - Sold out' : '';
        }
        if (disabled && item.remaining === 0) {
            return ' - Sold out';
        }
        return ' - ' + item.remaining.toLocaleString('en-KE') + ' left' + (disabled ? ' (refresh to select)' : '');
    }

    function render() {
        if (!availability) {
            return;
        }
        var configuredCount = Number(form.dataset.ticketCount);
        var optionsChanged = availability.tickets.length !== configuredCount;
        if (configuredCount === 1 && availability.tickets.length === 1 && count) {
            showCount(count, availability);
        }
        var selectedUnavailable = false;
        var selectedMaintenance = false;
        if (ticket && ticket.options) {
            Array.prototype.forEach.call(ticket.options, function (option) {
                if (!option.dataset.id || !option.dataset.baseLabel) {
                    return;
                }
                var item = availability.tickets.find(function (entry) { return entry.id === Number(option.dataset.id); });
                if (!item) {
                    optionsChanged = true;
                    return;
                }
                option.disabled = item.maintenance || item.remaining === 0;
                var label = option.dataset.baseLabel + optionSuffix(item, option.disabled);
                if (option.textContent !== label) {
                    option.textContent = label;
                }
            });
            var selectedOption = ticket.options[ticket.selectedIndex];
            if (selectedOption && selectedOption.dataset.id) {
                var selectedItem = availability.tickets.find(function (item) { return item.id === Number(selectedOption.dataset.id); });
                selectedUnavailable = !selectedItem || selectedItem.maintenance || selectedItem.remaining === 0;
                selectedMaintenance = !!(selectedItem && selectedItem.maintenance);
            }
        } else if (ticket) {
            var selected = availability.tickets.find(function (item) { return item.id === Number(ticket.dataset.id); });
            if (!selected) {
                optionsChanged = true;
            } else {
                selectedUnavailable = selected.maintenance || selected.remaining === 0;
                selectedMaintenance = selected.maintenance;
                if (inlineCount) {
                    inlineCount.textContent = optionSuffix(selected, selectedUnavailable);
                }
            }
        }
        var allUnavailable = availability.tickets.length > 0 && availability.tickets.every(function (item) {
            return item.maintenance || item.remaining === 0;
        });
        var maintenanceUnavailable = allUnavailable && availability.tickets.some(function (item) { return item.maintenance; });
        var availabilityChangedToAvailable = initiallyUnavailable && !allUnavailable;
        var registrationUnavailable = allUnavailable || selectedUnavailable || availabilityChangedToAvailable;
        if (submitButton) {
            submitButton.disabled = registrationUnavailable;
            if (availabilityChangedToAvailable) {
                submitButton.textContent = 'Refresh to Register';
            } else if (registrationUnavailable) {
                submitButton.textContent = maintenanceUnavailable || selectedMaintenance ? 'Temporarily Unavailable' : 'Sold Out';
            } else {
                submitButton.textContent = form.dataset.paymentRequired === 'true' ? 'Register & Pay' : 'Register';
            }
        }
        form.classList.toggle('cer-sold-out', registrationUnavailable);
        if (message) {
            if (availabilityChangedToAvailable) {
                message.textContent = 'Ticket availability has changed. Refresh this page to update the registration form.';
            } else if (registrationUnavailable) {
                message.textContent = maintenanceUnavailable || selectedMaintenance
                    ? 'Registration for this ticket is temporarily unavailable while maintenance is in progress. Please check back soon.'
                    : selectedUnavailable ? 'This ticket is not currently available. Please select another ticket.' : form.dataset.soldOutNote;
            } else {
                message.innerHTML = originalMessage;
            }
        }
        form.dataset.availabilityReady = optionsChanged ? 'false' : 'true';
        error.textContent = optionsChanged || availabilityChangedToAvailable || (registrationUnavailable && !maintenanceUnavailable && !selectedMaintenance && availability.remaining !== null && availability.remaining > 0)
            ? 'Ticket availability has changed. Refresh this page to update the registration form.' : '';
    }

    if (ticket && ticket.options) {
        ticket.addEventListener('change', render);
    }

    async function refresh() {
        if (refreshing || document.hidden) {
            return;
        }
        refreshing = true;
        try {
            var settings = window.cerRegistrationSettings;
            var url = new URL(settings.ajax_url, window.location.href);
            url.searchParams.set('action', 'cer_ticket_availability');
            url.searchParams.set('event_id', eventId.value);
            var response = await fetch(url.toString(), { cache: 'no-store' });
            if (!response.ok) {
                throw new Error('Availability request failed: ' + response.status);
            }
            var result = await response.json();
            if (!result.success || !validAvailability(result.data)) {
                throw new Error('Invalid availability response.');
            }
            availability = result.data;
            render();
        } catch (failure) {
            error.textContent = 'Live availability unavailable. Counts may be out of date; retrying automatically.';
            window.console.error('CER availability refresh failed:', failure);
        } finally {
            refreshing = false;
        }
    }

    render();
    refresh();
    window.setInterval(refresh, 30000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refresh();
        }
    });
})(document, window);
