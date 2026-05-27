/**
 * Gutenberg Block Extension: FAQ Schema Toggle
 * Author: Digitally Disruptive - Donald Raymundo
 * Author URI: https://digitallydisruptive.co.uk/
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

const TARGET_BLOCK = 'core/accordion';

/**
 * Registers the 'enableFaqSchema' attribute to the target block.
 * * @param {Object} settings The block settings.
 * @param {string} name     The block name.
 * @return {Object}         The modified block settings.
 */
const addFaqSchemaAttribute = (settings, name) => {
    if (name !== TARGET_BLOCK) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            enableFaqSchema: {
                type: 'boolean',
                default: false,
            },
        },
    };
};
addFilter('blocks.registerBlockType', 'dd/add-faq-schema-attr', addFaqSchemaAttribute);

/**
 * Injects a toggle control into the block's Inspector settings sidebar.
 * * @param {Function} BlockEdit The original BlockEdit component.
 * @return {Function}          The wrapped component with the Inspector panel.
 */
const withFaqSchemaControl = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (props.name !== TARGET_BLOCK) {
            return <BlockEdit {...props} />;
        }

        const { attributes, setAttributes } = props;
        const { enableFaqSchema } = attributes;

        return (
            <>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title="Schema Architecture" initialOpen={true}>
                        <ToggleControl
                            label="Enable FAQ Schema"
                            help={
                                enableFaqSchema 
                                ? 'FAQ Schema will be dynamically generated for this accordion.' 
                                : 'Toggle to extract this block\'s content into FAQPage JSON-LD.'
                            }
                            checked={enableFaqSchema}
                            onChange={(value) => setAttributes({ enableFaqSchema: value })}
                        />
                    </PanelBody>
                </InspectorControls>
            </>
        );
    };
}, 'withFaqSchemaControl');
addFilter('editor.BlockEdit', 'dd/with-faq-schema-control', withFaqSchemaControl);