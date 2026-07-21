/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/gallery` (Legacy) block: a native editor placeholder for
 * the legacy section builder's Gallery item. save() returns null — the block is
 * rendered server-side by coptrz_render_gallery_block() (functions.php), which
 * hands the whole stored `legacy` item back to ____gallery_modules() (modules.php),
 * the same function ___sections() calls, so output is byte-identical. Not meant
 * to be edited natively — the block exists purely so a converted section stops
 * snapshotting to Custom HTML.
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el }               = wp.element;
    const { useBlockProps }                    = wp.blockEditor;
    const { Placeholder }                      = wp.components;

    registerBlockType('coptrz/gallery', {
        title:    'Gallery (Legacy)',
        icon:     'format-gallery',
        category: 'design',
        description: 'Frozen legacy Gallery item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            // No `default` on `legacy` — Gutenberg falls back to the default
            // silently if the stored value ever fails the `object` type check
            // (e.g. an empty PHP array serializes as `[]`, not `{}`), which
            // would mask real data loss instead of surfacing it.
            legacy:  { type: 'object' },
            summary: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes } = props;
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'format-gallery',
                    label: 'Gallery (Legacy)',
                    instructions: attributes.summary || 'Legacy gallery — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
