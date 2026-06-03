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
    const { TextControl, PanelBody, ToggleControl } = wp.components;

    /**
     * Registers the Child Block: Tab Panel
     * * Added native 'supports' to allow users to modify backgrounds, padding, and margins natively.
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'], // Strict Parent-Child relationship
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true }
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

            // useBlockProps binds the native Gutenberg layout controls (padding, colors) to this wrapper element.
            const blockProps = useBlockProps({
                className: 'dd-tab-panel-edit',
                style: { border: '1px dashed #ccc', marginBottom: '10px' }
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
                    el('div', { className: 'dd-tab-panel-header', style: { fontWeight: 'bold', borderBottom: '1px solid #eee', padding: '10px', backgroundColor: '#f9f9f9' } }, 
                        'Tab Content: ' + attributes.tabTitle
                    ),
                    el('div', { style: { padding: '15px' } },
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
            // useBlockProps.save() ensures all custom styles (padding, background) are exported to the frontend HTML.
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
     * * Includes the Mobile Accordion toggle to alter frontend logic.
     */
    registerBlockType('dd/tabs', {
        title: 'Advanced Tabs',
        icon: 'index-card',
        category: 'design',
        attributes: {
            mobileAccordion: {
                type: 'boolean',
                default: true
            }
        },
        
        /**
         * Renders the editor UI for the Parent Tabs block.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps({
                className: 'dd-tabs-wrapper-edit',
                style: { border: '2px solid #007cba', padding: '2px', backgroundColor: '#f0f6fc' }
            });

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Responsive Settings', initialOpen: true },
                        el(ToggleControl, {
                            label: 'Convert to Accordion on Mobile (≤ 767px)',
                            checked: attributes.mobileAccordion,
                            onChange: function (val) { setAttributes({ mobileAccordion: val }); }
                        })
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
            const blockProps = useBlockProps.save({
                className: 'dd-tabs-wrapper',
                'data-mobile-accordion': props.attributes.mobileAccordion ? 'true' : 'false'
            });

            return el('div', blockProps,
                el(InnerBlocks.Content, null)
            );
        }
    });

})(window.wp);