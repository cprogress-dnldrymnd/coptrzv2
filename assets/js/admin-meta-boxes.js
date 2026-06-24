/**
 * Plugin/Snippet Name: Meta Shim - Admin UI
 * Description: Lightweight, dependency-free (beyond jQuery for sortable/color)
 *              behaviour layer for the native meta-box replacement of Carbon
 *              Fields. Handles tabs, repeater add/duplicate/reorder/collapse/
 *              delete, media + gallery pickers, association AJAX search and
 *              conditional-logic visibility. No build step, no framework.
 *
 *              Nested repeaters are cloned by replacing only the FIRST
 *              "__CMSIDX__" placeholder in each name/id/for attribute with a
 *              unique id; because a parent row's index always precedes any nested
 *              placeholder in the input-name string, this resolves the current
 *              level while leaving inner templates intact at any depth.
 *
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */
(function ($) {
    'use strict';

    var PLACEHOLDER = '__CMSIDX__';
    var uid = Date.now(); // Monotonic unique row index seed.

    /* ----------------------------------------------------------------- */
    /*  Helpers                                                          */
    /* ----------------------------------------------------------------- */

    function nextUid() {
        uid += 1;
        return uid;
    }

    /**
     * Replace ONLY the first PLACEHOLDER occurrence in the relevant attributes
     * of an element subtree, resolving the current repeater level's index.
     */
    function resolveIndex(node, index) {
        var attrs = ['name', 'id', 'for', 'data-cms-name'];
        var all = node.querySelectorAll('*');
        var list = [node].concat(Array.prototype.slice.call(all));
        list.forEach(function (el) {
            attrs.forEach(function (attr) {
                if (!el.getAttribute) return;
                var val = el.getAttribute(attr);
                if (val && val.indexOf(PLACEHOLDER) !== -1) {
                    el.setAttribute(attr, val.replace(PLACEHOLDER, index));
                }
            });
        });
    }

    /** Find the template row for a given group within a complex wrapper. */
    function templateFor(complex, group) {
        var tpls = complex.querySelector('.cms-complex__templates');
        if (!tpls) return null;
        var rows = tpls.querySelectorAll(':scope > .cms-row');
        for (var i = 0; i < rows.length; i++) {
            if (rows[i].getAttribute('data-cms-group') === group) {
                return rows[i];
            }
        }
        return rows[0] || null;
    }

    /* ----------------------------------------------------------------- */
    /*  Tabs                                                             */
    /* ----------------------------------------------------------------- */

    $(document).on('click', '.cms-tabs .cms-tab', function () {
        var index = this.getAttribute('data-cms-tab');
        var container = this.closest('.cms-container');
        container.querySelectorAll(':scope > .cms-tabs > .cms-tab').forEach(function (t) {
            t.classList.toggle('is-active', t === this);
        }, this);
        container.querySelectorAll(':scope > .cms-panel').forEach(function (p) {
            p.classList.toggle('is-hidden', p.getAttribute('data-cms-panel') !== index);
        });
    });

    /* ----------------------------------------------------------------- */
    /*  Repeater: add / duplicate / delete / collapse                   */
    /* ----------------------------------------------------------------- */

    $(document).on('click', '.cms-complex__add-btn', function () {
        var complex = this.closest('.cms-complex');
        var group = this.getAttribute('data-cms-group');
        var rowsWrap = complex.querySelector(':scope > .cms-complex__rows');
        if (atMax(complex, rowsWrap)) return;

        var tpl = templateFor(complex, group);
        if (!tpl) return;

        var clone = tpl.cloneNode(true);
        resolveIndex(clone, nextUid());
        rowsWrap.appendChild(clone);
        initRow(clone);
        refreshConditionals(complex);
    });

    $(document).on('click', '.cms-row__dup', function () {
        var row = this.closest('.cms-row');
        var complex = row.closest('.cms-complex');
        var rowsWrap = row.parentNode;
        if (atMax(complex, rowsWrap)) return;

        var clone = row.cloneNode(true);
        // A duplicate must get a brand-new index: rewrite this level's resolved
        // index back to a placeholder on the clone, then resolve afresh.
        rebaseToPlaceholder(clone, row);
        resolveIndex(clone, nextUid());
        row.parentNode.insertBefore(clone, row.nextSibling);
        initRow(clone);
        refreshConditionals(complex);
    });

    $(document).on('click', '.cms-row__del', function () {
        var row = this.closest('.cms-row');
        var complex = row.closest('.cms-complex');
        row.parentNode.removeChild(row);
        refreshConditionals(complex);
    });

    $(document).on('click', '.cms-row__toggle', function () {
        this.closest('.cms-row').classList.toggle('is-collapsed');
    });

    // Collapse/expand by clicking the header title too.
    $(document).on('click', '.cms-row__title', function () {
        this.closest('.cms-row').classList.toggle('is-collapsed');
    });

    /** Enforce data-cms-max (0 = unlimited). */
    function atMax(complex, rowsWrap) {
        var max = parseInt(complex.getAttribute('data-cms-max'), 10) || 0;
        if (!max) return false;
        return rowsWrap.querySelectorAll(':scope > .cms-row').length >= max;
    }

    /**
     * For duplication: the cloned row's inputs carry the *source* row's resolved
     * numeric index for this level. Swap that exact index segment back to the
     * PLACEHOLDER so resolveIndex() can assign a fresh unique one.
     */
    function rebaseToPlaceholder(clone, source) {
        var typeInput = source.querySelector(':scope > .cms-row__type');
        if (!typeInput) return;
        // name looks like: cms_fields[root][<idx>][_type]; grab <idx> (last [n] before [_type]).
        var m = typeInput.getAttribute('name').match(/\[(\d+)\]\[_type\]$/);
        if (!m) return;
        var idx = m[1];
        var marker = '[' + idx + ']';
        var attrs = ['name', 'id', 'for', 'data-cms-name'];
        var list = [clone].concat(Array.prototype.slice.call(clone.querySelectorAll('*')));
        list.forEach(function (el) {
            if (!el.getAttribute) return;
            attrs.forEach(function (attr) {
                var val = el.getAttribute(attr);
                if (val && val.indexOf(marker) !== -1) {
                    el.setAttribute(attr, val.replace(marker, '[' + PLACEHOLDER + ']'));
                }
            });
        });
    }

    /* ----------------------------------------------------------------- */
    /*  Drag reordering (jQuery UI sortable)                            */
    /* ----------------------------------------------------------------- */

    function makeSortable($rows) {
        if (!$.fn.sortable) return;
        $rows.sortable({
            handle: '.cms-row__handle',
            items: '> .cms-row',
            axis: 'y',
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            placeholder: 'cms-row--placeholder'
        });
    }

    /* ----------------------------------------------------------------- */
    /*  Media (image / file) + gallery                                  */
    /* ----------------------------------------------------------------- */

    $(document).on('click', '.cms-media__select', function () {
        var wrap = this.closest('.cms-media');
        var isImage = wrap.getAttribute('data-cms-media') === 'image';
        var frame = wp.media({ multiple: false, library: isImage ? { type: 'image' } : {} });
        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            wrap.querySelector('.cms-media__id').value = att.id;
            var preview = wrap.querySelector('.cms-media__preview');
            if (isImage) {
                var src = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                preview.innerHTML = '<img src="' + src + '" alt="" />';
            } else {
                preview.textContent = att.filename || att.title;
            }
        });
        frame.open();
    });

    $(document).on('click', '.cms-media__remove', function () {
        var wrap = this.closest('.cms-media');
        wrap.querySelector('.cms-media__id').value = '';
        wrap.querySelector('.cms-media__preview').innerHTML = '';
    });

    $(document).on('click', '.cms-gallery__add', function () {
        var wrap = this.closest('.cms-gallery');
        var name = wrap.getAttribute('data-cms-name');
        var items = wrap.querySelector('.cms-gallery__items');
        var frame = wp.media({ multiple: true, library: { type: 'image' } });
        frame.on('select', function () {
            frame.state().get('selection').toJSON().forEach(function (att) {
                var src = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                var span = document.createElement('span');
                span.className = 'cms-gallery__item';
                span.innerHTML = '<img src="' + src + '" alt="" />' +
                    '<input type="hidden" name="' + name + '[]" value="' + att.id + '" />' +
                    '<button type="button" class="cms-gallery__remove">&times;</button>';
                items.appendChild(span);
            });
        });
        frame.open();
    });

    $(document).on('click', '.cms-gallery__remove', function () {
        var item = this.closest('.cms-gallery__item');
        item.parentNode.removeChild(item);
    });

    /* ----------------------------------------------------------------- */
    /*  Association field (AJAX search)                                 */
    /* ----------------------------------------------------------------- */

    var searchTimer = null;

    $(document).on('input', '.cms-assoc__input', function () {
        var input = this;
        var wrap = input.closest('.cms-assoc');
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            runSearch(wrap, input.value);
        }, 250);
    });

    function runSearch(wrap, q) {
        var results = wrap.querySelector('.cms-assoc__results');
        if (!q) { results.innerHTML = ''; return; }
        $.getJSON(CoptrzMetaShim.ajax_url, {
            action: 'coptrz_meta_search',
            nonce: CoptrzMetaShim.nonce,
            q: q,
            types: wrap.getAttribute('data-cms-types')
        }, function (items) {
            results.innerHTML = '';
            items.forEach(function (it) {
                var li = document.createElement('li');
                li.textContent = it.label;
                li.setAttribute('data-value', it.value);
                li.setAttribute('data-label', it.label);
                results.appendChild(li);
            });
        });
    }

    $(document).on('click', '.cms-assoc__results li', function () {
        var wrap = this.closest('.cms-assoc');
        var max = parseInt(wrap.getAttribute('data-cms-max'), 10) || 0;
        var selected = wrap.querySelector('.cms-assoc__selected');
        if (max && selected.querySelectorAll('.cms-assoc__item').length >= max) {
            return;
        }
        var name = wrap.getAttribute('data-cms-name');
        var li = document.createElement('li');
        li.className = 'cms-assoc__item';
        li.innerHTML = '<input type="hidden" name="' + name + '[]" value="' + this.getAttribute('data-value') + '" />' +
            '<span class="cms-assoc__label">' + this.getAttribute('data-label') + '</span>' +
            '<button type="button" class="cms-assoc__remove" aria-label="Remove">&times;</button>';
        selected.appendChild(li);
        wrap.querySelector('.cms-assoc__results').innerHTML = '';
        wrap.querySelector('.cms-assoc__input').value = '';
    });

    $(document).on('click', '.cms-assoc__remove', function () {
        var item = this.closest('.cms-assoc__item');
        item.parentNode.removeChild(item);
    });

    /* ----------------------------------------------------------------- */
    /*  Conditional logic (show/hide)                                   */
    /* ----------------------------------------------------------------- */

    /** Read the current value of a sibling field by name within a scope. */
    function fieldValue(scope, fieldName) {
        var el = scope.querySelector('[data-cms-name="' + fieldName + '"]');
        if (!el) return '';
        var input = el.querySelector('input[type="checkbox"]');
        if (input) return input.checked ? input.value : '';
        var control = el.querySelector('input, select, textarea');
        return control ? control.value : '';
    }

    function compare(subject, op, value) {
        switch ((op || '=').toUpperCase()) {
            case '!=': return subject != value;
            case 'IN': return [].concat(value).indexOf(subject) !== -1;
            case 'NOT IN': return [].concat(value).indexOf(subject) === -1;
            default: return subject == value;
        }
    }

    /** Re-evaluate every conditional field inside a scope (row/panel/container). */
    function refreshConditionals(scope) {
        scope.querySelectorAll('[data-cms-conditional]').forEach(function (field) {
            var rules;
            try { rules = JSON.parse(field.getAttribute('data-cms-conditional')); }
            catch (e) { return; }
            if (!rules || !rules.length) return;

            // Evaluate against the nearest enclosing group/row scope.
            var local = field.closest('.cms-row__body') || field.closest('.cms-panel') || scope;
            var relation = (rules.relation || 'AND').toUpperCase();
            var visible = relation === 'OR' ? false : true;

            rules.forEach(function (rule) {
                if (!rule || !rule.field) return;
                var ok = compare(fieldValue(local, rule.field), rule.compare, rule.value);
                visible = relation === 'OR' ? (visible || ok) : (visible && ok);
            });
            field.classList.toggle('cms-hidden', !visible);
        });
    }

    $(document).on('change keyup', '.cms-input, .cms-set input, .cms-check input', function () {
        var scope = this.closest('.cms-container') || document;
        refreshConditionals(scope);
        updateHeaders(scope);
    });

    /* ----------------------------------------------------------------- */
    /*  Live repeater header titles                                     */
    /* ----------------------------------------------------------------- */

    function updateHeaders(scope) {
        scope.querySelectorAll('.cms-complex').forEach(function (complex) {
            var tpl = complex.getAttribute('data-cms-header');
            if (!tpl) return;
            complex.querySelectorAll(':scope > .cms-complex__rows > .cms-row').forEach(function (row) {
                var title = tpl.replace(/<%[-=]\s*(\w+)\s*%>/g, function (m, key) {
                    var v = fieldValue(row.querySelector(':scope > .cms-row__body') || row, key);
                    return v || '';
                });
                title = title.replace(/<[^>]*>/g, '').trim();
                if (title) {
                    var titleEl = row.querySelector(':scope > .cms-row__head > .cms-row__title');
                    if (titleEl) titleEl.textContent = title;
                }
            });
        });
    }

    /* ----------------------------------------------------------------- */
    /*  Per-row / per-scope initialisation                              */
    /* ----------------------------------------------------------------- */

    function initRow(row) {
        // Initialise any controls inside a freshly-inserted row.
        initColorPickers(row);
        initSortables(row);
    }

    function initColorPickers(scope) {
        if (!$.fn.wpColorPicker) return;
        $(scope).find('.cms-color').each(function () {
            if ($(this).hasClass('wp-color-picker')) return; // already inited
            $(this).wpColorPicker();
        });
    }

    function initSortables(scope) {
        $(scope).find('.cms-complex__rows').each(function () {
            if ($(this).data('cms-sortable')) return;
            makeSortable($(this));
            $(this).data('cms-sortable', true);
        });
    }

    /* ----------------------------------------------------------------- */
    /*  Boot                                                            */
    /* ----------------------------------------------------------------- */

    $(function () {
        document.querySelectorAll('.cms-container').forEach(function (container) {
            initColorPickers(container);
            initSortables(container);
            refreshConditionals(container);
            updateHeaders(container);
        });
    });

})(jQuery);
