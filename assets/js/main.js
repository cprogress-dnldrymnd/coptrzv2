jQuery(document).ready(function () {
    __mini_cart();
    __header_menu();
    __block_accordion();
    __fixed_heading_position();
    __swipers();
    __input_fields();
    __ajax_buy_now();
    __post_navigation();
    __filters();
    __ajax_trigger();
    __phone_input();
    __toggle_input();
});

function __toggle_input() {
    jQuery('.toggle-switch').click(function (e) {
        jQuery(this).toggleClass('active');
        jQuery('.drone-plans').toggleClass('large-drone-active');
        e.preventDefault();
    });


}

function __phone_input() {
    if (jQuery('input[type="tel"]').length != 0) {
        const input = document.querySelector('input[type="tel"]');
        const iti = window.intlTelInput(input, {
            initialCountry: "gb",
            separateDialCode: true,
        });
        jQuery('input[type="tel"]').val('+44');
        input.addEventListener("countrychange", function () {
            const selectedCountryData = iti.getSelectedCountryData();
            if (selectedCountryData) {
                const dialCode = selectedCountryData.dialCode;
                jQuery(this).val('+' + dialCode);
            }
        });
    }
}

function __ajax() {
    $archive_section = jQuery('.ajax-loading');
    $result_holder = jQuery('#results');
    $s = jQuery('#search').val();
    $archive_section.addClass('loading-post');
    $data = jQuery('#posts').attr('data');
    $query = jQuery('#posts').attr('query');
    if (jQuery('input[name="events_type"]').length > 0) {
        $events_type = jQuery('input[name="events_type"]:checked').val();
    } else {
        $events_type = false;
    }


    jQuery.ajax({
        type: "POST",

        url: ajax_object.ajax_url,

        data: {
            action: 'archive_ajax',
            s: $s,
            data: $data,
            query: $query,
            events_type: $events_type
        },

        success: function (response) {
            $result_holder.html(response);
            $archive_section.removeClass('loading-post');

        },
        error: function (e) {
            console.log(e);
        }
    });



}

function __ajax_trigger() {
    var typingTimer;
    var doneTypingInterval = 500;

    jQuery('input[name="s"]').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    jQuery('input[name="s"]').on('keydown', function () {
        clearTimeout(typingTimer);
    });

    function doneTyping() {
        __ajax();
    }


    jQuery('input[name="events_type"]').on('change', function () {
        __ajax();
    });

}

function __filters() {
    jQuery('.trigger-change-link').change(function (e) {
        $term_link = jQuery('option:selected', this).attr('term_link');
        window.location.href = $term_link;
    });

    jQuery('.number-post-trigger').change(function (e) {
        $val = jQuery(this).val();
        $url = location.protocol + '//' + location.host + location.pathname;
        $link = $url + "?posts_per_page=" + $val;
        window.location.href = $link;
    });
}


function __ajax_buy_now() {
    jQuery('.buy-now-trigger').click(function (e) {
        buy_now_ajax(jQuery(this));
        e.preventDefault();
    });
}

