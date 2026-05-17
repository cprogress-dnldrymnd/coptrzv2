/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects a Custom CSS control with a Hybrid Live Editor Preview and Viewport Tabs.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextareaControl, TabPanel } = wp.components;

    const ALLOWED_BLOCKS = [
        'core/group', 
        'core/separator', 
        'core/image', 
        'core/heading', 
        'core/paragraph',
        'core/button'
    ];

    /**
     * 1. Register the custom CSS attributes
     */
    function addCustomCssAttribute(settings, name) {
        if (!ALLOWED_BLOCKS.includes(name)) {
            return settings;
        }

        settings.attributes = Object.assign(settings.attributes || {}, {
            ddCustomCSS:       { type: 'string', default: '' },
            ddCustomCSSTablet: { type: 'string', default: '' },
            ddCustomCSSMobile: { type: 'string', default: '' }
        });

        return settings;
    }
    addFilter('blocks.registerBlockType', 'digitally-disruptive/custom-css-attr', addCustomCssAttribute);

    /**
     * Helper: Reusable Hybrid Compiler for Live Preview
     */
    const compileHybridCSS = (rawCSS, clientId) => {
        if (!rawCSS) return '';
        const blockId = `#block-${clientId}`;
        
        // 1. Extract all 'SELECTOR { ... }' blocks.
        const advancedBlocks = rawCSS.match(/SELECTOR[^{]*{[^}]*}/g) || [];
        
        // 2. Isolate standalone properties by stripping the SELECTOR blocks.
        const baseProperties = rawCSS.replace(/SELECTOR[^{]*{[^}]*}/g, '').trim();
        
        let compiled = '';
        if (baseProperties) {
            compiled += `${blockId} { ${baseProperties} }\n`;
        }
        advancedBlocks.forEach(block => {
            compiled += block.replace(/SELECTOR/g, blockId) + '\n';
        });
        
        return compiled;
    };

    /**
     * 2. Inject the Tabbed UI and Hybrid Live Preview Styles
     */
    const addCustomCssUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (!ALLOWED_BLOCKS.includes(props.name)) {
                return el(BlockEdit, props);
            }

            const { attributes, setAttributes, clientId } = props;

            // Compile the Live Preview CSS combining all active breakpoints
            let livePreviewCSS = '';
            
            if (attributes.ddCustomCSS) {
                livePreviewCSS += compileHybridCSS(attributes.ddCustomCSS, clientId);
            }
            if (attributes.ddCustomCSSTablet) {
                livePreviewCSS += `@media (max-width: 991px) {\n${compileHybridCSS(attributes.ddCustomCSSTablet, clientId)}}\n`;
            }
            if (attributes.ddCustomCSSMobile) {
                livePreviewCSS += `@media (max-width: 767px) {\n${compileHybridCSS(attributes.ddCustomCSSMobile, clientId)}}\n`;
            }

            return el(Fragment, {},

                // Conditionally render the compiled style tag
                livePreviewCSS ? el('style', null, livePreviewCSS) : null,

                el(BlockEdit, props),

                el(InspectorControls, {},
                    el(PanelBody, { title: 'Custom CSS', initialOpen: false },
                        
                        // Implement Tabbed Interface for clean logical partitioning
                        el(TabPanel, {
                            className: 'dd-custom-css-tabs',
                            activeClass: 'is-active',
                            tabs: [
                                { name: 'desktop', title: 'Desktop', className: 'tab-desktop' },
                                { name: 'tablet', title: 'Tablet', className: 'tab-tablet' },
                                { name: 'mobile', title: 'Mobile', className: 'tab-mobile' }
                            ]
                        }, function (tab) {
                            
                            let attrName, labelTxt;
                            if (tab.name === 'tablet') {
                                attrName = 'ddCustomCSSTablet'; 
                                labelTxt = 'Tablet CSS (≤ 991px)';
                            } else if (tab.name === 'mobile') {
                                attrName = 'ddCustomCSSMobile'; 
                                labelTxt = 'Mobile CSS (≤ 767px)';
                            } else {
                                attrName = 'ddCustomCSS'; 
                                labelTxt = 'Desktop CSS (Base)';
                            }

                            return el(TextareaControl, {
                                label: labelTxt,
                                help: 'Hybrid Mode: Enter raw properties directly to style the wrapper, OR use "SELECTOR" to target inner elements (e.g., color: red; SELECTOR:hover { color: blue; }).',
                                value: attributes[attrName],
                                onChange: function (val) { setAttributes({ [attrName]: val }); },
                                rows: 12,
                                style: { fontFamily: 'monospace', fontSize: '12px', marginTop: '15px' }
                            });
                        })
                    )
                )
            );
        };
    }, 'addCustomCssUI');

    addFilter('editor.BlockEdit', 'digitally-disruptive/custom-css-ui', addCustomCssUI);

})(window.wp);