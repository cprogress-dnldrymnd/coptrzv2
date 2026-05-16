/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects a Custom CSS control with Live Editor Preview.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextareaControl } = wp.components;

    // Architectural whitelist expanded for Group, Separator, Image, Heading, and Paragraph blocks
    const ALLOWED_BLOCKS = [
        'core/group', 
        'core/separator', 
        'core/image', 
        'core/heading', 
        'core/paragraph'
    ];

    /**
     * 1. Register the custom CSS attribute
     */
    function addCustomCssAttribute(settings, name) {
        if (!ALLOWED_BLOCKS.includes(name)) {
            return settings;
        }

        settings.attributes = Object.assign(settings.attributes || {}, {
            ddCustomCSS: { type: 'string', default: '' }
        });

        return settings;
    }
    addFilter('blocks.registerBlockType', 'digitally-disruptive/custom-css-attr', addCustomCssAttribute);

    /**
     * 2. Inject the Textarea UI and Live Preview Styles
     */
    const addCustomCssUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            // Bail early if the block type is not whitelisted
            if (!ALLOWED_BLOCKS.includes(props.name)) {
                return el(BlockEdit, props);
            }

            // Extract necessary data from React props
            const { attributes, setAttributes, clientId } = props;

            /**
             * LIVE PREVIEW ARCHITECTURE:
             * Gutenberg wraps blocks in the editor with `id="block-{clientId}"`.
             * We automatically wrap the user's raw CSS properties inside this ID selector.
             */
            const livePreviewCSS = attributes.ddCustomCSS
                ? `#block-${clientId} { ${attributes.ddCustomCSS} }`
                : '';

            return el(Fragment, {},

                // 1. Inject the Live Preview Style Block (conditionally rendered)
                attributes.ddCustomCSS ? el('style', null, livePreviewCSS) : null,

                // 2. Render the Standard Block Canvas
                el(BlockEdit, props),

                // 3. Render the Sidebar Controls
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Custom CSS', initialOpen: false },
                        el(TextareaControl, {
                            label: 'Scoped Block CSS',
                            help: 'Enter CSS properties directly (e.g., border: 2px solid red; border-radius: 10px;). They will automatically be scoped and previewed live.',
                            value: attributes.ddCustomCSS,
                            onChange: function (val) { setAttributes({ ddCustomCSS: val }); },
                            rows: 10,
                            style: { fontFamily: 'monospace', fontSize: '12px' }
                        })
                    )
                )
            );
        };
    }, 'addCustomCssUI');

    addFilter('editor.BlockEdit', 'digitally-disruptive/custom-css-ui', addCustomCssUI);

})(window.wp);