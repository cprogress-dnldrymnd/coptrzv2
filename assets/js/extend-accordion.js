(function(wp) {

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl } = wp.components;
    const { createElement, Fragment } = wp.element;

    const TARGET_BLOCK = 'core/accordion'; 

    const addFaqSchemaAttribute = (settings, name) => {
        if (name !== TARGET_BLOCK) return settings;
        
        return Object.assign({}, settings, {
            attributes: Object.assign({}, settings.attributes, {
                enableFaqSchema: { type: 'boolean', default: false }
            })
        });
    };
    addFilter('blocks.registerBlockType', 'dd/add-faq-schema-attr', addFaqSchemaAttribute);

    const withFaqSchemaControl = createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            if (props.name !== TARGET_BLOCK) {
                return createElement(BlockEdit, props);
            }

            const { attributes, setAttributes } = props;
            const { enableFaqSchema } = attributes;

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