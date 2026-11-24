(function ($) {
    'use strict';

    var strings = window.cerAdminEventFormStrings || {};
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
        var template = document.getElementById('cer-template-' + type);
        if (template) {
            return $(template.innerHTML.replace(/__INDEX__/g, String(index)));
        }

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
                '<div class="cer-field cer-full">' +
                    '<label><input type="checkbox" name="tickets[' + index + '][is_maintenance]" value="1" /> Scheduled for maintenance (temporarily disable registration)</label>' +
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
                    '<label for="speaker-photo-select-' + index + '">Speaker Photo</label>' +
                    '<button type="button" id="speaker-photo-select-' + index + '" class="button button-small cer-media-select" data-target="speaker-photo-' + index + '" data-preview="speaker-photo-preview-' + index + '">Select Photo</button>' +
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
                '</div>' +
                '<div class="cer-speaker-actions-col">' +
                    '<span class="cer-row-handle">Speaker #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button button-small cer-media-clear" data-target="speaker-photo-' + index + '" data-preview="speaker-photo-preview-' + index + '" aria-label="Clear speaker photo" disabled>Remove Photo</button>' +
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
                '<div class="cer-repeater-actions">' +
                    '<span class="cer-row-handle">Item #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button cer-remove-row">Remove</button>' +
                '</div>' +
            '</div>';
        }

        if ('partners' === type) {
            row = '<div class="cer-repeater-row cer-speaker-row cer-partner-row" data-row-index="' + index + '">' +
                '<input type="hidden" name="partners[' + index + '][id]" value="" />' +
                '<div class="cer-speaker-photo-col">' +
                    '<input type="hidden" name="partners[' + index + '][logo_id]" id="partner-logo-' + index + '" value="" />' +
                    '<div id="partner-logo-preview-' + index + '" class="cer-speaker-photo-thumb"></div>' +
                    '<label for="partner-logo-select-' + index + '">Partner Logo</label>' +
                    '<button type="button" id="partner-logo-select-' + index + '" class="button button-small cer-media-select" data-target="partner-logo-' + index + '" data-preview="partner-logo-preview-' + index + '">Select Logo</button>' +
                '</div>' +
                '<div class="cer-speaker-fields-col cer-partner-fields-col">' +
                    '<div class="cer-field">' +
                        '<label>Partner Name</label>' +
                        '<input type="text" name="partners[' + index + '][name]" value="" placeholder="Ministry of Health" />' +
                    '</div>' +
                    '<div class="cer-field">' +
                        '<label>Website Link</label>' +
                        '<input type="url" name="partners[' + index + '][url]" value="" placeholder="https://example.org" />' +
                    '</div>' +
                    '<div class="cer-partner-contact-group">' +
                        '<div class="cer-field">' +
                            '<label>Tel No</label>' +
                            '<input type="tel" name="partners[' + index + '][tel_no]" value="" placeholder="+254 20 123 4567" />' +
                        '</div>' +
                        '<div class="cer-field">' +
                            '<label>Email</label>' +
                            '<input type="email" name="partners[' + index + '][email]" value="" placeholder="hello@example.org" />' +
                        '</div>' +
                        '<div class="cer-field cer-partner-address-field">' +
                            '<label>Address</label>' +
                            '<input type="text" name="partners[' + index + '][address]" value="" placeholder="Nairobi, Kenya" />' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="cer-speaker-actions-col">' +
                    '<span class="cer-row-handle">Partner #'+ (index + 1) +'</span>' +
                    '<button type="button" class="button button-small cer-media-clear" data-target="partner-logo-' + index + '" data-preview="partner-logo-preview-' + index + '" aria-label="Clear partner logo" disabled>Remove Logo</button>' +
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
                $('.cer-media-clear').filter(function () {
                    return $(this).data('target') === targetId;
                }).prop('disabled', false);
            }
            $('#cer-form-status').text(strings.mediaSelected || 'Image selected. Save the event to apply this change.');

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

    function createPanel(title, tab) {
        return $('<section class="cer-panel" data-cer-tab="' + tab + '">' +
            '<div class="cer-panel-header"><h2></h2><div class="cer-panel-header-actions"></div></div>' +
            '<div class="cer-panel-body"><div class="cer-field-grid"></div></div>' +
            '</section>').find('h2').text(title).end();
    }

    function prepareEventTabs() {
        var $form = $('#cer-event-form');
        var $layout = $form.find('.cer-admin-layout').first();
        var $identityPanel = $layout.find('.cer-panel').first();
        var $fieldGrid = $identityPanel.find('.cer-field-grid').first();
        if (!$form.length || !$layout.length || !$identityPanel.length || !$fieldGrid.length) {
            return;
        }

        var identityNames = {
            event_slug: true,
            event_date: true,
            event_end_date: true,
            status: true,
            max_attendees: true,
            event_uuid: true
        };
        var appearanceNames = {
            brand_color_1: true,
            brand_color_2: true,
            background_image_id: true
        };
        var contentPanel = createPanel(strings.panels && strings.panels.content ? strings.panels.content : 'Page Content & Search Preview', 'content');
        var appearancePanel = createPanel(strings.panels && strings.panels.appearance ? strings.panels.appearance : 'Branding & Media', 'appearance');
        appearancePanel.find('.cer-field-grid').addClass('cer-branding-grid');

        $fieldGrid.children('.cer-field').detach().each(function () {
            var $field = $(this);
            var name = $field.find('[name]').first().attr('name') || '';
            if (identityNames[name]) {
                $fieldGrid.append($field);
            } else if (appearanceNames[name]) {
                appearancePanel.find('.cer-field-grid').append($field);
            } else {
                contentPanel.find('.cer-field-grid').append($field);
            }
        });

        ['background_image_id', 'brand_color_1', 'brand_color_2'].forEach(function (name) {
            var $field = appearancePanel.find('[name="' + name + '"]').closest('.cer-field');
            $field.addClass('cer-branding-' + name.replace(/_/g, '-'));
            appearancePanel.find('.cer-field-grid').append($field);
        });

        $identityPanel.attr('data-cer-tab', 'setup');
        $identityPanel.find('.cer-panel-header h2').text(strings.panels && strings.panels.identity ? strings.panels.identity : 'Event Identity & Publishing');
        var panels = [];
        $form.find('.cer-panel').detach().each(function () {
            var $panel = $(this);
            var tab = $panel.attr('data-cer-tab') || 'content';
            $panel.attr('data-cer-tab', tab);
            panels.push($panel);
        });

        panels.unshift(contentPanel);
        panels.push(appearancePanel);
        var labels = strings.tabs || {};
        var tabOrder = ['setup', 'content', 'program', 'partners', 'registration', 'appearance', 'mail'];
        var $tabs = $('<div class="cer-event-tabs"></div>');
        var $tabList = $('<div class="cer-event-tab-list" role="tablist"></div>').attr('aria-label', strings.eventSections || 'Event sections');
        var $tabPanels = $('<div class="cer-event-tab-panels"></div>');
        tabOrder.forEach(function (tab) {
            var panelId = 'cer-event-tab-panel-' + tab;
            var tabId = 'cer-event-tab-' + tab;
            var $button = $('<button type="button" class="cer-event-tab" role="tab"></button>')
                .attr('id', tabId)
                .attr('aria-controls', panelId)
                .attr('data-cer-tab-select', tab)
                .text(labels[tab] || tab);
            var $panel = $('<section class="cer-event-tab-panel" role="tabpanel"></section>')
                .attr('id', panelId)
                .attr('aria-labelledby', tabId)
                .attr('data-cer-tab-panel', tab)
                .prop('hidden', true);
            panels.forEach(function ($section) {
                if ($section.attr('data-cer-tab') === tab) {
                    $panel.append($section);
                }
            });
            $tabList.append($button);
            $tabPanels.append($panel);
        });

        $tabs.append($tabList, $tabPanels);
        $form.prepend($tabs);

        function selectTab(tab, moveFocus) {
            if (tabOrder.indexOf(tab) === -1) {
                tab = 'setup';
            }
            $tabList.find('[role="tab"]').each(function () {
                var selected = $(this).attr('data-cer-tab-select') === tab;
                $(this).attr('aria-selected', selected ? 'true' : 'false').attr('tabindex', selected ? '0' : '-1');
            });
            $tabPanels.children('.cer-event-tab-panel').each(function () {
                $(this).prop('hidden', $(this).attr('data-cer-tab-panel') !== tab);
            });
            $form.find('#cer-event-active-tab').val(tab);
            if (moveFocus) {
                $tabList.find('[data-cer-tab-select="' + tab + '"]').trigger('focus');
            }
        }

        $tabList.on('click', '[role="tab"]', function () {
            selectTab($(this).attr('data-cer-tab-select'), false);
        });
        $tabList.on('keydown', '[role="tab"]', function (event) {
            var index = tabOrder.indexOf($(this).attr('data-cer-tab-select'));
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft' || event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                if (event.key === 'ArrowRight') {
                    index = (index + 1) % tabOrder.length;
                } else if (event.key === 'ArrowLeft') {
                    index = (index + tabOrder.length - 1) % tabOrder.length;
                } else if (event.key === 'Home') {
                    index = 0;
                } else {
                    index = tabOrder.length - 1;
                }
                selectTab(tabOrder[index], true);
            }
        });

        selectTab($form.find('#cer-event-active-tab').val(), false);
    }

    function initializeFieldTabs($panel, groups) {
        var $grid = $panel.find('.cer-field-grid').first();
        var $fields = $grid.children('.cer-field').detach();
        var $tabs = $('<div class="cer-item-tab-list" role="tablist"></div>')
            .attr('aria-label', $panel.find('h2').first().text());
        var $panels = $('<div class="cer-item-tab-panels"></div>');
        var prefix = $panel.attr('data-cer-fields');
        groups.forEach(function (group, index) {
            var id = 'cer-fields-' + prefix + '-' + index;
            var $tab = $('<button type="button" class="cer-item-tab" role="tab"></button>')
                .attr({id: id + '-tab', 'aria-controls': id})
                .text(group.label);
            var $body = $('<section class="cer-item-tab-panel cer-field-grid" role="tabpanel"></section>')
                .attr({id: id, 'aria-labelledby': id + '-tab'});
            $fields.each(function () {
                var name = $(this).find('[name]').first().attr('name') || '';
                if (group.matches(name)) {
                    $body.append(this);
                }
            });
            $tabs.append($tab);
            $panels.append($body);
        });
        $fields.each(function () {
            if (!$.contains($panels[0], this)) {
                $panels.children().first().append(this);
            }
        });
        $grid.replaceWith($tabs, $panels);
        function select(index, focus) {
            $tabs.children().each(function (position) {
                $(this).attr({'aria-selected': position === index ? 'true' : 'false', tabindex: position === index ? 0 : -1});
                $panels.children().eq(position).prop('hidden', position !== index);
            });
            if (focus) {
                $tabs.children().eq(index).trigger('focus');
            }
        }
        $tabs.on('click', '[role="tab"]', function () {
            select($tabs.children().index(this), false);
        }).on('keydown', '[role="tab"]', function (event) {
            var index = $tabs.children().index(this);
            if (['ArrowLeft', 'ArrowRight', 'Home', 'End'].indexOf(event.key) === -1) {
                return;
            }
            event.preventDefault();
            if (event.key === 'Home') {
                index = 0;
            } else if (event.key === 'End') {
                index = groups.length - 1;
            } else {
                index = (index + (event.key === 'ArrowRight' ? 1 : groups.length - 1)) % groups.length;
            }
            select(index, true);
        });
        select(0, false);
    }

    function initializeContentTabs() {
        var labels = strings.contentGroups || {};
        var $content = $('[data-cer-tab="content"]');
        $content.each(function (index) {
            var $panel = $(this).attr('data-cer-fields', index);
            var names = $panel.find('[name]').map(function () { return this.name; }).get();
            if (names.indexOf('event_description') !== -1) {
                initializeFieldTabs($panel, [
                    {label: labels.hero || 'Hero content', matches: function (name) { return name === 'event_title' || name === 'event_description' || name === 'secondary_logo_id' || /^(hero_|primary_cta_|secondary_cta_)/.test(name); }},
                    {label: labels.search || 'Search preview', matches: function (name) { return /^meta_/.test(name); }}
                ]);
                $panel.find('[name="event_description"]').closest('.cer-field').addClass('cer-hero-description-field');
                $panel.find('[name="hero_features"]').closest('.cer-field').addClass('cer-hero-features-field');
                $panel.find('[name="secondary_logo_id"]').closest('.cer-field').addClass('cer-hero-logo-field');
                $panel.find('[name="event_title"]').closest('[role="tabpanel"]').addClass('cer-hero-fields');
            }
        });
    }

    function editableControls($field) {
        return $field.find('input, textarea, select').filter(function () {
            var $control = $(this);
            var type = ($control.attr('type') || '').toLowerCase();
            return !this.disabled && !this.readOnly &&
                !['hidden', 'checkbox', 'radio', 'button', 'submit', 'reset', 'file', 'color', 'image'].includes(type);
        });
    }

    var tabbedRepeaterFields = {
        tickets: 'name',
        speakers: 'name',
        sponsors: 'name',
        pillars: 'title',
        objectives: 'title',
        summit: 'title',
        partners: 'name',
        faqs: 'question',
        hero_details: 'label',
        page_content: 'header'
    };
    function initializeRepeaterTabs($container, preferredIndex) {
        var type = $container.data('repeater');
        var field = tabbedRepeaterFields[type];
        if (!field) {
            return;
        }

        var $rows = $container.find('.cer-repeater-row').detach();
        $container.children('.cer-item-tab-list, .cer-item-tab-panels').remove();
        if (!$rows.length) {
            return;
        }

        var $tabList = $('<div class="cer-item-tab-list" role="tablist"></div>')
            .attr('aria-label', $container.closest('.cer-panel').find('h2').first().text());
        var $panels = $('<div class="cer-item-tab-panels"></div>');
        var $activeTabInput = $('[name="cer_item_active_tabs[' + type + ']"]');
        var rows = [];
        var selectedIndex = preferredIndex;

        $rows.each(function () {
            var $row = $(this);
            var rowIndex = String($row.attr('data-row-index'));
            var panelId = 'cer-' + type + '-panel-' + rows.length;
            var tabId = 'cer-' + type + '-tab-' + rows.length;
            var $panel = $('<section class="cer-item-tab-panel" role="tabpanel"></section>')
                .attr('id', panelId)
                .attr('aria-labelledby', tabId)
                .prop('hidden', true);
            $panel.append($row);
            $panels.append($panel);
            rows.push({
                row: $row,
                panel: $panel,
                index: rowIndex,
                tabId: tabId,
                panelId: panelId
            });
            if ($row.find('.cer-inline-error').length && typeof selectedIndex === 'undefined') {
                selectedIndex = rowIndex;
            }
        });

        if (typeof selectedIndex === 'undefined') {
            selectedIndex = String($activeTabInput.val() || rows[0].index);
        }
        var selectedPosition = rows.findIndex(function (entry) {
            return entry.index === String(selectedIndex);
        });
        if (selectedPosition < 0) {
            selectedPosition = 0;
        }

        function rowLabel(entry, position) {
            var selector = '[name^="' + type + '["][name$="][' + field + ']"]';
            var value = $.trim(entry.row.find(selector).first().val() || '');
            if (value) {
                return value;
            }
            return (strings.itemFallback || 'Item') + ' ' + (position + 1);
        }

        rows.forEach(function (entry, position) {
            var $tab = $('<button type="button" class="cer-item-tab" role="tab"></button>')
                .attr('id', entry.tabId)
                .attr('aria-controls', entry.panelId)
                .attr('data-row-index', entry.index)
                .text(rowLabel(entry, position))
                .attr('title', rowLabel(entry, position));
            $tabList.append($tab);
        });

        $container.append($tabList, $panels);
        function selectItem(focusSelected) {
            rows.forEach(function (entry, position) {
                var selected = position === selectedPosition;
                var $tab = $tabList.children().eq(position);
                $tab.attr('aria-selected', selected ? 'true' : 'false')
                    .attr('tabindex', selected ? '0' : '-1');
                entry.panel.prop('hidden', !selected);
                if (selected) {
                    selectedPosition = position;
                    selectedIndex = entry.index;
                }
            });
            $activeTabInput.val(selectedIndex);
            if (focusSelected) {
                $tabList.children().eq(selectedPosition).trigger('focus');
            }
            var tab = $tabList.children().get(selectedPosition);
            if (tab && $tabList[0].getClientRects().length) {
                var tabBounds = tab.getBoundingClientRect();
                var listBounds = $tabList[0].getBoundingClientRect();
                if (tabBounds.left < listBounds.left) {
                    $tabList[0].scrollLeft += tabBounds.left - listBounds.left;
                } else if (tabBounds.right > listBounds.right) {
                    $tabList[0].scrollLeft += tabBounds.right - listBounds.right;
                }
            }
        }

        $tabList.off('.cerItemTabs').on('click.cerItemTabs', '[role="tab"]', function () {
            var position = $tabList.children().index(this);
            selectedPosition = position;
            selectedIndex = rows[position].index;
            selectItem(false);
        }).on('keydown.cerItemTabs', '[role="tab"]', function (event) {
            var position = $tabList.children().index(this);
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft' || event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                if (event.key === 'ArrowRight') {
                    position = (position + 1) % rows.length;
                } else if (event.key === 'ArrowLeft') {
                    position = (position + rows.length - 1) % rows.length;
                } else if (event.key === 'Home') {
                    position = 0;
                } else {
                    position = rows.length - 1;
                }
                selectedPosition = position;
                selectedIndex = rows[position].index;
                selectItem(true);
            }
        });

        $container.off('.cerItemTabs').on('input.cerItemTabs change.cerItemTabs', '[name^="' + type + '["][name$="][' + field + ']"]', function () {
            var $row = $(this).closest('.cer-repeater-row');
            var position = $panels.children().index($row.closest('.cer-item-tab-panel'));
            if (position >= 0) {
                $tabList.children().eq(position).text(rowLabel(rows[position], position))
                    .attr('title', rowLabel(rows[position], position));
            }
        });

        selectItem(false);
    }

    function controlLabel($control) {
        var $field = $control.closest('.cer-field');
        var $label = $field.find('label').not('.cer-toggle').first();
        if (!$label.length) {
            $label = $field.find('.cer-toggle span').first();
        }
        return $.trim($label.text()).replace(/\s+/g, ' ') || strings.editorTitle || 'Expanded editor';
    }

    var controlId = 0;

    function enhanceEditableFields($root) {
        $root.find('.cer-field').addBack('.cer-field').each(function () {
            var $field = $(this);
            var $controls = editableControls($field).filter(function () {
                return $(this).closest('.cer-field')[0] === $field[0];
            });
            if (!$controls.length) {
                return;
            }
            $controls.each(function () {
                var $control = $(this);
                if (!$control.attr('id')) {
                    controlId++;
                    $control.attr('id', 'cer-event-control-' + controlId);
                }
                var $label = $field.find('label').not('.cer-toggle').first();
                if ($label.length) {
                    $label.attr('for', $control.attr('id'));
                }
                if ($control.data('cerExpandedTrigger')) {
                    return;
                }
                var label = controlLabel($control);
                var $trigger = $('<button type="button" class="cer-field-expand-trigger"><span class="dashicons dashicons-editor-expand" aria-hidden="true"></span><span class="screen-reader-text"></span></button>')
                    .attr('aria-label', (strings.expandField || 'Edit in expanded view') + ': ' + label)
                    .attr('title', strings.expandField || 'Edit in expanded view');
                $trigger.find('.screen-reader-text').text(strings.expandField || 'Edit in expanded view');
                $trigger.data('cerExpandedTrigger', true);
                $trigger.data('cerExpandedSource', this);
                $field.append($trigger);
                $field.addClass('has-expanded-editor');
                $control.data('cerExpandedTrigger', true);
            });
        });
    }

    function ensureExpandedDialog() {
        var $dialog = $('#cer-expanded-editor');
        if ($dialog.length) {
            return $dialog;
        }
        $dialog = $('<dialog id="cer-expanded-editor" class="cer-expanded-editor" aria-modal="true" aria-labelledby="cer-expanded-editor-title">' +
            '<div class="cer-expanded-editor-content">' +
                '<h2 id="cer-expanded-editor-title"></h2>' +
                '<div class="cer-expanded-editor-fields"></div>' +
                '<div class="cer-expanded-editor-actions">' +
                    '<button type="button" class="button cer-expanded-cancel"></button>' +
                    '<button type="button" class="button button-primary cer-expanded-apply"></button>' +
                '</div>' +
            '</div>' +
            '</dialog>');
        $dialog.find('#cer-expanded-editor-title').text(strings.editorTitle || 'Expanded editor');
        $dialog.find('.cer-expanded-cancel').text(strings.cancel || 'Cancel');
        $dialog.find('.cer-expanded-apply').text(strings.apply || 'Apply');
        $('#cer-event-form').append($dialog);
        return $dialog;
    }

    function closeExpandedDialog($dialog) {
        var dialog = $dialog[0];
        if (typeof dialog.close === 'function') {
            dialog.close();
        } else {
            $dialog.removeAttr('open').trigger('cer-expanded-close');
        }
    }

    function sourceValue(source) {
        if ('event_description' === source.id && window.tinymce && window.tinymce.get('event_description')) {
            return window.tinymce.get('event_description').getContent();
        }
        return source.value;
    }

    function updateSource(source, editor) {
        if ('checkbox' === (source.type || '').toLowerCase()) {
            source.checked = editor.checked;
        } else {
            source.value = editor.value;
            if ('event_description' === source.id && window.tinymce && window.tinymce.get('event_description')) {
                window.tinymce.get('event_description').setContent(editor.value);
                window.tinymce.triggerSave();
            }
        }
        source.dispatchEvent(new Event('input', { bubbles: true }));
        source.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function openExpandedEditor(trigger, rowMode) {
        var $trigger = $(trigger);
        var $dialog = ensureExpandedDialog();
        var $fields = $dialog.find('.cer-expanded-editor-fields').empty();
        var $sources = rowMode
            ? $trigger.closest('.cer-repeater-row').find('.cer-field').find('input, textarea, select').filter(function () {
                var type = (this.type || '').toLowerCase();
                return !this.disabled && !this.readOnly && !['hidden', 'button', 'submit', 'reset', 'file', 'color'].includes(type);
            })
            : $($trigger.data('cerExpandedSource'));
        var editorControls = [];
        var origin = rowMode ? trigger : $trigger.data('cerExpandedSource');

        $sources.each(function (index) {
            var source = this;
            var $source = $(source);
            var label = controlLabel($source);
            var editorId = 'cer-expanded-control-' + index;
            var $wrapper = $('<div class="cer-expanded-field"></div>');
            var $label = $('<label></label>').attr('for', editorId).text(label);
            var editor = source.cloneNode(true);
            editor.removeAttribute('name');
            editor.id = editorId;
            editor.removeAttribute('required');
            if ('checkbox' === (source.type || '').toLowerCase()) {
                editor.checked = source.checked;
            } else {
                editor.value = sourceValue(source);
            }
            $wrapper.append($label, editor);
            $fields.append($wrapper);
            editorControls.push({ source: source, editor: editor });
        });

        $dialog.data('cerExpandedOrigin', origin).data('cerExpandedControls', editorControls);
        $dialog.find('.cer-expanded-editor-actions').toggleClass('is-single-field', !rowMode);
        if (rowMode) {
            var rowTitle = $.trim($trigger.closest('.cer-repeater-row').find('.cer-row-handle').first().text());
            $dialog.find('#cer-expanded-editor-title').text(rowTitle || strings.editorTitle || 'Expanded editor');
        } else {
            $dialog.find('#cer-expanded-editor-title').text(controlLabel($(origin)));
        }
        if (typeof $dialog[0].showModal === 'function') {
            $dialog[0].showModal();
        } else {
            $dialog.attr('open', 'open');
        }
        var firstEditor = $fields.find('input, textarea, select').first();
        if (firstEditor.length) {
            firstEditor.trigger('focus');
        }
    }

    function enhanceRepeaterEditing($root) {
        enhanceEditableFields($root);
        $root.find('.cer-repeater-row').each(function () {
            var $row = $(this);
            if (!$row.find('.cer-row-expand-trigger').length) {
                var $remove = $row.find('.cer-remove-row').first();
                var $button = $('<button type="button" class="button button-small cer-row-expand-trigger"></button>')
                    .text(strings.expandRow || 'Edit row')
                    .attr('aria-label', strings.expandRow || 'Edit row');
                $remove.length ? $button.insertBefore($remove) : $row.find('.cer-repeater-actions, .cer-speaker-actions-col').first().append($button);
            }
        });
    }

    function initializeExpandedEditing() {
        var $form = $('#cer-event-form');
        if (!$form.length) {
            return;
        }
        enhanceRepeaterEditing($form);

        var $dialog = ensureExpandedDialog();
        $form.on('click', '.cer-field-expand-trigger', function () {
            openExpandedEditor(this, false);
        });
        $form.on('click', '.cer-row-expand-trigger', function () {
            openExpandedEditor(this, true);
        });
        $dialog.on('click', '.cer-expanded-cancel', function () {
            closeExpandedDialog($dialog);
        });
        $dialog.on('click', '.cer-expanded-apply', function () {
            var controls = $dialog.data('cerExpandedControls') || [];
            var invalid = controls.find(function (pair) { return !pair.editor.checkValidity(); });
            if (invalid) {
                invalid.editor.reportValidity();
                return;
            }
            controls.forEach(function (pair) {
                updateSource(pair.source, pair.editor);
            });
            closeExpandedDialog($dialog);
        });
        $dialog.on('close cer-expanded-close', function () {
            var origin = $dialog.data('cerExpandedOrigin');
            if (origin && origin.id === 'event_description' && window.tinymce && window.tinymce.get('event_description')) {
                window.tinymce.get('event_description').focus();
            } else if (origin && typeof origin.focus === 'function') {
                origin.focus();
            }
        });
        $dialog.on('keydown', function (event) {
            if (event.key === 'Escape' || event.keyCode === 27) {
                event.preventDefault();
                closeExpandedDialog($dialog);
            }
        });
    }

    function initializePanelTabs($container, tabName) {
        var $panels = $container.children('.cer-panel');
        var $tabs = $('<div class="cer-item-tab-list cer-section-tab-list" role="tablist"></div>')
            .attr('aria-label', $('#cer-event-tab-' + tabName).text());
        var $activeTabInput = $('[name="cer_item_active_tabs[section_' + tabName + ']"]');
        var selected = parseInt($activeTabInput.val(), 10) || 0;
        if (selected < 0 || selected >= $panels.length) {
            selected = 0;
        }
        $panels.each(function (index) {
            var $panel = $(this);
            var id = 'cer-section-' + tabName + '-' + index;
            var itemType = $panel.attr('data-cer-item-type');
            $panel.removeClass('is-collapsed').addClass('cer-item-tab-panel cer-section-tab-panel')
                .attr({id: id, role: 'tabpanel', 'aria-labelledby': id + '-tab'});
            var $tab = $('<button type="button" class="cer-item-tab" role="tab"></button>')
                .attr({id: id + '-tab', 'aria-controls': id})
                .text($panel.find('.cer-panel-header h2').first().text());
            if ($panel.is('[data-cer-page-content]')) {
                $panel.find('.cer-panel-header-actions').first().addClass('cer-section-visibility').prependTo($panel.find('.cer-panel-body').first());
            }
            $tabs.append($tab);
            if ($panel.find('.cer-inline-error').length || (itemType && itemType === $('[name="cer_item_type"]').val())) {
                selected = index;
            }
        });
        function select(index, focus) {
            $activeTabInput.val(index);
            $panels.each(function (position) {
                $(this).prop('hidden', position !== index);
                $tabs.children().eq(position).attr({
                    'aria-selected': position === index ? 'true' : 'false',
                    tabindex: position === index ? 0 : -1
                });
            });
            if ('setup' === tabName) {
                $('[name="cer_item_type"]').val($panels.eq(index).attr('data-cer-item-type') || '');
            }
            if (focus) {
                $tabs.children().eq(index).trigger('focus');
            }
        }
        $tabs.on('click', '[role="tab"]', function () {
            select($tabs.children().index(this), false);
        }).on('keydown', '[role="tab"]', function (event) {
            var index = $tabs.children().index(this);
            if (['ArrowLeft', 'ArrowRight', 'Home', 'End'].indexOf(event.key) === -1) {
                return;
            }
            event.preventDefault();
            if ('Home' === event.key) {
                index = 0;
            } else if ('End' === event.key) {
                index = $panels.length - 1;
            } else {
                index = (index + ('ArrowRight' === event.key ? 1 : $panels.length - 1)) % $panels.length;
            }
            select(index, true);
        });
        $container.prepend($tabs);
        select(selected, false);
    }

    function revealControlTabs($control) {
        $($control.parents('[role="tabpanel"]').get().reverse()).each(function () {
            $('#' + $(this).attr('aria-labelledby')).trigger('click');
        });
    }

    function initializePanelAccordions() {
        $('.cer-event-tab-panel').each(function () {
            var tabName = $(this).attr('data-cer-tab-panel');
            if ('setup' === tabName || 'content' === tabName) {
                initializePanelTabs($(this), tabName);
                return;
            }
            var $panels = $(this).children('.cer-panel');
            if ($panels.length < 2) {
                return;
            }
            $panels.each(function (index) {
                var $panel = $(this);
                var $header = $panel.find('.cer-panel-header').first();
                var $actions = $header.find('.cer-panel-header-actions').first();
                if (!$actions.length) {
                    $actions = $('<div class="cer-panel-header-actions"></div>').appendTo($header);
                }
                var expanded = index === 0 || $panel.attr('data-cer-item-type') === $('[name="cer_item_type"]').val();
                if (!expanded) {
                    $panel.addClass('is-collapsed');
                } else {
                    $panel.removeClass('is-collapsed');
                }
                var bodyId = 'cer-event-panel-body-' + tabName + '-' + index;
                $panel.find('.cer-panel-body').first().attr('id', bodyId);
                var $toggle = $('<button type="button" class="cer-panel-toggle"></button>')
                    .attr('aria-expanded', expanded ? 'true' : 'false')
                    .attr('aria-controls', bodyId)
                    .attr('aria-label', (strings.toggleSection || 'Toggle section') + ': ' + $header.find('h2, h3').first().text().trim())
                    .append('<span class="dashicons ' + (expanded ? 'dashicons-arrow-up-alt2' : 'dashicons-arrow-down-alt2') + '" aria-hidden="true"></span>');
                $actions.append($toggle);
            });
        });

        $(document).on('click', '.cer-panel-toggle', function () {
            var $button = $(this);
            var $panel = $button.closest('.cer-panel');
            var collapsed = $panel.toggleClass('is-collapsed').hasClass('is-collapsed');
            if (!collapsed && $panel.attr('data-cer-item-type')) {
                $('[name="cer_item_type"]').val($panel.attr('data-cer-item-type'));
            }
            $button.attr('aria-expanded', collapsed ? 'false' : 'true');
            $button.find('.dashicons')
                .toggleClass('dashicons-arrow-up-alt2', !collapsed)
                .toggleClass('dashicons-arrow-down-alt2', collapsed);
        });
    }

    function markAndFocusErrors() {
        var $errors = $('.cer-inline-error');
        $errors.each(function (index) {
            var $error = $(this);
            var $field = $error.closest('.cer-field');
            var $control = editableControls($field).first();
            var errorId = 'cer-event-error-' + index;
            $field.addClass('has-error');
            $error.attr('id', errorId).attr('role', 'alert');
            if ($control.length) {
                $control.attr('aria-invalid', 'true').attr('aria-describedby', errorId);
            }
        });
        if ($errors.length) {
            var $firstError = $errors.first();
            revealControlTabs($firstError);
            var $panel = $firstError.closest('.cer-panel');
            $panel.removeClass('is-collapsed');
            $panel.find('.cer-panel-toggle').attr('aria-expanded', 'true')
                .find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
            window.setTimeout(function () {
                var $control = editableControls($firstError.closest('.cer-field')).first();
                if ($control.length) {
                    $control.trigger('focus');
                }
            }, 0);
        }
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

    $(document).on('click', '.cer-add-row', function () {
        var type = $(this).data('repeater');
        var container = $('.cer-repeater[data-repeater="' + type + '"]');
        var index = getNextIndex(container);
        var row = createRow(type, index);
        container.append(row);
        initializeRepeaterTabs(container, String(index));
        enhanceRepeaterEditing(container);
        row.find('input:not([type="hidden"]), textarea').first().trigger('focus');
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
        initializeRepeaterTabs(repeater);
        enhanceRepeaterEditing(repeater);
        repeater.find('[role="tab"][aria-selected="true"]').trigger('focus');
    });

    $(document).on('click', 'a.cer-item-action-confirm', function (event) {
        event.preventDefault();
        if (!window.confirm($(this).data('confirm'))) {
            return;
        }
        var url = new URL(this.href, window.location.href);
        url.searchParams.set('trash' === $(this).data('confirmKind') ? 'cer_confirm_item_trash' : 'cer_confirm_item_delete', '1');
        window.location.href = url.toString();
    });

    function submitItemTrashSearch() {
        if (document.querySelector('.cer-item-trash-view[data-cer-live-ready]')) {
            return;
        }
        var $search = $('#cer-item-trash-search-input');
        if (!$search.length) {
            return;
        }
        var url = new URL(window.location.href);
        url.searchParams.set('item_search', $search.val());
        url.searchParams.set('item_paged', '1');
        window.location.href = url.toString();
    }

    $(document).on('click', '.cer-item-trash-search-submit', submitItemTrashSearch);
    $(document).on('keydown', '#cer-item-trash-search-input', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitItemTrashSearch();
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
            $('#' + preview).text(strings.noMedia || 'No image selected.');
        }
        $(this).prop('disabled', true);
        $('#cer-form-status').text(strings.mediaCleared || 'Image cleared. Save the event to apply this change.');
    });

    function closeMapModal() {
        $('#cer-map-modal').prop('hidden', true);
        $('#cer-open-map-search').trigger('focus');
    }

    function updateMapActions() {
        var empty = !$('#cer-map-search-input').val().trim();
        $('#cer-map-search-go, #cer-map-use-location').prop('disabled', empty);
    }

    function mapEmbedUrlFromQuery(query) {
        return 'https://www.google.com/maps?q=' + encodeURIComponent(query) + '&output=embed';
    }

    function mapLinkFromQuery(query) {
        return 'https://www.google.com/maps?q=' + encodeURIComponent(query);
    }

    /* One picker for everything: the Google Maps embed `q` parameter accepts a
       place name/address or "lat,lng" coordinates alike (Maps Embed docs), so
       both flow through the same iframe preview and the same confirm step. */
    function parseCoordinates(query) {
        var match = query.match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
        return match ? { lat: match[1], lng: match[2] } : null;
    }

    function previewMapQuery(query) {
        if (!query) {
            return;
        }
        $('#cer-map-embed').attr('src', mapEmbedUrlFromQuery(query));
    }

    $('#cer-open-map-search').on('click', function () {
        var $modal = $('#cer-map-modal');
        var lat = $('#location-lat').val().trim();
        var lng = $('#location-lng').val().trim();
        var initialQuery = $('#location-address').val() || ((lat && lng) ? lat + ',' + lng : '') || $('#location-link').val() || '';
        $('#cer-map-search-input').val(initialQuery);
        if (initialQuery) {
            previewMapQuery(initialQuery);
        }
        $modal.attr('hidden', false);
        updateMapActions();
        $('#cer-map-search-input').trigger('focus');
    });

    $('#cer-close-map-search').on('click', function () {
        closeMapModal();
    });

    $('#cer-map-modal .cer-map-modal-backdrop').on('click', function () {
        closeMapModal();
    });

    $('#cer-map-search-go').on('click', function () {
        previewMapQuery($('#cer-map-search-input').val().trim());
    });

    $('#cer-map-search-input').on('input', updateMapActions);
    $('#cer-map-modal').on('keydown', function (event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeMapModal();
        } else if (event.key === 'Tab') {
            var $controls = $(this).find('button, input, iframe').filter(':visible:not(:disabled)');
            var first = $controls[0];
            var last = $controls[$controls.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    /* The picker modal sits inside the event <form>: Enter must preview the
       search, never submit the form (which used to close the modal and reload
       the page without applying the searched location). */
    $('#cer-map-search-input').on('keydown', function (event) {
        if (event.key === 'Enter' || event.keyCode === 13) {
            event.preventDefault();
            previewMapQuery($(this).val().trim());
        }
    });

    $('#cer-map-use-location').on('click', function () {
        var query = $('#cer-map-search-input').val().trim();
        if (query) {
            $('#location-link').val(mapLinkFromQuery(query));
            $('#location-address').val(query);
            var coords = parseCoordinates(query);
            if (coords) {
                $('#location-lat').val(coords.lat);
                $('#location-lng').val(coords.lng);
            }
        }
        closeMapModal();
        $('#cer-form-status').text(strings.locationApplied || 'Location updated. Save the event to apply this change.');
    });

    /* Max Attendees is derived: always the sum of the individual ticket
       quantities, so the field is read-only and recounted live. */
    function refreshMaxAttendees(soft) {
        var total = 0;
        $('[name^="tickets["][name$="[quantity_available]"]').each(function () {
            var value = parseInt($(this).val(), 10);
            if (!isNaN(value) && value > 0) {
                total += value;
            }
        });
        if (soft && total === 0) {
            return; // keep the stored value until a quantity is entered
        }
        $('#max-attendees').val(total);
    }

    $(document).on('input', '[name^="tickets["][name$="[quantity_available]"]', function () {
        refreshMaxAttendees(false);
    });
    $(document).on('click', '.cer-add-row, .cer-remove-row', function () {
        refreshMaxAttendees(false);
    });
    refreshMaxAttendees(true);

    prepareEventTabs();
    initializeContentTabs();
    $('.cer-repeater[data-repeater]').each(function () {
        initializeRepeaterTabs($(this));
    });
    initializeExpandedEditing();
    initializePanelAccordions();
    markAndFocusErrors();
    $('#cer-event-form')[0].addEventListener('invalid', function (event) {
        var $control = $(event.target);
        revealControlTabs($control);
        var $panel = $control.closest('.cer-panel');
        if ($panel.hasClass('is-collapsed')) {
            $panel.find('.cer-panel-toggle').trigger('click');
        }
    }, true);
    $('.cer-media-clear').each(function () {
        $(this).prop('disabled', !(parseInt($('#' + $(this).data('target')).val(), 10) > 0));
    });
    $('.cer-field input[type="color"]').each(function () {
        var $value = $('<output class="cer-color-value"></output>').attr('for', this.id).text(this.value);
        $(this).after($value).on('input', function () { $value.text(this.value); });
    });
})(jQuery); 
