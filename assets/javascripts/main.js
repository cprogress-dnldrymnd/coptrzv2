var $scrolling_section_is_active = false;

jQuery(document).ready(function ($) {
	swiper_slider();
	load_more_button_listener();
	ajax_form();
	ajax_buy_now();
	elementor_slider();
	post_navigation();
	download_guide();
	elementor_mega_menu();
	get_started_modal();
	ajax_add_to_cart();
	input_fields();
	incrementing_numbers();
	if (jQuery('.archive-section').length > 0 && !jQuery('body').hasClass('post-type-archive-careers')) {
		ajax();
	}
});

function incrementing_numbers() {
	jQuery('.counter-number').each(function () {
		jQuery(this).prop('Counter', 0).animate({
			Counter: $(this).text()
		}, {
			duration: 3000,
			easing: 'swing',
			step: function (now) {
				jQuery(this).text(Math.ceil(now));
			}
		});
	});
}

function input_fields() {
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


}

function ajax_add_to_cart() {
	jQuery('body').on('added_to_cart', function () {
		jQuery('#popup-mini-cart').addClass('elementor-menu-cart--shown elementor-added-to-cart');
		console.log('added_to_cart');
	});
	jQuery(document).on("click", '#popup-mini-cart.elementor-added-to-cart', function (event) {
		jQuery('#popup-mini-cart').removeClass('elementor-menu-cart--shown elementor-added-to-cart');
	});
}
function get_started_modal() {
	if (data.page.type == 'taxonomy') {
		var $product_url = '';
		var $taxonomy_url = data.page.page_url;
		var $is_clicked = false;
		jQuery('.products.elementor-grid #request-info-modal').click(function (e) {
			$product_url = jQuery(this).attr('url');
			$is_clicked = true;
		});

		jQuery(document).on('elementor/popup/show', (event, id, instance) => {
			if (id === 195511) {
				if ($is_clicked) {
					jQuery('#form-field-page').val($product_url);
					$is_clicked = false;
				} else {
					jQuery('#form-field-page').val($taxonomy_url);
				}
			}
		});
	} else {
		jQuery('#form-field-page').val(data.page.page_url);
	}
}

jQuery(window).on("resize", function () {
	download_guide_responsive();
});
function elementor_mega_menu() {
	if (window.innerWidth < 1025) {
		jQuery('#main-menu .e-n-menu-wrapper').addClass('d-none');
		jQuery('#main-menu').appendTo('#mobile-search');
		setTimeout(function () {
			jQuery('#main-menu .e-n-menu-wrapper').removeClass('d-none');
		}, 1000);
		jQuery('#main-menu .e-n-menu-content > div').each(function (index, element) {
			$id = jQuery(this).attr('id');
			jQuery(this).insertAfter('#main-menu button[aria-controls="' + $id + '"]');
		});
	}
}

function download_guide() {
	jQuery(".guide-row").hover(
		function () {
			var $target = jQuery(this).attr('target')
			jQuery($target).addClass('active');
		}, function () {
			var $target = jQuery(this).attr('target')
			jQuery($target).removeClass('active');
		}
	);

	jQuery(".box-holder .box").hover(
		function () {
			jQuery(this).addClass('active');
			var $target = jQuery(this).attr('id')
			jQuery('.guide-row[target="#' + $target + '"]').addClass('active');
		}, function () {
			var $target = jQuery(this).attr('id')
			jQuery('.guide-row[target="#' + $target + '"]').removeClass('active');
			jQuery(this).removeClass('active');
		}
	);

	download_guide_responsive();
}

function download_guide_responsive() {
	if (window.innerWidth < 992) {
		jQuery('.col-side-end .guide-row-end').appendTo('.col-side-start .guide-row-holder');
	} else {
		jQuery('.col-side-start .guide-row-end').appendTo('.col-side-end .guide-row-holder');
	}
}

