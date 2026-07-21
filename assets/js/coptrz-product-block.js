/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product` block: a native editor equivalent of the
 * legacy section builder's Product item (`[product_add_to_cart id="…"
 * is_training="…"]`). save() returns null — rendered server-side by
 * coptrz_render_product_block() (includes/legacy-blocks.php).
 */
(function (wp) {

    const { registerBlockType }        = wp.blocks;
    const { createElement: el }        = wp.element;
    const { useBlockProps }            = wp.blockEditor;
    const { Placeholder }              = wp.components;

    registerBlockType('coptrz/product', {
        title:    'Product (Legacy)',
        icon:     'cart',
        category: 'design',
        description: 'Frozen legacy Product item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            productId:   { type: 'number', default: 0 },
            productName: { type: 'string', default: '' },
            isTraining:  { type: 'boolean', default: false }
        },

        edit: function (props) {
            const { attributes } = props;
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'cart',
                    label: 'Product (Legacy)',
                    instructions: attributes.productName
                        ? 'Product: ' + attributes.productName + (attributes.isTraining ? ' (training template)' : '')
                        : 'Select a product — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
