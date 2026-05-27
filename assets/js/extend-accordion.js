(function(wp) {
    // Destructure native WordPress dependencies
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    // UPDATE THIS to match the exact output from your console check!
    const TARGET_BLOCK = 'core/details'; 

    /**
     * Registers the 'enableFaqSchema' attribute
     */
    const addFaqSchemaAttribute = (settings, name) => {
        if (name !== TARGET_BLOCK) return settings;
        
        return Object.assign({}, settings, {
            attributes: Object.assign({}, settings.attributes, {
                enableFaqSchema: { type: 'boolean', default: false }
            })
        });
    };
    addFilter('blocks.registerBlockType', 'dd/add-faq-schema-attr', addFaqSchemaAttribute);

    /**
     * Injects the toggle control into the Inspector sidebar
     */
    const withFaqSchemaControl = createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            if (props.name !== TARGET_BLOCK) {
                return createElement(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;
            const { enableFaqSchema } = attributes;

            // Build the React elements natively
            return createElement(
                Fragment,
                null,
                createElement(BlockEdit, props),
                createElement(
                    InspectorControls,
                    null,
                    createElement(
                        PanelBody,
                        { title: 'Schema Architecture', initialOpen: true },
                        createElement(ToggleControl, {
                            label: 'Enable FAQ Schema',
                            help: enableFaqSchema ? 'FAQ Schema will be dynamically generated.' : 'Toggle to extract this block\'s content into FAQPage JSON-LD.',
                            checked: enableFaqSchema,
                            onChange: (value) => setAttributes({ enableFaqSchema: value })
                        })
                    )
                )
            );
        };
    }, 'withFaqSchemaControl');
    addFilter('editor.BlockEdit', 'dd/with-faq-schema-control', withFaqSchemaControl);
})(window.wp);