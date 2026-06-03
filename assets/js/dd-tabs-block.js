/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Registers the Parent Tabs block and Child Tab Panel block.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InnerBlocks } = wp.blockEditor;
    const { TextControl, PanelBody } = wp.components;
    const { InspectorControls } = wp.blockEditor;
    const { Fragment } = wp.element;

    /**
     * Registers the Child Block: Tab Panel
     * * This block is strictly restricted to only exist within the 'dd/tabs' parent block.
     * * It contains its own InnerBlocks to allow users to add paragraphs, images, etc.
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'], // Restrict execution to the Parent block
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

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Tab Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Tab Title',
                            value: attributes.tabTitle,
                            onChange: function (val) { setAttributes({ tabTitle: val }); }
                        })
                    )
                ),
                el('div', { className: 'dd-tab-panel-edit', style: { border: '1px solid #ddd', padding: '15px', marginBottom: '10px', backgroundColor: '#fff' } },
                    el('div', { className: 'dd-tab-panel-header', style: { fontWeight: 'bold', borderBottom: '1px solid #eee', paddingBottom: '10px', marginBottom: '10px' } }, 
                        'Tab: ' + attributes.tabTitle
                    ),
                    el(InnerBlocks, {
                        template: [['core/paragraph', { placeholder: 'Enter tab content here...' }]]
                    })
                )
            );
        },

        /**
         * Serializes the Tab Panel block to the database.
         * * Injects the tabTitle into a data attribute so the frontend JS can generate the navigation.
         * * @param {Object} props The block properties.
         * @return {Object}      The HTML markup saved to the database.
         */
        save: function (props) {
            return el('div', { 
                className: 'dd-tab-panel', 
                'data-tab-title': props.attributes.tabTitle 
            },
                el(InnerBlocks.Content, null)
            );
        }
    });

    /**
     * Registers the Parent Block: Tabs Container
     * * This block acts as the structural wrapper and dictates the allowed child blocks.
     */
    registerBlockType('dd/tabs', {
        title: 'Advanced Tabs',
        icon: 'index-card',
        category: 'design',
        
        /**
         * Renders the editor UI for the Parent Tabs block.
         * * @param {Object} props The block properties provided by Gutenberg.
         * @return {Object}      The functional React component for the editor.
         */
        edit: function (props) {
            return el('div', { className: 'dd-tabs-wrapper-edit', style: { border: '2px dashed #ccc', padding: '20px' } },
                el('div', { style: { marginBottom: '15px', textTransform: 'uppercase', fontSize: '12px', color: '#666', letterSpacing: '1px' } }, 'Advanced Tabs Container'),
                el(InnerBlocks, {
                    allowedBlocks: ['dd/tab-panel'],
                    template: [
                        ['dd/tab-panel', { tabTitle: 'Tab 1' }],
                        ['dd/tab-panel', { tabTitle: 'Tab 2' }]
                    ]
                })
            );
        },

        /**
         * Serializes the Parent Tabs block to the database.
         * * @param {Object} props The block properties.
         * @return {Object}      The HTML markup saved to the database.
         */
        save: function (props) {
            return el('div', { className: 'dd-tabs-wrapper' },
                el(InnerBlocks.Content, null)
            );
        }
    });

})(window.wp);