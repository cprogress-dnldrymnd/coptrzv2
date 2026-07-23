/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * * Adds an optional link to core/group so the whole block becomes clickable
 * * on the front end (rendered as a Bootstrap `.stretched-link` overlay by
 * * dd_group_link_render() in functions.php).
 * * Encapsulated in an IIFE to prevent global window namespace collisions.
 */
(function (wp) {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment, createElement: el } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, ToggleControl } = wp.components;

    const GROUP_BLOCK = 'core/group';

    /**
     * 1. Register Attributes
     */
    function addGroupLinkAttributes(settings, name) {
        if (name === GROUP_BLOCK) {
            settings.attributes = Object.assign(settings.attributes || {}, {
                ddGroupLinkUrl: { type: 'string', default: '' },
                ddGroupLinkNewTab: { type: 'boolean', default: false },
                ddGroupLinkLabel: { type: 'string', default: '' }
            });
        }
        return settings;
    }
    addFilter('blocks.registerBlockType', 'digitally-disruptive/group-link-attrs', addGroupLinkAttributes);

    /**
     * 2. Inject the Controls into the Block Sidebar
     */
    const addGroupLinkUI = createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            const { name, attributes, setAttributes, clientId } = props;

            if (name !== GROUP_BLOCK) {
                return el(BlockEdit, props);
            }

            const blockSelector = `#block-${clientId}`;
            const livePreviewCSS = attributes.ddGroupLinkUrl
                ? `${blockSelector} { cursor: pointer; }\n`
                : '';

            return el(Fragment, {},
                livePreviewCSS ? el('style', null, livePreviewCSS) : null,
                el(BlockEdit, props),
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Group Link', initialOpen: false },
                        el(TextControl, {
                            label: 'Link URL',
                            help: 'Makes the entire group clickable, linking to this URL.',
                            type: 'url',
                            value: attributes.ddGroupLinkUrl,
                            onChange: function (val) { setAttributes({ ddGroupLinkUrl: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Open in new tab',
                            checked: attributes.ddGroupLinkNewTab,
                            onChange: function (val) { setAttributes({ ddGroupLinkNewTab: val }); }
                        }),
                        el(TextControl, {
                            label: 'Accessible label',
                            help: 'Optional. Describes the link for screen readers (e.g. "Learn more about X"). Recommended when the group has no visible link text.',
                            value: attributes.ddGroupLinkLabel,
                            onChange: function (val) { setAttributes({ ddGroupLinkLabel: val }); }
                        })
                    )
                )
            );
        };
    }, 'addGroupLinkUI');
    addFilter('editor.BlockEdit', 'digitally-disruptive/group-link-ui', addGroupLinkUI);
})(window.wp);
