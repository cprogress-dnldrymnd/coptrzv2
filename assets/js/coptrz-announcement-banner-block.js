/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/announcement-banner` block: a native Gutenberg
 * equivalent of the promo topbar hardcoded above <header> in header.php. An
 * empty attribute (the default) falls back to the CURRENT live promo values
 * at render time, so a freshly inserted block matches the real banner until
 * an editor explicitly overrides the link or an image.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_announcement_banner_block() `render_block` filter in
 * includes/header-blocks.php, which calls the shared
 * coptrz_announcement_banner_html() with only the non-empty overrides.
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el, Fragment }     = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, TextControl, Button, BaseControl, Placeholder } = wp.components;

    function imagePicker(label, url, onSelect, onClear) {
        return el(BaseControl, { label: label },
            el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px', flexWrap: 'wrap' } },
                url ? el('img', { src: url, style: { maxWidth: '80px', maxHeight: '40px', objectFit: 'contain' } }) : null,
                el(MediaUploadCheck, null,
                    el(MediaUpload, {
                        allowedTypes: ['image'],
                        value: undefined,
                        onSelect: onSelect,
                        render: function (o) {
                            return el(Button, { variant: 'secondary', onClick: o.open }, url ? 'Replace' : 'Select image');
                        }
                    })
                ),
                url ? el(Button, { variant: 'tertiary', isDestructive: true, onClick: onClear }, 'Reset to default') : null
            )
        );
    }

    registerBlockType('coptrz/announcement-banner', {
        title:    'Header — Announcement Banner',
        icon:     'megaphone',
        category: 'design',
        description: 'Embeds the promo announcement banner shown above the header.',
        attributes: {
            linkUrl:         { type: 'string', default: '' },
            desktopImageUrl: { type: 'string', default: '' },
            mobileImageUrl:  { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { linkUrl, desktopImageUrl, mobileImageUrl } = attributes;

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Announcement Banner', initialOpen: true },
                        el(TextControl, {
                            label: 'Link URL',
                            value: linkUrl,
                            placeholder: 'Leave blank to use the current live banner link',
                            onChange: function (val) { setAttributes({ linkUrl: val }); }
                        }),
                        imagePicker(
                            'Desktop image',
                            desktopImageUrl,
                            function (media) { setAttributes({ desktopImageUrl: media.url }); },
                            function () { setAttributes({ desktopImageUrl: '' }); }
                        ),
                        imagePicker(
                            'Mobile image',
                            mobileImageUrl,
                            function (media) { setAttributes({ mobileImageUrl: media.url }); },
                            function () { setAttributes({ mobileImageUrl: '' }); }
                        )
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon:  'megaphone',
                        label: 'Announcement Banner',
                        instructions: (desktopImageUrl || mobileImageUrl || linkUrl)
                            ? 'Custom banner configured.'
                            : 'Renders the current live promo banner. Set an image above to override it.'
                    })
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
