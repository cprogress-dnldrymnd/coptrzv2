/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/icon-legacy` block: native editor equivalent of the
 * legacy section builder's Icon column item. save() returns null — rendered
 * server-side by coptrz_render_icon_legacy_block() (includes/legacy-blocks.php),
 * which calls _____icon_modules() (modules.php) directly. That renderer inlines
 * the selected SVG file's contents into a `.icon-box` wrapper (colour/size
 * driven by CSS custom properties) rather than emitting an <img>, so this has
 * no native block equivalent (core/image would lose the inline-SVG recolouring).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, Button, BaseControl, ColorPalette } = wp.components;

    const UI   = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    registerBlockType('coptrz/icon-legacy', {
        title:    'Icon (Legacy)',
        icon:     'admin-customizer',
        category: 'design',
        description: 'An SVG icon — native equivalent of the section builder\'s Icon item.',
        supports: { html: false, reusable: false },
        attributes: {
            iconId:          { type: 'number', default: 0 },
            iconUrl:         { type: 'string', default: '' }, // editor preview only, not read by the renderer
            iconColor:       { type: 'string', default: '' },
            iconColorCustom: { type: 'string', default: '' },
            iconWidth:       { type: 'string', default: '' },
            iconHeight:      { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState(a.iconId ? 'preview' : 'edit');

            const emptyPlaceholder = el('div', {
                style: {
                    border: '1px dashed #c3c4c7', borderRadius: '4px', padding: '24px',
                    background: '#f6f7f7', textAlign: 'center', color: '#757575'
                }
            }, 'Icon (Legacy) — select an SVG in the sidebar.');

            return el(Fragment, null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Icon', initialOpen: true },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                allowedTypes: ['image/svg+xml'],
                                value: a.iconId,
                                onSelect: function (media) {
                                    setAttributes({ iconId: media.id, iconUrl: media.url });
                                },
                                render: function (o) {
                                    return el(Button, { variant: 'secondary', onClick: o.open },
                                        a.iconId ? 'Replace SVG' : 'Select SVG');
                                }
                            })
                        ),
                        a.iconId ? el(Button, {
                            variant: 'link',
                            isDestructive: true,
                            style: { marginTop: '8px', display: 'block' },
                            onClick: function () { setAttributes({ iconId: 0, iconUrl: '' }); }
                        }, 'Remove') : null
                    ),
                    el(PanelBody, { title: 'Appearance', initialOpen: true },
                        UI.selectField('Text Color', a.iconColor, OPTS.iconColor, function (v) { setAttributes({ iconColor: v }); }),
                        a.iconColor === 'text-custom' && el(BaseControl, { label: 'Custom Color' },
                            el(ColorPalette, {
                                value: a.iconColorCustom,
                                onChange: function (v) { setAttributes({ iconColorCustom: v || '' }); }
                            })
                        ),
                        UI.textField('Custom Icon Width', a.iconWidth, function (v) { setAttributes({ iconWidth: v }); }),
                        UI.textField('Custom Icon Height', a.iconHeight, function (v) { setAttributes({ iconHeight: v }); })
                    )
                ),
                el('div', useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/icon-legacy', attributes: a, placeholder: emptyPlaceholder })
                        : (a.iconUrl
                            ? el('img', { src: a.iconUrl, style: { width: a.iconWidth || '60px', height: a.iconHeight || '60px', objectFit: 'contain' } })
                            : emptyPlaceholder)
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
