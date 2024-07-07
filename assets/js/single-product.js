jQuery(document).ready(function () {
    __product_gallery();
    __product_variation();
    __move_paypal();
    __quantity();
    __swiper_sliders();
});

function __swiper_sliders() {
    jQuery('.swiper-linked-products').each(function (index, element) {
        $id = '#' + jQuery(this).attr('id');

        var swiper_linked_products = new Swiper($id, {
            loop: true,
            spaceBetween: 20,
            autoplay: false,
            breakpoints: {
                0: {
                    slidesPerView: 2,
                },

                768: {
                    slidesPerView: 3,
                },


                992: {
                    slidesPerView: 4,
                },


                1200: {
                    slidesPerView: 4.5
                },
            },
            navigation: {
                nextEl: "#swiper-next-" + $id,
                prevEl: "#swiper-prev-" + $id
            },
        });

    });

}
function __quantity() {
    jQuery('form.cart').on('click', 'button.plus, button.minus', function () {
        var qty = jQuery(this).closest('form.cart').find('.qty');
        var val = parseFloat(qty.val());
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var step = parseFloat(qty.attr('step'));
        if (jQuery(this).is('.plus')) {
            if (max && (max <= val)) {
                qty.val(max);
            } else {
                qty.val(val + step);
            }
        } else {
            if (min && (min >= val)) {
                qty.val(min);
            } else if (val > 1) {
                qty.val(val - step);
            }
        }
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