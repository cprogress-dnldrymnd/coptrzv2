/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Registers the `coptrz/header-icons` block: a native Gutenberg equivalent of
 * the account/academy/cart/mobile-burger icon cluster in
 * template-parts/header/header-right.php. Each icon is individually
 * toggleable so the block can be reused for a partial cluster.
 *
 * save() returns null — the block is rendered server-side by the
 * coptrz_render_header_icons_block() `render_block` filter in
 * includes/header-blocks.php, which calls the shared
 * coptrz_header_icons_html() — the SAME function header-right.php itself
 * calls, so this block can never drift from the real header.
 */
(function (wp) {

    const { registerBlockType }               = wp.blocks;
    const { createElement: el, Fragment }     = wp.element;
    const { InspectorControls, useBlockProps } = wp.blockEditor;
    const { PanelBody, ToggleControl, Placeholder } = wp.components;

    registerBlockType('coptrz/header-icons', {
        title:    'Header — Icons',
        icon:     'admin-users',
        category: 'design',
        description: 'Embeds the account / academy / cart / mobile-menu icon cluster from the header.',
        attributes: {
            showAccount: { type: 'boolean', default: true },
            showAcademy: { type: 'boolean', default: true },
            showCart:    { type: 'boolean', default: true },
            showBurger:  { type: 'boolean', default: true }
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { showAccount, showAcademy, showCart, showBurger } = attributes;

            var enabled = [];
            if (showAccount) { enabled.push('Account'); }
            if (showAcademy) { enabled.push('Academy'); }
            if (showCart)    { enabled.push('Cart'); }
            if (showBurger)  { enabled.push('Mobile Menu Burger'); }

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Icons', initialOpen: true },
                        el(ToggleControl, {
                            label: 'My Account icon',
                            checked: !!showAccount,
                            onChange: function (val) { setAttributes({ showAccount: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Coptrz Academy icon',
                            checked: !!showAcademy,
                            onChange: function (val) { setAttributes({ showAcademy: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Rentals cart button',
                            checked: !!showCart,
                            help: 'Only renders on rentals posts / specific landing pages, same as the header.',
                            onChange: function (val) { setAttributes({ showCart: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Mobile menu burger',
                            checked: !!showBurger,
                            onChange: function (val) { setAttributes({ showBurger: val }); }
                        })
                    )
                ),
                el(
                    'div',
                    useBlockProps(),
                    el(Placeholder, {
                        icon:  'admin-users',
                        label: 'Header Icons',
                        instructions: enabled.length ? ('Showing: ' + enabled.join(', ')) : 'No icons enabled — select at least one in the block settings.'
                    })
                )
            );
        },

        save: function () { return null; }
    });

})(window.wp);
