/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Registers the Parent Tabs block and Child Tab Panel block.
 */
(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InnerBlocks, InspectorControls, useBlockProps } = wp.blockEditor;
    const { TextControl, PanelBody, ToggleControl } = wp.components;

    /**
     * Child Block: Tab Panel
     */
    registerBlockType('dd/tab-panel', {
        title: 'Tab Panel',
        icon: 'feedback',
        category: 'design',
        parent: ['dd/tabs'],
        supports: {
            color: { background: true, text: true },
            spacing: { padding: true, margin: true }
        },
        attributes: {
            tabTitle: { type: 'string', default: 'New Tab' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;

            // Editor UI Box - Styling applies directly here
            const blockProps = useBlockProps({
                className: 'dd-tab-panel-edit',
                style: {
                    position: 'relative',
                    border: '1px solid #ddd',
                    padding: '35px 15px 15px', // Top padding reserves space for the UI badge 
                    marginBottom: '15px'
                }
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
                    // Floating Badge UI - Keeps the editor clean without breaking block props
                    el('span', {
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            background: '#f0f0f0',
                            color: '#333',
                            padding: '4px 12px',
                            fontSize: '11px',
                            fontWeight: '600',
                            borderBottomRightRadius: '4px',
                            borderRight: '1px solid #ddd',
                            borderBottom: '1px solid #ddd',
                            userSelect: 'none'
                        }
                    }, attributes.tabTitle),

                    el(InnerBlocks, {
                        template: [['core/paragraph', { placeholder: 'Enter tab content here...' }]]
                    })
                )
            );
        },

        save: function (props) {
            const blockProps = useBlockProps.save({
                className: 'dd-tab-panel',
                'data-tab-title': props.attributes.tabTitle
            });
            // Direct rendering of InnerBlocks.Content prevents frontend DOM invalidation
            return el('div', blockProps, el(InnerBlocks.Content));
        }
    });

    /**
     * Parent Block: Tabs Container
     */
    registerBlockType('dd/tabs', {
        title: 'Advanced Tabs',
        icon: 'index-card',
        category: 'design',
        attributes: {
            mobileAccordion: { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const blockProps = useBlockProps({
                className: 'dd-tabs-wrapper-edit',
                style: { border: '2px dashed #bbb', padding: '15px' }
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
                    el('div', { style: { marginBottom: '15px', fontSize: '11px', fontWeight: 'bold', color: '#666', textTransform: 'uppercase', letterSpacing: '0.5px' } }, 'Advanced Tabs Container'),
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

        save: function (props) {
            const blockProps = useBlockProps.save({
                className: 'dd-tabs-wrapper',
                'data-mobile-accordion': props.attributes.mobileAccordion ? 'true' : 'false'
            });
            return el('div', blockProps, el(InnerBlocks.Content));
        }
    });

})(window.wp);