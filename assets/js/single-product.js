jQuery(document).ready(function () {
    product_gallery();
});

function product_gallery() {
    var product_main_image = new Swiper('.product-main-image', {
        loop: true,
        autoplay: false,
        slidesPerView: 'auto',
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    var product_thumb = new Swiper('.product-thumb', {
        loop: true,
        autoplay: false,
        slidesPerView: 'auto',
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
} 