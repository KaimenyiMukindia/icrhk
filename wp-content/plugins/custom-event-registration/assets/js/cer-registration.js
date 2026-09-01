(function (document, window) {
    var form = document.getElementById('cer-registration-form');
    var messageBox = document.getElementById('cer-form-message');
    var ticketType = document.getElementById('cer-ticket-type');
    var cardTicketType = document.getElementById('cer-card-ticket-type');
    var cardEmailField = document.getElementById('cer-card-email');
    var ticketTypeId = document.getElementById('cer-ticket-type-id');
    var amountInput = document.getElementById('cer-amount');
    var summaryTicket = document.getElementById('cer-summary-ticket');
    var summaryAmount = document.getElementById('cer-summary-amount');
    var summaryMethod = document.getElementById('cer-summary-method');
    var paymentMethodHidden = document.getElementById('payment_method_hidden');
    var fullNameField = document.getElementById('cer-full-name');
    var nameFieldContainer = fullNameField ? fullNameField.closest('.cer-field-group') : null;
    var mpesaField = document.querySelector('.mpesa-field');
    var hostedPayment = document.getElementById('cer-hosted-payment');
    var hostedPaymentFrame = document.getElementById('cer-hosted-payment-frame');
    var paymentPollTimer = null;
    var btnMpesa = document.getElementById('btn-mpesa');
    var btnCard = document.getElementById('btn-card');
    var submitButton = form ? form.querySelector('button[type="submit"]') : null;
    var mpesaPanel = document.querySelector('[data-payment-panel="mpesa"]');
    var cardPanel = document.querySelector('[data-payment-panel="card"]');
    var cardRequestInFlight = false;
    var currentTrackingId = '';

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

    function resetFormState() {
        form.reset();
        setPaymentMethod('mpesa');
        updateSummary();
    }

    function resetPaymentState() {
        currentTrackingId = '';
        if (paymentPollTimer) {
            window.clearInterval(paymentPollTimer);
            paymentPollTimer = null;
        }
        if (hostedPaymentFrame) {
            hostedPaymentFrame.src = 'about:blank';
        }
        if (hostedPayment) {
            hostedPayment.classList.add('cer-modal-hidden');
            hostedPayment.classList.remove('cer-payment-complete', 'cer-payment-loading');
        }
        if (form) {
            form.classList.remove('cer-payment-submitted');
            form.removeAttribute('aria-hidden');
        }
    }

    function pollPaymentStatus(trackingId) {
        var settings = window.cerRegistrationSettings || {};
        var statusPath = settings.payment_status_path || '/laravel-engine/public/api/payment/status/';
        var statusUrl = statusPath.indexOf('http') === 0 ? statusPath : window.location.origin + statusPath;
        var check = function () {
            fetch(statusUrl + encodeURIComponent(trackingId), { credentials: 'same-origin' })
                .then(function (response) { return response.json(); })
                .then(function (result) {
                    var gatewayStatus = safeText(result.status || (result.response && result.response.payment_status_description)).toLowerCase();
                    if (gatewayStatus === 'completed' || gatewayStatus === 'paid' || gatewayStatus === 'successful' || gatewayStatus === 'success') {
                        if (paymentPollTimer) {
                            window.clearInterval(paymentPollTimer);
                            paymentPollTimer = null;
                        }
                        if (hostedPayment) {
                            hostedPayment.classList.add('cer-payment-complete');
                        }
                        setMessage('Payment confirmed. Thank you for registering.', 'success');
                    } else if (gatewayStatus === 'failed' || gatewayStatus === 'invalid') {
                        setMessage('Payment was not completed. Please finish the checkout in the payment panel.', 'error');
                    }
                })
                .catch(function () {});
        };

        check();
        paymentPollTimer = window.setInterval(check, 5000);
    }

    function setFieldRequired(selector, required) {
        var field = document.getElementById(selector);
        if (field) {
            field.required = !!required;
        }
    }

    function setPaymentMethod(method) {
        resetPaymentState();
        if (form) {
            form.classList.toggle('cer-card-selected', method === 'card');
        }
        if (paymentMethodHidden) {
            paymentMethodHidden.value = method;
        }

        if (btnMpesa && btnCard) {
            btnMpesa.classList.toggle('is-active', method === 'mpesa');
            btnCard.classList.toggle('is-active', method === 'card');
        }

        if (mpesaPanel && cardPanel) {
            mpesaPanel.classList.toggle('cer-modal-hidden', method !== 'mpesa');
            cardPanel.classList.toggle('cer-modal-hidden', method !== 'card');
            mpesaPanel.setAttribute('aria-hidden', method !== 'mpesa' ? 'true' : 'false');
            cardPanel.setAttribute('aria-hidden', method !== 'card' ? 'true' : 'false');
            Array.prototype.forEach.call(mpesaPanel.querySelectorAll('input, select'), function (field) {
                field.disabled = method !== 'mpesa';
            });
            Array.prototype.forEach.call(cardPanel.querySelectorAll('input, select'), function (field) {
                field.disabled = method !== 'card';
            });
        }

        if (fullNameField && nameFieldContainer) {
            fullNameField.required = method === 'mpesa';
            nameFieldContainer.style.display = '';
        }

        if (mpesaField) {
            mpesaField.classList.toggle('hidden', method !== 'mpesa');
            mpesaField.classList.toggle('block', method === 'mpesa');
        }

        setFieldRequired('cer-phone', method === 'mpesa');
        if (submitButton) {
            submitButton.textContent = method === 'card' ? 'Register & Pay with Card' : 'Register & Pay with M-Pesa';
            submitButton.style.display = '';
        }
        if (cardTicketType && ticketType && method === 'card') {
            cardTicketType.value = ticketType.value;
        }
        if (cardEmailField && method === 'mpesa') {
            cardEmailField.value = '';
        }
        if (hostedPayment && method === 'card') {
            hostedPayment.classList.remove('cer-modal-hidden');
            hostedPayment.classList.remove('cer-payment-loading');
            hostedPayment.classList.add('cer-card-ready');
            if (hostedPaymentFrame) {
                hostedPaymentFrame.src = 'about:blank';
                hostedPaymentFrame.setAttribute('data-preloaded', 'true');
            }
        }
        updateSubmitState();
        updateSummary();
    }

    window.togglePaymentMethod = function (method) {
        setPaymentMethod(method === 'card' ? 'card' : 'mpesa');
    };

    function updateSummary() {
        var method = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'card' : 'mpesa';
        var activeTicketType = method === 'card' ? cardTicketType : ticketType;
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
        var emailField = method === 'card' ? cardEmailField : document.getElementById('cer-email');
        var activeTicketType = method === 'card' ? cardTicketType : ticketType;
        var valid = !!activeTicketType && !!activeTicketType.value && !!emailField && emailField.checkValidity();
        if (method === 'mpesa') {
            valid = valid && !!fullNameField && fullNameField.checkValidity();
            var phoneField = document.getElementById('cer-phone');
            valid = valid && !!phoneField && phoneField.value.trim() !== '';
        }
        submitButton.disabled = !valid;
        if (hostedPayment && method === 'card') {
            hostedPayment.classList.remove('cer-modal-hidden');
            hostedPayment.classList.toggle('cer-card-ready', valid);
        }
    }

    if (ticketType) {
        ticketType.addEventListener('change', function () {
            updateSummary();
            updateSubmitState();
        });
    }

    if (cardTicketType) {
        cardTicketType.addEventListener('change', function () {
            updateSummary();
            updateSubmitState();
        });
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

        var amountValue = amountInput ? amountInput.value : '0';
        var method = paymentMethodHidden && paymentMethodHidden.value === 'card' ? 'card' : 'mpesa';
        var activeTicketType = method === 'card' ? cardTicketType : ticketType;

        if (!activeTicketType || !activeTicketType.value) {
            setMessage('Please select a ticket type.', 'error');
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
        }

        setMessage('Submitting registration...', 'info');

        var formData = new FormData(form);
        formData.set('security', window.cerRegistrationSettings.nonce);
        if (method === 'card') {
            formData.set('email', cardEmailField.value);
            formData.set('ticket_type', cardTicketType.value);
            formData.set('ticket_type_id', cardTicketType.options[cardTicketType.selectedIndex].dataset.id || '0');
            formData.set('full_name', '');
            formData.set('phone', '');
            cardRequestInFlight = true;
            if (hostedPayment) {
                hostedPayment.classList.remove('cer-modal-hidden');
                hostedPayment.classList.add('cer-payment-loading');
                hostedPayment.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        fetch(window.cerRegistrationSettings.ajax_url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (result.success) {
                    setMessage(result.data.message, 'success');

                    currentTrackingId = result.data.tracking_id || '';

                    if (result.data.redirect_url && method === 'mpesa') {
                        setMessage('M-Pesa checkout is opening. Check your phone and enter your PIN.', 'info');
                        window.open(result.data.redirect_url, '_blank');
                        if (result.data.tracking_id) {
                            pollPaymentStatus(result.data.tracking_id);
                        }
                        return;
                    }

                    if (result.data.redirect_url && method === 'card') {
                        form.classList.add('cer-payment-submitted');
                        form.setAttribute('aria-hidden', 'true');
                        Array.prototype.forEach.call(form.elements, function (element) {
                            element.disabled = true;
                        });

                        if (hostedPayment && hostedPaymentFrame) {
                            setMessage('Secure card payment checkout is ready.', 'info');
                            hostedPayment.classList.add('cer-payment-loading');
                            hostedPayment.classList.remove('cer-modal-hidden');
                            hostedPaymentFrame.src = result.data.redirect_url;
                            hostedPayment.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }

                        if (result.data.tracking_id) {
                            pollPaymentStatus(result.data.tracking_id);
                        }
                        return;
                    }

                    if (parseFloat(amountValue || '0') <= 0) {
                        resetFormState();
                        return;
                    }

                    window.setTimeout(function () {
                        resetFormState();
                    }, method === 'card' ? 4500 : 5500);
                } else {
                    var message = (result.data && result.data.message) ? result.data.message : 'Registration failed.';
                    setMessage(message, 'error');
                }
            })
            .catch(function () {
                setMessage('Unable to submit registration, please try again.', 'error');
            })
            .finally(function () {
                cardRequestInFlight = false;
                if (submitButton) {
                    submitButton.disabled = false;
                }
                updateSubmitState();
            });
    }

    form.addEventListener('submit', submitRegistration);

    if (cardEmailField) {
        cardEmailField.addEventListener('input', function () {
            updateSubmitState();
        });
    }

})(document, window);
