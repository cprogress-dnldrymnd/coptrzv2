/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Extends the core Button block with an option to open a popup modal.
 * If multiple buttons on the same page target the same popup, only one
 * modal instance is rendered in the DOM (deduplication handled server-side).
 */
(function (wp) {

    const { addFilter }                             = wp.hooks;
    const { createHigherOrderComponent }            = wp.compose;
    const { InspectorControls }                     = wp.blockEditor;
    const { PanelBody, SelectControl }              = wp.components;
    const { createElement: el, useState, useEffect, Fragment } = wp.element;

    const TARGET_BLOCK = 'core/button';

    // ── 1. Register the ddPopupId attribute on core/button ────────────────────
    function addPopupAttribute(settings, name) {
        if (name !== TARGET_BLOCK) return settings;
        settings.attributes = Object.assign(settings.attributes || {}, {
            ddPopupId: { type: 'number', default: 0 }
        });
        return settings;
    }
    addFilter('blocks.registerBlockType', 'dd/button-popup-attr', addPopupAttribute);

    // ── 2. Add InspectorControls panel for popup selection ────────────────────
    const withPopupControl = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (props.name !== TARGET_BLOCK) return el(BlockEdit, props);

            const { attributes, setAttributes } = props;
            const { ddPopupId } = attributes;

            const [popups,  setPopups]  = useState([]);
            const [loading, setLoading] = useState(true);

            useEffect(function () {
                wp.apiFetch({ path: '/wp/v2/popups?per_page=100&status=publish&_fields=id,title' })
                    .then(function (posts) {
                        var options = [{ label: '— None —', value: 0 }].concat(
                            posts.map(function (p) {
                                // strip any HTML tags that may appear in rendered titles
                                var tmp = document.createElement('div');
                                tmp.innerHTML = p.title.rendered;
                                return { label: tmp.textContent || tmp.innerText, value: p.id };
                            })
                        );
                        setPopups(options);
                        setLoading(false);
                    })
                    .catch(function () { setLoading(false); });
            }, []);

            return el(
                Fragment,
                null,
                el(BlockEdit, props),
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Open Popup', initialOpen: false },
                        el(SelectControl, {
                            label:    'Popup to open',
                            value:    ddPopupId,
                            options:  loading
                                ? [{ label: 'Loading…', value: 0 }]
                                : popups,
                            onChange: function (val) {
                                setAttributes({ ddPopupId: parseInt(val, 10) || 0 });
                            }
                        }),
                        ddPopupId > 0
                            ? el(
                                'p',
                                { style: { fontSize: '12px', color: '#757575', marginTop: '8px' } },
                                'The button’s link URL will be ignored — clicking will open the selected popup instead. ' +
                                'Multiple buttons targeting the same popup share a single modal instance.'
                            )
                            : null
                    )
                )
            );
        };
    }, 'withPopupControl');
    addFilter('editor.BlockEdit', 'dd/button-popup-control', withPopupControl);

})(window.wp);
