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

    var currency = (window.cerRegistrationSettings && window.cerRegistrationSettings.currency) || 'KES';

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
        var timer = window.setInterval(function () {
            attempts += 1;
            fetch((window.cerRegistrationSettings || {}).payment_verify_url, {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ reference: reference }), credentials: 'same-origin'
            }).then(function (response) { return response.json(); }).then(function (verification) {
                if (verification.status === 'success' && verification.registration_status === 'paid') {
                    window.clearInterval(timer);
                    setMessage('Payment confirmed. Your ticket will be emailed shortly.', 'success');
                }
            }).catch(function () {});
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
        setMessage(method === 'card' ? 'Preparing secure card checkout...' : 'Submitting registration...', 'info');

        var formData = new FormData(form);
        formData.set('security', window.cerRegistrationSettings.nonce);

        fetch(window.cerRegistrationSettings.ajax_url, {
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
                    setMessage(result.data.display_text || 'Approve the M-Pesa payment request on your phone.', 'info');
                    pollPaymentStatus(reference);
                    return;
                }

                var accessCode = result.data.access_code || '';
                if (!accessCode || typeof window.PaystackPop !== 'function') {
                    throw new Error('Paystack card checkout is not available.');
                }
                setMessage('Complete your card payment in the secure checkout.', 'info');
                var popup = new window.PaystackPop();
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
       descriptions unreachable on any touch device. */
    var pillarButtons = document.querySelectorAll('.cer-kamgc-pillars-front');

    Array.prototype.forEach.call(pillarButtons, function (button) {
        button.addEventListener('click', function () {
            var card = button.closest('.cer-kamgc-pillars-card');
            if (!card) {
                return;
            }
            var isOpen = card.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });

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
