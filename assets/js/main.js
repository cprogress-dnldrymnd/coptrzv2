jQuery(document).ready(function () {
    __mini_cart();
    __header_menu();
    __block_accordion();
    __fixed_heading_position();
    __swipers();
});

function __fixed_heading_position() {
    jQuery('.section.extend-right, .section.extend-left').each(function (index, element) {
        $this = jQuery(this);
        jQuery(this).find('> .container > h2').addClass('px-20px').prependTo($this);
    });
}

function __block_accordion() {
    setTimeout(function () {
        jQuery('.block-accordion').each(function (index, element) {
            $height = jQuery(this).find('.wp-block-group__inner-container>div').outerHeight();
            jQuery(this).find('.wp-block-group__inner-container>div').css('height', $height + 'px');
        });
    }, 1000);
}

function __header_menu() {
    jQuery('.has-children.main-nav').click(function (e) {
        if (jQuery(this).hasClass('active')) {
            jQuery(this).removeClass('active');
            jQuery(this).next().removeClass('active');
        } else {
            jQuery('.has-children.main-nav.active').removeClass('active');
            jQuery('.submenu.active').removeClass('active');

            jQuery(this).toggleClass('active');
            jQuery(this).next().toggleClass('active');
        }
        jQuery('body').removeClass('mini-cart-active');

        e.preventDefault();
    });

    jQuery('.has-children.sub-nav').click(function (e) {
        if (jQuery(this).hasClass('active')) {
            jQuery(this).removeClass('active');
            jQuery(this).next().removeClass('active');
        } else {
            jQuery('.has-children.sub-nav.active').removeClass('active');
            jQuery('.submenu2.active').removeClass('active');

            jQuery(this).toggleClass('active');
            jQuery(this).next().toggleClass('active');
        }
        jQuery('body').removeClass('mini-cart-active');

        e.preventDefault();
    });

    jQuery('.has-children-tab').click(function (e) {
        $target = jQuery(this).attr('target');
        jQuery('.has-children-tab.nav-link.active').removeClass('active');

        jQuery(this).toggleClass('active');
        jQuery(this).next().toggleClass('active');

        jQuery('.tab-links').addClass('d-none');
        jQuery($target).removeClass('d-none');
        e.preventDefault();

    });

    if (window.innerWidth > 991) {
    } else {
        $nav = jQuery('#menu-desktop .navbar');
        $menu_offcanvas = jQuery('<div class="offcanvas offcanvas-start" tabindex="-1" id="offCanvasMenu" aria-labelledby="offcanvasLabel">  <div class="offcanvas-body"> <div id="menu-mobile"> </div> </div> </div>');

        $menu_offcanvas.insertAfter('.header');
        $nav.appendTo('#menu-mobile');


        var offCanvasMenu = document.getElementById('offCanvasMenu')
        offCanvasMenu.addEventListener('show.bs.offcanvas', function () {
            jQuery('body').addClass('mobile-menu-active');
        });

        offCanvasMenu.addEventListener('hide.bs.offcanvas', function () {
            jQuery('body').removeClass('mobile-menu-active');
        });
    }


}

function __mini_cart() {
    if (window.innerWidth > 991) {
        jQuery('#mini-cart-button').click(function (e) {
            jQuery('body').toggleClass('mini-cart-active');
            jQuery('.has-children.nav-link').removeClass('active');
            jQuery('.has-children.nav-link').next().removeClass('active');
            e.preventDefault();
        });
        if (jQuery('.mini-cart-wrapper .mini-cart-holder').length == 0) {
            jQuery('.mini-cart-holder').inserAfter('#mini-cart-button');
        }
    } else {
        jQuery('#mini-cart-button').attr('data-bs-toggle', 'offcanvas').attr('data-bs-target', '#offCanvasMiniCart').attr('aria-controls', 'offCanvasMiniCart');
        $mini_cart = jQuery('.mini-cart-holder');
        $mini_cart_offcanvas = jQuery('<div class="offcanvas offcanvas-start" tabindex="-1" id="offCanvasMiniCart" aria-labelledby="offcanvasLabel">  <div class="offcanvas-body"> <div id="mini-cart-mobile"></div> </div> </div>');
        $mini_cart_offcanvas.insertAfter('.header');
        $mini_cart.appendTo('#mini-cart-mobile');
    }
}
function __swipers() {

    jQuery('.swiper-linked-products').each(function (index, element) {
        $id = jQuery(this).attr('id');
        var swiper_linked_products = new Swiper('#' + $id, {
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

    jQuery('.swiper-logo-slider').each(function (index, element) {
        $id = '#' + jQuery(this).attr('id');
        $number_of_slides = jQuery(this).attr('number_of_slides');
        $number_of_slides_tablet = jQuery(this).attr('number_of_slides_tablet');
        $number_of_slides_mobile = jQuery(this).attr('number_of_slides_mobile');

        var logoSwiper = new Swiper($id, {
            loop: true,
            freeMode: true,
            centeredSlides: true,
            speed: 5000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false
            },
            breakpoints: {
                0: {
                    slidesPerView: $number_of_slides_mobile,
                },

                768: {
                    slidesPerView: $number_of_slides_tablet,
                },


                992: {
                    slidesPerView: $number_of_slides,
                },

            },

        });

    });

    jQuery('.style-1 .swiper-sliders').each(function (index, element) {
        var $id = '#' + jQuery(this).attr('id');

        var swiper_sliders = new Swiper($id, {
            loop: true,
            autoplay: false,
            slidesPerView: 'auto',
            spaceBetween: 20,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

    });

    var swiper_sliders = new Swiper('.swiper-full-width', {
        loop: true,
        autoplay: false,
        slidesPerView: 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });




}