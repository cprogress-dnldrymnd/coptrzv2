jQuery(document).ready(function () {
    swipers();
});

function swipers() {
    jQuery('.swiper-logo-slider').each(function (index, element) {
        $id = '#' + jQuery(this).attr('id');
        $number_of_slides = jQuery(this).attr('number_of_slides');
        $number_of_slides_tablet = jQuery(this).attr('number_of_slides_tablet');
        $number_of_slides_mobile = jQuery(this).attr('number_of_slides_mobile');
        console.log($id);

        var logoSwiper = new Swiper($id, {
            loop: true,
            freeMode: true,
            centeredSlides: true,
            speed: 5000,
            spaceBetween: 30,
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
}