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
    const { TextControl, PanelBody, ToggleControl, ColorPalette, BaseControl, SelectControl } = wp.components;

    /**
     * Registers the Child Block: Tab Panel
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'],
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true },
            __experimentalBorder: {
                color: true,
                radius: true,
                style: true,
                width: true,
                __experimentalDefaultControls: { color: true, radius: true, style: true, width: true }
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
     * * Added conditional breakpoint attribute mapping.
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
                __experimentalDefaultControls: { color: true, radius: true, style: true, width: true }
            }
        },
        attributes: {
            mobileAccordion: { type: 'boolean', default: true },
            accordionBreakpoint: { type: 'string', default: '767' }, // New attribute for user-selected breakpoint
            navAlignment: { type: 'string', default: 'flex-start' },
            btnPadding: { type: 'string', default: '10px 20px' },
            btnBorderRadius: { type: 'string', default: '4px' },
            btnBorderWidth: { type: 'string', default: '0px' },
            btnBorderColor: { type: 'string', default: 'transparent' },
            btnBorderStyle: { type: 'string', default: 'solid' },
            btnBgColor: { type: 'string' },
            btnTextColor: { type: 'string' },
            btnActiveBgColor: { type: 'string' },
            btnActiveTextColor: { type: 'string' },
            btnFontSize: { type: 'string', default: '16px' },
            btnFontWeight: { type: 'string', default: 'normal' },
            // Opt-in layout switch. 'horizontal' preserves the original tabs behaviour
            // so existing blocks (which lack this attribute) are unaffected.
            layoutStyle: { type: 'string', default: 'horizontal' },
            // Highlight colour for the active panel in the stacked layout.
            stackedAccentColor: { type: 'string', default: '#6c47ff' }
        },
        
        /**
         * Renders the editor UI for the Parent Tabs block.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;
            
            const safeRadius = String(attributes.btnBorderRadius).includes('px') || String(attributes.btnBorderRadius).includes(' ') ? attributes.btnBorderRadius : `${attributes.btnBorderRadius}px`;
            const safeWidth = String(attributes.btnBorderWidth).includes('px') || String(attributes.btnBorderWidth).includes(' ') ? attributes.btnBorderWidth : `${attributes.btnBorderWidth}px`;

            const cssVariables = {
                '--dd-btn-bg': attributes.btnBgColor || 'transparent',
                '--dd-btn-color': attributes.btnTextColor || 'inherit',
                '--dd-btn-active-bg': attributes.btnActiveBgColor || '#000000',
                '--dd-btn-active-color': attributes.btnActiveTextColor || '#ffffff',
                '--dd-nav-align': attributes.navAlignment,
                '--dd-btn-padding': attributes.btnPadding,
                '--dd-btn-radius': safeRadius,
                '--dd-btn-border-width': safeWidth,
                '--dd-btn-border-color': attributes.btnBorderColor,
                '--dd-btn-border-style': attributes.btnBorderStyle,
                '--dd-btn-font-size': attributes.btnFontSize,
                '--dd-btn-font-weight': attributes.btnFontWeight,
                '--dd-stacked-accent': attributes.stackedAccentColor || '#6c47ff',
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
                    el(PanelBody, { title: 'Layout', initialOpen: true },
                        el(SelectControl, {
                            label: 'Layout Style',
                            value: attributes.layoutStyle,
                            options: [
                                { label: 'Horizontal Tabs (Default)', value: 'horizontal' },
                                { label: 'Vertical Stacked (Inline Content)', value: 'stacked' }
                            ],
                            help: 'Stacked: titles list vertically and the active title reveals its content inline beneath it.',
                            onChange: function (val) { setAttributes({ layoutStyle: val }); }
                        }),
                        attributes.layoutStyle === 'stacked' && el(BaseControl, { label: 'Active Highlight Color' },
                            el(ColorPalette, { value: attributes.stackedAccentColor, onChange: function (val) { setAttributes({ stackedAccentColor: val }); } })
                        )
                    ),
                    el(PanelBody, { title: 'Responsive Settings', initialOpen: false },
                        el(ToggleControl, {
                            label: 'Enable Accordion Conversion',
                            checked: attributes.mobileAccordion,
                            onChange: function (val) { setAttributes({ mobileAccordion: val }); }
                        }),
                        // Conditionally render the breakpoint selector only if the accordion is enabled
                        attributes.mobileAccordion && el(SelectControl, {
                            label: 'Trigger Breakpoint',
                            value: attributes.accordionBreakpoint,
                            options: [
                                { label: 'Mobile (≤ 767px)', value: '767' },
                                { label: 'Tablet (≤ 991px)', value: '991' }
                            ],
                            onChange: function (val) { setAttributes({ accordionBreakpoint: val }); }
                        })
                    ),
                    el(PanelBody, { title: 'Tab Button Styling (Dynamic)', initialOpen: false },
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
                            label: 'Font Size',
                            value: attributes.btnFontSize,
                            help: 'e.g., 16px, 1.2rem, 1em',
                            onChange: function (val) { setAttributes({ btnFontSize: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Font Weight',
                            value: attributes.btnFontWeight,
                            options: [
                                { label: 'Normal (400)', value: 'normal' },
                                { label: 'Medium (500)', value: '500' },
                                { label: 'Semi-Bold (600)', value: '600' },
                                { label: 'Bold (700)', value: 'bold' },
                                { label: 'Extra Bold (800)', value: '800' }
                            ],
                            onChange: function (val) { setAttributes({ btnFontWeight: val }); }
                        }),
                        el(TextControl, {
                            label: 'Button Padding',
                            value: attributes.btnPadding,
                            help: 'Top Right Bottom Left (e.g., 10px 20px 10px 20px)',
                            onChange: function (val) { setAttributes({ btnPadding: val }); }
                        }),
                        el(TextControl, {
                            label: 'Button Border Radius',
                            value: attributes.btnBorderRadius,
                            help: 'Top-Left Top-Right Bottom-Right Bottom-Left (e.g., 10px 10px 0 0)',
                            onChange: function (val) { setAttributes({ btnBorderRadius: val }); }
                        }),
                        el(TextControl, {
                            label: 'Button Border Width',
                            value: attributes.btnBorderWidth,
                            help: 'Top Right Bottom Left (e.g., 0 0 3px 0 for bottom line only)',
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
                    el('div', { style: { padding: '10px', textTransform: 'uppercase', fontSize: '11px', color: '#007cba', fontWeight: 'bold' } }, attributes.layoutStyle === 'stacked' ? 'Tabs Container — Vertical Stacked layout (titles + inline content render dynamically on frontend)' : 'Tabs Container (Navigation renders above dynamically on frontend)'),
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
            const safeRadius = String(props.attributes.btnBorderRadius).includes('px') || String(props.attributes.btnBorderRadius).includes(' ') ? props.attributes.btnBorderRadius : `${props.attributes.btnBorderRadius}px`;
            const safeWidth = String(props.attributes.btnBorderWidth).includes('px') || String(props.attributes.btnBorderWidth).includes(' ') ? props.attributes.btnBorderWidth : `${props.attributes.btnBorderWidth}px`;

            const cssVariables = {
                '--dd-btn-bg': props.attributes.btnBgColor,
                '--dd-btn-color': props.attributes.btnTextColor,
                '--dd-btn-active-bg': props.attributes.btnActiveBgColor,
                '--dd-btn-active-color': props.attributes.btnActiveTextColor,
                '--dd-nav-align': props.attributes.navAlignment,
                '--dd-btn-padding': props.attributes.btnPadding,
                '--dd-btn-radius': safeRadius,
                '--dd-btn-border-width': safeWidth,
                '--dd-btn-border-color': props.attributes.btnBorderColor,
                '--dd-btn-border-style': props.attributes.btnBorderStyle,
                '--dd-btn-font-size': props.attributes.btnFontSize,
                '--dd-btn-font-weight': props.attributes.btnFontWeight
            };

            const isStacked = props.attributes.layoutStyle === 'stacked';

            const wrapAttrs = {
                className: 'dd-tabs-wrapper',
                'data-mobile-accordion': props.attributes.mobileAccordion ? 'true' : 'false',
                'data-accordion-breakpoint': props.attributes.mobileAccordion ? props.attributes.accordionBreakpoint : 'none',
                style: cssVariables
            };

            // Only emit the stacked-specific markup when opted-in, so existing
            // (horizontal) blocks serialize identically and stay valid.
            if (isStacked) {
                cssVariables['--dd-stacked-accent'] = props.attributes.stackedAccentColor || '#6c47ff';
                wrapAttrs['data-layout'] = 'stacked';
                // Stacked always renders inline at every width — bypass the
                // breakpoint-driven accordion conversion rules entirely.
                wrapAttrs['data-mobile-accordion'] = 'false';
                wrapAttrs['data-accordion-breakpoint'] = 'none';
            }

            const blockProps = useBlockProps.save(wrapAttrs);

            return el('div', blockProps, el(InnerBlocks.Content, null));
        }
    });

})(window.wp);