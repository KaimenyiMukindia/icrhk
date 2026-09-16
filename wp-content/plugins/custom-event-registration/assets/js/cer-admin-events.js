(function ($) {
    'use strict';

    var $modal = $('#cer-share-modal');
    var $dialog = $modal.find('.cer-share-modal-dialog');
    var $url = $('#cer-share-url');
    var $name = $modal.find('.cer-share-event-name');
    var $copyStatus = $modal.find('.cer-share-copy-status');
    var lastFocusedElement = null;

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

        lastFocusedElement = document.activeElement;
        $name.text(title);
        $url.val(eventUrl);
        $copyStatus.text('');
        $.each(links, function (platform, link) {
            $modal.find('[data-share-platform="' + platform + '"]').attr('href', link);
        });
        $modal.removeAttr('hidden');
        $('body').addClass('cer-share-modal-open');
        $dialog.trigger('focus');
    }

    function closeModal() {
        $modal.attr('hidden', 'hidden');
        $('body').removeClass('cer-share-modal-open');
        if (lastFocusedElement) {
            $(lastFocusedElement).trigger('focus');
        }
    }

    $(document).on('click', '.cer-share-event', function () {
        openModal($(this));
    });

    $(document).on('click', '[data-cer-share-close="1"]', closeModal);

    $(document).on('keydown', function (event) {
        if ('Escape' === event.key && !$modal.is('[hidden]')) {
            closeModal();
        }
    });

    $('#cer-copy-share-url').on('click', function () {
        var input = $url[0];
        if (!input) {
            return;
        }

        input.select();
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value);
        } else {
            document.execCommand('copy');
        }
        $copyStatus.text('Copied');
    });
})(jQuery);