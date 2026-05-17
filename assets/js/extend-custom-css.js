/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects a Custom CSS control with a Hybrid Live Editor Preview.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextareaControl } = wp.components;

    const ALLOWED_BLOCKS = [
        'core/group', 
        'core/separator', 
        'core/image', 
        'core/heading', 
        'core/paragraph',
        'core/button'
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
     * 2. Inject the Textarea UI and Hybrid Live Preview Styles
     */
    const addCustomCssUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (!ALLOWED_BLOCKS.includes(props.name)) {
                return el(BlockEdit, props);
            }

            const { attributes, setAttributes, clientId } = props;

            /**
             * HYBRID COMPILER ARCHITECTURE:
             * 1. Extract all 'SELECTOR { ... }' blocks.
             * 2. Isolate standalone properties by stripping the SELECTOR blocks.
             * 3. Compile both formats securely into the live preview.
             */
            let livePreviewCSS = '';

            if ( attributes.ddCustomCSS ) {
                const rawCSS = attributes.ddCustomCSS;
                const blockId = `#block-${clientId}`;

                // Find all advanced rules utilizing the SELECTOR keyword
                const advancedBlocks = rawCSS.match(/SELECTOR[^{]*{[^}]*}/g) || [];
                
                // Remove the advanced rules to isolate the raw wrapper properties
                const baseProperties = rawCSS.replace(/SELECTOR[^{]*{[^}]*}/g, '').trim();

                // 1. Process Raw Properties (Wrapping Model)
                if ( baseProperties ) {
                    livePreviewCSS += `${blockId} { ${baseProperties} }\n`;
                }

                // 2. Process Advanced Blocks (Search & Replace Model)
                advancedBlocks.forEach( block => {
                    livePreviewCSS += block.replace(/SELECTOR/g, blockId) + '\n';
                });
            }

            return el(Fragment, {},

                // Conditionally render the compiled style tag
                attributes.ddCustomCSS ? el('style', null, livePreviewCSS) : null,

                el(BlockEdit, props),

                el(InspectorControls, {},
                    el(PanelBody, { title: 'Custom CSS', initialOpen: false },
                        el(TextareaControl, {
                            label: 'Scoped Block CSS',
                            help: 'Hybrid Mode: Enter raw properties directly to style the wrapper, OR use "SELECTOR" to target inner elements (e.g., color: red; SELECTOR:hover { color: blue; }).',
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