jQuery(document).ready(function () {

});

function product_gallery() {
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