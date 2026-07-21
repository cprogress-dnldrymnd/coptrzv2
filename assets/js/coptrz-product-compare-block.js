/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product-compare` block: a native editor equivalent of
 * the legacy section builder's Product Compare item (`[product_compare
 * id="…"]`). Replaces the section-converter's previous bare core/shortcode
 * mapping for this item so it carries a readable editor label instead of raw
 * shortcode text. save() returns null — rendered server-side by
 * coptrz_render_product_compare_block() (includes/legacy-blocks.php).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/product-compare', {
        title:    'Product Compare (Legacy)',
        icon:     'align-wide',
        category: 'design',
        description: 'Frozen legacy Product Compare item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            compareId:    { type: 'number', default: 0 },
            compareTitle: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes } = props;
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'align-wide',
                    label: 'Product Compare (Legacy)',
                    instructions: attributes.compareTitle
                        ? 'Comparison: ' + attributes.compareTitle
                        : 'Select a product comparison — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
