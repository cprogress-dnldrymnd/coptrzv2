jQuery(document).ready(function () {
    product_gallery();
    product_variation();
});

function product_variation() {

    // Check if radio input has changed
    jQuery('.radio-variations input[type="radio"]').change(function () {

        // Get the parent div element (radio-variations)
        let parentDiv = jQuery(this).closest('.radio-variations');

        // Get the product ID
        let product_id = parentDiv.data('product_id');

        // Get the attribute name
        let attribute_name = parentDiv.data('attribute_name');

        // Get the selected variation value (the value of the radio input)
        let selected_variation_value = jQuery(this).val();

        // Get all select elements in the parent form that have the attribute name
        let select_elements = jQuery(this).closest('form').find('select[name^="attribute_"]');

        // Filter to find the specific select element that matches the attribute name
        let attribute_select = select_elements.filter('[name="attribute_' + attribute_name + '"]');

        // Set the value of the select element to the selected variation value (update dropdown)
        attribute_select.val(selected_variation_value).trigger('change');

        // Get the selected variation ID
        let selected_variation_id = jQuery(this).data('variation_id');

        // Set the hidden input value to the selected variation ID
        $('input[name="variation_id"]').val(selected_variation_id);

        // Trigger WooCommerce events to update the displayed price and availability
        jQuery(this).closest('form').trigger('woocommerce_variation_select_change');
        jQuery(this).closest('form').find('input[name="variation_id"]').trigger('change');
    });

}

function product_gallery() {

    var product_thumb = new Swiper('.product-thumb', {
        loop: true,
        autoplay: false,
        spaceBetween: 10,
        breakpoints: {
            0: {
                slidesPerView: 3,
            },

            768: {
                slidesPerView: 4,
            },


            992: {
                slidesPerView: 5,
            },

            1200: {
                slidesPerView: 6,
            },

        },
    });

    var product_main_image = new Swiper('.product-main-image', {
        loop: true,
        autoplay: false,
        slidesPerView: 1,
        thumbs: {
            swiper: product_thumb,
        },
    });

} 