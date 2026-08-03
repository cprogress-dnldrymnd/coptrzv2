/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/header-menu` block: a native Gutenberg equivalent of
 * calling `header_menu()` directly. Takes no attributes — the menu itself is
 * managed at Appearance > Menus ("Header Menu" location), not per-block.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_header_menu_block() `render_block` filter in
 * includes/header-blocks.php, which calls the existing header_menu()
 * (includes/menus.php).
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { useBlockProps }                    = wp.blockEditor;
    const { Placeholder }                      = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/header-menu', {
        title:    'Header — Nav Menu',
        icon:     'menu',
        category: 'design',
        description: 'Embeds the desktop header navigation menu.',
        attributes: {},

        edit: function () {
            const [mode, setMode] = useState('preview');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'menu',
                label: 'Header Nav Menu',
                instructions: 'Renders the "Header Menu" nav location (Appearance > Menus).'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/header-menu', attributes: {}, placeholder: emptyPlaceholder, context: 'header' })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
