/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/spec-box-legacy` block: native editor equivalent of the
 * legacy section builder's Spec Box column item (a Bootstrap row of label/value
 * spec cells). save() returns null — rendered server-side by
 * coptrz_render_spec_box_legacy_block() (includes/legacy-blocks.php), which
 * calls __spec_box_module() (modules.php, extracted from the legacy
 * ____columns_modules() switch when this block was added).
 */
(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { createElement: el, Fragment, useState } = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, Placeholder } = wp.components;

    const UI = window.coptrzBlockUI || {};

    function defaultSpec() {
        return { label: 'Spec', value: '' };
    }

    registerBlockType('coptrz/spec-box-legacy', {
        title:    'Spec Box (Legacy)',
        icon:     'editor-table',
        category: 'design',
        description: 'A row of label/value spec cells — native equivalent of the section builder\'s Spec Box item.',
        supports: { html: false, reusable: false },
        attributes: {
            specs: { type: 'array', default: [] } // [{label, value}]
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const specs = attributes.specs;
            const [mode, setMode] = useState(specs.length === 0 ? 'edit' : 'preview');

            const emptyPlaceholder = el(Placeholder, {
                icon: 'editor-table',
                label: 'Spec Box (Legacy)',
                instructions: 'Add spec rows below.'
            });

            return el('div', useBlockProps(),
                el(UI.PreviewToggle, { mode: mode, setMode: setMode }),
                el(InspectorControls, null,
                    el(PanelBody, { title: 'Spec Box', initialOpen: true },
                        el('p', null, 'Manage spec rows in the block canvas.')
                    )
                ),
                mode === 'preview'
                    ? el(UI.LivePreview, { name: 'coptrz/spec-box-legacy', attributes: attributes, placeholder: emptyPlaceholder })
                    : el(Fragment, null,
                        specs.length === 0 ? emptyPlaceholder : null,
                        el(UI.Repeater, {
                            items: specs,
                            onChange: function (next) { setAttributes({ specs: next }); },
                            defaultItem: defaultSpec,
                            addLabel: '+ Add Spec',
                            rowLabel: function (item) { return item.label || 'Spec'; },
                            renderRow: function (item, idx, update) {
                                return el('div', null,
                                    UI.textField('Spec Label', item.label, function (v) { update({ label: v }); }),
                                    UI.textField('Spec Value', item.value, function (v) { update({ value: v }); })
                                );
                            }
                        })
                    )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