function buy_now_ajax(button) {
    $buy_now_id = button.attr('data-target');
    button.addClass('active').attr('disabled');
    console.log($buy_now_id);

    jQuery.ajax({

        type: "POST",

        //url: "/coptrz/wp-admin/admin-ajax.php",

        url: ajax_object.ajax_url,

        data: {
            action: 'buy_now_ajax',
            buy_now_id: $buy_now_id,
        },

        success: function (response) {
            button.removeClass('active');
            window.location.href = ajax_object.checkout_url;
        },
        error: function (e) {
            console.log(e);
        }

    });
}
function __input_fields() {
    jQuery('.remove-first-option-value select option:first-child').attr('value', '');

    jQuery(".wpforms-field input, .wpforms-field select, .wpforms-field textarea").on("blur input focus", function () {
        if (this.value) {
            jQuery(this).parent().addClass("filled");
        } else {
            jQuery(this).parent().removeClass("filled");
        }
    });

    jQuery(".wpforms-field input, .wpforms-field select,  .wpforms-field textarea").on("focus", function () {
        if (this) {
            jQuery(this).parent().addClass("filled");
        } else {
            jQuery(this).parent().removeClass("filled");
        }
    });


    jQuery(".wpcf7-form-control-wrap input, .wpcf7-form-control-wrap select, .wpcf7-form-control-wrap textarea").on("blur input focus", function () {
        if (this.value) {
            jQuery(this).parent().parent().addClass("filled");
        } else {
            jQuery(this).parent().parent().removeClass("filled");
        }
    });

    jQuery(".wpcf7-form-control-wrap input, .wpcf7-form-control-wrap select,  .wpcf7-form-control-wrap textarea").on("focus", function () {
        if (this) {
            jQuery(this).parent().parent().addClass("filled");
        } else {
            jQuery(this).parent().parent().removeClass("filled");
        }
    });
}

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
    setTimeout(function () {
        jQuery('.header').removeClass('overflow-hidden');
    }, 500);
    if (jQuery('header').length > 0) {
        if (window.innerWidth > 991) {
            jQuery(".has-submenu").hover(
                function () {
                    jQuery(this).find(' > .nav-link').addClass('active');
                    jQuery(this).find(' > .nav-link + div').addClass('active');

                    jQuery('body').removeClass('mini-cart-active');
                }, function () {
                    jQuery(this).find(' > .nav-link').removeClass('active');
                    jQuery(this).find(' > .nav-link + div').removeClass('active');
                }
            );

            jQuery('.submenu2 .col-lg-5 .nav-link').hover(
                function () {
                    $target = jQuery(this).attr('url_target');
                    jQuery('.has-children-tab.nav-link.active').removeClass('active');

                    jQuery(this).toggleClass('active');
                    jQuery(this).next().toggleClass('active');

                    jQuery('.tab-links').addClass('d-none');
                    jQuery($target).removeClass('d-none');
                    e.preventDefault();

                }, function () {
                    if (!jQuery(this).hasClass('has-children-tab')) {
                        $target = jQuery(this).attr('url_target');

                        jQuery(this).toggleClass('active');
                        jQuery(this).next().toggleClass('active');

                        jQuery('.tab-links').addClass('d-none');
                        jQuery($target).addClass('d-none');
                        e.preventDefault();
                    }
                }
            );
        } else {
            jQuery('.has-children.main-nav').click(function (e) {
                var $this = jQuery(this);

                $this.addClass('active');
                $this.next().addClass('active');

                setTimeout(function () {
                    $this.addClass('show-menu');
                    $this.next().addClass('show-menu');
                }, 50);

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
                $target = jQuery(this).attr('url_target');
                jQuery('.has-children-tab.nav-link.active').removeClass('active');

                jQuery(this).toggleClass('active');
                jQuery(this).next().toggleClass('active');

                jQuery('.tab-links').addClass('d-none');
                jQuery($target).removeClass('d-none');
                e.preventDefault();

            });

            jQuery('.close-submenu-1').click(function (e) {
                var $this = jQuery(this);
                $this.parent().parent().parent().removeClass('show-menu');
                $this.parent().parent().parent().prev().removeClass('show-menu');
                setTimeout(function () {
                    $this.parent().parent().parent().removeClass('active');
                    $this.parent().parent().parent().prev().removeClass('active');
                }, 50);
                e.preventDefault();
            });


            jQuery('.close-submenu-2').click(function (e) {
                jQuery(this).parent().parent().parent().parent().removeClass('active');
                jQuery(this).parent().parent().parent().parent().prev().removeClass('active');
                e.preventDefault();

            });

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
            jQuery('.mini-cart-holder').insertAfter('#mini-cart-button');
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
        /*

        $swiper_slides = jQuery(this).find('.swiper-slide');
        $swiper_slides.each(function (index, element) {
            $width = jQuery(this).innerWidth();
            jQuery(this).css('width', $width + 'px');
        });
*/
        var logoSwiper = new Swiper($id, {
            loop: true,
            freeMode: true,
            slidesPerView: 'auto',
            spaceBetween: 0,
            speed: 5000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false
            },
        });
    });

    jQuery('.swiper-sliders').each(function (index, element) {
        var $id = '#' + jQuery(this).attr('id');
        $number_of_slides = jQuery(this).attr('number_of_slides');
        $number_of_slides_tablet = jQuery(this).attr('number_of_slides_tablet');
        $number_of_slides_mobile = jQuery(this).attr('number_of_slides_mobile');
        var swiper_sliders = new Swiper($id, {
            loop: true,
            autoplay: false,
            breakpoints: {
                0: {
                    spaceBetween: 10,
                    slidesPerView: $number_of_slides_mobile,
                },

                768: {
                    spaceBetween: 10,
                    slidesPerView: $number_of_slides_tablet,
                },


                992: {
                    spaceBetween: 20,
                    slidesPerView: $number_of_slides,
                },
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
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

function __post_navigation() {
    if (jQuery('body').hasClass('single-post')) {
        $post_navigation = '';
        $key = 1;

        jQuery('#post-content h2').addClass('h2 post-nav');

        if (jQuery('#post-content h2').length > 0) {
            jQuery('#post-content h3').addClass('h3 post-nav');
        } else {
            jQuery('#post-content h3').addClass('h2 post-nav');
        }

        jQuery('#post-content h2, #post-content h3').each(function (index, element) {
            $id = 'content' + $key;
            $text = jQuery(this).text();
            if ($text != '') {
                jQuery(this).attr('id', $id);
                if (jQuery(this).hasClass('h3')) {
                    $class = 'h3-nav';
                } else {
                    $class = 'h2-nav';
                }
                $heading_val = '<li class="' + $class + '"> <a href="#' + $id + '">' + $text + '</a> </li>';
                $post_navigation = $post_navigation + $heading_val;
                $key++;

            }
        });

        $post_navigation_html = jQuery($post_navigation);
        $post_navigation_html.appendTo('#post-navigation');

        jQuery(document).on("click", '#post-navigation a', function (event) {
            $href = jQuery(this).attr('href');
            jQuery('.post-nav').removeClass('active');
            jQuery($href).addClass('active');
            jQuery('html, body').animate({
                scrollTop: jQuery($href).offset().top - 200
            }, 1000);
        });
    }
}
