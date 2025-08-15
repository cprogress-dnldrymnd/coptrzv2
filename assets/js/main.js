jQuery(document).ready(function () {
    __mini_cart();
    __header_menu();
    __block_accordion();
    __fixed_heading_position();
    __swipers();
    __input_fields();
    __post_navigation();
    __filters();
    __ajax_trigger();
    __phone_input();
    __toggle_input();
    __blog_content();
    __hero();
    __shop_coptrz_link();
    //__utm_parameters();
});



var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

function __shop_coptrz_link() {
    jQuery('a').each(function () {
        var href = jQuery(this).attr('href');
        if (href && href.indexOf('shop.coptrz.com') !== -1) {
            var newParams = 'utm_source=coptrz-main-site&utm_medium=referral'; // Customize your parameters here
            var separator = href.indexOf('?') !== -1 ? '&' : '?';
            jQuery(this).attr('href', href + separator + newParams);
        }
    });
}

function __utm_parameters() {
    setTimeout(function () {
        utm_val('utm_campaign');
        utm_val('utm_source');
        utm_val('utm_medium');
        utm_val('utm_term');
        utm_val('utm_content');
        utm_val('gclid');
        utm_val('dclid');
    }, 3000);

}

function utm_val(name) {
    var val = getUrlParameter(name);
    if (val != false) {
        jQuery('input[name="' + name + '"]').val(val);
    }
}


function __ajax_brands() {
    $result_holder = jQuery('#results');
    $archive_section = jQuery('.ajax-loading');
    $s = jQuery('input[name="brand_search"]').val();
    $archive_section.addClass('loading-post');

    jQuery.ajax({
        type: "POST",

        url: ajax_object.ajax_url,

        data: {
            action: 'brands_ajax',
            s: $s,
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

function __hero() {

    if (window.innerWidth > 991) {
        jQuery('body.hero-has-form #hero .background-image').prependTo('#hero');
    } else {
        jQuery('body.hero-has-form #hero .background-image').prependTo('.hero-left-content');
    }
}

function __blog_content() {
    if (jQuery('.the-content > *:nth-child(2) + ul').length > 0) {
        jQuery('#download-gvc').insertAfter('.the-content > *:nth-child(3)');
    } else {
        jQuery('#download-gvc').insertAfter('.the-content > *:nth-child(2)');
    }

    jQuery('.the-content iframe').each(function (index, element) {
        jQuery(this).parent().addClass('iframe-holder');
    });
}

function __toggle_input() {

    jQuery('input[name="drone_size"]').change(function (e) {
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
    $posts_per_page = jQuery('#posts').attr('posts_per_page');
    $link = jQuery('link[rel="canonical"').attr('href');
    if (jQuery('input[name="events_type"]').length > 0) {
        $events_type = jQuery('input[name="events_type"]:checked').val();
    } else {
        $events_type = false;
    }
    console.log($link);

    $link_url_param = '?url=' + $link;

    if ($posts_per_page != '') {
        var $posts_per_page_param = '&posts_per_page=' + $posts_per_page;
    }

    if ($s != '') {
        var $s_param = '&s=' + $s;
    }


    jQuery.ajax({
        type: "POST",

        url: ajax_object.ajax_url + $link_url_param + $posts_per_page_param + $s_param,

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
            jQuery('.post-grid-holder + .pagination').remove();

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

    jQuery('input[name="brand_search"]').on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping_brand, doneTypingInterval);
    });
    jQuery('input[name="brand_search"]').on('keydown', function () {
        clearTimeout(typingTimer);
    });


    function doneTyping() {
        __ajax();
    }


    function doneTyping_brand() {
        __ajax_brands();
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

    jQuery('.wpcf7-form-control').attr('autocomplete', 'off');
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
            jQuery('.has-children.main-nav, .has-children.sub-nav, .has-children-tab').click(function (e) {
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


            jQuery('.close-submenu-1').click(function (e) {
                var $this = jQuery(this);
                $this.parent().parent().parent().removeClass('show-menu');
                $this.parent().parent().parent().prev().removeClass('show-menu');
                setTimeout(function () {
                    $this.parent().parent().parent().removeClass('active');
                    $this.parent().parent().parent().prev().removeClass('active');
                }, 300);
                e.preventDefault();
            });


            jQuery('.close-submenu-2').click(function (e) {
                var $this = jQuery(this);
                $this.parent().parent().parent().parent().removeClass('show-menu');
                $this.parent().parent().parent().parent().prev().removeClass('show-menu');
                setTimeout(function () {
                    $this.parent().parent().parent().parent().removeClass('active');
                    $this.parent().parent().parent().parent().prev().removeClass('active');

                }, 300);
                e.preventDefault();
            });


            jQuery('.close-submenu-3').click(function (e) {
                var $this = jQuery(this);
                $this.parent().removeClass('show-menu');
                $this.parent().prev().removeClass('show-menu');
                setTimeout(function () {
                    $this.parent().removeClass('show-menu');
                    $this.parent().prev().removeClass('show-menu');
                }, 300);
                e.preventDefault();
            });

            jQuery('.tab-links').each(function (index, element) {
                $id = jQuery(this).attr('id');

                $parent_id = '#anchor-' + $id;

                jQuery(this).insertAfter($parent_id);

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
            speed: 3000,
            autoplay: {
                delay: 10000,
                disableOnInteraction: false
            },
        });
    });

    jQuery('.swiper-sliders').each(function (index, element) {
        var $id = '#' + jQuery(this).attr('id');
        $number_of_slides = jQuery(this).attr('number_of_slides');
        $number_of_slides_tablet = jQuery(this).attr('number_of_slides_tablet');
        $number_of_slides_mobile = jQuery(this).attr('number_of_slides_mobile');
        $autoplay = jQuery(this).attr('autoplay');

        if ($autoplay == '1') {
            var swiper_sliders = new Swiper($id, {
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
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
                    nextEl: ".post-grid .swiper-button-next",
                    prevEl: ".post-grid .swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                },
            });
        } else {
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
                    nextEl: ".post-grid .swiper-button-next",
                    prevEl: ".post-grid .swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                },
            });
        }


    });

    $link = jQuery('.case-study-slider .swiper-slide[key="0"]').attr('url');
    jQuery('.case-study-slider .button-accent a').attr('href', $link)

    var swiper_fullwidth = new Swiper('.swiper-full-width', {
        loop: true,
        autoplay: false,
        slidesPerView: 1,
        navigation: {
            nextEl: ".swiper-full-width .swiper-button-next",
            prevEl: ".swiper-full-width .swiper-button-prev",
        },
        on: {
            slideChange: function () {
                var index = this.realIndex;
                $link = jQuery('.case-study-slider .swiper-slide[key="' + index + '"]').attr('url');
                jQuery('.case-study-slider .button-accent a').attr('href', $link)
            },
        }
    });




    var swiper_case_study = new Swiper('.swiper-case-study-v2', {
        loop: true,
        autoplay: false,
        spaceBetween: 25,
        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            576: {
                slidesPerView: 2
            },
        },
        navigation: {
            nextEl: ".swiper-full-width .swiper-button-next",
            prevEl: ".swiper-full-width .swiper-button-prev",
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
