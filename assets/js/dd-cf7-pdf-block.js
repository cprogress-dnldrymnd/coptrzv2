/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `dd/cf7-pdf-form` block: a native Gutenberg equivalent of hand
 * typing `[contact-form-7 id="…" pdf_url="…"]` in a Shortcode block. The editor
 * picks a CF7 form from a dropdown and chooses a PDF either from the media
 * library or from the Documents post type.
 *
 * save() returns null — the block is rendered server-side by the
 * dd_render_cf7_pdf_block() `render_block` filter in functions.php, which rebuilds
 * the shortcode from the stored attributes and runs do_shortcode(). Building from
 * attributes (single source of truth) is why the chosen PDF is always stored as a
 * literal URL in `pdfUrl` (a Document is resolved to its file URL at selection time
 * via /dd/v1/documents), so the emitted shortcode matches a hand-typed one.
 */
(function (wp) {

    const { registerBlockType }                                = wp.blocks;
    const { createElement: el, useState, useEffect, Fragment, RawHTML } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, SelectControl, Button, BaseControl, TextControl } = wp.components;

    function buildShortcode(a) {
        if (!a.formId) { return ''; }
        var sc = '[contact-form-7 id="' + a.formId + '"';
        if (a.formTitle) { sc += ' title="' + a.formTitle + '"'; }
        if (a.pdfUrl)    { sc += ' pdf_url="' + a.pdfUrl + '"'; }
        if (a.speakUrl)  { sc += ' speak_to_an_expert_url="' + a.speakUrl + '"'; }
        sc += ']';
        return sc;
    }

    registerBlockType('dd/cf7-pdf-form', {
        title:    'Contact Form + PDF',
        icon:     'media-document',
        category: 'design',
        attributes: {
            formId:        { type: 'string', default: '' },
            formTitle:     { type: 'string', default: '' },
            pdfSource:     { type: 'string', default: 'media' },
            pdfUrl:        { type: 'string', default: '' },
            pdfDocumentId: { type: 'number', default: 0 },
            speakUrl:      { type: 'string', default: '' }
        },

        // Migrate instances saved under the earlier *static* version, whose
        // save() wrote the literal shortcode into the block markup. Matching that
        // here lets Gutenberg validate them and re-serialize to the current
        // (empty) markup instead of flagging them invalid. (Instances from the
        // very first dynamic version were already empty, so they match the current
        // null save() directly and need no deprecation.)
        deprecated: [
            {
                attributes: {
                    formId:        { type: 'string', default: '' },
                    formTitle:     { type: 'string', default: '' },
                    pdfSource:     { type: 'string', default: 'media' },
                    pdfUrl:        { type: 'string', default: '' },
                    pdfDocumentId: { type: 'number', default: 0 }
                },
                save: function (props) {
                    var sc = buildShortcode(props.attributes);
                    return sc ? el(RawHTML, null, sc) : null;
                }
            }
        ],

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { formId, formTitle, pdfSource, pdfUrl, pdfDocumentId, speakUrl } = attributes;

            const [forms,        setForms]        = useState([]);
            const [formsLoading, setFormsLoading] = useState(true);
            const [docs,         setDocs]         = useState([]);
            const [docsLoading,  setDocsLoading]  = useState(true);

            // Fetch CF7 forms once (values are the CF7 hash used as the shortcode id).
            useEffect(function () {
                wp.apiFetch({ path: '/dd/v1/cf7-forms' })
                    .then(function (items) {
                        var options = [{ label: '— Select a form —', value: '', title: '' }].concat(
                            (items || []).map(function (f) {
                                return { label: f.title, value: f.hash, title: f.title };
                            })
                        );
                        setForms(options);
                        setFormsLoading(false);
                    })
                    .catch(function () { setFormsLoading(false); });
            }, []);

            // Fetch Documents once (each carries its resolved PDF url and the
            // document's own speak-to-an-expert url).
            useEffect(function () {
                wp.apiFetch({ path: '/dd/v1/documents' })
                    .then(function (items) {
                        var options = [{ label: '— Select a document —', value: 0, url: '', speakUrl: '' }].concat(
                            (items || []).map(function (d) {
                                return { label: d.title, value: d.id, url: d.url || '', speakUrl: d.speak_url || '' };
                            })
                        );
                        setDocs(options);
                        setDocsLoading(false);
                    })
                    .catch(function () { setDocsLoading(false); });
            }, []);

            function pdfSummary() {
                if (pdfSource === 'document') {
                    if (!pdfDocumentId) { return 'No document selected'; }
                    var match = docs.filter(function (d) { return d.value === pdfDocumentId; })[0];
                    return 'Document: ' + (match ? match.label : '#' + pdfDocumentId);
                }
                return pdfUrl ? 'PDF: ' + pdfUrl.split('/').pop() : 'No PDF selected';
            }

            var codeBoxStyle = {
                display: 'block',
                marginTop: '6px',
                padding: '6px 8px',
                background: '#fff',
                border: '1px solid #e0e0e0',
                borderRadius: '3px',
                color: '#1e1e1e',
                fontFamily: 'monospace',
                userSelect: 'all'
            };

            // For a Document source, show the document's *live* speak url (from the
            // fetched list) so it stays accurate even for a block configured before
            // this field existed; for Media it's the manually-entered custom URL.
            var selectedDoc = docs.filter(function (o) { return o.value === pdfDocumentId; })[0];
            var effectiveSpeakUrl = (pdfSource === 'document')
                ? (selectedDoc ? selectedDoc.speakUrl : speakUrl)
                : speakUrl;

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Contact Form', initialOpen: true },
                        el(SelectControl, {
                            label:   'Contact form',
                            value:   formId,
                            help:    'The form must contain a [hidden pdf_url default:shortcode_attr] field for the PDF to attach.',
                            options: formsLoading
                                ? [{ label: 'Loading…', value: '' }]
                                : forms.map(function (o) { return { label: o.label, value: o.value }; }),
                            onChange: function (val) {
                                var match = forms.filter(function (o) { return o.value === val; })[0];
                                setAttributes({ formId: val, formTitle: match ? match.title : '' });
                            }
                        })
                    ),
                    el(
                        PanelBody,
                        { title: 'PDF', initialOpen: true },
                        el(SelectControl, {
                            label:   'PDF source',
                            value:   pdfSource,
                            options: [
                                { label: 'Media library', value: 'media' },
                                { label: 'Document (post type)', value: 'document' }
                            ],
                            onChange: function (val) {
                                // Clear resolved values so a stale one from the
                                // other source can't leak into the shortcode.
                                setAttributes({ pdfSource: val, pdfUrl: '', pdfDocumentId: 0, speakUrl: '' });
                            }
                        }),
                        pdfSource === 'media'
                            ? el(
                                BaseControl,
                                { label: 'PDF file' },
                                el('div', null,
                                    el(MediaUploadCheck, null,
                                        el(MediaUpload, {
                                            allowedTypes: ['application/pdf'],
                                            value: undefined,
                                            onSelect: function (media) {
                                                setAttributes({ pdfUrl: media.url });
                                            },
                                            render: function (o) {
                                                return el(Button, {
                                                    variant: 'secondary',
                                                    onClick: o.open
                                                }, pdfUrl ? 'Replace PDF' : 'Select PDF');
                                            }
                                        })
                                    ),
                                    pdfUrl
                                        ? el('div', { style: { marginTop: '8px' } },
                                            el('span', { style: { fontSize: '12px', color: '#757575', wordBreak: 'break-all' } }, pdfUrl),
                                            el(Button, {
                                                variant: 'link',
                                                isDestructive: true,
                                                style: { marginLeft: '8px' },
                                                onClick: function () { setAttributes({ pdfUrl: '' }); }
                                            }, 'Remove')
                                          )
                                        : null
                                )
                              )
                            : el(SelectControl, {
                                label:   'Document',
                                value:   pdfDocumentId,
                                help:    (pdfDocumentId && !pdfUrl) ? 'This document has no PDF file attached.' : undefined,
                                options: docsLoading
                                    ? [{ label: 'Loading…', value: 0 }]
                                    : docs.map(function (o) { return { label: o.label, value: o.value }; }),
                                onChange: function (val) {
                                    var id    = parseInt(val, 10) || 0;
                                    var match = docs.filter(function (o) { return o.value === id; })[0];
                                    // Store the document's resolved PDF url and its
                                    // speak-to-an-expert url so save() emits literals.
                                    setAttributes({
                                        pdfDocumentId: id,
                                        pdfUrl:        match ? match.url : '',
                                        speakUrl:      match ? match.speakUrl : ''
                                    });
                                }
                            })
                    ),
                    el(
                        PanelBody,
                        { title: 'Speak to an Expert', initialOpen: false },
                        pdfSource === 'media'
                            ? el(TextControl, {
                                label:       'Speak to an expert URL',
                                type:        'url',
                                value:       speakUrl,
                                placeholder: 'https://…',
                                help:        'Optional custom URL. Requires a [hidden speak_to_an_expert_url default:shortcode_attr] field in the form.',
                                onChange:    function (val) { setAttributes({ speakUrl: val }); }
                            })
                            : el(
                                BaseControl,
                                {
                                    label: 'Speak to an expert URL',
                                    help:  'Taken from the selected document’s “Speak to an expert url” field. Requires a [hidden speak_to_an_expert_url default:shortcode_attr] field in the form.'
                                },
                                el('div', {
                                    style: {
                                        fontSize: '12px',
                                        color: effectiveSpeakUrl ? '#1e1e1e' : '#757575',
                                        wordBreak: 'break-all',
                                        padding: '6px 0'
                                    }
                                }, effectiveSpeakUrl ? effectiveSpeakUrl : 'This document has no “Speak to an expert url” set.')
                            )
                    )
                ),
                el(
                    'div',
                    useBlockProps({ className: 'dd-cf7-pdf-block-edit' }),
                    el('div', {
                        style: {
                            border: '1px dashed #c3c4c7',
                            borderRadius: '4px',
                            padding: '16px',
                            background: '#f6f7f7'
                        }
                    },
                        el('strong', null, '📄 Contact Form + PDF'),
                        el('div', { style: { marginTop: '6px', fontSize: '13px', color: '#1e1e1e' } },
                            formTitle ? 'Form: ' + formTitle : 'No form selected'),
                        el('div', { style: { marginTop: '2px', fontSize: '13px', color: '#1e1e1e' } },
                            pdfSummary()),
                        el('div', { style: { marginTop: '2px', fontSize: '13px', color: '#1e1e1e' } },
                            effectiveSpeakUrl ? 'Speak to an expert: ' + effectiveSpeakUrl : 'No speak-to-an-expert URL'),
                        el('div', {
                            style: {
                                marginTop: '12px',
                                paddingTop: '10px',
                                borderTop: '1px solid #e0e0e0',
                                fontSize: '12px',
                                color: '#757575'
                            }
                        },
                            'For these values to reach the form, the selected Contact Form 7 form must include the matching hidden field(s):',
                            el('code', { style: codeBoxStyle }, '[hidden pdf_url default:shortcode_attr]'),
                            el('code', { style: codeBoxStyle }, '[hidden speak_to_an_expert_url default:shortcode_attr]')
                        )
                    )
                )
            );
        },

        // Rendered server-side: the dd_render_cf7_pdf_block() `render_block` filter
        // in functions.php rebuilds the shortcode from the attributes and runs it
        // through do_shortcode(). Nothing needs to be written to the block markup.
        save: function () { return null; }
    });

})(window.wp);
