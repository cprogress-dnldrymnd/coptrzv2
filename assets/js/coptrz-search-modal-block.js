/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/search-modal` block: a native Gutenberg equivalent of
 * `[theme_search_modal]`. save() returns null — rendered server-side by
 * coptrz_render_search_modal_block() in includes/search-modal.php.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, TextControl, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/search-modal', {
        title: 'Search Modal',
        icon: 'search',
        category: 'widgets',
        description: 'Accessible search button that opens a post-type-filtered search dialog.',
        keywords: ['search', 'modal', 'find'],
        attributes: {
            label: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { label } = attributes;
            const [mode, setMode] = useState('preview');

            var emptyPlaceholder = el(Placeholder, {
                icon: 'search',
                label: 'Search Modal',
                instructions: label
                    ? 'Trigger label: ' + label
                    : 'Icon-only search trigger. Optional label in block settings.'
            });

            return el(
                Fragment,
                null,
                UI.PreviewToggle
                    ? el(UI.PreviewToggle, { mode: mode, setMode: setMode })
                    : null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Search Modal', initialOpen: true },
                        el(TextControl, {
                            label: 'Button label (optional)',
                            help: 'Leave empty for an icon-only trigger (recommended in the header).',
                            value: label,
                            onChange: function (val) { setAttributes({ label: val }); }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps({ className: 'coptrz-search-modal-block-editor' }),
                    mode === 'preview' && UI.LivePreview
                        ? el(UI.LivePreview, {
                            name: 'coptrz/search-modal',
                            attributes: attributes,
                            placeholder: emptyPlaceholder
                        })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
