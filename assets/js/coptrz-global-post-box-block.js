/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/global-post-box` block: one card in a converted
 * "Global Post Box Selection" section item. The section-converter's
 * `global_post_box_selection` mapper (section-converter.php) emits a parent
 * core/group carrying the legacy row classes plus one of these per selected
 * post, so individual boxes stay deletable/reorderable in the editor — deliberately
 * more granular than the other legacy wrappers, since this feature is meant to
 * be dismantled piecemeal in the future. save() returns null — rendered
 * server-side by coptrz_render_global_post_box_block() (includes/legacy-blocks.php),
 * which calls __post_box() (modules.php) directly, the same function
 * ___sections() uses. Column-width classes are resolved once at conversion time
 * (including the legacy 3-posts/col-md-6 special case) and baked into `colClasses`,
 * not recomputed per box.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/global-post-box', {
        title:    'Global Post Box (Legacy)',
        icon:     'id',
        category: 'design',
        description: 'One frozen legacy Global Post Box card — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            postId:     { type: 'number', default: 0 },
            postTitle:  { type: 'string', default: '' },
            colClasses: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes } = props;
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'id',
                    label: 'Global Post Box (Legacy)',
                    instructions: attributes.postTitle
                        ? attributes.postTitle
                        : 'Legacy global post box — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
