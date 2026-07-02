/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `dd/cf7-pdf-form` block: a native Gutenberg equivalent of hand
 * typing `[contact-form-7 id="…" pdf_url="…"]` in a Shortcode block. The editor
 * picks a CF7 form from a dropdown and chooses a PDF either from the media
 * library or from the Documents post type.
 *
 * This is a STATIC block: save() emits the literal shortcode into the post
 * content, exactly like a native Shortcode block. That way the stored markup —
 * and therefore the Dynamic Text Extension `pdf_url` field and the theme's
 * existing pdf_url resolution — sees the same input as a hand-typed shortcode.
 * The chosen PDF is always stored as a literal URL (a Document is resolved to
 * its file URL at selection time via the /dd/v1/documents endpoint).
 */
(function (wp) {

    const { registerBlockType }                                = wp.blocks;
    const { createElement: el, useState, useEffect, Fragment, RawHTML } = wp.element;
    const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, SelectControl, Button, BaseControl }    = wp.components;

    function buildShortcode(a) {
        if (!a.formId) { return ''; }
        var sc = '[contact-form-7 id="' + a.formId + '"';
        if (a.formTitle) { sc += ' title="' + a.formTitle + '"'; }
        if (a.pdfUrl)    { sc += ' pdf_url="' + a.pdfUrl + '"'; }
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
            pdfDocumentId: { type: 'number', default: 0 }
        },

        // Migrate instances created under the earlier dynamic version (which
        // saved an empty block comment). Without this they'd be flagged invalid
        // and keep their empty markup — so no shortcode would ever be output.
        deprecated: [
            {
                attributes: {
                    formId:        { type: 'string', default: '' },
                    formTitle:     { type: 'string', default: '' },
                    pdfSource:     { type: 'string', default: 'media' },
                    pdfUrl:        { type: 'string', default: '' },
                    pdfDocumentId: { type: 'number', default: 0 }
                },
                save: function () { return null; }
            }
        ],

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { formId, formTitle, pdfSource, pdfUrl, pdfDocumentId } = attributes;

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

            // Fetch Documents once (each carries its resolved PDF url).
            useEffect(function () {
                wp.apiFetch({ path: '/dd/v1/documents' })
                    .then(function (items) {
                        var options = [{ label: '— Select a document —', value: 0, url: '' }].concat(
                            (items || []).map(function (d) {
                                return { label: d.title, value: d.id, url: d.url || '' };
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
                                // Clear the resolved URL so a stale value from the
                                // other source can't leak into the shortcode.
                                setAttributes({ pdfSource: val, pdfUrl: '', pdfDocumentId: 0 });
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
                                    // Store the document's resolved URL so save()
                                    // always emits a literal pdf_url.
                                    setAttributes({ pdfDocumentId: id, pdfUrl: match ? match.url : '' });
                                }
                            })
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
                            pdfSummary())
                    )
                )
            );
        },

        // Static block: emit the literal CF7 shortcode into the post content,
        // exactly like a native Shortcode block.
        save: function (props) {
            var sc = buildShortcode(props.attributes);
            return sc ? el(RawHTML, null, sc) : null;
        }
    });

})(window.wp);
