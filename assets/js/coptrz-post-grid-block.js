/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/post-grid` block: a native Gutenberg equivalent of the
 * legacy section builder's "Post Grid" item (includes/post-meta.php, `post_grid`
 * inside `section_items`), covering the same three configuration axes: Post
 * Type (which posts to query, and how to filter them), Post Box Styles (the
 * per-card wrapper's classes/inline styles), and Post Elements (an ordered,
 * repeatable list of what renders inside each card).
 *
 * The field taxonomy (post types + their sources, and every select's option
 * list) is localized as `coptrzPostGrid` — see coptrz_post_grid_post_types() /
 * coptrz_post_grid_field_options() in functions.php — rather than hard-coded
 * here, so editor and legacy admin field can't drift apart.
 *
 * save() returns null — rendered server-side by coptrz_render_post_grid_block()
 * (functions.php), which converts these attributes back into the row-array
 * shape ____post_grid_module() (includes/modules.php) expects and calls it
 * directly (there's no shortcode to delegate to, unlike dd/cf7-pdf-form or
 * coptrz/layouts).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState, useRef } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const {
        PanelBody, SelectControl, ToggleControl, TextControl, Button,
        FormTokenField, Placeholder, ButtonGroup
    } = wp.components;

    const REGISTRY = window.coptrzPostGrid || { postTypes: {}, fieldOptions: {} };
    const POST_TYPES = REGISTRY.postTypes || {};
    const OPTS = REGISTRY.fieldOptions || {};

    const SOURCE_LABELS = { all: 'All', manually: 'Manually', category: 'By Category' };

    /* --------------------------------------------------------------- */
    /*  Small helpers                                                    */
    /* --------------------------------------------------------------- */

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
     * than freeform text — used for both the "Manually" post picker and the
     * "Category" term picker. Stores {id, title} pairs so the picked labels
     * survive a reload without needing to re-resolve IDs on mount.
     */
    function IdTokenPicker(props) {
        var value = props.value || [];
        const [suggestions, setSuggestions] = useState([]);
        const timerRef = useRef(null);

        function onInputChange(input) {
            if (timerRef.current) { clearTimeout(timerRef.current); }
            if (!input || input.length < 2) { setSuggestions([]); return; }
            timerRef.current = setTimeout(function () {
                wp.apiFetch({ path: props.fetchPath + '?search=' + encodeURIComponent(input) + '&per_page=20' })
                    .then(function (items) {
                        setSuggestions((items || []).map(function (it) {
                            var title = it.name ? it.name : (it.title && it.title.rendered ? it.title.rendered : ('#' + it.id));
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

    /* --------------------------------------------------------------- */
    /*  Post Elements: types, defaults, per-type field editors           */
    /* --------------------------------------------------------------- */

    var ELEMENT_TYPES = [
        { type: 'post_title', label: 'Post Title', singleton: true },
        { type: 'featured_image', label: 'Featured Image', singleton: true },
        { type: 'post_excerpt', label: 'Excerpt', singleton: true },
        { type: 'permalink', label: 'Button (link to post)', singleton: true },
        { type: 'icon', label: 'Icon', singleton: true },
        { type: 'custom_field', label: 'Custom Field', singleton: false }
    ];

    function elementLabel(type) {
        var found = ELEMENT_TYPES.filter(function (t) { return t.type === type; })[0];
        return found ? found.label : type;
    }

    function defaultElement(type) {
        switch (type) {
            case 'post_title': return { type: type, textBefore: '', textAfter: '', tag: '', textColor: '', textColorCustom: '' };
            case 'featured_image': return { type: type, size: '', isBackgroundImage: false };
            case 'post_excerpt': return { type: type };
            case 'permalink': return { type: type, hideButtonOnMobile: false, buttonText: '', buttonStyle: 'button-accent' };
            case 'icon': return { type: type, iconId: 0, iconColor: '', iconColorCustom: '', iconWidth: '', iconHeight: '' };
            case 'custom_field': return { type: type, key: '', fieldType: 'p', wrapperClass: '' };
            default: return { type: type };
        }
    }

    // Renders the settings specific to one element row. `update(patch)` merges
    // a partial object into that row's attributes.
    function elementFields(item, update) {
        switch (item.type) {
            case 'post_title':
                return el(Fragment, null,
                    textField('Text Before', item.textBefore, function (v) { update({ textBefore: v }); }),
                    textField('Text After', item.textAfter, function (v) { update({ textAfter: v }); }),
                    selectField('Tag', item.tag, OPTS.postTitleTag, function (v) { update({ tag: v }); }),
                    selectField('Text Color', item.textColor, OPTS.postTitleColor, function (v) { update({ textColor: v }); }),
                    item.textColor === 'text-custom'
                        ? textField('Custom Text Color', item.textColorCustom, function (v) { update({ textColorCustom: v }); }, { type: 'text', placeholder: '#RRGGBB' })
                        : null
                );
            case 'featured_image':
                return el(Fragment, null,
                    selectField('Size', item.size, OPTS.imageSize, function (v) { update({ size: v }); }),
                    boolField('Use as background image', item.isBackgroundImage, function (v) { update({ isBackgroundImage: v }); })
                );
            case 'post_excerpt':
                return null;
            case 'permalink':
                return el(Fragment, null,
                    textField('Button Text', item.buttonText, function (v) { update({ buttonText: v }); }),
                    selectField('Button Style', item.buttonStyle, OPTS.buttonStyle, function (v) { update({ buttonStyle: v }); }),
                    boolField('Hide button on mobile', item.hideButtonOnMobile, function (v) { update({ hideButtonOnMobile: v }); })
                );
            case 'icon':
                return el(Fragment, null,
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            allowedTypes: ['image/svg+xml'],
                            value: item.iconId || undefined,
                            onSelect: function (media) { update({ iconId: media.id }); },
                            render: function (o) {
                                return el(Button, { variant: 'secondary', onClick: o.open, style: { marginBottom: '8px' } },
                                    item.iconId ? 'Replace SVG Icon' : 'Select SVG Icon');
                            }
                        })
                    ),
                    selectField('Icon Color', item.iconColor, OPTS.iconColor, function (v) { update({ iconColor: v }); }),
                    item.iconColor === 'text-custom'
                        ? textField('Custom Icon Color', item.iconColorCustom, function (v) { update({ iconColorCustom: v }); }, { type: 'text', placeholder: '#RRGGBB' })
                        : null,
                    textField('Custom Width', item.iconWidth, function (v) { update({ iconWidth: v }); }),
                    textField('Custom Height', item.iconHeight, function (v) { update({ iconHeight: v }); })
                );
            case 'custom_field':
                return el(Fragment, null,
                    textField('Meta Key', item.key, function (v) { update({ key: v }); }, { help: 'Raw post-meta key, e.g. _testimonial_content' }),
                    selectField('Render As', item.fieldType, OPTS.customFieldType, function (v) { update({ fieldType: v }); }),
                    textField('Wrapper Class', item.wrapperClass, function (v) { update({ wrapperClass: v }); })
                );
            default:
                return null;
        }
    }

    function PostElementsRepeater(props) {
        var elements = props.elements || [];

        function setElements(next) { props.onChange(next); }

        function updateAt(idx, patch) {
            var next = elements.slice();
            next[idx] = Object.assign({}, next[idx], patch);
            setElements(next);
        }
        function removeAt(idx) {
            var next = elements.slice();
            next.splice(idx, 1);
            setElements(next);
        }
        function move(idx, dir) {
            var target = idx + dir;
            if (target < 0 || target >= elements.length) { return; }
            var next = elements.slice();
            var tmp = next[idx];
            next[idx] = next[target];
            next[target] = tmp;
            setElements(next);
        }
        function add(type) {
            if (!type) { return; }
            setElements(elements.concat([defaultElement(type)]));
        }

        var customFieldCount = elements.filter(function (e) { return e.type === 'custom_field'; }).length;
        var usedSingletons = {};
        elements.forEach(function (e) {
            var def = ELEMENT_TYPES.filter(function (t) { return t.type === e.type; })[0];
            if (def && def.singleton) { usedSingletons[e.type] = true; }
        });
        var addOptions = [{ label: '+ Add element…', value: '' }].concat(
            ELEMENT_TYPES.filter(function (t) {
                if (t.type === 'custom_field') { return customFieldCount < 5; }
                return !usedSingletons[t.type];
            }).map(function (t) { return { label: t.label, value: t.type }; })
        );

        return el(Fragment, null,
            elements.map(function (item, idx) {
                return el('div', {
                    key: idx,
                    style: { border: '1px solid #ddd', borderRadius: '4px', padding: '10px', marginBottom: '10px', background: '#fff' }
                },
                    el('div', { style: { display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '6px' } },
                        el('strong', null, elementLabel(item.type)),
                        el('div', null,
                            el(ButtonGroup, null,
                                el(Button, { icon: 'arrow-up-alt2', label: 'Move up', onClick: function () { move(idx, -1); }, disabled: idx === 0 }),
                                el(Button, { icon: 'arrow-down-alt2', label: 'Move down', onClick: function () { move(idx, 1); }, disabled: idx === elements.length - 1 }),
                                el(Button, { icon: 'trash', label: 'Remove', isDestructive: true, onClick: function () { removeAt(idx); } })
                            )
                        )
                    ),
                    elementFields(item, function (patch) { updateAt(idx, patch); })
                );
            }),
            el(SelectControl, {
                value: '',
                options: addOptions,
                onChange: add
            })
        );
    }

    /* --------------------------------------------------------------- */
    /*  Block registration                                               */
    /* --------------------------------------------------------------- */

    registerBlockType('coptrz/post-grid', {
        title: 'Post Grid (Legacy)',
        icon: 'grid-view',
        category: 'design',
        description: 'A configurable grid (or slider) of posts — the native equivalent of the section builder\'s Post Grid item.',
        attributes: {
            postType:      { type: 'string', default: '' },
            source:        { type: 'string', default: 'all' },
            manualPosts:   { type: 'array', default: [] },
            categoryTerms: { type: 'array', default: [] },
            isSlider:      { type: 'boolean', default: false },
            slidesDesktop: { type: 'string', default: '6' },
            slidesTablet:  { type: 'string', default: '' },
            slidesMobile:  { type: 'string', default: '' },
            boxStyles:     { type: 'object', default: {} },
            elements:      { type: 'array', default: [] }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                postType, source, manualPosts, categoryTerms,
                isSlider, slidesDesktop, slidesTablet, slidesMobile,
                boxStyles, elements
            } = attributes;

            var typeDef = POST_TYPES[postType];

            function setBoxStyle(patch) {
                setAttributes({ boxStyles: Object.assign({}, boxStyles, patch) });
            }

            var postTypeOptions = [{ label: '— Select a post type —', value: '' }].concat(
                Object.keys(POST_TYPES).map(function (slug) { return { label: POST_TYPES[slug].label, value: slug }; })
            );

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Post Type', initialOpen: true },
                        el(SelectControl, {
                            label: 'Post Type',
                            value: postType,
                            options: postTypeOptions,
                            onChange: function (val) {
                                setAttributes({ postType: val, source: 'all', manualPosts: [], categoryTerms: [] });
                            }
                        }),
                        typeDef ? el(SelectControl, {
                            label: 'Source',
                            value: source,
                            options: typeDef.sources.map(function (s) { return { label: SOURCE_LABELS[s] || s, value: s }; }),
                            onChange: function (val) { setAttributes({ source: val, manualPosts: [], categoryTerms: [] }); }
                        }) : null,
                        (typeDef && source === 'manually') ? el(IdTokenPicker, {
                            label: 'Posts',
                            fetchPath: '/wp/v2/' + typeDef.restBase,
                            value: manualPosts,
                            onChange: function (v) { setAttributes({ manualPosts: v }); },
                            help: 'Search by title and select specific posts.'
                        }) : null,
                        (typeDef && source === 'category' && typeDef.taxonomy) ? el(IdTokenPicker, {
                            label: 'Categories',
                            fetchPath: '/wp/v2/' + typeDef.taxonomy,
                            value: categoryTerms,
                            onChange: function (v) { setAttributes({ categoryTerms: v }); },
                            help: 'Posts in any of the selected categories.'
                        }) : null
                    ),
                    el(
                        PanelBody,
                        { title: 'Post Box Settings', initialOpen: false },
                        boolField('Display as a slider', isSlider, function (v) { setAttributes({ isSlider: v }); }),
                        isSlider ? textField('Slides (Desktop)', slidesDesktop, function (v) { setAttributes({ slidesDesktop: v }); }, { type: 'number' }) : null,
                        isSlider ? textField('Slides (Tablet)', slidesTablet, function (v) { setAttributes({ slidesTablet: v }); }, { type: 'number' }) : null,
                        isSlider ? textField('Slides (Mobile)', slidesMobile, function (v) { setAttributes({ slidesMobile: v }); }, { type: 'number' }) : null
                    ),
                    el(
                        PanelBody,
                        { title: 'Post Box Styles', initialOpen: false },
                        el('p', { style: { fontStyle: 'italic', color: '#757575' } }, 'Leave a group untouched to skip it entirely, matching the legacy field.'),
                        el('strong', null, 'Background & Text'),
                        selectField('Background Color', boxStyles.backgroundColor, OPTS.backgroundColor, function (v) { setBoxStyle({ backgroundColor: v }); }),
                        boxStyles.backgroundColor === 'bg-custom' ? textField('Custom Background Color', boxStyles.backgroundColorCustom, function (v) { setBoxStyle({ backgroundColorCustom: v }); }, { placeholder: '#RRGGBB' }) : null,
                        selectField('Text Color', boxStyles.textColor, OPTS.textColor, function (v) { setBoxStyle({ textColor: v }); }),
                        boxStyles.textColor === 'text-custom' ? textField('Custom Text Color', boxStyles.textColorCustom, function (v) { setBoxStyle({ textColorCustom: v }); }, { placeholder: '#RRGGBB' }) : null,

                        el('hr'),
                        el('strong', null, 'Padding'),
                        selectField('Top', boxStyles.paddingTop, OPTS.paddingTop, function (v) { setBoxStyle({ paddingTop: v }); }),
                        selectField('Bottom', boxStyles.paddingBottom, OPTS.paddingBottom, function (v) { setBoxStyle({ paddingBottom: v }); }),
                        selectField('Left', boxStyles.paddingLeft, OPTS.paddingLeft, function (v) { setBoxStyle({ paddingLeft: v }); }),
                        selectField('Right', boxStyles.paddingRight, OPTS.paddingRight, function (v) { setBoxStyle({ paddingRight: v }); }),

                        el('hr'),
                        el('strong', null, 'Margin'),
                        selectField('Top', boxStyles.marginTop, OPTS.marginTop, function (v) { setBoxStyle({ marginTop: v }); }),
                        selectField('Bottom', boxStyles.marginBottom, OPTS.marginBottom, function (v) { setBoxStyle({ marginBottom: v }); }),
                        selectField('Left', boxStyles.marginLeft, OPTS.marginLeft, function (v) { setBoxStyle({ marginLeft: v }); }),
                        selectField('Right', boxStyles.marginRight, OPTS.marginRight, function (v) { setBoxStyle({ marginRight: v }); }),

                        el('hr'),
                        el('strong', null, 'Alignment'),
                        selectField('Align Items', boxStyles.alignItems, OPTS.alignItems, function (v) { setBoxStyle({ alignItems: v }); }),
                        selectField('Justify Content', boxStyles.justifyContent, OPTS.justifyContent, function (v) { setBoxStyle({ justifyContent: v }); }),
                        selectField('Text Align', boxStyles.textAlign, OPTS.textAlign, function (v) { setBoxStyle({ textAlign: v }); }),

                        el('hr'),
                        el('strong', null, 'Column Width'),
                        selectField('Desktop', boxStyles.columnWidth, OPTS.columnWidth, function (v) { setBoxStyle({ columnWidth: v }); }),
                        selectField('Tablet', boxStyles.columnWidthTablet, OPTS.columnWidthTablet, function (v) { setBoxStyle({ columnWidthTablet: v }); }),
                        selectField('Mobile', boxStyles.columnWidthMobile, OPTS.columnWidthMobile, function (v) { setBoxStyle({ columnWidthMobile: v }); }),

                        el('hr'),
                        el('strong', null, 'Border'),
                        selectField('Radius', boxStyles.borderRadius, OPTS.borderRadius, function (v) { setBoxStyle({ borderRadius: v }); }),
                        boxStyles.borderRadius === 'custom' ? textField('Custom Radius', boxStyles.borderRadiusCustom, function (v) { setBoxStyle({ borderRadiusCustom: v }); }) : null,
                        selectField('Style', boxStyles.borderStyle, OPTS.borderStyle, function (v) { setBoxStyle({ borderStyle: v }); }),
                        boxStyles.borderStyle === 'border-custom' ? el(Fragment, null,
                            selectField('Color', boxStyles.borderColor, OPTS.borderColor, function (v) { setBoxStyle({ borderColor: v }); }),
                            boxStyles.borderColor === 'border-custom-color' ? textField('Custom Border Color', boxStyles.borderColorCustom, function (v) { setBoxStyle({ borderColorCustom: v }); }) : null,
                            selectField('Width', boxStyles.borderWidth, OPTS.borderWidth, function (v) { setBoxStyle({ borderWidth: v }); }),
                            boxStyles.borderWidth === 'custom' ? el(Fragment, null,
                                textField('Top Width', boxStyles.borderWidthTop, function (v) { setBoxStyle({ borderWidthTop: v }); }, { type: 'number' }),
                                textField('Right Width', boxStyles.borderWidthRight, function (v) { setBoxStyle({ borderWidthRight: v }); }, { type: 'number' }),
                                textField('Bottom Width', boxStyles.borderWidthBottom, function (v) { setBoxStyle({ borderWidthBottom: v }); }, { type: 'number' }),
                                textField('Left Width', boxStyles.borderWidthLeft, function (v) { setBoxStyle({ borderWidthLeft: v }); }, { type: 'number' })
                            ) : null
                        ) : null,

                        el('hr'),
                        textField('Custom Class', boxStyles.customClass, function (v) { setBoxStyle({ customClass: v }); })
                    ),
                    el(
                        PanelBody,
                        { title: 'Post Elements', initialOpen: false },
                        el(PostElementsRepeater, {
                            elements: elements,
                            onChange: function (v) { setAttributes({ elements: v }); }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon: 'grid-view',
                        label: 'Post Grid',
                        instructions: postType
                            ? (typeDef ? typeDef.label : postType) + ' — ' + (SOURCE_LABELS[source] || source)
                                + (isSlider ? ' (slider)' : ' (grid)')
                                + ', ' + elements.length + ' element' + (elements.length === 1 ? '' : 's')
                            : 'Select a post type from the block settings.'
                    })
                )
            );
        },

        // Rendered server-side: coptrz_render_post_grid_block() converts these
        // attributes into ____post_grid_module()'s expected input and calls it.
        save: function () { return null; }
    });

})(window.wp);
