/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/events-widget` block: native editor equivalent of the
 * legacy section builder's Events Widget item (currently a single "countdown"
 * sub-widget). save() returns null — rendered server-side by
 * coptrz_render_events_widget_block() (includes/legacy-blocks.php), which runs
 * `[event_countdown]` through do_shortcode() when enabled.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    registerBlockType('coptrz/events-widget', {
        title:    'Events Widget (Legacy)',
        icon:     'clock',
        category: 'design',
        description: 'An event countdown timer — native equivalent of the section builder\'s Events Widget item.',
        supports: { html: false, reusable: false },
        attributes: {
            showCountdown: { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const [mode, setMode] = useState(attributes.showCountdown ? 'preview' : 'edit');

            const emptyPlaceholder = el(Placeholder, {
                icon:  'clock',
                label: 'Events Widget (Legacy)',
                instructions: attributes.showCountdown
                    ? 'Event countdown — reads the current event post\'s start/end date.'
                    : 'Countdown is hidden.'
            });

            return el(
                Fragment,
                null,
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Events Widget Settings', initialOpen: true },
                        UI.boolField('Show Countdown', attributes.showCountdown, function (v) { setAttributes({ showCountdown: v }); })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    mode === 'preview'
                        ? el(UI.LivePreview, { name: 'coptrz/events-widget', attributes: attributes, placeholder: emptyPlaceholder })
                        : emptyPlaceholder
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
