jQuery(document).ready(function () {
    product_gallery();
    product_variation();
});

function product_variation() {
    const customVariations = document.getElementsByClassName('custom-wc-variations');
    if (customVariations.length > 0) {
        Array.from(customVariations).forEach(function (variation) {
            const radios = variation.querySelectorAll('input[type=radio]');
            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    const variationName = radio.getAttribute('data-variation-name');
                    const selectBox = document.querySelector('select[name=' + variationName + ']');
                    selectBox.value = radio.getAttribute('data-value');
                    jQuery(selectBox).trigger('change');
                });
            });
        });
    }
}

function product_gallery() {

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