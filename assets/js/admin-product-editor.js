/**
 * @package   DigitallyDisruptive
 * @author    Digitally Disruptive - Donald Raymundo
 * @link      https://digitallydisruptive.co.uk/
 * Product-editor-only fixes needed after enabling the block editor for
 * `product` (coptrz_enable_product_block_editor(), includes/woocommerce.php).
 * Enqueued only on the product edit screen — see coptrz_enqueue_product_editor_assets().
 *
 * 1) Variation edits are lost on Update. WooCommerce's variations metabox
 *    (assets/js/admin/meta-boxes-product-variation.js) binds its pre-save AJAX
 *    to `form#post`, which does not exist in the block editor, and
 *    save_variations() is otherwise only ever called from that box's own AJAX
 *    handler (never from the metabox POST). So an edited-but-unsaved variation
 *    is silently discarded when the post is saved from the block editor. Fixed
 *    by locking post saving while any `.woocommerce_variation` carries
 *    WooCommerce's own `variation-needs-update` class (the same class it adds
 *    on edit and removes on successful AJAX save), with a notice pointing the
 *    user at the panel's own "Save changes" button.
 *
 * 2) `product` supports `excerpt`, so Gutenberg adds its own Excerpt sidebar
 *    panel alongside WooCommerce's "Product short description" TinyMCE box —
 *    both write post_excerpt, and the metabox POST (which runs after the REST
 *    save) always wins, silently discarding anything typed in the sidebar
 *    panel. Removed here so WooCommerce's box is the only writer.
 */
(function (wp, jQuery) {

    const { dispatch, select } = wp.data;
    const { __ } = wp.i18n;

    if (select('core/editor')) {
        dispatch('core/editor').removeEditorPanel('post-excerpt');
    }

    if (!jQuery) {
        return;
    }

    const LOCK_NAME = 'coptrz-wc-variations';
    const NOTICE_ID = 'coptrz-wc-variations-dirty';

    function variationsAreDirty() {
        return jQuery('.woocommerce_variation.variation-needs-update').length > 0;
    }

    function reconcileLock() {
        const editor = dispatch('core/editor');
        const notices = dispatch('core/notices');
        if (!editor || !notices) {
            return;
        }

        if (variationsAreDirty()) {
            editor.lockPostSaving(LOCK_NAME);
            notices.createNotice(
                'warning',
                __('Save your variation changes in the Variations panel before updating this product.', 'coptrz-theme'),
                { id: NOTICE_ID, isDismissible: false }
            );
        } else {
            editor.unlockPostSaving(LOCK_NAME);
            notices.removeNotice(NOTICE_ID);
        }
    }

    // All four are jQuery .trigger() calls fired on/above #woocommerce-product-data
    // by meta-boxes-product-variation.js, so one delegated binding catches every
    // case: initial load, a field edited, the bulk-defaults dropdown used, and a
    // successful "Save changes". Reconciling from the DOM class each time (rather
    // than tracking a boolean) means add/delete/reload of variations can't desync
    // the lock.
    jQuery('#woocommerce-product-data').on(
        'woocommerce_variations_input_changed woocommerce_variations_defaults_changed woocommerce_variations_loaded woocommerce_variations_saved',
        reconcileLock
    );

})(window.wp, window.jQuery);
