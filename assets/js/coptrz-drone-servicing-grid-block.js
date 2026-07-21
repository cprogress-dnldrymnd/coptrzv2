/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/drone-servicing-grid` block: a native editor placeholder
 * for the legacy section builder's Drone Servicing Grid item. save() returns
 * null — rendered server-side by coptrz_render_drone_servicing_grid_block()
 * (includes/legacy-blocks.php), which calls __drone_servicing() (woocommerce.php)
 * directly, the same function ___sections() uses. The stored `legacy.servicing_drones`
 * array must keep each row's `_type` key intact (drone/battery/controller/payload
 * feature groups) — __drone_servicing() dispatches on it.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { useBlockProps }     = wp.blockEditor;
    const { Placeholder }       = wp.components;

    registerBlockType('coptrz/drone-servicing-grid', {
        title:    'Drone Servicing Grid (Legacy)',
        icon:     'grid-view',
        category: 'design',
        description: 'Frozen legacy Drone Servicing Grid item — rendered by the original renderer, not natively editable.',
        supports: { html: false, reusable: false },
        attributes: {
            legacy: { type: 'object' }
        },

        edit: function (props) {
            const { attributes } = props;
            const drones = (attributes.legacy && attributes.legacy.servicing_drones) || [];
            return el(
                'div',
                useBlockProps(),
                el(Placeholder, {
                    icon:  'grid-view',
                    label: 'Drone Servicing Grid (Legacy)',
                    instructions: drones.length
                        ? drones.length + ' service(s): ' + drones.map(function (d) { return d.service_name; }).join(', ')
                        : 'Legacy drone servicing grid — content managed elsewhere.'
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
