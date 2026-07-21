/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product-slider` (Legacy) block: a native editor
 * placeholder for the legacy section builder's Product Slider item. save()
 * returns null — rendered server-side by coptrz_render_product_slider_block()
 * (includes/legacy-blocks.php), which rebuilds the WP_Query args from the
 * stored source fields and calls __linked_products() (includes/woocommerce.php)
 * directly — the same function the legacy [product_slider] shortcode delegates
 * to. Source fields (not the resolved query) are stored so a "Main Query"
 * source keeps resolving from the live request, matching legacy behaviour.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/product-slider', {
        title:    'Product Slider (Legacy)',
        icon:     'slides',
        category: 'design',
        description: 'Frozen legacy Product Slider item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            sourceType:  { type: 'string', default: '' },
            categoryIds: { type: 'array', default: [] },
            brandIds:    { type: 'array', default: [] },
            productIds:  { type: 'array', default: [] },
            numberposts: { type: 'string', default: '' },
            heading:     { type: 'string', default: '' },
            buttonText:  { type: 'string', default: '' },
            buttonUrl:   { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes } = props;
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'slides',
                    label: 'Product Slider (Legacy)',
                    instructions: attributes.heading
                        ? 'Heading: ' + attributes.heading
                        : 'Legacy product slider — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
