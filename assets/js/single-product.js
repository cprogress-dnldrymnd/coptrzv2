jQuery(document).ready(function () {
    product_gallery();
    product_variation();
    paypal();
});

function paypal() {
    setTimeout(function () {
        jQuery('.ppcp-messages').insertAfter('.summary .price');
    }, 500);
}

function product_variation() {

    jQuery('input[name="variation-radio"]').change(function (e) {
        $value = jQuery(this).val();
        jQuery('input[name="variation_id"]').val($value);

        setTimeout(function () {
            jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
        }, 500);

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