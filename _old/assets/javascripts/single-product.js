jQuery(document).ready(function ($) {
    quantity_plus_minus();
    variation_radio();
    product_gallery();
    fancyboxgallery();
    ajax_buy_now();
    elementor_slider();
    swiper_slider();
    ajax_add_to_cart();
    //product_stock_status();
    // image_variation_change();
});



function product_stock_status() {
    jQuery('.variation-label.status-onbackorder, .variation-label.status-outofstock').click(function (e) {
        alert('Sorry you cannot add this product to cart. Please select another variation.\n\n\n');
        console.log('test');
        return false;
        e.preventDefault();
    });
}

function ajax_add_to_cart() {
    if (window.innerWidth < 1025) {
        jQuery('body').on('added_to_cart', function () {
            window.location.href = 'https://coptrz.com/basket/';
        });
    } else {
        jQuery('body').on('added_to_cart', function () {
            jQuery('#popup-mini-cart').addClass('elementor-menu-cart--shown elementor-added-to-cart');
        });
        jQuery(document).on("click", '#popup-mini-cart.elementor-added-to-cart', function (event) {
            jQuery('#popup-mini-cart').removeClass('elementor-menu-cart--shown elementor-added-to-cart');
        });
    }

}
function swiper_slider() {
    if (jQuery('.mySwiper-ReviewsDefault').length > 0) {
        var testimonialSwiper = new Swiper(".mySwiper-ReviewsDefault", {
            loop: true,
            spaceBetween: 46,
            autoHeight: true,
            slidesPerView: 2,
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
            },

            breakpoints: {
                0: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },

                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },

                992: {
                    slidesPerView: 2,
                    spaceBetween: 46,
                },


            },

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    }
    if (jQuery('.mySwiper-ReviewsCaseStudy').length > 0) {

        var testimonialSwiper = new Swiper(".mySwiper-ReviewsCaseStudy", {
            loop: true,
            spaceBetween: 0,
            autoHeight: true,
            slidesPerView: 2,
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
            },
            breakpoints: {
                0: {
                    slidesPerView: 1,
                },

                768: {
                    slidesPerView: 2,
                },

                992: {
                    slidesPerView: 2,
                },


            },
            pagination: {
                el: ".swiper-pagination",
            },
        });
    }

}

function elementor_slider() {
    if (jQuery('.elementor-swiper-slider-js').length > 0) {
        jQuery('body:not(.elementor-editor-active) .elementor-swiper-slider-js > div').addClass('swiper-wrapper');
        $pagination = jQuery('<div class="swiper-pagination swiper-pagination-elementor"></div>');
        $pagination.insertAfter('.elementor-swiper-slider-js .swiper-wrapper');
        setTimeout(function () {
            var elementorSlider = new Swiper(".elementor-swiper-slider-js", {
                loop: false,
                spaceBetween: 0,
                slidesPerView: 1,
                autoplay: false,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                },

            });

        }, 1000);
    }
    if (jQuery('.elementor-swiper-slider-js-vertical').length > 0) {

        jQuery('body:not(.elementor-editor-active) .elementor-swiper-slider-js-vertical > div').addClass('swiper-wrapper');
        $pagination = jQuery('<div class="swiper-pagination swiper-pagination--vertical swiper-pagination-elementor"></div>');
        $pagination.insertAfter('.elementor-swiper-slider-js-vertical .swiper-wrapper');
        setTimeout(function () {
            if (window.innerWidth > 991) {
                var $direction = 'vertical';
            } else {
                var $direction = 'horizontal';
            }

            var elementorSliderVertical = new Swiper(".elementor-swiper-slider-js-vertical", {
                loop: false,
                slidesPerView: 1,
                direction: $direction,
                autoHeight: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: true,
                },
                pagination: {
                    el: ".swiper-pagination--vertical",
                    clickable: true
                },
            });

        }, 1000);
    }
}

function ajax_buy_now() {
    jQuery('.buy-now').click(function (e) {
        buy_now_ajax(jQuery(this));
        e.preventDefault();
    });
}

