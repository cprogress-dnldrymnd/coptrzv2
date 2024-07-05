jQuery(document).ready(function () {
    __product_gallery();
    __product_variation();
    __move_paypal();
    __quantity();
});

function __quantity() {
    jQuery('.quantity-minus').click(function () {
        var qtyInput = $(this).siblings('.quantity-input');
        var qty = parseInt(qtyInput.val());
        if (qty > 1) {
            qtyInput.val(qty - 1);
        }
    });

    jQuery('.quantity-plus').click(function () {
        var qtyInput = $(this).siblings('.quantity-input');
        var qty = parseInt(qtyInput.val());
        qtyInput.val(qty + 1);
    });
}
function __move_paypal() {
    setTimeout(function () {
        jQuery('.ppcp-messages').insertAfter('.summary > .price');
    }, 1000);
}

function __product_variation() {

    jQuery('input[name="variation-radio"]').change(function (e) {
        $value = jQuery(this).val();
        jQuery('input[name="variation_id"]').val($value);

        setTimeout(function () {
            jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
        }, 500);

        e.preventDefault();
    });
}

function __product_gallery() {

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