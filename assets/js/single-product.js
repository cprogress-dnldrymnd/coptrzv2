jQuery(document).ready(function () {
    product_gallery();
    product_variation();
});

function product_variation() {

    jQuery('input[name="variation-radio"]').change(function (e) {
        $value = jQuery(this).val();
        $data_variations = jQuery(this).attr('data_variations');

        jQuery('input[name="variation_id"]').val($value);
        jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');


        $variations = JSON.parse($data_variations);
        jQuery.each($variations, function ($variation_name, $variation_val) {
            jQuery('#' + $variation_name).val($variation_val);
        });


        e.preventDefault();
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