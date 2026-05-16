const { addFilter } = wp.hooks;
const { createHigherOrderComponent } = wp.compose;
const { Fragment } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;

/**
 * 1. Register the custom attribute in the block schema
 */
function addCustomAttributeToGroup(settings, name) {
    // Only apply this to the core/group block
    if (name !== 'core/group') {
        return settings;
    }

    // Append our new attribute
    settings.attributes = {
        ...settings.attributes,
        customDataId: {
            type: 'string',
            default: '',
        }
    };

    return settings;
}
addFilter('blocks.registerBlockType', 'digitally-disruptive/add-attr', addCustomAttributeToGroup);

/**
 * 2. Inject the UI Control into the Block Sidebar
 */
const addCustomAttributeUI = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        // Only apply to core/group
        if (props.name !== 'core/group') {
            return <BlockEdit {...props} />;
        }

        const { attributes, setAttributes } = props;

        return (
            <Fragment>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title="Advanced Custom Attributes" initialOpen={true}>
                        <TextControl
                            label="Custom Data ID"
                            help="Outputs as data-custom-id='[your-value]' on the frontend."
                            value={attributes.customDataId}
                            onChange={(val) => setAttributes({ customDataId: val })}
                        />
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
    };
}, 'addCustomAttributeUI');
addFilter('editor.BlockEdit', 'digitally-disruptive/add-attr-ui', addCustomAttributeUI);