/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/tabs-legacy` block: a native editor placeholder for the
 * legacy section builder's Tabs item (Bootstrap nav-tabs, distinct from the
 * native `dd/tabs` block). save() returns null — rendered server-side by
 * coptrz_render_tabs_legacy_block() (includes/legacy-blocks.php), which calls
 * ___tab_modules() (modules.php) directly, the same function ___sections() uses.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/tabs-legacy', {
        title:    'Tabs (Legacy)',
        icon:     'index-card',
        category: 'design',
        description: 'Frozen legacy Tabs item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            // No `default` on `legacy` — see coptrz-gallery-block.js for why.
            legacy: { type: 'object' }
        },

        edit: function (props) {
            const { attributes } = props;
            const tabs = (attributes.legacy && attributes.legacy.tabs) || [];
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'index-card',
                    label: 'Tabs (Legacy)',
                    instructions: tabs.length
                        ? tabs.length + ' tab(s): ' + tabs.map(function (t) { return t.heading; }).join(', ')
                        : 'Legacy tabs — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
