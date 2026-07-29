/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/cf7-legacy` block: native editor equivalent of the
 * legacy section builder's Cf7 column item (a `[contact-form-7 id='…']`
 * shortcode wrapped in a `.form-box $style` div). save() returns null —
 * rendered server-side by coptrz_render_cf7_legacy_block()
 * (includes/legacy-blocks.php), which calls __cf7_module() (modules.php).
 *
 * Reuses the `/dd/v1/cf7-forms` REST route already registered for
 * `dd/cf7-pdf-form` (hooks.php) — but stores the numeric `id` (the CF7 form's
 * POST ID, matching the legacy Carbon `association` field) rather than the
 * `hash` dd/cf7-pdf-form uses, since __cf7_module() builds
 * `[contact-form-7 id='$id']` with the plain post ID.
 */
(function (wp) {

    const { registerBlockType }        = wp.blocks;
    const { createElement: el, useState, useEffect, Fragment } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;

    const UI   = window.coptrzBlockUI || {};
    const OPTS = window.coptrzLegacyBlocks || {};

    registerBlockType('coptrz/cf7-legacy', {
        title:    'Contact Form (Legacy)',
        icon:     'feedback',
        category: 'design',
        description: 'A Contact Form 7 embed — native equivalent of the section builder\'s Cf7 item.',
        supports: { html: false, reusable: false },
        attributes: {
            formId:    { type: 'number', default: 0 },
            formTitle: { type: 'string', default: '' },
            style:     { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;

            const [forms, setForms] = useState([]);
            const [loading, setLoading] = useState(true);
            const [mode, setMode] = useState(a.formId ? 'preview' : 'edit');

            useEffect(function () {
                wp.apiFetch({ path: '/dd/v1/cf7-forms' })
                    .then(function (items) {
                        setForms((items || []).map(function (f) {
                            return { label: f.title, value: f.id, title: f.title };
                        }));
                        setLoading(false);
                    })
                    .catch(function () { setLoading(false); });
            }, []);

            var options = [{ label: '— Select a form —', value: 0 }].concat(
                loading ? [{ label: 'Loading…', value: 0 }] : forms
            );

            const emptyPlaceholder = el('div', {
                style: {
                    border: '1px dashed #c3c4c7', borderRadius: '4px', padding: '16px',
                    background: '#f6f7f7'
                }
            },
                el('strong', null, 'Contact Form (Legacy)'),
                el('div', { style: { marginTop: '6px', fontSize: '13px', color: '#1e1e1e' } },
                    a.formTitle ? 'Form: ' + a.formTitle : 'No form selected')
            );

            return el(Fragment, null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Contact Form', initialOpen: true },
                        el(SelectControl, {
                            label: 'Contact form',
                            value: a.formId,
                            options: options,
                            onChange: function (val) {
                                var id = parseInt(val, 10) || 0;
                                var match = forms.filter(function (f) { return f.value === id; })[0];
                                setAttributes({ formId: id, formTitle: match ? match.title : '' });
                            }
                        }),
                        UI.selectField('Style', a.style, OPTS.cf7Style, function (v) { setAttributes({ style: v }); })
                    )
                ),
                el('div', useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/cf7-legacy', attributes: a, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
