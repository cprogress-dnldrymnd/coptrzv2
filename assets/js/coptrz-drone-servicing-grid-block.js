/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/drone-servicing-grid` block: native editor equivalent
 * of the legacy section builder's Drone Servicing Grid item. save() returns
 * null — rendered server-side by coptrz_render_drone_servicing_grid_block()
 * (includes/legacy-blocks.php), which rebuilds the row shape __drone_servicing()
 * (includes/woocommerce.php) expects and calls it directly.
 *
 * Each drone's specs are a FIXED set of four (drone/battery/controller/payload),
 * not a free repeater — the legacy field uses
 * `set_duplicate_groups_allowed(false)` over exactly those four named groups
 * (post-meta.php), so a spec is either present (optionally with a custom
 * "quantity" value shown instead of a checkmark) or absent entirely (shown as
 * an X in __drone_servicing()'s comparison table) — see the `enabled` flag
 * below, which the render filter uses to decide whether to emit that spec's
 * `_type` row at all.
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    var SPEC_TYPES = [
        { key: 'drone', label: 'Drone' },
        { key: 'battery', label: 'Battery' },
        { key: 'controller', label: 'Controller' },
        { key: 'payload', label: 'Payload' }
    ];

    function defaultFeatures() {
        var f = {};
        SPEC_TYPES.forEach(function (s) { f[s.key] = { enabled: false, quantity: '' }; });
        return f;
    }

    function defaultDrone() {
        return { serviceName: 'New Service', serviceSubheading: '', servicePrice: '', features: defaultFeatures() };
    }

    registerBlockType('coptrz/drone-servicing-grid', {
        title:    'Drone Servicing Grid (Legacy)',
        icon:     'grid-view',
        category: 'design',
        description: 'A drone servicing comparison grid — native equivalent of the section builder\'s Drone Servicing Grid item.',
        supports: { html: false, reusable: false },
        attributes: {
            heading:     { type: 'string', default: '' },
            description: { type: 'string', default: '' },
            drones:      { type: 'array', default: [] } // [{serviceName, serviceSubheading, servicePrice, features:{drone:{enabled,quantity}, ...}}]
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const a = attributes;

            return el(
                'div',
                useBlockProps(),
                el(
                    InspectorControls,
                    null,
                    el(PanelBody, { title: 'Drone Servicing Grid Settings', initialOpen: true },
                        UI.textField('Heading', a.heading, function (v) { setAttributes({ heading: v }); }),
                        UI.textField('Description', a.description, function (v) { setAttributes({ description: v }); })
                    )
                ),
                a.drones.length === 0
                    ? el(Placeholder, { icon: 'grid-view', label: 'Drone Servicing Grid (Legacy)', instructions: 'Add drone services below.' })
                    : null,
                el(UI.Repeater, {
                    items: a.drones,
                    onChange: function (next) { setAttributes({ drones: next }); },
                    defaultItem: defaultDrone,
                    addLabel: '+ Add Service',
                    rowLabel: function (item) { return item.serviceName || 'Service'; },
                    renderRow: function (item, idx, update) {
                        var features = item.features || defaultFeatures();
                        function updateFeature(key, patch) {
                            var next = Object.assign({}, features, { [key]: Object.assign({}, features[key], patch) });
                            update({ features: next });
                        }
                        return el(Fragment, null,
                            UI.textField('Service Name', item.serviceName, function (v) { update({ serviceName: v }); }),
                            UI.textField('Service Subheading', item.serviceSubheading, function (v) { update({ serviceSubheading: v }); }),
                            UI.textField('Service Price', item.servicePrice, function (v) { update({ servicePrice: v }); }),
                            el('div', { style: { marginTop: '8px', borderTop: '1px solid #eee', paddingTop: '8px' } },
                                el('strong', null, 'Specs'),
                                SPEC_TYPES.map(function (s) {
                                    var f = features[s.key] || { enabled: false, quantity: '' };
                                    return el('div', { key: s.key, style: { display: 'flex', alignItems: 'center', gap: '8px', marginTop: '6px' } },
                                        UI.boolField(s.label, f.enabled, function (v) { updateFeature(s.key, { enabled: v }); }),
                                        f.enabled ? UI.textField('Custom Text (optional)', f.quantity, function (v) { updateFeature(s.key, { quantity: v }); }) : null
                                    );
                                })
                            )
                        );
                    }
                })
            );
        },

        save: function () { return null; }
    });

})(window.wp);
