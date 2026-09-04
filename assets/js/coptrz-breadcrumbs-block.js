/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/breadcrumbs` block: native editor equivalent of
 * `[breadcrumbs]`. save() returns null — rendered server-side by
 * coptrz_render_breadcrumbs_block() (includes/shortcodes.php), which builds
 * the shortcode and runs do_shortcode().
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder, SelectControl } = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/breadcrumbs', {
        title:    'Breadcrumbs',
        icon:     'arrow-right-alt',
        category: 'design',
        description: 'Trail of links showing the current page hierarchy (equivalent of the [breadcrumbs] shortcode).',
        supports: { html: false, reusable: false },
        attributes: {
            type:         { type: 'string', default: 'page' },
            id:           { type: 'number', default: 0 },
            archiveTitle: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState('preview');

            var instructions = 'Type: ' + a.type;
            if (a.type === 'archive' && a.archiveTitle) {
                instructions += ' — ' + a.archiveTitle;
            } else if (a.id) {
                instructions += ' — ID ' + a.id;
            } else if (a.type === 'page') {
                instructions += ' — current page';
            }

            const emptyPlaceholder = el(Placeholder, {
                icon:  'arrow-right-alt',
                label: 'Breadcrumbs',
                instructions: instructions
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Breadcrumbs Settings', initialOpen: true },
                        el(SelectControl, {
                            label: 'Type',
                            value: a.type,
                            options: [
                                { label: 'Page / Post', value: 'page' },
                                { label: 'Term', value: 'term' },
                                { label: 'Archive', value: 'archive' }
                            ],
                            onChange: function (v) { setAttributes({ type: v }); }
                        }),
                        (a.type === 'page' || a.type === 'term') && UI.textField(
                            a.type === 'term' ? 'Term ID' : 'Post ID (0 = current)',
                            String(a.id || 0),
                            function (v) { setAttributes({ id: parseInt(v, 10) || 0 }); },
                            { type: 'number' }
                        ),
                        a.type === 'archive' && UI.textField(
                            'Archive Title',
                            a.archiveTitle,
                            function (v) { setAttributes({ archiveTitle: v }); }
                        )
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/breadcrumbs', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
