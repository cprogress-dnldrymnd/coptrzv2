/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/accordion-legacy` block: native editor equivalent of
 * the legacy section builder's Accordion item (distinct from native
 * `core/details` — this is a frozen-renderer wrapper that keeps the
 * FAQs-by-selection / FAQs-by-category dynamic sourcing intact). save() returns
 * null — rendered server-side by coptrz_render_accordion_legacy_block()
 * (includes/legacy-blocks.php), which calls __accordion_module() (modules.php)
 * directly. Custom-source descriptions are edited via RichText and rendered
 * with `autop = false` (they're already real HTML); FAQ-sourced descriptions
 * come from raw post_content and always keep wpautop — see the `autop`
 * handling in __accordion_module() (modules.php).
 *
 * FAQ / FAQ-category pickers use the shared /dd/v1/block-pickers REST route
 * (includes/hooks.php) since `faq` has show_in_rest = false.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    function defaultItem() {
        return { heading: 'New Item', description: '' };
    }

    registerBlockType('coptrz/accordion-legacy', {
        title:    'Accordion (Legacy)',
        icon:     'list-view',
        category: 'design',
        description: 'A Bootstrap-style accordion — native equivalent of the section builder\'s Accordion item.',
        supports: { html: false, reusable: false },
        attributes: {
            items:         { type: 'array',   default: [] }, // [{heading, description}] — custom source only
            source:        { type: 'string',  default: '' }, // '' | faqs | faqs_category
            faqs:          { type: 'array',   default: [] }, // [{id, title}]
            faqsCategory:  { type: 'array',   default: [] }, // [{id, title}]
            openFirstItem: { type: 'boolean', default: false },
            withBorder:    { type: 'boolean', default: false },
            lowerOpacity:  { type: 'boolean', default: false }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const isCustom = a.source === '';

            return el(
                'div',
                useBlockProps(),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Accordion Settings', initialOpen: true },
                        UI.selectField('Accordion Source', a.source, OPTS.accordionSource, function (v) { setAttributes({ source: v }); }),
                        a.source === 'faqs' && el(UI.IdTokenPicker, {
                            label: 'Select FAQs',
                            fetchPath: '/dd/v1/block-pickers?type=faq',
                            value: a.faqs,
                            onChange: function (v) { setAttributes({ faqs: v }); }
                        }),
                        a.source === 'faqs_category' && el(UI.IdTokenPicker, {
                            label: 'Select FAQs Category',
                            fetchPath: '/dd/v1/block-pickers?type=faqs_category',
                            value: a.faqsCategory,
                            onChange: function (v) { setAttributes({ faqsCategory: v }); }
                        }),
                        UI.boolField('Open First Item', a.openFirstItem, function (v) { setAttributes({ openFirstItem: v }); }),
                        UI.boolField('With Border', a.withBorder, function (v) { setAttributes({ withBorder: v }); }),
                        UI.boolField('Lower Opacity for Inactive', a.lowerOpacity, function (v) { setAttributes({ lowerOpacity: v }); })
                    )
                ),
                isCustom
                    ? el(Fragment, null,
                        a.items.length === 0
                            ? el(Placeholder, { icon: 'list-view', label: 'Accordion (Legacy)', instructions: 'Add accordion items below.' })
                            : null,
                        el(UI.Repeater, {
                            items: a.items,
                            onChange: function (next) { setAttributes({ items: next }); },
                            defaultItem: defaultItem,
                            addLabel: '+ Add Item',
                            rowLabel: function (item) { return item.heading || 'Item'; },
                            renderRow: function (item, idx, update) {
                                return el('div', null,
                                    UI.textField('Heading', item.heading, function (v) { update({ heading: v }); }),
                                    el('div', { style: { marginTop: '8px' } },
                                        el('label', { style: { display: 'block', marginBottom: '4px', fontWeight: 600 } }, 'Description'),
                                        el(RichText, {
                                            tagName: 'div',
                                            className: 'coptrz-legacy-richtext',
                                            style: { border: '1px solid #ddd', borderRadius: '2px', padding: '8px', minHeight: '80px' },
                                            value: item.description,
                                            onChange: function (v) { update({ description: v }); },
                                            placeholder: 'Item description…'
                                        })
                                    )
                                );
                            }
                        })
                    )
                    : el(Placeholder, {
                        icon: 'list-view',
                        label: 'Accordion (Legacy)',
                        instructions: a.source === 'faqs' ? (a.faqs.length + ' FAQ(s) selected') : (a.faqsCategory.length ? 'Category: ' + a.faqsCategory.map(function (c) { return c.title; }).join(', ') : 'Select a FAQ category in the block settings.')
                    })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
