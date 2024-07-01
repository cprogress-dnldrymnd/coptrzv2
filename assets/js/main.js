jQuery(document).ready(function () {
    swipers();
});

function swipers() {
    var logoSwiper = new Swiper(".logo-slider", {
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
                slidesPerView: 3,
            },

            992: {
                slidesPerView: 4,
            },


        },

    });
}