/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/header-cta` block: a native Gutenberg equivalent of
 * the two theme-option header CTA buttons rendered in
 * template-parts/header/header-right.php ("Header Button" / "Header Button 2",
 * both configured under Theme Options > Header). The button content/style/
 * target themselves are NOT block attributes — they stay theme-option-driven,
 * only whether each renders is per-block.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_header_cta_block() `render_block` filter in
 * includes/header-blocks.php, which calls the shared coptrz_header_cta_html()
 * — the SAME function header-right.php itself calls.
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el, Fragment }     = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, Placeholder } = wp.components;

    registerBlockType('coptrz/header-cta', {
        title:    'Header — CTA Buttons',
        icon:     'button',
        category: 'design',
        description: 'Embeds the theme-option header CTA button(s).',
        attributes: {
            showButtonOne: { type: 'boolean', default: true },
            showButtonTwo: { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { showButtonOne, showButtonTwo } = attributes;

            var enabled = [];
            if (showButtonTwo) { enabled.push('Header Button 2'); }
            if (showButtonOne) { enabled.push('Header Button'); }

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'CTA Buttons', initialOpen: true },
                        el(ToggleControl, {
                            label: 'Header Button',
                            checked: !!showButtonOne,
                            help: 'Theme Options > Header > Header Button.',
                            onChange: function (val) { setAttributes({ showButtonOne: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Header Button 2',
                            checked: !!showButtonTwo,
                            help: 'Theme Options > Header > Header Button 2.',
                            onChange: function (val) { setAttributes({ showButtonTwo: val }); }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon:  'button',
                        label: 'Header CTA Buttons',
                        instructions: enabled.length ? ('Showing: ' + enabled.join(', ')) : 'No buttons enabled — select at least one in the block settings.'
                    })
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
