/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/tabs-legacy` block: native editor equivalent of the
 * legacy section builder's Tabs item (Bootstrap nav-tabs — distinct from the
 * native `dd/tabs` block, this is a frozen-renderer wrapper, not a conversion
 * onto it). save() returns null — rendered server-side by
 * coptrz_render_tabs_legacy_block() (includes/legacy-blocks.php), which calls
 * ___tab_modules() (modules.php) directly with `autop = false`, since these
 * descriptions come from RichText (already real HTML) rather than a legacy
 * textarea (bare newlines wpautop() turns into paragraphs).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    function defaultTab() {
        return { heading: 'New Tab', description: '' };
    }

    registerBlockType('coptrz/tabs-legacy', {
        title:    'Tabs (Legacy)',
        icon:     'index-card',
        category: 'design',
        description: 'Bootstrap-style tabs — native equivalent of the section builder\'s Tabs item.',
        supports: { html: false, reusable: false },
        attributes: {
            tabs: { type: 'array', default: [] } // [{heading, description}]
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const tabs = attributes.tabs;

            return el(
                'div',
                useBlockProps(),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Tabs', initialOpen: true },
                        el('p', null, 'Manage tab content in the block canvas.')
                    )
                ),
                tabs.length === 0
                    ? el(Placeholder, {
                        icon: 'index-card',
                        label: 'Tabs (Legacy)',
                        instructions: 'Add tabs below.'
                    })
                    : null,
                el(UI.Repeater, {
                    items: tabs,
                    onChange: function (next) { setAttributes({ tabs: next }); },
                    defaultItem: defaultTab,
                    addLabel: '+ Add Tab',
                    rowLabel: function (item) { return item.heading || 'Tab'; },
                    renderRow: function (item, idx, update) {
                        return el('div', null,
                            UI.textField('Tab Heading', item.heading, function (v) { update({ heading: v }); }),
                            el('div', { style: { marginTop: '8px' } },
                                el('label', { style: { display: 'block', marginBottom: '4px', fontWeight: 600 } }, 'Description'),
                                el(RichText, {
                                    tagName: 'div',
                                    className: 'coptrz-legacy-richtext',
                                    style: { border: '1px solid #ddd', borderRadius: '2px', padding: '8px', minHeight: '80px' },
                                    value: item.description,
                                    onChange: function (v) { update({ description: v }); },
                                    placeholder: 'Tab description…'
                                })
                            )
                        );
                    }
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
