/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/global-post-box` block: one card in a converted
 * "Global Post Box Selection" section item. The section-converter's
 * `global_post_box_selection` mapper (section-converter.php) emits a parent
 * core/group carrying the legacy row classes plus one of these per selected
 * post, so individual boxes stay deletable/reorderable/re-configurable in the
 * editor. save() returns null — rendered server-side by
 * coptrz_render_global_post_box_block() (includes/legacy-blocks.php), which
 * calls __post_box() (modules.php) directly.
 *
 * Column-width is now a per-box editable select (rather than a class string
 * baked in at conversion time) — the legacy 3-posts/col-md-6 special case
 * (modules.php) still resolves once at conversion time into each box's initial
 * value, since it depends on the whole selection's post count, but is freely
 * editable afterwards like any other setting.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    registerBlockType('coptrz/global-post-box', {
        title:    'Global Post Box (Legacy)',
        icon:     'id',
        category: 'design',
        description: 'One card from a Global Post Box selection — native equivalent of that section builder item.',
        supports: { html: false, reusable: false },
        attributes: {
            postId:            { type: 'number', default: 0 },
            postTitle:         { type: 'string', default: '' },
            columnWidth:       { type: 'string', default: '' },
            columnWidthTablet: { type: 'string', default: '' },
            columnWidthMobile: { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState(a.postId ? 'preview' : 'edit');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'id',
                label: 'Global Post Box (Legacy)',
                instructions: a.postTitle
                    ? a.postTitle
                    : 'Select a post in the block settings.'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Global Post Box Settings', initialOpen: true },
                        el(UI.SinglePostPicker, {
                            label: 'Post',
                            fetchPath: '/dd/v1/block-pickers?type=globalpostboxes',
                            value: a.postId ? { id: a.postId, title: a.postTitle } : null,
                            onChange: function (v) { setAttributes({ postId: v ? v.id : 0, postTitle: v ? v.title : '' }); }
                        }),
                        UI.selectField('Column Width Desktop', a.columnWidth, OPTS.galleryColumnWidth, function (v) { setAttributes({ columnWidth: v }); }),
                        UI.selectField('Column Width Tablet', a.columnWidthTablet, OPTS.galleryColumnWidthTablet, function (v) { setAttributes({ columnWidthTablet: v }); }),
                        UI.selectField('Column Width Mobile', a.columnWidthMobile, OPTS.galleryColumnWidthMobile, function (v) { setAttributes({ columnWidthMobile: v }); })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/global-post-box', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
