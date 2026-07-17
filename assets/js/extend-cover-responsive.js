/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Extends the core Cover block with optional per-breakpoint background images.
 * An editor can supply a distinct Mobile (<=767px) and Tablet (768-991px) image;
 * the desktop image is the block's existing background. Swapping is done at
 * render time via a native <picture>/<source media> wrapper (see
 * dd_cover_responsive_render in functions.php) — this file only stores the
 * chosen image ids/urls as block attributes.
 */
(function (wp) {

    const { addFilter }                             = wp.hooks;
    const { createHigherOrderComponent }            = wp.compose;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, Button }                     = wp.components;
    const { createElement: el, Fragment }           = wp.element;

    const TARGET_BLOCK = 'core/cover';

    // ── 1. Register the responsive-image attributes on core/cover ─────────────
    function addResponsiveBgAttributes(settings, name) {
        if (name !== TARGET_BLOCK) return settings;
        settings.attributes = Object.assign(settings.attributes || {}, {
            ddMobileImageId:  { type: 'number', default: 0 },
            ddMobileImageUrl: { type: 'string', default: '' },
            ddTabletImageId:  { type: 'number', default: 0 },
            ddTabletImageUrl: { type: 'string', default: '' }
        });
        return settings;
    }
    addFilter('blocks.registerBlockType', 'dd/cover-responsive-attrs', addResponsiveBgAttributes);

    // ── 2. Add the "Responsive Background" InspectorControls panel ────────────
    // A single reusable image slot (Mobile / Tablet share this markup).
    function imageSlot(label, hint, imageUrl, onSelect, onRemove) {
        return el(
            'div',
            { style: { marginBottom: '16px' } },
            el('p', { style: { fontWeight: 600, margin: '0 0 4px' } }, label),
            el('p', { style: { fontSize: '12px', color: '#757575', margin: '0 0 8px' } }, hint),
            imageUrl
                ? el(
                    'img',
                    {
                        src:   imageUrl,
                        alt:   '',
                        style: {
                            display:      'block',
                            width:        '100%',
                            height:       'auto',
                            marginBottom: '8px',
                            borderRadius: '2px'
                        }
                    }
                )
                : null,
            el(MediaUploadCheck, null,
                el(MediaUpload, {
                    allowedTypes: ['image'],
                    onSelect:     onSelect,
                    render: function (obj) {
                        return el(
                            'div',
                            { style: { display: 'flex', gap: '8px' } },
                            el(Button, {
                                variant:  'secondary',
                                onClick:  obj.open
                            }, imageUrl ? 'Replace' : 'Select image'),
                            imageUrl
                                ? el(Button, {
                                    variant:    'tertiary',
                                    isDestructive: true,
                                    onClick:    onRemove
                                }, 'Remove')
                                : null
                        );
                    }
                })
            )
        );
    }

    const withResponsiveBgControl = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (props.name !== TARGET_BLOCK) return el(BlockEdit, props);

            const { attributes, setAttributes } = props;
            const {
                ddMobileImageUrl,
                ddTabletImageUrl
            } = attributes;

            return el(
                Fragment,
                null,
                el(BlockEdit, props),
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Responsive Background', initialOpen: false },
                        el('p', {
                            style: { fontSize: '12px', color: '#757575', marginTop: 0 }
                        }, 'Optionally show a different background image on smaller screens. ' +
                           'The block’s own image is used on desktop (≥992px).'),
                        imageSlot(
                            'Mobile image',
                            'Shown at ≤767px.',
                            ddMobileImageUrl,
                            function (media) {
                                setAttributes({
                                    ddMobileImageId:  media.id || 0,
                                    ddMobileImageUrl: media.url || ''
                                });
                            },
                            function () {
                                setAttributes({ ddMobileImageId: 0, ddMobileImageUrl: '' });
                            }
                        ),
                        imageSlot(
                            'Tablet image',
                            'Shown at 768–991px.',
                            ddTabletImageUrl,
                            function (media) {
                                setAttributes({
                                    ddTabletImageId:  media.id || 0,
                                    ddTabletImageUrl: media.url || ''
                                });
                            },
                            function () {
                                setAttributes({ ddTabletImageId: 0, ddTabletImageUrl: '' });
                            }
                        )
                    )
                )
            );
        };
    }, 'withResponsiveBgControl');
    addFilter('editor.BlockEdit', 'dd/cover-responsive-control', withResponsiveBgControl);

})(window.wp);
