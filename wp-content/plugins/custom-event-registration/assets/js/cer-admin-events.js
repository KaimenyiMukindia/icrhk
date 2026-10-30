(function ($) {
    'use strict';

    var $modal = $('#cer-share-modal');
    var $url = $('#cer-share-url');
    var $name = $modal.find('.cer-share-event-name');
    var $copyStatus = $modal.find('.cer-share-copy-status');
    var activeDialog = null;

    function arrangeRadial($items, inner, outer, start) {
        $items.each(function (index) {
            var step = Math.PI / $items.length;
            var from = start + step * index + 0.015;
            var to = start + step * (index + 1) - 0.015;
            var points = [];
            function point(radius, angle) {
                return (50 + radius * Math.cos(angle) * 50) + '% ' + (50 + radius * Math.sin(angle) * 50) + '%';
            }
            for (var segment = 0; segment <= 16; segment++) {
                points.push(point(outer, from + (to - from) * segment / 16));
            }
            for (var reverse = 16; reverse >= 0; reverse--) {
                points.push(point(inner, from + (to - from) * reverse / 16));
            }
            this.style.clipPath = 'polygon(' + points.join(',') + ')';
            var angle = (from + to) / 2;
            this.style.setProperty('--cer-label-x', (50 + (inner + outer) * 25 * Math.cos(angle)) + '%');
            this.style.setProperty('--cer-label-y', (50 + (inner + outer) * 25 * Math.sin(angle)) + '%');
            if (!$(this).children('.cer-slice-label').length) {
                $(this).wrapInner('<span class="cer-slice-label"></span>');
            }
        });
    }

    function positionMenu() {
        if (!activeDialog || !activeDialog.open) {
            return;
        }
        var trigger = document.querySelector('[aria-controls="' + activeDialog.id + '"]');
        var bounds = trigger.getBoundingClientRect();
        var x = bounds.left + bounds.width / 2;
        var y = bounds.top + bounds.height / 2;
        var width = document.documentElement.clientWidth;
        var height = window.innerHeight;
        if (bounds.bottom < 0 || bounds.top > height || bounds.right < 0 || bounds.left > width) {
            activeDialog.close();
            return;
        }
        var topEdge = $modal.is('[hidden]') ? 12 : $modal.find('.cer-share-modal-dialog')[0].getBoundingClientRect().bottom + 12;
        var directions = [
            { start: Math.PI / 2, space: Math.min(x - 12, y - topEdge, height - y - 12) },
            { start: -Math.PI / 2, space: Math.min(width - x - 12, y - topEdge, height - y - 12) },
            { start: Math.PI, space: Math.min(y - topEdge, x - 12, width - x - 12) },
            { start: 0, space: Math.min(height - y - 12, x - 12, width - x - 12) }
        ];
        directions.sort(function (a, b) { return b.space - a.space; });
        var direction = directions[0];
        var radius = Math.max(1, Math.min(280, direction.space));
        activeDialog.style.setProperty('--cer-radius', radius + 'px');
        activeDialog.style.left = (x - radius) + 'px';
        activeDialog.style.top = (y - radius) + 'px';
        arrangeRadial($(activeDialog).find('.cer-row-action-list > a, .cer-row-action-list > button'), 0.18, 0.57, direction.start);
        arrangeRadial($modal.find('.cer-share-platform'), 0.59, 0.99, direction.start);
    }

    $('.cer-event-actions-dialog').each(function () {
        var dialog = this;
        $(dialog).on('click', '.cer-radial-close', function () { dialog.close(); });
        $(dialog).on('cancel', function (event) {
            if (!$modal.is('[hidden]')) {
                event.preventDefault();
                closeModal();
            }
        });
        $(dialog).on('close', function () {
            closeModal(false);
            $('[aria-controls="' + dialog.id + '"]').attr('aria-expanded', 'false');
            if (activeDialog === dialog) {
                activeDialog = null;
            }
        });
        $(dialog).on('click', function (event) {
            if (event.target === dialog || event.target.classList.contains('cer-row-action-list')) {
                dialog.close();
            }
        });
    });

    $(document).on('click', '.cer-event-actions-trigger', function () {
        this.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'instant' });
        activeDialog = document.getElementById(this.getAttribute('aria-controls'));
        activeDialog.appendChild($modal[0]);
        activeDialog.showModal();
        positionMenu();
        this.setAttribute('aria-expanded', 'true');
    });
    window.addEventListener('resize', positionMenu);
    window.addEventListener('scroll', positionMenu, true);

    function buildShareLinks(title, eventUrl) {
        var encodedUrl = encodeURIComponent(eventUrl);
        var encodedText = encodeURIComponent('Join ' + title);

        return {
            facebook: 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl,
            twitter: 'https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodedText,
            linkedin: 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl,
            whatsapp: 'https://wa.me/?text=' + encodedText + '%20' + encodedUrl,
            email: 'mailto:?subject=' + encodeURIComponent(title) + '&body=' + encodeURIComponent('Join ' + title + ': ' + eventUrl)
        };
    }

    function openModal(button) {
        var title = button.attr('data-event-title') || '';
        var eventUrl = button.attr('data-event-url') || '';
        var links = buildShareLinks(title, eventUrl);

        if (!$modal.is('[hidden]')) {
            closeModal();
            return;
        }
        $name.text(title);
        $url.val(eventUrl);
        $copyStatus.removeClass('is-error').text('');
        $.each(links, function (platform, link) {
            $modal.find('[data-share-platform="' + platform + '"]').attr('href', link);
        });
        $modal.removeAttr('hidden');
        button.attr('aria-expanded', 'true');
        positionMenu();
        $modal.find('.cer-share-platform').first().trigger('focus');
    }

    function closeModal(returnFocus) {
        $modal.attr('hidden', 'hidden');
        if (activeDialog) {
            var $share = $(activeDialog).find('.cer-share-event');
            $share.attr('aria-expanded', 'false');
            if (returnFocus !== false) {
                $share.trigger('focus');
            }
            positionMenu();
        }
    }

    $(document).on('click', '.cer-share-event', function () {
        openModal($(this));
    });

    $(document).on('click', '[data-cer-share-close="1"]', function () {
        closeModal();
    });

    $('#cer-copy-share-url').on('click', function () {
        var input = $url[0];
        if (!input) {
            return;
        }

        var $button = $(this);
        function copied() {
            $copyStatus.removeClass('is-error').text($button.attr('data-copied'));
            $button.trigger('focus');
        }
        function fallbackCopy() {
            input.focus();
            input.select();
            try {
                if (!document.execCommand('copy')) {
                    throw new Error('Clipboard copy was refused.');
                }
                copied();
            } catch (error) {
                console.warn('CER share link copy failed', error);
                $copyStatus.addClass('is-error').text($button.attr('data-copy-error'));
            }
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(copied).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }
    });
})(jQuery);