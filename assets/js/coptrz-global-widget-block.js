/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/global-widget` block: a native Gutenberg equivalent of
 * hand typing one of the "Global Widgets" shortcodes (`[brands_logo_slider]`,
 * `[case_study_slider_grid]`, `[testimonials]`, …) in a Shortcode block. These
 * widgets take no content of their own — their content lives centrally (brand
 * logos in the `pa_brands` taxonomy, featured case studies in a theme option),
 * so the editor only needs to pick which widget renders here.
 *
 * The widget list is localized as `coptrzGlobalWidgets` (see
 * coptrz_global_widgets() in functions.php) rather than fetched over REST — it's
 * static PHP data, and localizing keeps one source of truth shared with the
 * section-converter's `global_widgets` mapper.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_global_widget_block() `render_block` filter in functions.php,
 * which rebuilds the shortcode from the stored `widget` (and, for the Case Study
 * Slider only, `style`) attribute and runs do_shortcode().
 */
(function (wp) {

    const { registerBlockType }                    = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps }      = wp.blockEditor;
    const { PanelBody, SelectControl, Placeholder }  = wp.components;

    const UI = window.coptrzBlockUI || {};
    var WIDGETS = window.coptrzGlobalWidgets || {};

    registerBlockType('coptrz/global-widget', {
        title:    'Global Widget (Legacy)',
        icon:     'slides',
        category: 'design',
        description: 'Embeds a Global Widget (brands slider, case study slider, testimonials, …).',
        attributes: {
            widget: { type: 'string', default: '' },
            style:  { type: 'string', default: '' }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { widget, style } = attributes;
            const [mode, setMode] = useState(widget ? 'preview' : 'edit');

            var widgetOptions = [{ label: '— Select a widget —', value: '' }].concat(
                Object.keys(WIDGETS).map(function (slug) {
                    return { label: WIDGETS[slug].label || slug, value: slug };
                })
            );

            var current    = WIDGETS[widget];
            var styleChoices = current && current.styles ? current.styles : null;

            var emptyPlaceholder = el(Placeholder, {
                icon:  'slides',
                label: 'Global Widget',
                instructions: current
                    ? 'Widget: ' + current.label + (style ? ' (' + (styleChoices ? styleChoices[style] : style) + ')' : '')
                    : 'Select a widget from the block settings.'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Global Widget', initialOpen: true },
                        el(SelectControl, {
                            label:   'Widget',
                            value:   widget,
                            options: widgetOptions,
                            onChange: function (val) {
                                // Reset style when switching widgets so a stale
                                // value from a previous selection can't leak in.
                                setAttributes({ widget: val, style: '' });
                            }
                        }),
                        styleChoices
                            ? el(SelectControl, {
                                label:   'Style',
                                value:   style,
                                options: Object.keys(styleChoices).map(function (val) {
                                    return { label: styleChoices[val], value: val };
                                }),
                                onChange: function (val) { setAttributes({ style: val }); }
                            })
                            : null
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/global-widget', attributes: attributes, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        // Rendered server-side: coptrz_render_global_widget_block() rebuilds the
        // shortcode from the widget/style attributes.
        save: function () { return null; }
    });

})(window.wp);
