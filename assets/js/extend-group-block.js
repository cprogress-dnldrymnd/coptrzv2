const { addFilter } = wp.hooks;
const { createHigherOrderComponent } = wp.compose;
const { Fragment, createElement: el } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;

/**
 * 1. Register the custom attribute in the block schema
 */
function addCustomAttributeToGroup( settings, name ) {
    // Only apply this to the core/group block
    if ( name !== 'core/group' ) {
        return settings;
    }

    // Append our new attribute
    settings.attributes = Object.assign( settings.attributes || {}, {
        customDataId: {
            type: 'string',
            default: '',
        }
    });

    return settings;
}
addFilter( 'blocks.registerBlockType', 'digitally-disruptive/add-attr', addCustomAttributeToGroup );

/**
 * 2. Inject the UI Control into the Block Sidebar (Vanilla JS approach)
 */
const addCustomAttributeUI = createHigherOrderComponent( function( BlockEdit ) {
    return function( props ) {
        // Only apply to core/group
        if ( props.name !== 'core/group' ) {
            return el( BlockEdit, props );
        }

        const attributes = props.attributes;
        const setAttributes = props.setAttributes;

        // Build the Inspector panel using pure JavaScript instead of JSX
        return el( Fragment, {},
            el( BlockEdit, props ),
            el( InspectorControls, {},
                el( PanelBody, { title: 'Advanced Custom Attributes', initialOpen: true },
                    el( TextControl, {
                        label: 'Custom Data ID',
                        help: "Outputs as data-custom-id='[your-value]' on the frontend.",
                        value: attributes.customDataId,
                        onChange: function( val ) { 
                            setAttributes( { customDataId: val } ); 
                        }
                    } )
                )
            )
        );
    };
}, 'addCustomAttributeUI' );

addFilter( 'editor.BlockEdit', 'digitally-disruptive/add-attr-ui', addCustomAttributeUI );