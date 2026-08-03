/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/site-logo` block: a native Gutenberg equivalent of
 * hand typing `[site_logo]` in a Shortcode block. Takes no attributes — the
 * logo itself is set via the "Logo" theme option, not per-block.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_site_logo_block() `render_block` filter in
 * includes/header-blocks.php, which runs do_shortcode('[site_logo]').
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { useBlockProps }                    = wp.blockEditor;
    const { Placeholder }                      = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/site-logo', {
        title:    'Header — Site Logo',
        icon:     'admin-home',
        category: 'design',
        description: 'Embeds the site logo (equivalent of the [site_logo] shortcode).',
        attributes: {},

        edit: function () {
            const [mode, setMode] = useState('preview');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'admin-home',
                label: 'Site Logo',
                instructions: 'Renders the site logo set in Theme Options.'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/site-logo', attributes: {}, placeholder: emptyPlaceholder, context: 'header' })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
