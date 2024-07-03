jQuery(document).ready(function () {
    swipers();
    mini_cart();
    header_menu();
});


function header_menu() {
    jQuery('.has-children.nav-link').click(function (e) {
        if (jQuery(this).hasClass('active')) {
            jQuery(this).removeClass('active');
            jQuery(this).next().removeClass('active');
        } else {
            jQuery('.has-children.nav-link.active').removeClass('active');
            jQuery('.submenu.active').removeClass('active');

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
}

function mini_cart() {
    jQuery('#mini-cart-button').click(function (e) {
        jQuery('body').toggleClass('mini-cart-active');
        jQuery('.has-children.nav-link').removeClass('active');
        jQuery('.has-children.nav-link').next().removeClass('active');
        e.preventDefault();
    });
}
function swipers() {
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