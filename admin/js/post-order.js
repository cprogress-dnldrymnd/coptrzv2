(function ($) {
    'use strict';

    if (typeof coptrzPostOrder === 'undefined') {
        return;
    }

    var mode = coptrzPostOrder.mode || 'list';

    if (mode === 'page') {
        initPageMode();
    } else {
        initListMode();
    }

    function initPageMode() {
        var $list = $('#coptrz-order-list');
        if (!$list.length || !$list.find('li').length) {
            return;
        }

        $list.sortable({
            items: '> li',
            cursor: 'move',
            axis: 'y',
            handle: '.coptrz-order-grip, .coptrz-order-title',
            placeholder: 'coptrz-order-placeholder',
            forcePlaceholderSize: true,
            update: function () {
                var order = [];
                $list.find('li').each(function () {
                    var id = $(this).attr('data-id');
                    if (id && /^\d+$/.test(id)) {
                        order.push(id);
                    }
                });
                saveOrder(order, 0);
            }
        });
    }

    function initListMode() {
        var $list = $('#the-list');
        if (!$list.length || !$list.find('tr').length) {
            return;
        }

        // Skip the "no items" placeholder row.
        if ($list.find('tr.no-items').length) {
            return;
        }

        $list.addClass('coptrz-post-order-list');

        $list.sortable({
            items: 'tr:not(.inline-edit-row)',
            cursor: 'move',
            axis: 'y',
            handle: '.column-title, .check-column',
            placeholder: 'coptrz-post-order-placeholder',
            helper: function (e, $tr) {
                var $helper = $tr.clone();
                $helper.children().each(function (i) {
                    $(this).width($tr.children().eq(i).outerWidth());
                });
                return $helper;
            },
            start: function (e, ui) {
                ui.placeholder.height(ui.item.outerHeight());
            },
            update: function () {
                var order = [];
                $list.find('tr').each(function () {
                    var id = this.id && this.id.replace('post-', '');
                    if (id && /^\d+$/.test(id)) {
                        order.push(id);
                    }
                });
                saveOrder(order, coptrzPostOrder.offset);
            }
        });
    }

    function saveOrder(order, offset) {
        if (!order.length) {
            return;
        }

        $.post(coptrzPostOrder.ajaxUrl, {
            action: 'coptrz_save_post_order',
            nonce: coptrzPostOrder.nonce,
            post_type: coptrzPostOrder.postType,
            offset: offset,
            order: order
        })
            .done(function (res) {
                if (res && res.success) {
                    flashNotice(coptrzPostOrder.i18n.saved, 'success');
                } else {
                    flashNotice(coptrzPostOrder.i18n.failed, 'error');
                }
            })
            .fail(function () {
                flashNotice(coptrzPostOrder.i18n.failed, 'error');
            });
    }

    function flashNotice(message, type) {
        var $existing = $('#coptrz-post-order-notice');
        if ($existing.length) {
            $existing.remove();
        }

        var $notice = $('<div id="coptrz-post-order-notice" class="notice is-dismissible"><p></p></div>');
        $notice.addClass(type === 'error' ? 'notice-error' : 'notice-success');
        $notice.find('p').text(message);
        $('.wrap h1').first().after($notice);

        setTimeout(function () {
            $notice.fadeOut(200, function () {
                $(this).remove();
            });
        }, 2500);
    }
})(jQuery);
