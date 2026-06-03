/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Registers the Parent Tabs block and Child Tab Panel block with Advanced Layout Supports.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InnerBlocks, InspectorControls, useBlockProps } = wp.blockEditor;
    const { TextControl, PanelBody, ToggleControl, ColorPalette, BaseControl } = wp.components;

    /**
     * Registers the Child Block: Tab Panel
     * * Added native 'supports' to allow users to modify backgrounds, padding, margins, and borders.
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'], // Strict Parent-Child relationship
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true },
            border: { color: true, radius: true, style: true, width: true }
        },
        attributes: {
            tabTitle: { 
                type: 'string', 
                default: 'New Tab' 
            }
        },
        
        /**
         * Renders the editor UI for the individual Tab Panel.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;

            // useBlockProps MUST remain on the root element to prevent Gutenberg from double-injecting styles.
            const blockProps = useBlockProps({
                className: 'dd-tab-panel-edit',
            });

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Tab Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Tab Navigation Title',
                            value: attributes.tabTitle,
                            onChange: function (val) { setAttributes({ tabTitle: val }); }
                        })
                    )
                ),
                el('div', blockProps,
                    el('div', { className: 'dd-tab-panel-header', style: { fontWeight: 'bold', borderBottom: '1px solid #eee', padding: '10px', backgroundColor: '#f9f9f9', marginBottom: '15px' } }, 
                        'Tab Content: ' + attributes.tabTitle
                    ),
                    el('div', { className: 'dd-tab-panel-inner' },
                        el(InnerBlocks, {
                            template: [['core/paragraph', { placeholder: 'Enter tab content here...' }]]
                        })
                    )
                )
            );
        },

        /**
         * Serializes the Tab Panel block to the database.
         * * @param {Object} props The block properties.
         * @return {Object}      The HTML markup saved to the database.
         */
        save: function (props) {
            const blockProps = useBlockProps.save({
                className: 'dd-tab-panel',
                'data-tab-title': props.attributes.tabTitle
            });

            return el('div', blockProps,
                el('div', { className: 'dd-tab-panel-inner' },
                    el(InnerBlocks.Content, null)
                )
            );
        }
    });

    /**
     * Registers the Parent Block: Tabs Container
     * * Includes styling attributes passed down via CSS variables to target dynamically generated JS buttons.
     * * Added native layout supports for the parent container (colors, spacing, borders).
     */
    registerBlockType('dd/tabs', {
        title: 'Advanced Tabs',
        icon: 'index-card',
        category: 'design',
        // Enable standard Gutenberg styling panel for the parent wrapper
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true, blockGap: true },
            border: { color: true, radius: true, style: true, width: true }
        },
        attributes: {
            mobileAccordion: {
                type: 'boolean',
                default: true
            },
            btnBgColor: { type: 'string' },
            btnTextColor: { type: 'string' },
            btnActiveBgColor: { type: 'string' },
            btnActiveTextColor: { type: 'string' }
        },
        
        /**
         * Renders the editor UI for the Parent Tabs block.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;
            
            // Construct CSS variables based on selected attributes
            const cssVariables = {
                '--dd-btn-bg': attributes.btnBgColor || 'transparent',
                '--dd-btn-color': attributes.btnTextColor || 'inherit',
                '--dd-btn-active-bg': attributes.btnActiveBgColor || '#000000',
                '--dd-btn-active-color': attributes.btnActiveTextColor || '#ffffff',
                border: '2px solid #007cba', 
                padding: '2px', 
                backgroundColor: '#f0f6fc'
            };

            // Gutenberg automatically maps selected native supports (padding, borders) onto this object
            const blockProps = useBlockProps({
                className: 'dd-tabs-wrapper-edit',
                style: cssVariables
            });

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Responsive Settings', initialOpen: true },
                        el(ToggleControl, {
                            label: 'Convert to Accordion on Mobile (≤ 767px)',
                            checked: attributes.mobileAccordion,
                            onChange: function (val) { setAttributes({ mobileAccordion: val }); }
                        })
                    ),
                    el(PanelBody, { title: 'Tab Button Styling', initialOpen: false },
                        el(BaseControl, { label: 'Default Background Color' },
                            el(ColorPalette, {
                                value: attributes.btnBgColor,
                                onChange: function (val) { setAttributes({ btnBgColor: val }); }
                            })
                        ),
                        el(BaseControl, { label: 'Default Text Color' },
                            el(ColorPalette, {
                                value: attributes.btnTextColor,
                                onChange: function (val) { setAttributes({ btnTextColor: val }); }
                            })
                        ),
                        el(BaseControl, { label: 'Active Background Color' },
                            el(ColorPalette, {
                                value: attributes.btnActiveBgColor,
                                onChange: function (val) { setAttributes({ btnActiveBgColor: val }); }
                            })
                        ),
                        el(BaseControl, { label: 'Active Text Color' },
                            el(ColorPalette, {
                                value: attributes.btnActiveTextColor,
                                onChange: function (val) { setAttributes({ btnActiveTextColor: val }); }
                            })
                        )
                    )
                ),
                el('div', blockProps,
                    el('div', { style: { padding: '10px', textTransform: 'uppercase', fontSize: '11px', color: '#007cba', fontWeight: 'bold' } }, 'Tabs Container (Navigation renders above dynamically on frontend)'),
                    el(InnerBlocks, {
                        allowedBlocks: ['dd/tab-panel'],
                        template: [
                            ['dd/tab-panel', { tabTitle: 'Tab 1' }],
                            ['dd/tab-panel', { tabTitle: 'Tab 2' }]
                        ]
                    })
                )
            );
        },

        /**
         * Serializes the Parent Tabs block to the database.
         * * @param {Object} props The block properties.
         * @return {Object}      The HTML markup saved to the database.
         */
        save: function (props) {
            // Apply CSS variables alongside any native supports the user configures in the sidebar
            const cssVariables = {
                '--dd-btn-bg': props.attributes.btnBgColor,
                '--dd-btn-color': props.attributes.btnTextColor,
                '--dd-btn-active-bg': props.attributes.btnActiveBgColor,
                '--dd-btn-active-color': props.attributes.btnActiveTextColor,
            };

            const blockProps = useBlockProps.save({
                className: 'dd-tabs-wrapper',
                'data-mobile-accordion': props.attributes.mobileAccordion ? 'true' : 'false',
                style: cssVariables
            });

            return el('div', blockProps,
                el(InnerBlocks.Content, null)
            );
        }
    });

})(window.wp);