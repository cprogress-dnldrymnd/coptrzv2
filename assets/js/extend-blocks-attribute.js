/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects custom data attributes into specified core Gutenberg blocks.
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;

    /**
     * Defines the specific core blocks permitted to receive the custom attributes.
     * Note: 'core/group' natively handles Row and Stack variations.
     * * @type {string[]}
     */
    const ALLOWED_BLOCKS = [
        'core/list',
        'core/group',
        'core/heading',
        'core/paragraph',
        'core/image',
        'core/button'
    ];

    /**
     * Registers the Custom Attribute properties into the block schema.
     * * This guarantees WordPress correctly serializes the values in the database and prevents block recovery/invalidation errors.
     * * @param {Object} settings The original block settings object.
     * @param {string} name     The namespace and name of the block being processed.
     * @return {Object}         The modified block settings object containing the newly injected attributes.
     */
    function addCustomDataAttributes(settings, name) {
        if (!ALLOWED_BLOCKS.includes(name)) {
            return settings;
        }

        settings.attributes = Object.assign(settings.attributes || {}, {
            customDataId:   { type: 'string', default: '' },
            customDataRole: { type: 'string', default: '' }
        });

        return settings;
    }
    addFilter('blocks.registerBlockType', 'digitally-disruptive/custom-data-attr', addCustomDataAttributes);

    /**
     * Injects the Inspector Controls UI into the block editor sidebar.
     * * Wraps the original BlockEdit component in a Higher-Order Component to render native TextControls mapped to our custom attributes.
     * * @param {Function} BlockEdit The original block edit component passed by the Gutenberg editor.
     * @return {Function}          The wrapped functional component containing the injected settings panel.
     */
    const withCustomDataUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (!ALLOWED_BLOCKS.includes(props.name)) {
                return el(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;

            return el(Fragment, {},
                el(BlockEdit, props),
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Advanced Custom Attributes', initialOpen: false },
                        el(TextControl, {
                            label: 'Custom Data ID',
                            help: 'Outputs directly to DOM as data-custom-id="value"',
                            value: attributes.customDataId,
                            onChange: function (val) { setAttributes({ customDataId: val }); }
                        }),
                        el(TextControl, {
                            label: 'Custom Data Role',
                            help: 'Outputs directly to DOM as data-custom-role="value"',
                            value: attributes.customDataRole,
                            onChange: function (val) { setAttributes({ customDataRole: val }); }
                        })
                    )
                )
            );
        };
    }, 'withCustomDataUI');
    addFilter('editor.BlockEdit', 'digitally-disruptive/custom-data-ui', withCustomDataUI);

    /**
     * Modifies the root properties of the block upon database serialization.
     * * This maps the internal React attributes to the actual physical data-* DOM attributes rendered on the frontend.
     * * @param {Object} extraProps The original wrapper properties of the element being saved.
     * @param {Object} blockType  The block type configuration and metadata.
     * @param {Object} attributes The current active attributes of the block.
     * @return {Object}           The modified properties object containing the attached physical data-* attributes.
     */
    function applyCustomAttributesOnSave(extraProps, blockType, attributes) {
        if (!ALLOWED_BLOCKS.includes(blockType.name)) {
            return extraProps;
        }

        if (attributes.customDataId) {
            extraProps['data-custom-id'] = attributes.customDataId;
        }

        if (attributes.customDataRole) {
            extraProps['data-custom-role'] = attributes.customDataRole;
        }

        return extraProps;
    }
    addFilter('blocks.getSaveContent.extraProps', 'digitally-disruptive/apply-custom-attributes-save', applyCustomAttributesOnSave);

})(window.wp);