function buy_now_ajax(button) {

    $buy_now_id = button.prev().val();
    console.log('buy now');
    console.log($buy_now_id);
    jQuery('.buy-now').attr('disabled');
    button.addClass('active').attr('disabled');
    jQuery.ajax({

        type: "POST",

        //url: "/coptrz/wp-admin/admin-ajax.php",

        url: "/wp-admin/admin-ajax.php",

        data: {
            action: 'buy_now_ajax',
            buy_now_id: $buy_now_id,
        },

        success: function (response) {
            button.removeClass('active');
            window.location.href = 'https://coptrz.com/checkout/';
        },
        error: function (e) {
            console.log(e);
        }

    });
}
function get_started_modal() {
    if (data.page.type == 'taxonomy') {
        var $product_url = '';
        var $taxonomy_url = data.page.page_url;
        var $is_clicked = false;
        jQuery('.products.elementor-grid #request-info-modal').click(function (e) {
            $product_url = jQuery(this).attr('url');
            $is_clicked = true;
        });

        jQuery(document).on('elementor/popup/show', (event, id, instance) => {
            if (id === 195511) {
                if ($is_clicked) {
                    jQuery('#form-field-page').val($product_url);
                    $is_clicked = false;
                } else {
                    jQuery('#form-field-page').val($taxonomy_url);
                }
            }
        });
    } else {
        jQuery('#form-field-page').val(data.page.page_url);
    }
}

function image_variation_change() {
    jQuery('.single_variation_wrap').on('show_variation', function (event, variation) {
        $url = 'https://coptrz.com/?add-to-cart=' + variation.variation_id + '&buy-now=true';
        jQuery('.buy-now').attr('href', $url);
        console.log($url);
        if (variation.image) {
            $slide = jQuery('.swiper-slider-variation-' + variation.variation_id).attr('key');
            $image_count = jQuery('.product-gallery').attr('image_count');
            $goto = parseInt($image_count) + parseInt($slide);
            mySwiperThumb.slideTo($goto);
            mySwiperMain.slideTo($goto);
        }
    });
}


function fancyboxgallery() {
    Fancybox.bind('[data-fancybox="iframe"] a', {
        type: 'iframe',
    });

    Fancybox.bind('[data-fancybox="product-gallery"]', {
        hideScrollbar: false,
        buttons: [
            "slideShow",
            "thumbs",
            "zoom",
            "fullScreen",
            "share",
            "close"
        ],
        Thumbs: {
            type: "modern",
        },
        Toolbar: {
            display: {
                left: ["infobar"],
                middle: [
                    "zoomIn",
                    "zoomOut",
                    "toggle1to1",
                    "rotateCCW",
                    "rotateCW",
                    "flipX",
                    "flipY",
                ],
                right: ["slideshow", "thumbs", "close"],
            },
        },
        loop: true,
        protect: true
    });
}
function product_gallery() {
    var mySwiperThumb = new Swiper(".mySwiperThumb", {
        loop: true,
        spaceBetween: 10,
        centeredSlides: true,
        watchSlidesProgress: true,
        pagination: {
            el: ".swiper-pagination",
            dynamicBullets: true,
            clickable: true
        },
        breakpoints: {
            0: {
                slidesPerView: 4,
            },

            1200: {
                slidesPerView: 4,
            },
            1400: {
                slidesPerView: 5,
            },
        },
    });

    var mySwiperMain = new Swiper(".mySwiperMain", {
        loop: true,
        spaceBetween: 0,
        centeredSlides: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },

        thumbs: {
            swiper: mySwiperThumb,
        },
    });

}

function variation_radio() {

    if (jQuery('.variation-radio').length > 0) {
        $val = jQuery('.variation-radio input:first-child').val();
        $id = 'p_' + $val;
        $price = data.price[$id];
        if ($price) {
            jQuery('.price-box-main').html($price);
        }
        console.log($price);

        jQuery('.ajax_add_to_cart').attr('data-product_id', $val).removeClass('disabled');


        setTimeout(function () {
            jQuery('.ajax_add_to_cart').removeClass('disabled');
        }, 500);

        jQuery('.variation-radio input:first-child + label').click();

        jQuery('.variation-radio').change(function (e) {
            $val = jQuery('input[name="variation-radio"]:checked').val();
            $id = 'p_' + $val;
            $price = data.price[$id];
            if ($price) {
                jQuery('.price-box-main').html($price);
            }


            jQuery('.ajax_add_to_cart').attr('data-product_id', $val).removeClass('disabled');


        });



    }
}


function quantity_plus_minus() {
    jQuery(document).on('click', 'button.plus, button.minus', function () {
        var qty = jQuery(this).parent('.quantity').find('.qty');
        var val = parseFloat(qty.val());
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var step = parseFloat(qty.attr('step'));


        var ajax_add_to_cart = jQuery('.ajax_add_to_cart');


        if (jQuery(this).is('.plus')) {
            if (max && (max <= val)) {
                qty.val(max).change();
                ajax_add_to_cart.attr('data-quantity', max);
            } else {
                qty.val(val + step).change();
                ajax_add_to_cart.attr('data-quantity', val + step);
            }
        } else {
            if (min && (min >= val)) {
                qty.val(min).change();
                ajax_add_to_cart.attr('data-quantity', min);
            } else if (val > 1) {
                qty.val(val - step).change();
                ajax_add_to_cart.attr('data-quantity', val - step);

            }
        }




    });
}