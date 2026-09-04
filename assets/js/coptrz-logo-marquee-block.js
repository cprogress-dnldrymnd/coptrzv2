/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/logo-marquee` block: native editor equivalent of
 * `[dd_logo_marquee]`. Supports sourcing logos from a `dd_marquee_group` CPT
 * or selecting images directly on the block. save() returns null — rendered
 * server-side by coptrz_render_logo_marquee_block() (includes/marquee.php).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState, useEffect } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, Button, Placeholder, SelectControl } = wp.components;

    const UI = window.coptrzBlockUI || {};

    /**
     * Thumbnail strip for selected gallery images (direct source mode).
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
                    ? el('img', { key: id, src: urls[id], style: { width: '60px', height: '60px', objectFit: 'contain', borderRadius: '4px', background: '#f0f0f0' } })
                    : el('div', { key: id, style: { width: '60px', height: '60px', background: '#eee', borderRadius: '4px' } });
            })
        );
    }

    function hasContent(a) {
        if (a.source === 'direct') {
            return Array.isArray(a.imageIds) && a.imageIds.length > 0;
        }
        return !!a.groupId;
    }

    registerBlockType('coptrz/logo-marquee', {
        title:    'Logo Marquee',
        icon:     'images-alt2',
        category: 'design',
        description: 'Infinite scrolling logo marquee — from a Marquee Group or images selected on this block.',
        supports: { html: false, reusable: false },
        attributes: {
            source:     { type: 'string',  default: 'group' },
            groupId:    { type: 'number',  default: 0 },
            groupTitle: { type: 'string',  default: '' },
            imageIds:   { type: 'array',   default: [] },
            speed:      { type: 'string',  default: '30' },
            grayscale:  { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;
            const [mode, setMode] = useState(hasContent(a) ? 'preview' : 'edit');

            var instructions = 'Choose a logo source in the block settings.';
            if (a.source === 'group' && a.groupTitle) {
                instructions = 'Marquee Group: ' + a.groupTitle;
            } else if (a.source === 'direct' && a.imageIds.length) {
                instructions = a.imageIds.length + ' logo' + (a.imageIds.length === 1 ? '' : 's') + ' selected.';
            }

            const emptyPlaceholder = el(Placeholder, {
                icon:  'images-alt2',
                label: 'Logo Marquee',
                instructions: instructions
            },
                a.source === 'direct' && el(GalleryPreview, { ids: a.imageIds }),
                a.source === 'direct' && el(MediaUploadCheck, null,
                    el(MediaUpload, {
                        multiple: true,
                        gallery: true,
                        allowedTypes: ['image'],
                        value: a.imageIds,
                        onSelect: function (media) {
                            var ids = (media || []).map(function (m) { return m.id; });
                            setAttributes({ imageIds: ids });
                        },
                        render: function (o) {
                            return el(Button, { variant: 'secondary', onClick: o.open },
                                a.imageIds.length ? 'Edit Logos (' + a.imageIds.length + ')' : 'Select Logos');
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
                    el(PanelBody, { title: 'Logo Marquee Settings', initialOpen: true },
                        el(SelectControl, {
                            label: 'Logo Source',
                            value: a.source,
                            options: [
                                { label: 'Marquee Group', value: 'group' },
                                { label: 'Images on this block', value: 'direct' }
                            ],
                            onChange: function (v) { setAttributes({ source: v }); }
                        }),
                        a.source === 'group' && el(UI.SinglePostPicker, {
                            label: 'Marquee Group',
                            fetchPath: '/dd/v1/block-pickers?type=dd_marquee_group',
                            value: a.groupId ? { id: a.groupId, title: a.groupTitle } : null,
                            onChange: function (v) {
                                setAttributes({ groupId: v ? v.id : 0, groupTitle: v ? v.title : '' });
                            }
                        }),
                        a.source === 'direct' && el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                multiple: true,
                                gallery: true,
                                allowedTypes: ['image'],
                                value: a.imageIds,
                                onSelect: function (media) {
                                    var ids = (media || []).map(function (m) { return m.id; });
                                    setAttributes({ imageIds: ids });
                                },
                                render: function (o) {
                                    return el(Button, {
                                        variant: 'secondary',
                                        onClick: o.open,
                                        style: { marginBottom: '8px' }
                                    }, a.imageIds.length ? 'Edit Logos (' + a.imageIds.length + ')' : 'Select Logos');
                                }
                            })
                        ),
                        a.source === 'direct' && el(GalleryPreview, { ids: a.imageIds }),
                        UI.textField('Speed (seconds)', a.speed, function (v) { setAttributes({ speed: v }); }, { type: 'number' }),
                        UI.boolField('Grayscale', a.grayscale, function (v) { setAttributes({ grayscale: v }); })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/logo-marquee', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
