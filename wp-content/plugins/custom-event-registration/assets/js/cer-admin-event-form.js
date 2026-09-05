(function ($) {
    'use strict';

    var repeaterTypes = {
        tickets: 'deleted_tickets',
        speakers: 'deleted_speakers',
        sponsors: 'deleted_sponsors',
        pillars: 'deleted_pillars',
        objectives: 'deleted_objectives',
        summit: 'deleted_summit',
        partners: 'deleted_partners',
        faqs: 'deleted_faqs'
    };

    function getNextIndex(container) {
        var rows = container.find('.cer-repeater-row');
        var maxIndex = 0;

        rows.each(function () {
            var rowIndex = parseInt($(this).data('rowIndex'), 10);
            if (!isNaN(rowIndex) && rowIndex > maxIndex) {
                maxIndex = rowIndex;
            }
        });

        return maxIndex + 1;
    }

    function createRow(type, index) {
        var row = '';

        if ('tickets' === type) {
            row = '<div class="cer-repeater-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="tickets[' + index + '][id]" value="" />' +
                '<div class="cer-field">' +
                    '<label>Ticket Name</label>' +
                    '<input type="text" name="tickets[' + index + '][name]" value="" placeholder="Community Outreach" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>Price</label>' +
                    '<input type="number" name="tickets[' + index + '][price]" value="" step="0.01" min="0" placeholder="5000" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>Quantity Available</label>' +
                    '<input type="number" name="tickets[' + index + '][quantity_available]" value="" min="0" placeholder="200" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Description</label>' +
                    '<textarea name="tickets[' + index + '][description]" placeholder="Describe what is included in this ticket type."></textarea>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Ticket #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('speakers' === type) {
            row = '<div class="cer-repeater-row cer-speaker-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="speakers[' + index + '][id]" value="" />' +
                '<div class="cer-speaker-photo-col">' +
                    '<input type="hidden" name="speakers[' + index + '][photo_id]" id="speaker-photo-' + index + '" value="" />' +
                    '<div id="speaker-photo-preview-' + index + '" class="cer-speaker-photo-thumb"></div>' +
                '</div>' +
                '<div class="cer-speaker-fields-col">' +
                    '<div class="cer-field">' +
                        '<label>Speaker Name</label>' +
                        '<input type="text" name="speakers[' + index + '][name]" value="" placeholder="Enter speaker\'s full name" />' +
                    '</div>' +
                    '<div class="cer-field">' +
                        '<label>Role / Organization</label>' +
                        '<input type="text" name="speakers[' + index + '][role]" value="" placeholder="e.g. Human Rights Advocate" />' +
                    '</div>' +
                    '<div class="cer-field cer-speaker-field-full">' +
                        '<label>Short Bio</label>' +
                        '<textarea name="speakers[' + index + '][bio]" placeholder="Short bio or profile summary"></textarea>' +
                    '</div>' +
                    '<div class="cer-field cer-speaker-field-full">' +
                        '<label class="cer-toggle"><input type="checkbox" name="speakers[' + index + '][is_visible]" value="1" checked /></label>' +
                    '</div>' +
                '</div>' +
                '<div class="cer-speaker-actions-col">' +
                    '<span class="cer-row-handle">Speaker #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button button-small cer-media-select" data-target="speaker-photo-' + index + '" data-preview="speaker-photo-preview-' + index + '">Select Photo</button>' +
                    '<button type="button" class="button button-small cer-media-clear" data-target="speaker-photo-' + index + '" data-preview="speaker-photo-preview-' + index + '">Remove Photo</button>' +
                    '<button type="button" class="button button-small cer-remove-row">Remove Speaker</button>' +
                '</div>' +
            '</div>';
        }

        if ('sponsors' === type) {
            row = '<div class="cer-repeater-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="sponsors[' + index + '][id]" value="" />' +
                '<div class="cer-field">' +
                    '<label>Package Name</label>' +
                    '<input type="text" name="sponsors[' + index + '][name]" value="" placeholder="Bronze" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>CTA URL</label>' +
                    '<input type="url" name="sponsors[' + index + '][cta_url]" value="" placeholder="https://example.com/contact" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Description</label>' +
                    '<textarea name="sponsors[' + index + '][description]" placeholder="Write a brief description of the package."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Benefits</label>' +
                    '<textarea name="sponsors[' + index + '][benefits]" placeholder="One benefit per line or a short paragraph."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label class="cer-toggle"><input type="checkbox" name="sponsors[' + index + '][is_visible]" value="1" checked /></label>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Package #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('pillars' === type) {
            row = '<div class="cer-repeater-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="pillars[' + index + '][id]" value="" />' +
                '<div class="cer-field">' +
                    '<label>Pillar Title</label>' +
                    '<input type="text" name="pillars[' + index + '][title]" value="" placeholder="Policy & Governance" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>Icon / Symbol</label>' +
                    '<input type="text" name="pillars[' + index + '][icon]" value="" placeholder="policy" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Description</label>' +
                    '<textarea name="pillars[' + index + '][description]" placeholder="Describe what this pillar covers."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label class="cer-toggle"><input type="checkbox" name="pillars[' + index + '][is_visible]" value="1" checked /></label>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Pillar #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('objectives' === type) {
            row = '<div class="cer-repeater-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="objectives[' + index + '][id]" value="" />' +
                '<div class="cer-field">' +
                    '<label>Objective Title</label>' +
                    '<input type="text" name="objectives[' + index + '][title]" value="" placeholder="Strengthen Coordination" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>Icon / Symbol</label>' +
                    '<input type="text" name="objectives[' + index + '][icon]" value="" placeholder="flag" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Description</label>' +
                    '<textarea name="objectives[' + index + '][description]" placeholder="Describe this objective."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label class="cer-toggle"><input type="checkbox" name="objectives[' + index + '][is_visible]" value="1" checked /></label>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Objective #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('summit' === type) {
            row = '<div class="cer-repeater-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="summit[' + index + '][id]" value="" />' +
                '<div class="cer-field">' +
                    '<label>Structure Title</label>' +
                    '<input type="text" name="summit[' + index + '][title]" value="" placeholder="Day 1: Plenary Sessions" />' +
                '</div>' +
                '<div class="cer-field">' +
                    '<label>Icon / Symbol</label>' +
                    '<input type="text" name="summit[' + index + '][icon]" value="" placeholder="event_note" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Description</label>' +
                    '<textarea name="summit[' + index + '][description]" placeholder="Describe this part of the summit structure."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label class="cer-toggle"><input type="checkbox" name="summit[' + index + '][is_visible]" value="1" checked /></label>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Item #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('partners' === type) {
            row = '<div class="cer-repeater-row cer-speaker-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="partners[' + index + '][id]" value="" />' +
                '<div class="cer-speaker-photo-col">' +
                    '<input type="hidden" name="partners[' + index + '][logo_id]" id="partner-logo-' + index + '" value="" />' +
                    '<div id="partner-logo-preview-' + index + '" class="cer-speaker-photo-thumb"></div>' +
                '</div>' +
                '<div class="cer-speaker-fields-col">' +
                    '<div class="cer-field">' +
                        '<label>Partner Name</label>' +
                        '<input type="text" name="partners[' + index + '][name]" value="" placeholder="Ministry of Health" />' +
                    '</div>' +
                    '<div class="cer-field">' +
                        '<label>Website Link</label>' +
                        '<input type="url" name="partners[' + index + '][link_url]" value="" placeholder="https://example.org" />' +
                    '</div>' +
                    '<div class="cer-field cer-speaker-field-full">' +
                        '<label class="cer-toggle"><input type="checkbox" name="partners[' + index + '][is_visible]" value="1" checked /></label>' +
                    '</div>' +
                '</div>' +
                '<div class="cer-speaker-actions-col">' +
                    '<span class="cer-row-handle">Partner #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button button-small cer-media-select" data-target="partner-logo-' + index + '" data-preview="partner-logo-preview-' + index + '">Select Logo</button>' +
                    '<button type="button" class="button button-small cer-media-clear" data-target="partner-logo-' + index + '" data-preview="partner-logo-preview-' + index + '">Remove Logo</button>' +
                    '<button type="button" class="button button-small cer-remove-row">Remove Partner</button>' +
                '</div>' +
            '</div>';
        }

        if ('faqs' === type) {
            row = '<div class="cer-repeater-row cer-single-col" data-row-index="' + index + '">' +
                '<input type="hidden" name="faqs[' + index + '][id]" value="" />' +
                '<div class="cer-field cer-full">' +
                    '<label>Question</label>' +
                    '<input type="text" name="faqs[' + index + '][question]" value="" placeholder="How do I register?" />' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label>Answer</label>' +
                    '<textarea name="faqs[' + index + '][answer]" placeholder="Provide the answer to this question."></textarea>' +
                '</div>' +
                '<div class="cer-field cer-full">' +
                    '<label class="cer-toggle"><input type="checkbox" name="faqs[' + index + '][is_visible]" value="1" checked /></label>' +
                '</div>' +
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Question #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        return $(row);
    }

    function openMediaPicker(button) {
        var targetId = button.data('target');
        var previewId = button.data('preview');
        var mediaUploader = wp.media({
            title: 'Select Media',
            multiple: false
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            var target = $('#' + targetId);
            var preview = $('#' + previewId);

            if (target.length) {
                target.val(attachment.id);
            }

            if (preview.length) {
                if (attachment.url) {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; border-radius: 8px;" alt="" />');
                } else {
                    preview.html('');
                }
            }
        });

        mediaUploader.open();
    }

    function addHiddenDeleteInput(type, id) {
        var key = repeaterTypes[type];
        if (!key || !id) {
            return;
        }

        var deletedInputs = $('.cer-deleted-inputs');
        $('<input>', {
            type: 'hidden',
            name: key + '[]',
            value: id
        }).appendTo(deletedInputs);
    }

    function initDatetimePickers() {
        if (!window.flatpickr) {
            return;
        }

        $('.cer-datetime-picker').each(function () {
            var $field = $(this);
            if ($field.data('flatpickr')) {
                return;
            }

            $field.flatpickr({
                enableTime: true,
                time_24hr: true,
                dateFormat: 'Y-m-d H:i',
                allowInput: true,
                altInput: false
            });
        });
    }

    $('.cer-add-row').on('click', function () {
        var type = $(this).data('repeater');
        var container = $('.cer-repeater[data-repeater="' + type + '"]');
        var index = getNextIndex(container);
        var row = createRow(type, index);
        container.append(row);
    });

    $(document).on('click', '.cer-remove-row', function () {
        var row = $(this).closest('.cer-repeater-row');
        var repeater = row.closest('.cer-repeater');
        var type = repeater.data('repeater');
        var existingId = row.find('input[name$="[id]"]').val();

        if (existingId) {
            addHiddenDeleteInput(type, existingId);
        }

        row.remove();

        if (repeater.find('.cer-repeater-row').length < 1) {
            repeater.append(createRow(type, getNextIndex(repeater)));
        }
    });

    initDatetimePickers();

    $(document).on('click', '.cer-media-select', function () {
        openMediaPicker($(this));
    });

    $(document).on('click', '.cer-media-clear', function () {
        var target = $(this).data('target');
        var preview = $(this).data('preview');

        if (target) {
            $('#' + target).val('');
        }

        if (preview) {
            $('#' + preview).html('');
        }
    });

    function mapEmbedUrlFromQuery(query) {
        return 'https://www.google.com/maps?q=' + encodeURIComponent(query) + '&output=embed';
    }

    function mapLinkFromQuery(query) {
        return 'https://www.google.com/maps?q=' + encodeURIComponent(query);
    }

    $('#cer-open-map-search').on('click', function () {
        var $modal = $('#cer-map-modal');
        var initialQuery = $('#location-address').val() || $('#location-link').val() || '';
        $('#cer-map-search-input').val(initialQuery);
        if (initialQuery) {
            $('#cer-map-embed').attr('src', mapEmbedUrlFromQuery(initialQuery));
        }
        $modal.attr('hidden', false);
    });

    $('#cer-close-map-search').on('click', function () {
        $('#cer-map-modal').attr('hidden', true);
    });

    $('#cer-map-modal .cer-map-modal-backdrop').on('click', function () {
        $('#cer-map-modal').attr('hidden', true);
    });

    $('#cer-map-search-go').on('click', function () {
        var query = $('#cer-map-search-input').val().trim();
        if (!query) {
            return;
        }
        $('#cer-map-embed').attr('src', mapEmbedUrlFromQuery(query));
    });

    $('#cer-map-use-location').on('click', function () {
        var query = $('#cer-map-search-input').val().trim();
        if (query) {
            $('#location-link').val(mapLinkFromQuery(query));
            $('#location-address').val(query);
        }
        $('#cer-map-modal').attr('hidden', true);
    });

    $('#cer-use-coordinates').on('click', function () {
        var lat = $('#location-lat').val().trim();
        var lng = $('#location-lng').val().trim();
        if (lat && lng) {
            $('#location-link').val('https://www.google.com/maps?q=' + encodeURIComponent(lat + ',' + lng));
        }
    });

    $('.cer-panel').each(function (index) {
        if (index > 0 && !$(this).hasClass('is-collapsed')) {
            $(this).addClass('is-collapsed');
        }

        var panel = $(this);
        var header = panel.find('.cer-panel-header');
        if (!header.length) {
            return;
        }

        var actions = header.find('.cer-panel-header-actions');
        if (header.find('.cer-panel-toggle').length) {
            return;
        }

        if (!actions.length) {
            actions = $('<div class="cer-panel-header-actions"></div>');
            header.append(actions);
        }

        var collapsed = panel.hasClass('is-collapsed');
        var toggle = $('<button type="button" class="cer-panel-toggle" aria-expanded="' + (collapsed ? 'false' : 'true') + '"><span class="dashicons ' + (collapsed ? 'dashicons-arrow-down-alt2' : 'dashicons-arrow-up-alt2') + '"></span></button>');
        actions.append(toggle);
    });

    $(document).on('click', '.cer-panel-toggle', function () {
        var button = $(this);
        var panel = button.closest('.cer-panel');
        var icon = button.find('.dashicons');
        var collapsed = panel.toggleClass('is-collapsed').hasClass('is-collapsed');

        button.attr('aria-expanded', collapsed ? 'false' : 'true');
        icon.toggleClass('dashicons-arrow-up-alt2', !collapsed);
        icon.toggleClass('dashicons-arrow-down-alt2', collapsed);
    });
})(jQuery); 
