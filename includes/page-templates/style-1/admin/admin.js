/**
 * Enterprise Page Fields — Admin UI
 * Depends on: jQuery, jQuery UI Sortable, wp.media
 */
(function ($) {
    'use strict';

    var EP = {

        sectionCount: 0,

        // ------------------------------------------------------------------
        init: function () {
            if (!$('#ep-flexible-content').length) return;

            // Seed counter from existing rows so new rows don't collide.
            this.sectionCount = $('#ep-sections-list .ep-section-row').length;

            this.initSortable();
            this.bindEvents();
            this.initAllImageFields();
            this.initAllColorFields();
        },

        // ------------------------------------------------------------------
        // Sortable sections
        // ------------------------------------------------------------------
        initSortable: function () {
            $('#ep-sections-list').sortable({
                handle: '.ep-handle',
                placeholder: 'ep-sortable-placeholder',
                tolerance: 'pointer',
                update: function () {
                    EP.reindexSections();
                }
            });

            // Sortable repeater rows (inside each section)
            this.initRepeaterSortable($('#ep-sections-list'));
        },

        initRepeaterSortable: function ($ctx) {
            $ctx.find('.ep-repeater-rows').each(function () {
                if ($(this).data('sortable-init')) return;
                $(this).sortable({
                    handle: '.ep-row-handle',
                    items: '.ep-repeater-row',
                    tolerance: 'pointer',
                });
                $(this).data('sortable-init', true);
            });
        },

        // ------------------------------------------------------------------
        // Event Binding
        // ------------------------------------------------------------------
        bindEvents: function () {
            var $root = $('#ep-flexible-content');

            // ---- Toggle add-section picker --------------------------------
            $root.on('click', '.ep-add-section-btn', function (e) {
                e.stopPropagation();
                var $picker = $(this).siblings('.ep-layout-picker');
                $picker.toggle();
                $(this).toggleClass('is-open');
            });

            // Close picker on outside click
            $(document).on('click.ep', function () {
                $('.ep-layout-picker').hide();
                $('.ep-add-section-btn').removeClass('is-open');
            });

            $root.on('click', '.ep-layout-picker', function (e) {
                e.stopPropagation();
            });

            // ---- Add layout -----------------------------------------------
            $root.on('click', '.ep-add-layout-btn', function () {
                var layout = $(this).data('layout');
                EP.addSection(layout);
                $('.ep-layout-picker').hide();
                $('.ep-add-section-btn').removeClass('is-open');
            });

            // ---- Remove section -------------------------------------------
            $root.on('click', '.ep-btn-remove', function (e) {
                e.stopPropagation();
                if (!confirm('Remove this section? This cannot be undone.')) return;
                $(this).closest('.ep-section-row').slideUp(200, function () {
                    $(this).remove();
                    EP.reindexSections();
                });
            });

            // ---- Toggle collapse ------------------------------------------
            $root.on('click', '.ep-section-header', function (e) {
                // Don't collapse when clicking buttons inside the header.
                if ($(e.target).closest('.ep-section-actions, .ep-handle').length) return;
                var $row = $(this).closest('.ep-section-row');
                $row.toggleClass('is-collapsed');
                $row.find('.ep-btn-toggle').text($row.hasClass('is-collapsed') ? '+' : '−');
            });

            $root.on('click', '.ep-btn-toggle', function (e) {
                e.stopPropagation();
                var $row = $(this).closest('.ep-section-row');
                $row.toggleClass('is-collapsed');
                $(this).text($row.hasClass('is-collapsed') ? '+' : '−');
            });

            // ---- Move up / down -------------------------------------------
            $root.on('click', '.ep-btn-up', function (e) {
                e.stopPropagation();
                var $row = $(this).closest('.ep-section-row');
                var $prev = $row.prev('.ep-section-row');
                if ($prev.length) {
                    $row.insertBefore($prev);
                    EP.reindexSections();
                    EP.flashRow($row);
                }
            });

            $root.on('click', '.ep-btn-down', function (e) {
                e.stopPropagation();
                var $row = $(this).closest('.ep-section-row');
                var $next = $row.next('.ep-section-row');
                if ($next.length) {
                    $row.insertAfter($next);
                    EP.reindexSections();
                    EP.flashRow($row);
                }
            });

            // ---- Add repeater row ----------------------------------------
            $root.on('click', '.ep-add-row', function () {
                var $repeater = $(this).closest('.ep-repeater');
                var $rows     = $repeater.find('.ep-repeater-rows');
                var $tpl      = $repeater.find('.ep-repeater-template');
                var rowCount  = $rows.find('.ep-repeater-row').length;

                // Clone the template HTML and replace placeholder index.
                var html = $tpl.html().replace(/__RIDX__/g, rowCount);
                var $newRow = $(html);

                $rows.append($newRow);
                EP.initImageFields($newRow);
                EP.initColorFields($newRow);

                // Ensure the containing repeater-rows has sortable.
                EP.initRepeaterSortable($rows.closest('.ep-section-row'));
            });

            // ---- Remove repeater row -------------------------------------
            $root.on('click', '.ep-remove-row', function () {
                $(this).closest('.ep-repeater-row').remove();
            });

            // ---- Live section title update --------------------------------
            $root.on('input change', '.ep-heading-source', function () {
                var $row = $(this).closest('.ep-section-row');
                var val  = $(this).val().trim();
                $row.find('.ep-section-title').text(val);
            });

            // ---- Post IDs lookup on blur ----------------------------------
            $root.on('blur', '.ep-post-ids-input', function () {
                EP.lookupPostTitles($(this));
            });
        },

        // ------------------------------------------------------------------
        // Add a new section from a layout template
        // ------------------------------------------------------------------
        addSection: function (layout) {
            var $tpl = $('#ep-tpl-' + layout);
            if (!$tpl.length) {
                console.warn('EP: no template found for layout:', layout);
                return;
            }

            // Replace placeholder index with current count.
            var html = $tpl.html().replace(/__IDX__/g, this.sectionCount);
            this.sectionCount++;

            var $newRow = $(html);
            $('#ep-sections-list').append($newRow);
            $newRow.hide().slideDown(200);

            EP.initImageFields($newRow);
            EP.initColorFields($newRow);
            EP.initRepeaterSortable($newRow);

            // Scroll to new section.
            $('html, body').animate(
                { scrollTop: $newRow.offset().top - 80 },
                300
            );
        },

        // ------------------------------------------------------------------
        // Re-index all sections after a sort/add/remove
        // ------------------------------------------------------------------
        reindexSections: function () {
            $('#ep-sections-list .ep-section-row').each(function (sIdx) {
                $(this).find('[name]').each(function () {
                    var name = $(this).attr('name');
                    // Replace the first numeric/placeholder index in "sections[X]"
                    name = name.replace(/^sections\[([^\]]+)\]/, 'sections[' + sIdx + ']');
                    $(this).attr('name', name);
                });
            });
        },

        // ------------------------------------------------------------------
        // Image upload (wp.media)
        // ------------------------------------------------------------------
        initAllImageFields: function () {
            this.initImageFields($('#ep-flexible-content'));
        },

        initImageFields: function ($ctx) {
            $ctx.find('.ep-upload-image').off('click.ep').on('click.ep', function (e) {
                e.preventDefault();
                var $btn   = $(this);
                var $field = $btn.closest('.ep-image-field');

                var frame = wp.media({
                    title:   'Select Image',
                    button:  { text: 'Use this image' },
                    multiple: false
                });

                frame.on('select', function () {
                    var att  = frame.state().get('selection').first().toJSON();
                    var thumb = att.sizes && att.sizes.thumbnail
                              ? att.sizes.thumbnail.url
                              : att.url;

                    $field.find('.ep-image-id').val(att.id);
                    $field.find('.ep-image-preview').html('<img src="' + thumb + '" alt="">');
                    $btn.text('Change Image');
                    $field.find('.ep-remove-image').show();
                });

                frame.open();
            });

            $ctx.find('.ep-remove-image').off('click.ep').on('click.ep', function (e) {
                e.preventDefault();
                var $field = $(this).closest('.ep-image-field');
                $field.find('.ep-image-id').val('');
                $field.find('.ep-image-preview').html('');
                $field.find('.ep-upload-image').text('Upload Image');
                $(this).hide();
            });
        },

        // ------------------------------------------------------------------
        // Colour field — live swatch preview
        // ------------------------------------------------------------------
        initAllColorFields: function () {
            this.initColorFields($('#ep-flexible-content'));
        },

        initColorFields: function ($ctx) {
            $ctx.find('.ep-color-input').off('input.ep').on('input.ep', function () {
                $(this).siblings('.ep-color-swatch').css('background-color', $(this).val());
            });
        },

        // ------------------------------------------------------------------
        // Post IDs AJAX title lookup
        // ------------------------------------------------------------------
        lookupPostTitles: function ($input) {
            var $preview = $input.siblings('.ep-post-ids-preview');
            var raw      = $input.val().trim();
            var ids      = raw.split(',')
                              .map(function (v) { return parseInt(v.trim(), 10); })
                              .filter(function (v) { return v > 0; });

            if (!ids.length) {
                $preview.html('');
                return;
            }

            $.post(pts1Admin.ajaxurl, {
                action: 'pts1_get_post_titles',
                nonce:  pts1Admin.nonce,
                ids:    ids
            }, function (res) {
                if (!res.success) return;
                var html = '<ul>';
                $.each(res.data, function (id, title) {
                    html += '<li><span class="ep-post-id">#' + id + '</span> ' + title + '</li>';
                });
                html += '</ul>';
                $preview.html(html);
            });
        },

        // ------------------------------------------------------------------
        // Tiny helper — flash a row to confirm move
        // ------------------------------------------------------------------
        flashRow: function ($row) {
            $row.addClass('ep-flash');
            setTimeout(function () { $row.removeClass('ep-flash'); }, 500);
        }
    };

    $(document).ready(function () {
        EP.init();
    });

})(jQuery);