function post_navigation() {
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


function ajax_form() {
	jQuery(".archive-form-filter").change(function (e) {
		e.preventDefault();
		ajax(0);
	});

	var typingTimer;
	var doneTypingInterval = 500;

	jQuery('.archive-section #search').on('keyup', function () {
		clearTimeout(typingTimer);
		typingTimer = setTimeout(doneTyping, doneTypingInterval);
	});

	jQuery('.archive-section #search').on('keydown', function () {
		clearTimeout(typingTimer);
	});

	function doneTyping() {
		ajax();
	}
}

function load_more_button_listener($) {
	jQuery(document).on("click", '#load-more', function (event) {
		event.preventDefault();
		var offset = jQuery('.post-item').length;
		ajax(offset, 'append');
	});


	jQuery(document).on("click", '.pagination .page-numbers', function (event) {
		event.preventDefault();
		$page = jQuery(this).text();
		jQuery('#results').addClass('pagination-trigger');
		ajax($page, 'html');
		return false;
	});

	jQuery(document).on("change", '#sortpostby', function (event) {
		event.preventDefault();
		jQuery('#results').removeClass('pagination-trigger');
		ajax(false, 'html');
	});

	jQuery(document).on("click", '.reset-all', function (event) {
		event.preventDefault();
		jQuery('#sortpostby').val('');
		jQuery('input[name="s"]').val('');
		clear_category();
		jQuery('#results').removeClass('pagination-trigger');
		ajax(false, 'html');
	});

	jQuery(document).on("click", '.clear-category', function (event) {
		event.preventDefault();
		clear_category();
		jQuery('#results').removeClass('pagination-trigger');
		ajax(false, 'html');
	});

	jQuery(document).on("click", '#load-more-careers', function (event) {
		event.preventDefault();
		var offset = jQuery('.post-item').length;
		careers_ajax(offset, 'append');
	});





}

function clear_category() {
	jQuery('input[name="terms[]"]').each(function () {
		this.checked = false;
	});
}

function results_height() {
	if (jQuery('#results').length > 0) {
		$height = jQuery('#results .results-holder').outerHeight();
		jQuery('#results').css('height', $height + 'px');
	}
}

function ajax($offset, $event_type = 'html') {

	var $sortby = jQuery('#sortpostby').val();

	var $loadmore = jQuery('#load-more');

	var $archive_section = jQuery('.archive-section');

	var $result_holder = jQuery('#results .results-holder');

	var $post_type = jQuery("input[name='post-type']").val();

	var $taxonomy = jQuery("input[name='taxonomy']").val();

	var $is_search = jQuery("input[name='is_search']").val();

	var $s = jQuery("input[name='s']").val();

	var $terms_category = jQuery("input[name='terms-category']").val();

	var $terms_value = "";

	var $page = 1;

	var $posts_per_page = 12;
	if (!$offset) {
		$page = 1;
	} else {
		$page = $offset;
	}

	if ($page == 1) {
		var $offset_val = 0;
	} else {
		var $offset_val = ($page * $posts_per_page) + 1;
	}

	var $terms = jQuery("input[name='terms[]']:checked");
	$terms.each(function () {
		$terms_value += jQuery(this).val() + ",";
	});

	var $post_types_value = "";

	var $post_types = jQuery("input[name='post_types[]']:checked");
	$post_types.each(function () {
		$post_types_value += jQuery(this).val() + ",";
	});




	$loading = jQuery('<div class="loading-results"> <svg class="spin" xmlns="http://www.w3.org/2000/svg" id="Group_27" data-name="Group 27" width="123" height="123" viewBox="0 0 123 123"> <g id="Ellipse_2" data-name="Ellipse 2" fill="none" stroke="#2DA1FF" stroke-width="2"> <circle cx="61.5" cy="61.5" r="61.5" stroke="none"></circle> <circle cx="61.5" cy="61.5" r="60.5" fill="none"></circle> </g> <circle id="Ellipse_8" data-name="Ellipse 8" cx="6.5" cy="6.5" r="6.5" transform="translate(30 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle> <circle id="Ellipse_9" data-name="Ellipse 9" cx="6.5" cy="6.5" r="6.5" transform="translate(55 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle> <circle id="Ellipse_10" data-name="Ellipse 10" cx="6.5" cy="6.5" r="6.5" transform="translate(80 55)" fill="none" stroke="#2DA1FF" stroke-width="3"></circle> </svg></div>');

	$archive_section.addClass('loading-post');

	if ($event_type == 'html') {
		jQuery('#results  .results-holder').html($loading);
		$loadmore.addClass('d-none');
	} else {
		$loadmore.addClass('loading');
		$loadmore.find('span').text('Loading');
	}

	jQuery.ajax({

		type: "POST",

		//url: "/coptrz/wp-admin/admin-ajax.php",

		url: "/wp-admin/admin-ajax.php",

		data: {

			action: 'archive_ajax',

			post_type: $post_type,

			taxonomy: $taxonomy,

			is_search: $is_search,

			terms: $terms_value,

			post_types: $post_types_value,

			terms_category: $terms_category,

			page: $page,

			posts_per_page: $posts_per_page,

			s: $s,

			offset: $offset_val,

			sortby: $sortby

		},

		success: function (response) {
			if ($event_type == 'append') {
				$result_holder_row = $result_holder.find('.row');
				jQuery(response).appendTo($result_holder_row);
			} else {
				$result_holder.html(response);
				jQuery('#pagination').html('');
				jQuery('.pagination').appendTo('#pagination');
			}
			$loadmore.removeClass('d-none loading');

			$loadmore.find('span').text('Load more');

			$archive_section.removeClass('loading-post');

			results_height();
		},
		error: function (e) {
			console.log(e);
		}

	});
}

function ajax_buy_now() {
	jQuery('.buy-now').click(function (e) {
		buy_now_ajax(jQuery(this));
		e.preventDefault();
	});
}

function buy_now_ajax(button) {
	$buy_now_id = button.prev().val();
	jQuery('.buy-now').attr('disabled');
	button.addClass('active').attr('disabled');
	jQuery.ajax({

		type: "POST",

		//url: "/coptrz/wp-admin/admin-ajax.php",

		url: "/wp-admin/admin-ajax.php",

		data: {
			action: 'buy_now_ajax',
			buy_now_id: $buy_now_id,
		},

		success: function (response) {
			button.removeClass('active');
			window.location.href = 'https://coptrz.com/checkout/';
		},
		error: function (e) {
			console.log(e);
		}

	});
}


function mobile_menu() {
	var coptrMenu = document.getElementById('coptrzMenu');
	coptrMenu.addEventListener('hidden.bs.offcanvas', function () {
		jQuery('html').removeClass('mobile-menu-active');
	})
	coptrMenu.addEventListener('show.bs.offcanvas', function () {
		jQuery('html').addClass('mobile-menu-active');
	})
}

function elementor_slider() {
	jQuery('body:not(.elementor-editor-active) .elementor-swiper-slider-js > div').addClass('swiper-wrapper');
	$pagination = jQuery('<div class="swiper-pagination swiper-pagination-elementor"></div>');
	$pagination.insertAfter('.elementor-swiper-slider-js .swiper-wrapper');
	setTimeout(function () {
		var elementorSlider = new Swiper(".elementor-swiper-slider-js", {
			loop: false,
			spaceBetween: 0,
			slidesPerView: 1,
			autoplay: false,
			pagination: {
				el: ".swiper-pagination",
				clickable: true
			},

		});

	}, 1000);
	jQuery('body:not(.elementor-editor-active) .elementor-swiper-slider-js-vertical > div').addClass('swiper-wrapper');
	$pagination = jQuery('<div class="swiper-pagination swiper-pagination--vertical swiper-pagination-elementor"></div>');
	$pagination.insertAfter('.elementor-swiper-slider-js-vertical .swiper-wrapper');
	setTimeout(function () {
		if (window.innerWidth > 991) {
			var $direction = 'vertical';
		} else {
			var $direction = 'horizontal';
		}

		var elementorSliderVertical = new Swiper(".elementor-swiper-slider-js-vertical", {
			loop: false,
			slidesPerView: 1,
			direction: $direction,
			autoHeight: true,
			autoplay: {
				delay: 5000,
				disableOnInteraction: true,
			},
			pagination: {
				el: ".swiper-pagination--vertical",
				clickable: true
			},
		});

	}, 1000);



}


function swiper_slider() {

	var logoSwiper = new Swiper(".mySwiper-logoSwiper", {
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

	jQuery('.mySwiper-logoSwiper-Module').each(function (index, element) {
		$id = jQuery(this).attr('id');
		$number_of_slides = jQuery(this).attr('number_of_slides');
		$number_of_slides_tablet = jQuery(this).attr('number_of_slides_tablet');
		$number_of_slides_mobile = jQuery(this).attr('number_of_slides_mobile');


		var logoSwiper = new Swiper(".mySwiper-logoSwiper", {
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






	var vendorSwiper = new Swiper(".mySwiper-vendorCategory", {
		loop: false,
		spaceBetween: 20,
		autoplay: false,
		freeMode: true,
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},

			400: {
				slidesPerView: 2,
			},
			576: {
				slidesPerView: 3,
			},

			768: {
				slidesPerView: 'auto',
			},

		},

	});

	var vendorProduct = new Swiper(".mySwiper-productSwiper", {
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
				slidesPerView: 1,
			},

			576: {
				slidesPerView: 2,
			},

			992: {
				slidesPerView: 2,
			},


			1200: {
				slidesPerView: 3,
			},


		},

	});


	var vendorProductMedium = new Swiper(".mySwiper-productSwiper-medium", {
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
				slidesPerView: 1,
			},

			576: {
				slidesPerView: 2,
			},

			992: {
				slidesPerView: 3,
			},


			1200: {
				slidesPerView: 4,
			},


		},

	});

	var vendorProductSmall = new Swiper(".mySwiper-productSwiper-small", {
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

			992: {
				slidesPerView: 3,
			},


			1200: {
				slidesPerView: 5,
			},


		},

	});





	var testimonialSwiper = new Swiper(".mySwiper-ReviewsDefault", {
		loop: true,
		spaceBetween: 46,
		autoHeight: true,
		slidesPerView: 2,
		autoplay: {
			delay: 5000,
			disableOnInteraction: true,
		},

		breakpoints: {
			0: {
				slidesPerView: 1,
				spaceBetween: 20,
			},

			768: {
				slidesPerView: 2,
				spaceBetween: 20,
			},

			992: {
				slidesPerView: 2,
				spaceBetween: 46,
			},


		},

		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
	});

	var testimonialSwiper = new Swiper(".mySwiper-ReviewsCaseStudy", {
		loop: true,
		spaceBetween: 0,
		autoHeight: true,
		slidesPerView: 2,
		autoplay: {
			delay: 5000,
			disableOnInteraction: true,
		},
		breakpoints: {
			0: {
				slidesPerView: 1,
			},

			768: {
				slidesPerView: 2,
			},

			992: {
				slidesPerView: 2,
			},


		},
		pagination: {
			el: ".swiper-pagination",
		},


	});
}