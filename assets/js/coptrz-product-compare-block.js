/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/product-compare` block: native editor equivalent of
 * the legacy section builder's Product Compare item (`[product_compare
 * id="…"]`). save() returns null — rendered server-side by
 * coptrz_render_product_compare_block() (includes/legacy-blocks.php).
 *
 * `compareproducts` has show_in_rest = false, so this picker uses the shared
 * /dd/v1/block-pickers route (includes/hooks.php) rather than core REST.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/product-compare', {
        title:    'Product Compare (Legacy)',
        icon:     'align-wide',
        category: 'design',
        description: 'A product comparison table embed — native equivalent of the section builder\'s Product Compare item.',
        supports: { html: false, reusable: false },
        attributes: {
            compareId:    { type: 'number', default: 0 },
            compareTitle: { type: 'string', default: '' }
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
                    el(PanelBody, { title: 'Product Compare Settings', initialOpen: true },
                        el(UI.SinglePostPicker, {
                            label: 'Comparison',
                            fetchPath: '/dd/v1/block-pickers?type=compareproducts',
                            value: a.compareId ? { id: a.compareId, title: a.compareTitle } : null,
                            onChange: function (v) { setAttributes({ compareId: v ? v.id : 0, compareTitle: v ? v.title : '' }); }
                        })
                    )
                ),
                el(Placeholder, {
                    icon:  'align-wide',
                    label: 'Product Compare (Legacy)',
                    instructions: a.compareTitle
                        ? 'Comparison: ' + a.compareTitle
                        : 'Select a product comparison in the block settings.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
