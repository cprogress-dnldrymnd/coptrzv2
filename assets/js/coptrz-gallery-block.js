/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/gallery` (Legacy) block: native editor equivalent of the
 * legacy section builder's Gallery item (post-meta.php `gallery`). Flattened,
 * typed attributes (matching coptrz/post-grid's convention) rather than an
 * opaque blob, so the block is genuinely editable and a fresh insert works with
 * sensible defaults. save() returns null — rendered server-side by
 * coptrz_render_gallery_block() (includes/legacy-blocks.php), which converts
 * these attributes back into the row shape ____gallery_modules() (modules.php)
 * expects and calls it directly — the same function ___sections() uses.
 *
 * Option lists (gallery style, column widths, spacing) are localized as
 * `coptrzLegacyBlocks` — see coptrz_legacy_block_field_options() in
 * functions.php — transcribed verbatim from post-meta.php so editor and legacy
 * admin field can't drift apart.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState, useEffect } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, Button, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    /**
     * Thumbnail strip for the selected gallery images. Resolves ids -> urls via
     * core REST (no wp-data/core-data dependency needed for a one-shot fetch).
     */
    function GalleryPreview(props) {
        var ids = props.ids || [];
        const [urls, setUrls] = useState({});

        useEffect(function () {
            if (!ids.length) { setUrls({}); return; }
            wp.apiFetch({ path: '/wp/v2/media?include=' + ids.join(',') + '&per_page=' + ids.length + '&_fields=id,media_details' })
                .then(function (items) {
                    var map = {};
                    (items || []).forEach(function (it) {
                        var sizes = it.media_details && it.media_details.sizes;
                        var src = sizes && sizes.thumbnail ? sizes.thumbnail.source_url : (sizes && sizes.full ? sizes.full.source_url : '');
                        map[it.id] = src;
                    });
                    setUrls(map);
                })
                .catch(function () { setUrls({}); });
        }, [ids.join(',')]);

        if (!ids.length) { return null; }

        return el('div', { style: { display: 'flex', flexWrap: 'wrap', gap: '6px', marginBottom: '8px' } },
            ids.map(function (id) {
                return urls[id]
                    ? el('img', { key: id, src: urls[id], style: { width: '60px', height: '60px', objectFit: 'cover', borderRadius: '4px' } })
                    : el('div', { key: id, style: { width: '60px', height: '60px', background: '#eee', borderRadius: '4px' } });
            })
        );
    }

    registerBlockType('coptrz/gallery', {
        title:    'Gallery (Legacy)',
        icon:     'format-gallery',
        category: 'design',
        description: 'A configurable gallery (logo slider or image grid) — native equivalent of the section builder\'s Gallery item.',
        supports: { html: false, reusable: false },
        attributes: {
            galleryIds:          { type: 'array',   default: [] },
            galleryStyle:        { type: 'string',  default: 'grid' },
            numberOfSlides:       { type: 'string',  default: '6' },
            numberOfSlidesTablet: { type: 'string',  default: '' },
            numberOfSlidesMobile: { type: 'string',  default: '' },
            columnWidth:          { type: 'string',  default: 'col-lg' },
            columnWidthTablet:    { type: 'string',  default: '' },
            columnWidthMobile:    { type: 'string',  default: '' },
            horizontalSpacing:    { type: 'string',  default: '' },
            verticalSpacing:      { type: 'string',  default: '' },
            sameImageHeight:      { type: 'boolean', default: false }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const isGrid = a.galleryStyle !== 'logo-slider';
            const [mode, setMode] = useState(a.galleryIds.length ? 'preview' : 'edit');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'format-gallery',
                label: 'Gallery (Legacy)',
                instructions: a.galleryIds.length ? '' : 'Select the images for this gallery.'
            },
                el(GalleryPreview, { ids: a.galleryIds }),
                el(MediaUploadCheck, null,
                    el(MediaUpload, {
                        multiple: true,
                        gallery: true,
                        allowedTypes: ['image'],
                        value: a.galleryIds,
                        onSelect: function (media) {
                            var ids = (media || []).map(function (m) { return m.id; });
                            setAttributes({ galleryIds: ids });
                        },
                        render: function (o) {
                            return el(Button, { variant: 'secondary', onClick: o.open },
                                a.galleryIds.length ? 'Edit Gallery Images (' + a.galleryIds.length + ')' : 'Select Gallery Images');
                        }
                    })
                )
            );

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Gallery Settings', initialOpen: true },
                        UI.selectField('Gallery Style', a.galleryStyle, OPTS.galleryStyle, function (v) { setAttributes({ galleryStyle: v }); }),
                        !isGrid && UI.textField('Number of Slides Desktop', a.numberOfSlides, function (v) { setAttributes({ numberOfSlides: v }); }, { type: 'number' }),
                        !isGrid && UI.textField('Number of Slides Tablet', a.numberOfSlidesTablet, function (v) { setAttributes({ numberOfSlidesTablet: v }); }, { type: 'number' }),
                        !isGrid && UI.textField('Number of Slides Mobile', a.numberOfSlidesMobile, function (v) { setAttributes({ numberOfSlidesMobile: v }); }, { type: 'number' }),
                        isGrid && UI.selectField('Column Width Desktop', a.columnWidth, OPTS.galleryColumnWidth, function (v) { setAttributes({ columnWidth: v }); }),
                        isGrid && UI.selectField('Column Width Tablet', a.columnWidthTablet, OPTS.galleryColumnWidthTablet, function (v) { setAttributes({ columnWidthTablet: v }); }),
                        isGrid && UI.selectField('Column Width Mobile', a.columnWidthMobile, OPTS.galleryColumnWidthMobile, function (v) { setAttributes({ columnWidthMobile: v }); }),
                        isGrid && UI.selectField('Horizontal Spacing', a.horizontalSpacing, OPTS.gallerySpacing, function (v) { setAttributes({ horizontalSpacing: v }); }),
                        isGrid && UI.selectField('Vertical Spacing', a.verticalSpacing, OPTS.gallerySpacing, function (v) { setAttributes({ verticalSpacing: v }); }),
                        isGrid && UI.boolField('Same Image Height', a.sameImageHeight, function (v) { setAttributes({ sameImageHeight: v }); })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/gallery', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
