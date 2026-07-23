/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 *
 * Registers the `coptrz/hero` block: a native Gutenberg equivalent of the
 * legacy per-post "Hero" meta container (includes/post-meta.php,
 * __hero_fields() / __hero_button_fields() / __hero_form_fields()), covering
 * every option those three tabs expose in one Inspector-only block — the
 * canvas shows a summary Placeholder, all editing happens in the sidebar.
 *
 * The block is storage only: save() returns null, and its render_block filter
 * (coptrz_render_hero_block(), includes/hero-block.php) always emits '' — the
 * hero itself still renders at each template's existing position via
 * ___hero_modules() (includes/modules.php), which reads this block's
 * attributes via coptrz_hero_block_attrs() when present. See
 * includes/hero-block.php's docblock for why the hero can't render inline.
 *
 * Option lists are localized as `coptrzHero` from coptrz_hero_field_options()
 * (functions.php) so the block and the legacy admin fields can't drift apart.
 * The button/form-product post picker searches `/dd/v1/hero-link-targets`
 * (includes/hooks.php); CF7 forms and documents reuse the existing
 * `/dd/v1/cf7-forms` / `/dd/v1/documents` routes from dd/cf7-pdf-form.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState, useEffect, useRef } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const {
        PanelBody, SelectControl, ToggleControl, TextControl, TextareaControl,
        Button, ButtonGroup, FormTokenField, Placeholder
    } = wp.components;

    const OPTS = window.coptrzHero || {};

    /* --------------------------------------------------------------- */
    /*  Small helpers (kept local — each coptrz/* block file is self-    */
    /*  contained, no shared JS module system without a build step)      */
    /* --------------------------------------------------------------- */

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
     * Background media picker for one breakpoint (desktop/tablet/mobile share
     * this markup) — image or video, mirrors the desktop MediaUpload render
     * prop but factored out so tablet/mobile don't triplicate it.
     */
    function backgroundMediaSlot(label, hint, imageUrl, imageId, onSelect, onRemove) {
        return el(
            'div',
            { style: { marginBottom: '16px' } },
            el('p', { style: { fontWeight: 600, margin: '0 0 4px' } }, label),
            hint ? el('p', { style: { fontSize: '12px', color: '#757575', margin: '0 0 8px' } }, hint) : null,
            el(MediaUploadCheck, null,
                el(MediaUpload, {
                    allowedTypes: ['image', 'video'],
                    value: imageId || undefined,
                    onSelect: onSelect,
                    render: function (o) {
                        return el(Fragment, null,
                            imageUrl ? el('img', { src: imageUrl, style: { maxWidth: '100%', marginBottom: '8px' } }) : null,
                            el(Button, { variant: 'secondary', onClick: o.open }, imageId ? 'Replace' : 'Select'),
                            imageId ? el(Button, { variant: 'link', isDestructive: true, onClick: onRemove }, 'Remove') : null
                        );
                    }
                })
            )
        );
    }

    /**
     * Single-item search-as-you-type picker, backed by a real ID rather than
     * freeform text. Same debounced-suggestions approach as
     * coptrz-post-grid-block.js's IdTokenPicker, constrained to one token.
     * Stores {id, title} so the picked label survives a reload without
     * re-resolving the ID on mount.
     */
    function SinglePostPicker(props) {
        var value = props.value; // {id, title} | null
        const [suggestions, setSuggestions] = useState([]);
        const timerRef = useRef(null);

        function onInputChange(input) {
            if (timerRef.current) { clearTimeout(timerRef.current); }
            if (!input || input.length < 2) { setSuggestions([]); return; }
            timerRef.current = setTimeout(function () {
                wp.apiFetch({ path: props.fetchPath + (props.fetchPath.indexOf('?') > -1 ? '&' : '?') + 'search=' + encodeURIComponent(input) })
                    .then(function (items) {
                        setSuggestions((items || []).map(function (it) {
                            return { id: it.id, title: it.title || ('#' + it.id) };
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
     * Fetch-once dropdown for small, non-searched lists (CF7 forms,
     * documents) — same shape as coptrz-layouts-block.js's Layout picker.
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

    /* --------------------------------------------------------------- */
    /*  Buttons repeater                                                  */
    /* --------------------------------------------------------------- */

    function defaultButton() {
        return { type: '', text: '', postId: 0, postTitle: '', urlCustom: '', style: 'button-accent', target: 'target="_self"' };
    }

    function ButtonRow(props) {
        var item = props.item;
        function update(patch) { props.onChange(Object.assign({}, item, patch)); }

        var postTypeSlug = (OPTS.buttonTypePostTypes || {})[item.type];

        return el('div', { style: { border: '1px solid #ddd', borderRadius: '4px', padding: '10px', marginBottom: '10px', background: '#fff' } },
            el('div', { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '6px' } },
                el('strong', null, item.text || 'Button'),
                el(ButtonGroup, null,
                    el(Button, { icon: 'arrow-up-alt2', label: 'Move up', onClick: props.moveUp, disabled: props.isFirst }),
                    el(Button, { icon: 'arrow-down-alt2', label: 'Move down', onClick: props.moveDown, disabled: props.isLast }),
                    el(Button, { icon: 'trash', label: 'Remove', isDestructive: true, onClick: props.onRemove })
                )
            ),
            selectField('Button Type', item.type, OPTS.buttonType, function (v) {
                update({ type: v, postId: 0, postTitle: '' });
            }),
            textField('Button Text', item.text, function (v) { update({ text: v }); }),
            (item.type === 'custom')
                ? textField('Button URL', item.urlCustom, function (v) { update({ urlCustom: v }); })
                : (postTypeSlug ? el(SinglePostPicker, {
                    label: 'Select ' + (OPTS.buttonType && OPTS.buttonType[item.type] ? OPTS.buttonType[item.type] : 'Item'),
                    fetchPath: '/dd/v1/hero-link-targets?type=' + postTypeSlug,
                    value: item.postId ? { id: item.postId, title: item.postTitle } : null,
                    onChange: function (v) { update({ postId: v ? v.id : 0, postTitle: v ? v.title : '' }); }
                }) : null),
            selectField('Button Style', item.style, OPTS.buttonStyle, function (v) { update({ style: v }); }),
            selectField('Button Target', item.target, OPTS.buttonTarget, function (v) { update({ target: v }); })
        );
    }

    function ButtonsRepeater(props) {
        var buttons = props.buttons || [];

        function set(next) { props.onChange(next); }
        function updateAt(idx, next) {
            var copy = buttons.slice();
            copy[idx] = next;
            set(copy);
        }
        function removeAt(idx) {
            var copy = buttons.slice();
            copy.splice(idx, 1);
            set(copy);
        }
        function move(idx, dir) {
            var target = idx + dir;
            if (target < 0 || target >= buttons.length) { return; }
            var copy = buttons.slice();
            var tmp = copy[idx];
            copy[idx] = copy[target];
            copy[target] = tmp;
            set(copy);
        }

        return el(Fragment, null,
            buttons.map(function (item, idx) {
                return el(ButtonRow, {
                    key: idx,
                    item: item,
                    isFirst: idx === 0,
                    isLast: idx === buttons.length - 1,
                    moveUp: function () { move(idx, -1); },
                    moveDown: function () { move(idx, 1); },
                    onRemove: function () { removeAt(idx); },
                    onChange: function (next) { updateAt(idx, next); }
                });
            }),
            el(Button, { variant: 'secondary', onClick: function () { set(buttons.concat([defaultButton()])); } }, '+ Add Button')
        );
    }

    /* --------------------------------------------------------------- */
    /*  Block registration                                               */
    /* --------------------------------------------------------------- */

    registerBlockType('coptrz/hero', {
        title: 'Hero',
        icon: 'cover-image',
        category: 'design',
        description: 'The page hero — heading, background, buttons and an optional lead-gen form. Native equivalent of the legacy Hero meta box.',
        supports: { html: false, multiple: false, reusable: false },
        attributes: {
            hidden: { type: 'boolean', default: false },
            breadcrumbsHidden: { type: 'boolean', default: false },
            heading: { type: 'string', default: '' },
            description: { type: 'string', default: '' },
            backgroundType: { type: 'string', default: 'self-hosted' },
            backgroundId: { type: 'number', default: 0 },
            backgroundUrl: { type: 'string', default: '' },
            backgroundYoutube: { type: 'string', default: '' },
            backgroundTabletId: { type: 'number', default: 0 },
            backgroundTabletUrl: { type: 'string', default: '' },
            backgroundTabletYoutube: { type: 'string', default: '' },
            backgroundMobileId: { type: 'number', default: 0 },
            backgroundMobileUrl: { type: 'string', default: '' },
            backgroundMobileYoutube: { type: 'string', default: '' },
            height: { type: 'string', default: '' },
            alignment: { type: 'string', default: '' },
            buttons: { type: 'array', default: [] },
            formEnable: { type: 'boolean', default: false },
            formImageId: { type: 'number', default: 0 },
            formImageUrl: { type: 'string', default: '' },
            formHeading: { type: 'string', default: '' },
            formDescription: { type: 'string', default: '' },
            formRedirectType: { type: 'string', default: '' },
            formPdfRedirectId: { type: 'number', default: 0 },
            formPdfRedirectUrl: { type: 'string', default: '' },
            formDocumentRedirectId: { type: 'number', default: 0 },
            formDocumentRedirectTitle: { type: 'string', default: '' },
            formRedirectUrl: { type: 'string', default: '' },
            formStyle: { type: 'string', default: '' },
            formType: { type: 'string', default: '' },
            formScript: { type: 'string', default: '' },
            formId: { type: 'number', default: 0 },
            formTitle: { type: 'string', default: '' },
            formProductId: { type: 'number', default: 0 },
            formProductTitle: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;

            var summaryParts = [];
            summaryParts.push(a.heading ? a.heading : 'Defaults to page title');
            if (a.height === 'small-hero') { summaryParts.push('Small height'); }
            if (a.alignment) { summaryParts.push(OPTS.alignment ? OPTS.alignment[a.alignment] : a.alignment); }
            if (a.backgroundType === 'youtube' && a.backgroundYoutube) { summaryParts.push('YouTube background'); }
            else if (a.backgroundId) { summaryParts.push('Background image/video set'); }
            if (a.buttons && a.buttons.length) { summaryParts.push(a.buttons.length + ' button' + (a.buttons.length === 1 ? '' : 's')); }
            if (a.formEnable) { summaryParts.push('Form: ' + (a.formTitle || a.formType || 'enabled')); }

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Hero', initialOpen: true },
                        boolField('Hide Hero', a.hidden, function (v) { setAttributes({ hidden: v }); }),
                        a.hidden ? el('p', { style: { fontStyle: 'italic', color: '#757575' } }, 'The hero is replaced by breadcrumbs + the page title only — nothing else below applies.') : null,
                        boolField('Hide Breadcrumbs', a.breadcrumbsHidden, function (v) { setAttributes({ breadcrumbsHidden: v }); }),
                        textField('Heading', a.heading, function (v) { setAttributes({ heading: v }); }, { placeholder: 'Defaults to page title' }),
                        el(TextareaControl, { label: 'Description', value: a.description || '', onChange: function (v) { setAttributes({ description: v }); } }),
                        selectField('Height', a.height, OPTS.height, function (v) { setAttributes({ height: v }); }),
                        selectField('Alignment', a.alignment, OPTS.alignment, function (v) { setAttributes({ alignment: v }); })
                    ),
                    el(
                        PanelBody,
                        { title: 'Background', initialOpen: false },
                        selectField('Background Type', a.backgroundType, OPTS.backgroundType, function (v) { setAttributes({ backgroundType: v }); }),
                        a.backgroundType === 'youtube'
                            ? textField('Background YouTube ID', a.backgroundYoutube, function (v) { setAttributes({ backgroundYoutube: v }); })
                            : el(MediaUploadCheck, null,
                                el(MediaUpload, {
                                    allowedTypes: ['image', 'video'],
                                    value: a.backgroundId || undefined,
                                    onSelect: function (media) { setAttributes({ backgroundId: media.id, backgroundUrl: media.url }); },
                                    render: function (o) {
                                        return el(Fragment, null,
                                            a.backgroundUrl ? el('img', { src: a.backgroundUrl, style: { maxWidth: '100%', marginBottom: '8px' } }) : null,
                                            el(Button, { variant: 'secondary', onClick: o.open }, a.backgroundId ? 'Replace Background' : 'Select Background'),
                                            a.backgroundId ? el(Button, { variant: 'link', isDestructive: true, onClick: function () { setAttributes({ backgroundId: 0, backgroundUrl: '' }); } }, 'Remove') : null
                                        );
                                    }
                                })
                            ),
                        el('hr', { style: { margin: '16px 0' } }),
                        el('p', { style: { fontSize: '12px', color: '#757575', marginTop: 0 } },
                            'Optionally override the background on smaller screens. Leave blank to use the background above.'),
                        a.backgroundType === 'youtube'
                            ? textField('Tablet YouTube ID', a.backgroundTabletYoutube, function (v) { setAttributes({ backgroundTabletYoutube: v }); }, { help: 'Shown at 768–991px. Optional — falls back to the background above.' })
                            : backgroundMediaSlot(
                                'Tablet background',
                                'Shown at 768–991px. Optional — falls back to the background above.',
                                a.backgroundTabletUrl, a.backgroundTabletId,
                                function (media) { setAttributes({ backgroundTabletId: media.id, backgroundTabletUrl: media.url }); },
                                function () { setAttributes({ backgroundTabletId: 0, backgroundTabletUrl: '' }); }
                            ),
                        a.backgroundType === 'youtube'
                            ? textField('Mobile YouTube ID', a.backgroundMobileYoutube, function (v) { setAttributes({ backgroundMobileYoutube: v }); }, { help: 'Shown at ≤767px. Optional — falls back to the background above.' })
                            : backgroundMediaSlot(
                                'Mobile background',
                                'Shown at ≤767px. Optional — falls back to the background above.',
                                a.backgroundMobileUrl, a.backgroundMobileId,
                                function (media) { setAttributes({ backgroundMobileId: media.id, backgroundMobileUrl: media.url }); },
                                function () { setAttributes({ backgroundMobileId: 0, backgroundMobileUrl: '' }); }
                            )
                    ),
                    el(
                        PanelBody,
                        { title: 'Buttons', initialOpen: false },
                        el(ButtonsRepeater, {
                            buttons: a.buttons,
                            onChange: function (v) { setAttributes({ buttons: v }); }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'Hero Form', initialOpen: false },
                        boolField('Enable Form Hero', a.formEnable, function (v) { setAttributes({ formEnable: v }); }),
                        a.formEnable ? el(Fragment, null,
                            el(MediaUploadCheck, null,
                                el(MediaUpload, {
                                    allowedTypes: ['image'],
                                    value: a.formImageId || undefined,
                                    onSelect: function (media) { setAttributes({ formImageId: media.id, formImageUrl: media.url }); },
                                    render: function (o) {
                                        return el(Fragment, null,
                                            a.formImageUrl ? el('img', { src: a.formImageUrl, style: { maxWidth: '100%', marginBottom: '8px' } }) : null,
                                            el(Button, { variant: 'secondary', onClick: o.open, style: { marginBottom: '8px' } }, a.formImageId ? 'Replace Image' : 'Select Image')
                                        );
                                    }
                                })
                            ),
                            textField('Form Heading', a.formHeading, function (v) { setAttributes({ formHeading: v }); }),
                            el(TextareaControl, { label: 'Form Description', value: a.formDescription || '', onChange: function (v) { setAttributes({ formDescription: v }); } }),
                            selectField('Style', a.formStyle, OPTS.formStyle, function (v) { setAttributes({ formStyle: v }); }),
                            selectField('Hero Form Type', a.formType, OPTS.formType, function (v) { setAttributes({ formType: v }); }),
                            (a.formType === 'script')
                                ? el(TextareaControl, { label: 'Script', value: a.formScript || '', onChange: function (v) { setAttributes({ formScript: v }); } })
                                : (a.formType === 'product')
                                    ? el(SinglePostPicker, {
                                        label: 'Select Product',
                                        fetchPath: '/dd/v1/hero-link-targets?type=product',
                                        value: a.formProductId ? { id: a.formProductId, title: a.formProductTitle } : null,
                                        onChange: function (v) { setAttributes({ formProductId: v ? v.id : 0, formProductTitle: v ? v.title : '' }); }
                                    })
                                    : el(FetchOnceSelect, {
                                        label: 'Select Form',
                                        fetchPath: '/dd/v1/cf7-forms',
                                        value: a.formId,
                                        emptyLabel: '— Select a form —',
                                        onChange: function (id, title) { setAttributes({ formId: id, formTitle: title }); }
                                    }),
                            el('hr'),
                            selectField('Form Redirect Type', a.formRedirectType, OPTS.formRedirectType, function (v) { setAttributes({ formRedirectType: v }); }),
                            (a.formRedirectType === 'pdf')
                                ? el(MediaUploadCheck, null,
                                    el(MediaUpload, {
                                        allowedTypes: ['application/pdf'],
                                        value: a.formPdfRedirectId || undefined,
                                        onSelect: function (media) { setAttributes({ formPdfRedirectId: media.id, formPdfRedirectUrl: media.url }); },
                                        render: function (o) {
                                            return el(Button, { variant: 'secondary', onClick: o.open }, a.formPdfRedirectId ? 'Replace PDF' : 'Select PDF');
                                        }
                                    })
                                )
                                : null,
                            (a.formRedirectType === 'document')
                                ? el(FetchOnceSelect, {
                                    label: 'Select Document',
                                    fetchPath: '/dd/v1/documents',
                                    value: a.formDocumentRedirectId,
                                    emptyLabel: '— Select a document —',
                                    onChange: function (id, title) { setAttributes({ formDocumentRedirectId: id, formDocumentRedirectTitle: title }); }
                                })
                                : null,
                            (a.formRedirectType === 'custom')
                                ? textField('Redirect URL', a.formRedirectUrl, function (v) { setAttributes({ formRedirectUrl: v }); })
                                : null
                        ) : null
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon: 'cover-image',
                        label: 'Hero',
                        instructions: a.hidden
                            ? 'Hero hidden — breadcrumbs + page title only.'
                            : summaryParts.join(' · ')
                    })
                )
            );
        },

        // Rendered at the template's existing hero position by ___hero_modules()
        // (includes/modules.php) — this block itself renders nothing where it
        // sits (coptrz_render_hero_block(), includes/hero-block.php).
        save: function () { return null; }
    });

})(window.wp);
