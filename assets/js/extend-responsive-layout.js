/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Injects responsive layout controls into core blocks:
 * * - core/columns: "Stack on tablet" (768-991px band; core's own
 * *   "Stack on mobile" already covers <=781px).
 * * - core/group (Grid layout variation): "Max columns" overrides for
 * *   tablet (<=991px) and mobile (<=767px).
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl } = wp.components;

    const COLUMNS_BLOCK = 'core/columns';
    const GROUP_BLOCK = 'core/group';

    /**
     * 1. Register Attributes
     */
    function addResponsiveLayoutAttributes(settings, name) {
        if (name === COLUMNS_BLOCK) {
            settings.attributes = Object.assign(settings.attributes || {}, {
                ddStackOnTablet: { type: 'boolean', default: false }
            });
        } else if (name === GROUP_BLOCK) {
            settings.attributes = Object.assign(settings.attributes || {}, {
                ddGridColumnsTablet: { type: 'string', default: '' },
                ddGridColumnsMobile: { type: 'string', default: '' }
            });
        }
        return settings;
    }
    addFilter('blocks.registerBlockType', 'digitally-disruptive/responsive-layout-attrs', addResponsiveLayoutAttributes);

    /**
     * 2. Inject the Controls into the Block Sidebar
     */
    const addResponsiveLayoutUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            const { name, attributes, setAttributes, clientId } = props;
            const blockSelector = `#block-${clientId}`;

            if (name === COLUMNS_BLOCK) {
                const livePreviewCSS = attributes.ddStackOnTablet
                    ? `@media (min-width: 768px) and (max-width: 991px) {\n${blockSelector} { flex-wrap: wrap !important; }\n${blockSelector} > .wp-block-column { flex-basis: 100% !important; }\n}\n`
                    : '';

                return el(Fragment, {},
                    livePreviewCSS ? el('style', null, livePreviewCSS) : null,
                    el(BlockEdit, props),
                    el(InspectorControls, {},
                        el(PanelBody, { title: 'Responsive Layout', initialOpen: false },
                            el(ToggleControl, {
                                label: 'Stack on tablet',
                                help: 'Stacks columns to full width between 768px and 991px.',
                                checked: attributes.ddStackOnTablet,
                                onChange: function (val) { setAttributes({ ddStackOnTablet: val }); }
                            })
                        )
                    )
                );
            }

            if (name === GROUP_BLOCK && attributes.layout && attributes.layout.type === 'grid') {
                let livePreviewCSS = '';
                if (attributes.ddGridColumnsTablet) {
                    livePreviewCSS += `@media (max-width: 991px) { ${blockSelector} { grid-template-columns: repeat(${parseInt(attributes.ddGridColumnsTablet, 10)}, minmax(0, 1fr)) !important; } }\n`;
                }
                if (attributes.ddGridColumnsMobile) {
                    livePreviewCSS += `@media (max-width: 767px) { ${blockSelector} { grid-template-columns: repeat(${parseInt(attributes.ddGridColumnsMobile, 10)}, minmax(0, 1fr)) !important; } }\n`;
                }

                return el(Fragment, {},
                    livePreviewCSS ? el('style', null, livePreviewCSS) : null,
                    el(BlockEdit, props),
                    el(InspectorControls, {},
                        el(PanelBody, { title: 'Responsive Grid Columns', initialOpen: false },
                            el(TextControl, {
                                label: 'Max columns (Tablet)',
                                help: 'Columns at 991px and below. Leave blank to inherit the desktop value.',
                                type: 'number',
                                min: 1,
                                value: attributes.ddGridColumnsTablet,
                                onChange: function (val) { setAttributes({ ddGridColumnsTablet: val }); }
                            }),
                            el(TextControl, {
                                label: 'Max columns (Mobile)',
                                help: 'Columns at 767px and below. Leave blank to inherit the desktop value.',
                                type: 'number',
                                min: 1,
                                value: attributes.ddGridColumnsMobile,
                                onChange: function (val) { setAttributes({ ddGridColumnsMobile: val }); }
                            })
                        )
                    )
                );
            }

            return el(BlockEdit, props);
        };
    }, 'addResponsiveLayoutUI');
    addFilter('editor.BlockEdit', 'digitally-disruptive/responsive-layout-ui', addResponsiveLayoutUI);
})(window.wp);
