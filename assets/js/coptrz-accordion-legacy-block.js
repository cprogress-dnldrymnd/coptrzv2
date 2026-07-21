/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/accordion-legacy` block: a native editor placeholder for
 * the legacy section builder's Accordion item (distinct from native `core/details`
 * blocks). save() returns null — rendered server-side by
 * coptrz_render_accordion_legacy_block() (includes/legacy-blocks.php), which
 * calls __accordion_module() (modules.php) directly, the same function
 * ___sections() uses — including its FAQs-by-selection / FAQs-by-category
 * dynamic sourcing.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/accordion-legacy', {
        title:    'Accordion (Legacy)',
        icon:     'list-view',
        category: 'design',
        description: 'Frozen legacy Accordion item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            legacy: { type: 'object' }
        },

        edit: function (props) {
            const { attributes } = props;
            const source = (attributes.legacy && attributes.legacy.accordion_source) || '';
            const items  = (attributes.legacy && attributes.legacy.accordion) || [];
            var instructions = 'Legacy accordion — content managed elsewhere.';
            if (source === 'faqs') {
                instructions = 'Source: FAQs (manually selected)';
            } else if (source === 'faqs_category') {
                instructions = 'Source: FAQs by category';
            } else if (items.length) {
                instructions = items.length + ' item(s): ' + items.map(function (a) { return a.heading; }).join(', ');
            }
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'list-view',
                    label: 'Accordion (Legacy)',
                    instructions: instructions
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
