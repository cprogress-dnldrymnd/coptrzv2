jQuery(document).ready(function () {
    product_gallery();
});

function product_gallery() {
    var product_main_image = new Swiper('.product-main-image', {
        loop: true,
        autoplay: false,
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

    var product_thumb = new Swiper('.product-thumb', {
        loop: true,
        autoplay: false,
        spaceBetween: 10,
    });
} 