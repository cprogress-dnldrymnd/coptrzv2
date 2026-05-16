/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects a Custom CSS control into Group, Row, Stack, and Grid blocks.
 * * Encapsulated in an IIFE to prevent global scope collisions.
 */
(function( wp ) {
    
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextareaControl } = wp.components;

    /**
     * 1. Register the custom CSS attribute
     */
    function addCustomCssAttribute( settings, name ) {
        // Target core/group, which encompasses Row, Stack, and Grid variations
        if ( name !== 'core/group' ) {
            return settings;
        }

        settings.attributes = Object.assign( settings.attributes || {}, {
            ddCustomCSS: { type: 'string', default: '' }
        });

        return settings;
    }
    addFilter( 'blocks.registerBlockType', 'digitally-disruptive/custom-css-attr', addCustomCssAttribute );

    /**
     * 2. Inject the Textarea UI into the Block Sidebar
     */
    const addCustomCssUI = createHigherOrderComponent( function( BlockEdit ) {
        return function( props ) {
            if ( props.name !== 'core/group' ) {
                return el( BlockEdit, props );
            }

            const attributes = props.attributes;
            const setAttributes = props.setAttributes;

            return el( Fragment, {},
                el( BlockEdit, props ),
                el( InspectorControls, {},
                    el( PanelBody, { title: 'Custom CSS', initialOpen: false },
                        el( TextareaControl, {
                            label: 'Scoped Block CSS',
                            help: 'Use "SELECTOR" to target this specific block wrapper. Example: SELECTOR { background: red; } SELECTOR h2 { color: blue; }',
                            value: attributes.ddCustomCSS,
                            onChange: function( val ) { setAttributes( { ddCustomCSS: val } ); },
                            rows: 10,
                            style: { fontFamily: 'monospace', fontSize: '12px' }
                        } )
                    )
                )
            );
        };
    }, 'addCustomCssUI' );

    addFilter( 'editor.BlockEdit', 'digitally-disruptive/custom-css-ui', addCustomCssUI );

})( window.wp );