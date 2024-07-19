jQuery(document).ready(function () {
    __product_gallery();
    __product_variation();
    __move_paypal();
    __quantity();
    __training();
});

function __training() {
    __training_swiper();

    jQuery('.trigger-training-ajax').click(function (e) {
        __training_ajax();
    });

    jQuery('.trigger-training-ajax-select').change(function (e) {
        __training_ajax();
    });

    jQuery('.trigger-location-change').click(function (e) {
        $value = jQuery(this).attr('value');
        jQuery('select[name="location"]').val($value).trigger('change');
        e.preventDefault();
    });


}

function __training_swiper() {
    var swiper_training = new Swiper('.swiper-training', {
        loop: false,
        autoplay: false,
        slidesPerView: 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
}

function __training_ajax() {
    jQuery('.ajax-loading').addClass('loading-post');

    setTimeout(function () {
        $result_holder = jQuery('#results');
        $product_id = jQuery('input[name="product_id"]').val();
        $delivery_method = jQuery('input[name="delivery_method"]:checked').val();
        $sortby = jQuery('select[name="sort"]').val();
        $location = jQuery('select[name="location"]').val();

        if ($delivery_method == 'online-self-paced') {
            $location = 'online';
        }
        jQuery('.training-map-holder span').removeClass('active');
        if ($location) {
            $delivery_method = 'classroom';
            jQuery('#' + $location).addClass('active');
        }

        jQuery.ajax({
            type: "POST",

            url: ajax_object.ajax_url,

            data: {
                action: 'training_ajax',
                product_id: $product_id,
                delivery_method: $delivery_method,
                location: $location,
                sortby: $sortby,
            },

            success: function (response) {
                $result_holder.html(response);
                __training_swiper();
                jQuery('.ajax-loading').removeClass('loading-post');
            },
            error: function (e) {
                console.log(e);
            }
        });

    }, 300);

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