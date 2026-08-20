/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Shared editor UI helpers for the "legacy wrapper" blocks (Gallery, Product
 * Slider, Tabs, Accordion, Drone Servicing Grid, Events Widget, Product,
 * Product Compare, Global Post Box — assets/js/coptrz-*-block.js). Lifted from
 * the two independent lineages that already existed
 * (coptrz-post-grid-block.js's IdTokenPicker/PostElementsRepeater and
 * coptrz-hero-block.js's SinglePostPicker/FetchOnceSelect/ButtonsRepeater) —
 * nine more blocks made three-plus copies of the same code no longer viable.
 *
 * No build step in this theme (see CLAUDE.md), so this assigns to a global
 * namespace, `window.coptrzBlockUI`, rather than exporting a module. Must be
 * enqueued — and declared as a dependency — BEFORE any block script that reads
 * off it (see digitally_disruptive_enqueue_swiper_editor_assets(), functions.php).
 *
 * coptrz-post-grid-block.js and coptrz-layouts-block.js keep their own local
 * field-building helpers rather than adopting selectField/textField/etc from
 * here — they already work, and moving working code is pure risk with no
 * user-visible benefit. Both do still depend on this file for LivePreview/
 * PreviewToggle (see coptrz_block_preview_renderers(), includes/block-preview.php).
 * This is the version for everything new.
 */
(function (wp) {

    const { createElement: el, Fragment, useState, useRef, useEffect } = wp.element;
    const {
        SelectControl, TextControl, ToggleControl, FormTokenField,
        Button, ButtonGroup
    } = wp.components;

    // {value: label, …} (order-preserving) -> [{label, value}, …] for SelectControl.
    function opts(obj) {
        return Object.keys(obj || {}).map(function (v) { return { label: obj[v], value: v }; });
    }

    function selectField(label, value, optionsObj, onChange, help) {
        return el(SelectControl, {
            label: label,
            value: value || '',
            options: opts(optionsObj),
            onChange: onChange,
            help: help
        });
    }

    function textField(label, value, onChange, extra) {
        return el(TextControl, Object.assign({
            label: label,
            value: value || '',
            onChange: onChange
        }, extra || {}));
    }

    function boolField(label, checked, onChange) {
        return el(ToggleControl, { label: label, checked: !!checked, onChange: onChange });
    }

    /**
     * Multi-select-by-search picker backed by real IDs (posts or terms) rather
     * than freeform text. Stores {id, title} pairs so picked labels survive a
     * reload without re-resolving IDs on mount. `fetchPath` may already contain
     * a `?type=…` query string (the shared /dd/v1/block-pickers route) — the
     * search param is appended with `&` or `?` as appropriate.
     */
    function IdTokenPicker(props) {
        var value = props.value || [];
        const [suggestions, setSuggestions] = useState([]);
        const timerRef = useRef(null);

        function onInputChange(input) {
            if (timerRef.current) { clearTimeout(timerRef.current); }
            if (!input || input.length < 2) { setSuggestions([]); return; }
            timerRef.current = setTimeout(function () {
                var sep = props.fetchPath.indexOf('?') > -1 ? '&' : '?';
                wp.apiFetch({ path: props.fetchPath + sep + 'search=' + encodeURIComponent(input) })
                    .then(function (items) {
                        setSuggestions((items || []).map(function (it) {
                            var title = it.name ? it.name : (it.title && it.title.rendered ? it.title.rendered : (it.title || ('#' + it.id)));
                            return { id: it.id, title: title };
                        }));
                    })
                    .catch(function () { setSuggestions([]); });
            }, 300);
        }

        function onChange(tokens) {
            var known = {};
            value.forEach(function (v) { known[v.title] = v; });
            suggestions.forEach(function (s) { known[s.title] = s; });
            var next = tokens.map(function (t) { return known[t]; }).filter(function (v) { return !!v; });
            props.onChange(next);
        }

        return el(FormTokenField, {
            label: props.label,
            value: value.map(function (v) { return v.title; }),
            suggestions: suggestions.map(function (s) { return s.title; }),
            onInputChange: onInputChange,
            onChange: onChange,
            help: props.help
        });
    }

    /**
     * Single-item search-as-you-type picker, backed by a real ID. Same
     * debounced-suggestions approach as IdTokenPicker, constrained to one token.
     */
    function SinglePostPicker(props) {
        var value = props.value; // {id, title} | null
        const [suggestions, setSuggestions] = useState([]);
        const timerRef = useRef(null);

        function onInputChange(input) {
            if (timerRef.current) { clearTimeout(timerRef.current); }
            if (!input || input.length < 2) { setSuggestions([]); return; }
            timerRef.current = setTimeout(function () {
                var sep = props.fetchPath.indexOf('?') > -1 ? '&' : '?';
                wp.apiFetch({ path: props.fetchPath + sep + 'search=' + encodeURIComponent(input) })
                    .then(function (items) {
                        setSuggestions((items || []).map(function (it) {
                            return { id: it.id, title: it.title || it.name || ('#' + it.id) };
                        }));
                    })
                    .catch(function () { setSuggestions([]); });
            }, 300);
        }

        function onChange(tokens) {
            if (!tokens.length) { props.onChange(null); return; }
            var lastTitle = tokens[tokens.length - 1];
            var match = suggestions.filter(function (s) { return s.title === lastTitle; })[0];
            if (match) { props.onChange(match); }
        }

        return el(FormTokenField, {
            label: props.label,
            value: value ? [value.title] : [],
            suggestions: suggestions.map(function (s) { return s.title; }),
            onInputChange: onInputChange,
            onChange: onChange,
            maxLength: 1,
            help: props.help
        });
    }

    /**
     * Fetch-once dropdown for small, non-searched lists.
     */
    function FetchOnceSelect(props) {
        const [items, setItems] = useState([]);
        const [loading, setLoading] = useState(true);

        useEffect(function () {
            wp.apiFetch({ path: props.fetchPath })
                .then(function (res) { setItems(res || []); setLoading(false); })
                .catch(function () { setLoading(false); });
        }, []);

        var options = [{ label: props.emptyLabel || '— None —', value: 0 }].concat(
            items.map(function (it) { return { label: it.title || ('#' + it.id), value: it.id }; })
        );

        return el(SelectControl, {
            label: props.label,
            value: props.value || 0,
            options: loading ? [{ label: 'Loading…', value: 0 }] : options,
            onChange: function (val) {
                var id = parseInt(val, 10) || 0;
                var match = items.filter(function (it) { return it.id === id; })[0];
                props.onChange(id, match ? (match.title || '') : '');
            },
            help: props.help
        });
    }

    /**
     * Generic reorderable repeater. `items` is a plain array; `renderRow(item,
     * idx, update)` renders one row's fields, where `update(patch)` merges a
     * partial object into that row. `defaultItem()` produces a new row for
     * "+ Add". `addLabel` customises the add button's text.
     */
    function Repeater(props) {
        var items = props.items || [];

        function set(next) { props.onChange(next); }
        function updateAt(idx, patch) {
            var next = items.slice();
            next[idx] = Object.assign({}, next[idx], patch);
            set(next);
        }
        function removeAt(idx) {
            var next = items.slice();
            next.splice(idx, 1);
            set(next);
        }
        function move(idx, dir) {
            var target = idx + dir;
            if (target < 0 || target >= items.length) { return; }
            var next = items.slice();
            var tmp = next[idx];
            next[idx] = next[target];
            next[target] = tmp;
            set(next);
        }

        return el(Fragment, null,
            items.map(function (item, idx) {
                return el('div', {
                    key: idx,
                    style: { border: '1px solid #ddd', borderRadius: '4px', padding: '10px', marginBottom: '10px', background: '#fff' }
                },
                    el('div', { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '6px' } },
                        el('strong', null, (props.rowLabel ? props.rowLabel(item, idx) : ('Item ' + (idx + 1)))),
                        el(ButtonGroup, null,
                            el(Button, { icon: 'arrow-up-alt2', label: 'Move up', onClick: function () { move(idx, -1); }, disabled: idx === 0 }),
                            el(Button, { icon: 'arrow-down-alt2', label: 'Move down', onClick: function () { move(idx, 1); }, disabled: idx === items.length - 1 }),
                            el(Button, { icon: 'trash', label: 'Remove', isDestructive: true, onClick: function () { removeAt(idx); } })
                        )
                    ),
                    props.renderRow(item, idx, function (patch) { updateAt(idx, patch); })
                );
            }),
            el(Button, {
                variant: 'secondary',
                onClick: function () { set(items.concat([props.defaultItem()])); }
            }, props.addLabel || '+ Add')
        );
    }

    /**
     * Shared preview fetch plumbing — prevents section-heavy pages from
     * freezing the editor. Without this, every LivePreview mount fires
     * /dd/v1/block-preview at once (thundering herd of PHP SSR + RawHTML).
     *
     * - Concurrency queue: at most PREVIEW_MAX_CONCURRENT in-flight POSTs.
     * - Session cache: keyed by name|postId|attrKey; scroll away/back reuses HTML.
     */
    var PREVIEW_MAX_CONCURRENT = 3;
    var previewInFlight = 0;
    var previewQueue = [];
    var previewCache = typeof Map !== 'undefined' ? new Map() : null;

    function drainPreviewQueue() {
        while (previewInFlight < PREVIEW_MAX_CONCURRENT && previewQueue.length) {
            (function (item) {
                previewInFlight++;
                Promise.resolve()
                    .then(item.task)
                    .then(function (res) {
                        previewInFlight--;
                        item.resolve(res);
                        drainPreviewQueue();
                    }, function (err) {
                        previewInFlight--;
                        item.reject(err);
                        drainPreviewQueue();
                    });
            })(previewQueue.shift());
        }
    }

    function enqueuePreviewFetch(task) {
        return new Promise(function (resolve, reject) {
            previewQueue.push({ task: task, resolve: resolve, reject: reject });
            drainPreviewQueue();
        });
    }

    function previewCacheKey(name, postId, attrKey) {
        return name + '|' + postId + '|' + attrKey;
    }

    function getCurrentPostId() {
        try {
            return wp.data.select('core/editor').getCurrentPostId() || 0;
        } catch (e) {
            return 0;
        }
    }

    /**
     * Live server-rendered preview for a `save: null` block, fetched from
     * `/dd/v1/block-preview` (includes/block-preview.php). Debounced 400ms
     * since these renderers call wp_unique_id() — every response is
     * byte-different, so re-fetching on every keystroke would re-key the
     * canvas DOM constantly. `props.attributes` is the block's live (possibly
     * unsaved) attributes object; `props.placeholder` is rendered instead
     * whenever the preview comes back empty (a function_exists() guard
     * bailed, or the block has no content yet) — pass the block's existing
     * empty-state Placeholder so that case looks exactly as it does today.
     * The post ID is read imperatively (not via useSelect) since it never
     * changes within one editor session.
     *
     * Fetches only when the preview shell is in (or near) the viewport, or
     * when `props.clientId` is set and that block is selected — so opening a
     * long page does not SSR every section at once. Off-screen shells show a
     * lightweight pending placeholder until scrolled into view.
     *
     * `props.context` is an optional modifier (currently only `'header'`) that
     * (1) appends a `coptrz-block-preview--{context}` class, and (2) for
     * `'header'`, nests the rendered HTML inside the real header's ancestor
     * chain (`.header > .header-inner > .row.header-right`) so the theme's
     * `.header …`-scoped CSS (dark backdrop, white text, `d-lg-*` utilities —
     * see assets/scss/base/_block-preview.scss) actually matches. Header
     * element blocks render fragments (icon clusters, nav, logo) that are only
     * legible inside that real markup context, not on the bare white canvas.
     */
    function LivePreview(props) {
        var name = props.name;
        var attributes = props.attributes || {};
        var placeholder = props.placeholder || null;
        var context = props.context || '';
        var clientId = props.clientId || '';
        var attrKey = JSON.stringify(attributes);

        const [state, setState] = useState({ html: '', rendered: false, loading: false, waiting: true });
        const [isVisible, setIsVisible] = useState(false);
        const [isSelected, setIsSelected] = useState(false);
        const seqRef = useRef(0);
        const timerRef = useRef(null);
        const rootRef = useRef(null);
        const fetchedKeyRef = useRef('');

        // Viewport gate — only SSR when near the editor canvas viewport.
        useEffect(function () {
            var node = rootRef.current;
            if (!node) { return; }
            if (typeof IntersectionObserver === 'undefined') {
                setIsVisible(true);
                return;
            }
            var obs = new IntersectionObserver(function (entries) {
                var entry = entries[0];
                if (entry) { setIsVisible(!!entry.isIntersecting); }
            }, { root: null, rootMargin: '200px 0px', threshold: 0 });
            obs.observe(node);
            return function () { obs.disconnect(); };
        }, []);

        // Optional selected-block boost (callers may pass clientId).
        useEffect(function () {
            if (!clientId || !wp.data || !wp.data.subscribe) { return; }
            function check() {
                try {
                    setIsSelected(!!wp.data.select('core/block-editor').isBlockSelected(clientId));
                } catch (e) { /* store not ready */ }
            }
            check();
            return wp.data.subscribe(check);
        }, [clientId]);

        var shouldFetch = isVisible || isSelected;

        useEffect(function () {
            if (timerRef.current) { clearTimeout(timerRef.current); timerRef.current = null; }

            if (!shouldFetch) {
                // Keep existing HTML if we already rendered; otherwise stay pending.
                setState(function (s) {
                    if (s.rendered || s.loading) { return s; }
                    return { html: '', rendered: false, loading: false, waiting: true };
                });
                return;
            }

            var postId = getCurrentPostId();
            var cacheKey = previewCacheKey(name, postId, attrKey);

            if (previewCache && previewCache.has(cacheKey)) {
                fetchedKeyRef.current = cacheKey;
                var cached = previewCache.get(cacheKey);
                setState({ html: cached.html, rendered: cached.rendered, loading: false, waiting: false });
                return;
            }

            setState(function (s) {
                return Object.assign({}, s, { loading: true, waiting: false });
            });

            timerRef.current = setTimeout(function () {
                var mySeq = ++seqRef.current;
                // Re-check cache after debounce (a sibling LivePreview may have filled it).
                if (previewCache && previewCache.has(cacheKey)) {
                    if (mySeq !== seqRef.current) { return; }
                    fetchedKeyRef.current = cacheKey;
                    var hit = previewCache.get(cacheKey);
                    setState({ html: hit.html, rendered: hit.rendered, loading: false, waiting: false });
                    return;
                }

                fetchedKeyRef.current = cacheKey;
                enqueuePreviewFetch(function () {
                    return wp.apiFetch({
                        path: '/dd/v1/block-preview',
                        method: 'POST',
                        data: { name: name, attributes: attributes, post_id: postId }
                    });
                }).then(function (res) {
                    if (mySeq !== seqRef.current) { return; }
                    var html = (res && res.html) || '';
                    var rendered = !!(res && res.rendered);
                    if (previewCache) {
                        previewCache.set(cacheKey, { html: html, rendered: rendered });
                    }
                    setState({ html: html, rendered: rendered, loading: false, waiting: false });
                }).catch(function () {
                    if (mySeq !== seqRef.current) { return; }
                    setState({ html: '', rendered: false, loading: false, waiting: false });
                });
            }, 400);

            return function () { if (timerRef.current) { clearTimeout(timerRef.current); } };
            // eslint-disable-next-line
        }, [name, attrKey, shouldFetch]);

        var inner;
        if (state.waiting && !state.rendered) {
            inner = el('div', { className: 'coptrz-block-preview-empty coptrz-block-preview-pending' },
                'Preview when visible…');
        } else if (!state.rendered) {
            inner = placeholder || el('div', { className: 'coptrz-block-preview-empty' },
                state.loading ? 'Loading preview…' : 'Nothing to preview yet.');
        } else {
            var rawHtml = el(wp.element.RawHTML, null, state.html);
            inner = context === 'header'
                ? el('div', { className: 'header small-text' },
                    el('div', { className: 'header-inner rounded-10px' },
                        el('div', { className: 'row g-2 header-right align-items-center' }, rawHtml)
                    )
                )
                : rawHtml;
        }

        var className = 'coptrz-block-preview'
            + (context ? ' coptrz-block-preview--' + context : '')
            + (state.loading && state.rendered ? ' is-refreshing' : '')
            + (state.waiting && !state.rendered ? ' is-pending' : '');

        return el('div', { ref: rootRef, className: className }, inner);
    }

    /**
     * Block-toolbar Preview/Edit switch. `mode` lives in the block's own React
     * state (not an attribute) — it's editor UI only, so it never affects
     * serialization or invalidates existing saved blocks.
     */
    function PreviewToggle(props) {
        const { BlockControls } = wp.blockEditor;
        const { ToolbarGroup, ToolbarButton } = wp.components;
        return el(BlockControls, null,
            el(ToolbarGroup, null,
                el(ToolbarButton, {
                    icon: 'visibility',
                    label: 'Preview',
                    isPressed: props.mode === 'preview',
                    onClick: function () { props.setMode('preview'); }
                }),
                el(ToolbarButton, {
                    icon: 'edit',
                    label: 'Edit',
                    isPressed: props.mode === 'edit',
                    onClick: function () { props.setMode('edit'); }
                })
            )
        );
    }

    window.coptrzBlockUI = {
        opts: opts,
        selectField: selectField,
        textField: textField,
        boolField: boolField,
        IdTokenPicker: IdTokenPicker,
        SinglePostPicker: SinglePostPicker,
        FetchOnceSelect: FetchOnceSelect,
        Repeater: Repeater,
        LivePreview: LivePreview,
        PreviewToggle: PreviewToggle
    };

})(window.wp);
