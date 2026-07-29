/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/divider-legacy` block: native editor equivalent of the
 * legacy section builder's Divider column item (a plain <hr> carrying margin
 * utility classes and a border-color class). save() returns null — rendered
 * server-side by coptrz_render_divider_legacy_block() (includes/legacy-blocks.php),
 * which calls __divider_module() (modules.php, extracted from the legacy
 * ____columns_modules() switch when this block was added). `border_color_custom`/
 * `border_width` are legacy Carbon fields the renderer never reads — matched
 * here by simply not exposing those controls, rather than adding dead ones.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody } = wp.components;

    const UI   = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    registerBlockType('coptrz/divider-legacy', {
        title:    'Divider (Legacy)',
        icon:     'minus',
        category: 'design',
        description: 'A styled horizontal rule — native equivalent of the section builder\'s Divider item.',
        supports: { html: false, reusable: false },
        attributes: {
            marginTop:    { type: 'string', default: '' },
            marginBottom: { type: 'string', default: '' },
            marginLeft:   { type: 'string', default: '' },
            marginRight:  { type: 'string', default: '' },
            borderColor:  { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState('preview');

            return el('div', useBlockProps(),
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Divider Settings', initialOpen: true },
                        UI.selectField('Margin Top', a.marginTop, OPTS.dividerMarginTop, function (v) { setAttributes({ marginTop: v }); }),
                        UI.selectField('Margin Bottom', a.marginBottom, OPTS.dividerMarginBottom, function (v) { setAttributes({ marginBottom: v }); }),
                        UI.selectField('Margin Left', a.marginLeft, OPTS.dividerMarginLeft, function (v) { setAttributes({ marginLeft: v }); }),
                        UI.selectField('Margin Right', a.marginRight, OPTS.dividerMarginRight, function (v) { setAttributes({ marginRight: v }); }),
                        UI.selectField('Border Color', a.borderColor, OPTS.dividerBorderColor, function (v) { setAttributes({ borderColor: v }); })
                    )
                ),
                mode === 'preview'
                    ? el(UI.LivePreview, { name: 'coptrz/divider-legacy', attributes: a, placeholder: el('hr', { style: { borderTop: '1px solid #ccc' } }) })
                    : el('hr', { style: { borderTop: '1px solid #ccc' } })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
