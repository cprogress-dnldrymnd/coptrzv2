/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/layouts` block: a native Gutenberg equivalent of hand
 * typing `[layouts id="…"]` in a Shortcode block. The editor picks a published
 * `layouts` post from a dropdown; the `layouts` CPT is show_in_rest, so this
 * uses core's own `/wp/v2/layouts` route (no custom REST endpoint needed).
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_layouts_block() `render_block` filter in functions.php, which
 * rebuilds `[layouts id="…"]` from the stored `layoutId` attribute and runs
 * do_shortcode(). Building from the attribute (single source of truth) means
 * editing the referenced Layout post still updates every page that embeds it.
 */
(function (wp) {

    const { registerBlockType }                    = wp.blocks;
    const { createElement: el, useState, useEffect, Fragment } = wp.element;
    const { InspectorControls, useBlockProps }      = wp.blockEditor;
    const { PanelBody, SelectControl, Placeholder }  = wp.components;

    registerBlockType('coptrz/layouts', {
        title:    'Layout (Legacy)',
        icon:     'layout',
        category: 'design',
        description: 'Embeds a reusable Layout post (equivalent of the [layouts id="…"] shortcode).',
        attributes: {
            layoutId:    { type: 'number', default: 0 },
            layoutTitle: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { layoutId, layoutTitle } = attributes;

            const [layouts, setLayouts] = useState([]);
            const [loading, setLoading] = useState(true);

            useEffect(function () {
                wp.apiFetch({ path: '/wp/v2/layouts?per_page=100&status=publish&orderby=title&order=asc' })
                    .then(function (items) {
                        var options = [{ label: '— Select a layout —', value: 0, title: '' }].concat(
                            (items || []).map(function (l) {
                                return { label: l.title && l.title.rendered ? l.title.rendered : '(no title)', value: l.id, title: l.title ? l.title.rendered : '' };
                            })
                        );
                        setLayouts(options);
                        setLoading(false);
                    })
                    .catch(function () { setLoading(false); });
            }, []);

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Layout', initialOpen: true },
                        el(SelectControl, {
                            label:   'Layout',
                            value:   layoutId,
                            options: loading
                                ? [{ label: 'Loading…', value: 0 }]
                                : layouts.map(function (o) { return { label: o.label, value: o.value }; }),
                            onChange: function (val) {
                                var id    = parseInt(val, 10) || 0;
                                var match = layouts.filter(function (o) { return o.value === id; })[0];
                                setAttributes({ layoutId: id, layoutTitle: match ? match.title : '' });
                            }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon:  'layout',
                        label: 'Layout',
                        instructions: layoutId
                            ? 'Layout: ' + (layoutTitle || ('#' + layoutId))
                            : 'Select a Layout post from the block settings.'
                    })
                )
            );
        },

        // Rendered server-side: coptrz_render_layouts_block() rebuilds the
        // [layouts id="…"] shortcode from the layoutId attribute.
        save: function () { return null; }
    });

})(window.wp);
