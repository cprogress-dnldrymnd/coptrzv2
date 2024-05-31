var vendorSwiper = new Swiper(".mySwiper-vendorSwiper", {
  loop: false,
  spaceBetween: 20,
  autoplay: false,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  pagination: {
    el: ".swiper-pagination",
    dynamicBullets: true,
    clickable: true
  },
  breakpoints: {
    0: {
      slidesPerView: 2,
    },


    576: {
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

    1300: {
      slidesPerView: 6,
    },

    1400: {
      slidesPerView: 8,
    },


  },

});