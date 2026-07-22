/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product` block: native editor equivalent of the legacy
 * section builder's Product item (`[product_add_to_cart id="…"
 * is_training="…"]`). save() returns null — rendered server-side by
 * coptrz_render_product_block() (includes/legacy-blocks.php).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/product', {
        title:    'Product (Legacy)',
        icon:     'cart',
        category: 'design',
        description: 'A WooCommerce product embed — native equivalent of the section builder\'s Product item.',
        supports: { html: false, reusable: false },
        attributes: {
            productId:   { type: 'number', default: 0 },
            productName: { type: 'string', default: '' },
            isTraining:  { type: 'boolean', default: false }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            return el(
                'div',
                useBlockProps(),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Product Settings', initialOpen: true },
                        el(UI.SinglePostPicker, {
                            label: 'Product',
                            fetchPath: '/dd/v1/block-pickers?type=product',
                            value: a.productId ? { id: a.productId, title: a.productName } : null,
                            onChange: function (v) { setAttributes({ productId: v ? v.id : 0, productName: v ? v.title : '' }); }
                        }),
                        UI.boolField('Training Template', a.isTraining, function (v) { setAttributes({ isTraining: v }); })
                    )
                ),
                el(Placeholder, {
                    icon:  'cart',
                    label: 'Product (Legacy)',
                    instructions: a.productName
                        ? 'Product: ' + a.productName + (a.isTraining ? ' (training template)' : '')
                        : 'Select a product in the block settings.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
