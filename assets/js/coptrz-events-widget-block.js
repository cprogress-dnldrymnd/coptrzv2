/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/events-widget` block: a native editor placeholder for
 * the legacy section builder's Events Widget item (currently a single
 * "countdown" sub-widget). save() returns null — rendered server-side by
 * coptrz_render_events_widget_block() (includes/legacy-blocks.php), which
 * mirrors the `case 'events_widget':` loop in modules.php and runs
 * `[event_countdown]` through do_shortcode().
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/events-widget', {
        title:    'Events Widget (Legacy)',
        icon:     'clock',
        category: 'design',
        description: 'Frozen legacy Events Widget item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            legacy: { type: 'object' }
        },

        edit: function (props) {
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'clock',
                    label: 'Events Widget (Legacy)',
                    instructions: 'Event countdown — reads the current event post\'s start/end date.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
