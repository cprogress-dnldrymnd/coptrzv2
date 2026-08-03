/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product-slider` (Legacy) block: native editor
 * equivalent of the legacy section builder's Product Slider item. save()
 * returns null — rendered server-side by coptrz_render_product_slider_block()
 * (includes/legacy-blocks.php), which rebuilds the WP_Query args from these
 * source fields and calls __linked_products() (includes/woocommerce.php)
 * directly — the same function the legacy [product_slider] shortcode delegates
 * to. Source fields (not the resolved query) are stored so a "Main Query"
 * source keeps resolving from the live request, matching legacy behaviour.
 *
 * category/brand/product pickers use the shared /dd/v1/block-pickers REST
 * route (includes/hooks.php) since `pa_brands` (a WooCommerce attribute
 * taxonomy) has no show_in_rest at all — core `/wp/v2/*` isn't an option for it.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    registerBlockType('coptrz/product-slider', {
        title:    'Product Slider (Legacy)',
        icon:     'slides',
        category: 'design',
        description: 'A filtered slider of WooCommerce products — native equivalent of the section builder\'s Product Slider item.',
        supports: { html: false, reusable: false },
        attributes: {
            sourceType:  { type: 'string', default: 'category' },
            categoryIds: { type: 'array', default: [] }, // [{id, title}]
            brandIds:    { type: 'array', default: [] }, // [{id, title}]
            productIds:  { type: 'array', default: [] }, // [{id, title}]
            numberposts: { type: 'string', default: '' },
            heading:     { type: 'string', default: '' },
            buttonText:  { type: 'string', default: '' },
            buttonUrl:   { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState('preview');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'slides',
                label: 'Product Slider (Legacy)',
                instructions: a.heading
                    ? 'Heading: ' + a.heading
                    : 'Configure the source and heading in the block settings.'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Product Slider Settings', initialOpen: true },
                        UI.textField('Heading', a.heading, function (v) { setAttributes({ heading: v }); }),
                        UI.textField('Button Text', a.buttonText, function (v) { setAttributes({ buttonText: v }); }),
                        UI.textField('Button URL', a.buttonUrl, function (v) { setAttributes({ buttonUrl: v }); }),
                        UI.textField('Number of Posts', a.numberposts, function (v) { setAttributes({ numberposts: v }); }, { type: 'number', help: 'Leave empty to display all' }),
                        UI.selectField('Source', a.sourceType, OPTS.productSliderSource, function (v) { setAttributes({ sourceType: v }); }),
                        a.sourceType === 'category' && el(UI.IdTokenPicker, {
                            label: 'Category',
                            fetchPath: '/dd/v1/block-pickers?type=product_cat',
                            value: a.categoryIds,
                            onChange: function (v) { setAttributes({ categoryIds: v }); }
                        }),
                        a.sourceType === 'category' && el(UI.IdTokenPicker, {
                            label: 'Brand',
                            fetchPath: '/dd/v1/block-pickers?type=pa_brands',
                            value: a.brandIds,
                            onChange: function (v) { setAttributes({ brandIds: v }); }
                        }),
                        a.sourceType === 'manually' && el(UI.IdTokenPicker, {
                            label: 'Products',
                            fetchPath: '/dd/v1/block-pickers?type=product',
                            value: a.productIds,
                            onChange: function (v) { setAttributes({ productIds: v }); }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/product-slider', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
