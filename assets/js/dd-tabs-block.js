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
    const { TextControl, PanelBody, ToggleControl, ColorPalette, BaseControl, SelectControl, RangeControl } = wp.components;

    /**
     * Registers the Child Block: Tab Panel
     * * Reverted to native Gutenberg Border supports using '__experimentalDefaultControls' to force UI visibility.
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'],
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true },
            // Force the native Border UI to display by default without needing to click the 3 dots
            __experimentalBorder: {
                color: true,
                radius: true,
                style: true,
                width: true,
                __experimentalDefaultControls: {
                    color: true,
                    radius: true,
                    style: true,
                    width: true
                }
            }
        },
        attributes: {
            tabTitle: { type: 'string', default: 'New Tab' }
        },
        
        /**
         * Renders the editor UI for the individual Tab Panel.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;
            
            // Native UI automatically maps padding, background, and border selections onto blockProps
            const blockProps = useBlockProps({ className: 'dd-tab-panel-edit' });

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
                        el(InnerBlocks, { template: [['core/paragraph', { placeholder: 'Enter tab content here...' }]] })
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
     * * Retains custom styling parameters for the JS-generated buttons, while enabling native supports for the parent wrapper.
     */
    registerBlockType('dd/tabs', {
        title: 'Advanced Tabs',
        icon: 'index-card',
        category: 'design',
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true, blockGap: true },
            __experimentalBorder: {
                color: true,
                radius: true,
                style: true,
                width: true,
                __experimentalDefaultControls: {
                    color: true,
                    radius: true,
                    style: true,
                    width: true
                }
            }
        },
        attributes: {
            mobileAccordion: { type: 'boolean', default: true },
            navAlignment: { type: 'string', default: 'flex-start' },
            btnPadding: { type: 'string', default: '10px 20px' },
            btnBorderRadius: { type: 'number', default: 0 },
            btnBorderWidth: { type: 'number', default: 0 },
            btnBorderColor: { type: 'string', default: 'transparent' },
            btnBorderStyle: { type: 'string', default: 'solid' },
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
            
            const cssVariables = {
                '--dd-btn-bg': attributes.btnBgColor || 'transparent',
                '--dd-btn-color': attributes.btnTextColor || 'inherit',
                '--dd-btn-active-bg': attributes.btnActiveBgColor || '#000000',
                '--dd-btn-active-color': attributes.btnActiveTextColor || '#ffffff',
                '--dd-nav-align': attributes.navAlignment,
                '--dd-btn-padding': attributes.btnPadding,
                '--dd-btn-radius': `${attributes.btnBorderRadius}px`,
                '--dd-btn-border-width': `${attributes.btnBorderWidth}px`,
                '--dd-btn-border-color': attributes.btnBorderColor,
                '--dd-btn-border-style': attributes.btnBorderStyle,
                border: '2px solid #007cba', 
                padding: '2px', 
                backgroundColor: '#f0f6fc'
            };

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
                    el(PanelBody, { title: 'Tab Button Styling (Dynamic)', initialOpen: false },
                        el('p', { style: { fontSize: '12px', fontStyle: 'italic', color: '#666' } }, 'These styles specifically target the dynamic buttons generated by JS. To style the parent wrapper, use the native Styles tab (half-moon icon).'),
                        el(SelectControl, {
                            label: 'Navigation Alignment',
                            value: attributes.navAlignment,
                            options: [
                                { label: 'Left', value: 'flex-start' },
                                { label: 'Center', value: 'center' },
                                { label: 'Right', value: 'flex-end' }
                            ],
                            onChange: function (val) { setAttributes({ navAlignment: val }); }
                        }),
                        el(TextControl, {
                            label: 'Button Padding',
                            value: attributes.btnPadding,
                            help: 'Standard CSS padding (e.g., 10px 20px)',
                            onChange: function (val) { setAttributes({ btnPadding: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Button Border Radius (px)',
                            value: attributes.btnBorderRadius,
                            min: 0,
                            max: 50,
                            onChange: function (val) { setAttributes({ btnBorderRadius: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Button Border Width (px)',
                            value: attributes.btnBorderWidth,
                            min: 0,
                            max: 10,
                            onChange: function (val) { setAttributes({ btnBorderWidth: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Button Border Style',
                            value: attributes.btnBorderStyle,
                            options: [
                                { label: 'Solid', value: 'solid' },
                                { label: 'Dashed', value: 'dashed' },
                                { label: 'Dotted', value: 'dotted' }
                            ],
                            onChange: function (val) { setAttributes({ btnBorderStyle: val }); }
                        }),
                        el(BaseControl, { label: 'Button Border Color' },
                            el(ColorPalette, { value: attributes.btnBorderColor, onChange: function (val) { setAttributes({ btnBorderColor: val }); } })
                        ),
                        el(BaseControl, { label: 'Default Background Color' },
                            el(ColorPalette, { value: attributes.btnBgColor, onChange: function (val) { setAttributes({ btnBgColor: val }); } })
                        ),
                        el(BaseControl, { label: 'Default Text Color' },
                            el(ColorPalette, { value: attributes.btnTextColor, onChange: function (val) { setAttributes({ btnTextColor: val }); } })
                        ),
                        el(BaseControl, { label: 'Active Background Color' },
                            el(ColorPalette, { value: attributes.btnActiveBgColor, onChange: function (val) { setAttributes({ btnActiveBgColor: val }); } })
                        ),
                        el(BaseControl, { label: 'Active Text Color' },
                            el(ColorPalette, { value: attributes.btnActiveTextColor, onChange: function (val) { setAttributes({ btnActiveTextColor: val }); } })
                        )
                    )
                ),
                el('div', blockProps,
                    el('div', { style: { padding: '10px', textTransform: 'uppercase', fontSize: '11px', color: '#007cba', fontWeight: 'bold' } }, 'Tabs Container (Navigation renders above dynamically on frontend)'),
                    el(InnerBlocks, {
                        allowedBlocks: ['dd/tab-panel'],
                        template: [['dd/tab-panel', { tabTitle: 'Tab 1' }], ['dd/tab-panel', { tabTitle: 'Tab 2' }]]
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
            const cssVariables = {
                '--dd-btn-bg': props.attributes.btnBgColor,
                '--dd-btn-color': props.attributes.btnTextColor,
                '--dd-btn-active-bg': props.attributes.btnActiveBgColor,
                '--dd-btn-active-color': props.attributes.btnActiveTextColor,
                '--dd-nav-align': props.attributes.navAlignment,
                '--dd-btn-padding': props.attributes.btnPadding,
                '--dd-btn-radius': `${props.attributes.btnBorderRadius}px`,
                '--dd-btn-border-width': `${props.attributes.btnBorderWidth}px`,
                '--dd-btn-border-color': props.attributes.btnBorderColor,
                '--dd-btn-border-style': props.attributes.btnBorderStyle
            };

            const blockProps = useBlockProps.save({
                className: 'dd-tabs-wrapper',
                'data-mobile-accordion': props.attributes.mobileAccordion ? 'true' : 'false',
                style: cssVariables
            });

            return el('div', blockProps, el(InnerBlocks.Content, null));
        }
    });

})(window.wp);