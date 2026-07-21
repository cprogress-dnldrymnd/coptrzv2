/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/section-split` block: a structural marker used only on
 * `product` posts to divide post_content into the two slots the product template
 * renders separately — everything above this block prints above the buy box
 * (___sections(), includes/woocommerce.php), everything below prints after it
 * (___sections('sections_after_main')). See coptrz_product_content_split() in
 * includes/section-converter.php, which splits post_content on this block.
 *
 * Carries no attributes and never emits markup: save() returns null, and the
 * render_block filter (coptrz_render_section_split_block(), functions.php)
 * always returns an empty string. Restricted to the `product` post type since
 * it has no meaning anywhere else.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { select }            = wp.data;

    if (select('core/editor').getCurrentPostType() !== 'product') {
        return;
    }

    registerBlockType('coptrz/section-split', {
        title:    'Section Split (Product summary)',
        icon:     'sort',
        category: 'design',
        description: 'Marks where the product summary / buy box sits. Blocks above render above it, blocks below render after it.',
        supports: {
            html: false,
            multiple: false,
            reusable: false
        },

        edit: function () {
            return el(
                'div',
                Object.assign({}, useBlockProps(), {
                    style: {
                        padding: '14px 16px',
                        textAlign: 'center',
                        background: '#f0f0f1',
                        border: '1px dashed #8c8f94',
                        color: '#50575e',
                        fontWeight: 600
                    }
                }),
                '▼ Product summary / buy box renders here ▼'
            );
        },

        // Structural marker only — never rendered on the front end.
        save: function () { return null; }
    });

})(window.wp);
