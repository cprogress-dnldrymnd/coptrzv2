<?php



class Shortcodes
{

	function contact_number()
	{
		return Theme_Options::contact_number();
	}
	function email_address()
	{
		return Theme_Options::email_address();
	}
	function post_title()
	{
		global $product_id_global;
		if ($product_id_global) {
			$alt_title = get__post_meta('alt_title');
		} else {
			$alt_title = get__post_meta_by_id($product_id_global, 'alt_title');
		}
		if ($alt_title) {
			return $alt_title;
		} else {
			if ($product_id_global) {
				return get_the_title($product_id_global);
			} else {
				return get_the_title();
			}
		}
	}

	function post_title_html()
	{
		$alt_title = get__post_meta('alt_title');
		if ($alt_title) {
			return '<h2 class="text-uppercase text-center">' . $alt_title . '</h2>';
		} else {
			return '<h2 class="text-uppercase text-center">' . get_the_title() . '</h2>';
		}
	}

	function get_param($atts)
	{
		extract(
			shortcode_atts(
				array(
					'value' => '',
				),
				$atts
			)
		);
		if (isset($_GET[$value])) {
			return $_GET[$value];
		}
	}

	function svg($atts)
	{
		$SVG = new SVG;

		extract(
			shortcode_atts(
				array(
					'icon' => '',
				),
				$atts
			)
		);

		return $SVG->{$icon};
	}

	function site_logo()
	{
		return Theme_Options::logo();
	}

	function contact_details()
	{
		ob_start(); ?>
		<ul class="contact-details white-color">
			<?php if (Theme_Options::contact_number_text()) { ?>
				<li>
					t: <?= Theme_Options::contact_number() ?>
				</li>
			<?php } ?>
			<?php if (Theme_Options::email_address()) { ?>
				<li>
					e: <?= Theme_Options::email_address() ?>
				</li>
			<?php } ?>
		</ul>
	<?php
		return ob_get_clean();
	}

	function woocommerce_icons()
	{
		ob_start();
	?>
		<div class="header-icons">
			<ul class="list-unstyled m-0 p-0 d-flex align-items-center">
				<li>
					<a href="#">
						<?php SVG::user() ?>
					</a>
				</li>
				<li>
					<a href="#">
						<?php SVG::cart() ?>
					</a>
				</li>
			</ul>
		</div>
	<?php
		return ob_get_clean();
	}

	function socials()
	{
		ob_start(); ?>
		<ul class="socials white-color d-flex align-items-center">
			<?php if (Theme_Options::facebook()) { ?>
				<li>
					<a href="<?= Theme_Options::facebook() ?>"> <?php SVG::facebook() ?> </a>
				</li>
			<?php } ?>

			<?php if (Theme_Options::twitter()) { ?>
				<li>
					<a href="<?= Theme_Options::twitter() ?>"> <?php SVG::twitter() ?> </a>
				</li>
			<?php } ?>

			<?php if (Theme_Options::linkedin()) { ?>
				<li>
					<a href="<?= Theme_Options::twitter() ?>"> <?php SVG::linkedin() ?> </a>
				</li>
			<?php } ?>
			<?php if (Theme_Options::youtube()) { ?>
				<li>
					<a href="<?= Theme_Options::twitter() ?>"> <?php SVG::youtube() ?> </a>
				</li>
			<?php } ?>
		</ul>
	<?php
		return ob_get_clean();
	}


	function address()
	{
		ob_start();

	?>
		<div class="address">
			<h3 class="mb-3">
				<strong>Where to find us</strong>
			</h3>
			<div class="details">
				<?= wpautop(Theme_Options::address()) ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}

	function opening_times()
	{
		ob_start();

	?>
		<div class="address">
			<h3 class="mb-3">
				<strong>Opening Times</strong>
			</h3>
			<div class="details">
				<?= wpautop(Theme_Options::opening_times()) ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}

	function site_url()
	{
		return get_site_url();
	}

	function webinar_box()
	{
		$video = get__post_meta('video');
		$minutes = get__post_meta('minutes');
		$video_id = preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-_]+|(?<=(?:v|i)\/)[^&?\n]+|(?<=embed\/)[^"&?\n]+|(?<=‌​(?:v|i)=)[^&?\n]+|(?<=youtu.be\/)[^&?\n]+#', $video, $matches);
		$image = get_the_post_thumbnail_url(get_the_ID(), 'large') ? get_the_post_thumbnail_url(get_the_ID(), 'large') : 'http://img.youtube.com/vi/' . $matches[0] . '/hqdefault.jpg';
		ob_start();
	?>
		<div class="webinar-box">
			<a class="image-box position-relative d-block" href="<?= $video ? $video : get_permalink() ?>" <?= $video ? 'data-fancybox' : '' ?>>
				<img src="<?= $image ?>" alt="">
				<div class="elementor-custom-embed-play" role="button" aria-label="Play Video" tabindex="0">
					<svg aria-hidden="true" class="e-font-icon-svg e-eicon-play" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
						<path d="M838 162C746 71 633 25 500 25 371 25 258 71 163 162 71 254 25 367 25 500 25 633 71 746 163 837 254 929 367 979 500 979 633 979 746 933 838 837 929 746 975 633 975 500 975 367 929 254 838 162M808 192C892 279 933 379 933 500 933 621 892 725 808 808 725 892 621 938 500 938 379 938 279 896 196 808 113 725 67 621 67 500 67 379 108 279 196 192 279 108 383 62 500 62 621 62 721 108 808 192M438 392V642L642 517 438 392Z">
						</path>
					</svg> <span class="elementor-screen-only">Play Video</span>
				</div>
			</a>
			<div class="content-box p-4 content-margin">
				<div class="heading-box">
					<h4><?= get_the_title() ?></h4>
				</div>
				<?php if ($minutes) { ?>
					<div class="time-box">
						<span class="d-flex icon align-items-center">
							<span class="elementor-icon-list-icon me-3">
								<svg aria-hidden="true" class="e-font-icon-svg e-far-clock" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
									<path fill: var(--accent-color); d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z">
									</path>
								</svg> </span>
							<span class="elementor-icon-list-text"><?= $minutes ?> Minutes</span>
						</span>
					</div>
				<?php } ?>
				<div class="button-box button-accent">
					<a class="w-100" href="<?= $video ? $video : get_permalink() ?>" <?= $video ? 'data-fancybox' : '' ?>>PLAY</a>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	function webinar_date_time()
	{
		ob_start();
		$date = get__post_meta('date');
		$time = get__post_meta('time');
		if ($date && $time) {
		?>

			<div class="webinar-date">
				<span class="date">
					<?= date('D j M', strtotime($date)); ?> //
				</span>
				<br>
				<span class="time">
					<?= date('g:i a', strtotime($time)); ?>

				</span>

			</div>
		<?php
			return ob_get_clean();
		}
	}

	function login_link()
	{
		if (is_user_logged_in()) {
			$login_link = 'https://www.coptrzacademy-usp.io/';
			$login_text = 'Login';
		} else {
			$login_link = '/login';
			$login_text = 'Login';
		}
		return '<a class="px-0" style="text-decoration: underline" href="' . $login_link . '" >' . $login_text . '</a>';
	}


	function login_button()
	{
		if (is_user_logged_in()) {
			$login_link = 'https://www.coptrzacademy-usp.io/';
			$login_text = 'LOG IN';
		} else {
			$login_link = '/login';
			$login_text = 'LOG IN';
		}
		return '<div class="button-box button-white"><a class="px-0" style="text-decoration: underline" href="' . $login_link . '" >' . $login_text . '</a></div>';
	}

	function terms($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'taxonomy' => '',
				),
				$atts
			)
		);

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
		?>
		<ul class="term-list">
			<?php
			foreach ($terms as $term) {
			?>
				<li>
					<a href="<?= get_term_link($term->term_id) ?>"> <?= $term->name ?></a>
				</li>
			<?php
			}

			?>
		</ul>
		<?php
		return ob_get_clean();
	}

	function post_term($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'taxonomy' => '',
				),
				$atts
			)
		);

		$terms = get_the_terms(get_the_ID(), $taxonomy);

		if ($terms) {
			foreach ($terms as $term) {
		?>
				<span class="fw-bold"><?= $term->name ?></span>
		<?php
			}
		}
		return ob_get_clean();
	}
	function brands($atts, $content = null)
	{
		ob_start();
		$DisplayData = new DisplayData;
		extract(
			shortcode_atts(
				array(
					'letters'     => '',
					'class'       => 'col-lg-6',
					'is_featured' => 'false'
				),
				$atts
			)
		);

		$brands_arr = array();


		if ($is_featured == 'true') {
			$featured_brands = get__theme_option('featured_brands');
			$featured_brands_arr = explode(',', $featured_brands);
			foreach ($featured_brands_arr as $brand) {
				$term = get_term($brand);
				$image = get__term_meta($brand, 'image');
				$menu_icon = get__term_meta($brand, 'menu_icon');

				$brands_arr[$term->term_id] = array(
					'image'       => $menu_icon ? $menu_icon : $image,
					'name'        => $term->name,
					'is_featured' => true
				);
			}
		} else {
			$terms = get_terms(
				array(
					'taxonomy'    => 'pa_brands',
					'hide_empty'  => false,
					'orderby'     => 'name',
					'order'       => 'ASC',
					'is_featured' => false
				)
			);

			$letters_arr = explode(',', $letters);

			foreach ($terms as $term) {
				$image = get__term_meta($term->term_id, 'image');
				$hide_vendor = get__term_meta($term->term_id, 'hide_vendor');
				$menu_icon = get__term_meta($term->term_id, 'menu_icon');
				$hide_vendor_on_menu = get__term_meta($term->term_id, 'hide_vendor_on_menu');

				if (!$hide_vendor && !$hide_vendor_on_menu) {
					foreach ($letters_arr as $letter) {
						if (str_starts_with($term->name, $letter)) {
							$brands_arr[$term->term_id] = array(
								'image'       => $menu_icon ? $menu_icon : $image,
								'name'        => $term->name,
							);
						}
					}
				}
			}
		}

		?>
		<div class="image-box-menu">
			<div class="row gx-3 gy-4">

				<?php foreach ($brands_arr as $key => $brand) { ?>
					<?php
					?>
					<div class="<?= $class ?>">
						<a class="inner d-flex" href="<?= get_term_link($key) ?>">
							<?php
							if (isset($brand['is_featured']) && $brand['is_featured']) {
								$DisplayData->image(
									array(
										'image_id' => $brand['image'],
										'size'     => 'medium'
									),
									'position-relative image-contain-transform'
								);
							}
							?>
							<div class="brands-details <?= !isset($brand['is_featured']) ? 'p-0' : '' ?>">
								<?php
								$DisplayData->heading(
									array(
										'heading' => $brand['name'],
										'tag'     => 'h5'
									)
								);

								?>
							</div>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}

	function featured_drones($atts, $content = null)
	{
		ob_start();
		$DisplayData = new DisplayData;
		extract(
			shortcode_atts(
				array(
					'class' => 'col-lg-12'
				),
				$atts
			)
		);
		$featured_drones = get__theme_option('featured_drones');

	?>
		<div class="image-box-menu">
			<div class="row gx-3 gy-4">

				<?php foreach ($featured_drones as $drones) { ?>
					<?php
					$menu_description = get__post_meta_by_id($drones, 'menu_description');

					if ($menu_description) {
						$description = $menu_description;
						$class = 'long-desc';
					} else {
						$description = get_the_excerpt($drones);
						$class = 'short-desc';
					}
					?>
					<div class="<?= $class ?>">
						<a class="inner d-flex" href="<?= get_permalink($drones) ?>">
							<?php
							$DisplayData->image(
								array(
									'image_id' => get_post_thumbnail_id($drones),
									'size'     => 'medium'
								),
								'position-relative image-contain-transform mb-3'
							);
							?>
							<div class="brands-details">
								<?php
								$DisplayData->heading(
									array(
										'heading' => get_the_title($drones),
										'tag'     => 'h5'
									)
								);
								$DisplayData->description(
									array(
										'description' => $description
									)
								);
								?>
							</div>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}

	function login_dropdown()
	{
		ob_start();

	?>
		<div class="dropdown">
			<button class="dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
				Login
				<svg viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" height="16" width="16" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z" fill-rule="evenodd"></path>
				</svg>
			</button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
				<li><a class="dropdown-item" href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')) ?>">My Orders</a>
				</li>
				<li><a class="dropdown-item" target="_blank" href="https://www.coptrzacademy-usp.io/login">Coptrz Academy</a></li>
			</ul>
		</div>
	<?php
		return ob_get_clean();
	}

	function breadcrumbs($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'post_type'       => '',
					'post_type_label' => '',
					'type'            => ''
				),
				$atts
			)
		);

	?>
		<div class="breadcrumbs-holder ">

			<ul class="breadcrumbs  breadcrumbs-<?= $type ?> list-unstyled d-flex ">

				<li>

					<a href="<?= get_site_url() ?>"> Home </a>

				</li>

				<?php if ($post_type != 'false' && !is_shop()) { ?>
					<li>

						<a href="<?= get_post_type_archive_link($post_type) ?>"> <?= $post_type_label ?> </a>

					</li>
				<?php } ?>

				<li>

					<?php
					if (is_archive()) {
						echo get_the_archive_title();
					} else {
						echo get_the_title();
					}
					?>
					</span>

				</li>

			</ul>

		</div>
	<?php
		return ob_get_clean();
	}

	function testimonial_carousel()
	{
		ob_start();
		$type = 'case-study';
		$class = 'mySwiper-ReviewsCaseStudy';
		echo '<section class="customer-reviews">';
		include(get_stylesheet_directory() . '/template-parts/modules/_customer_reviews.php');
		echo '</section>';

		return ob_get_clean();
	}

	function coptrz_review()
	{
		ob_start();
		echo '<section class="customer-reviews background-primary md-padding">';
		include(get_stylesheet_directory() . '/template-parts/modules/_customer_reviews.php');
		echo '</section>';

		return ob_get_clean();
	}

	function featured_blogs()
	{
		$DisplayData = new DisplayData;
		if (is_home()) {
			$title = 'Blogs';
			$suggested_articles = carbon_get_theme_option('suggested_articles');
			$taxonomy = 'category';
		} else if (is_post_type_archive('casestudies')) {
			$title = 'Case Studies';
			$suggested_articles = carbon_get_theme_option('featured_case_studies');
			$taxonomy = 'case_study_category';
		} else if (is_post_type_archive('guides')) {
			$title = 'Guides';
			$suggested_articles = carbon_get_theme_option('featured_guides');
			$taxonomy = 'Guide_category';
		} else if (is_post_type_archive('events')) {
			$title = 'Events';
			$suggested_articles = carbon_get_theme_option('featured_events');
			$taxonomy = 'events_category';
		} else if (is_post_type_archive('webinars')) {
			$title = 'Webinars';
			$suggested_articles = carbon_get_theme_option('featured_webinars');
			$taxonomy = 'webinars_category';
		}
		ob_start();
	?>
		<div class="featured-blogs">
			<div class="filter-header d-flex justify-content-between align-items-center">
				<label for="search"><strong>Featured <?= $title ?></strong></label>
			</div>

			<?php foreach ($suggested_articles as $articles) { ?>
				<?php
				$post_id = $articles['id'];
				$categories = get_the_terms($post_id, $taxonomy);
				$author_id = get_post_field('post_author', $post_id);
				$author_name = get_the_author_meta('display_name', $author_id);
				?>
				<div class="row">
					<a href="<?= get_permalink($post_id) ?>"></a>
					<div class="col-3">
						<?php
						$DisplayData->image(
							array(
								'image_id'    => get_post_thumbnail_id($post_id),
								'size'        => 'medium',
								'placeholder' => true
							),
							'position-relative image-cover-transform'
						);
						?>
					</div>
					<div class="col-9">
						<div class="meta-box d-flex flex-wrap">
							<?php if ($categories) { ?>
								<span class="date">
									<a href="<?= get_term_link($categories[0]->term_id, $taxonomy) ?>"><?= $categories[0]->name ?></a>
								</span>
							<?php } ?>
							<div class="bull">&bull;</div>
							<span class="author">

								<?= $author_name ?>
							</span>
						</div>
						<?php
						$DisplayData->heading(
							array(
								'heading' => get_the_title($post_id),
								'tag'     => 'h4'
							)
						);
						?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php

		return ob_get_clean();
	}

	function blog_meta()
	{
		ob_start();
		$reading_time = get__post_meta('reading_time');
	?>
		<div class="blog-meta">
			<div class="row">
				<div class="col-auto">
					<p><strong>Last updated on</strong></p>
					<p><?= get_the_date() ?></p>
				</div>
				<?php if ($reading_time) { ?>
					<div class="col-auto">
						<p><strong>Read time</strong></p>
						<p><?= $reading_time ?></p>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php
		return ob_get_clean();
	}

	function social_share()
	{
		global $post;
		$url = get_permalink($post->ID);
		$title = str_replace(' ', '%20', get_the_title($post->ID));
		
		$social_buttons = '';
		$social_buttons .= '<div class="social-share-buttons">';
		$social_buttons .= '<a href="https://www.facebook.com/sharer.php?u=' . $url . '&t=' . $title . '" target="_blank">Share on Facebook</a>';
		$social_buttons .= '<a href="https://twitter.com/share?url=' . $url . '&text=' . $title . '" target="_blank">Share on Twitter</a>';
		// Add more social networks here (e.g., LinkedIn, Pinterest, etc.)
		$social_buttons .= '</div>';
		return $social_buttons;
	}


	function post_link()
	{
		ob_start();
	?>
		<button onclick="copy_link()" class="post-link-copy">
			<input class="d-none" id="copy-link" value="<?= get_permalink(get_the_ID()) ?>">
			<span>Copy Link</span>
		</button>
		<script>
			function copy_link() {
				// Get the text field
				var copyText = document.getElementById("copy-link");

				// Select the text field
				copyText.select();
				copyText.setSelectionRange(0, 99999); // For mobile devices

				// Copy the text inside the text field
				navigator.clipboard.writeText(copyText.value);

				jQuery('.post-link-copy span').text('Link Copied');
			}
		</script>
	<?php
		return ob_get_clean();
	}

	function related_posts()
	{
		ob_start();
		include(get_stylesheet_directory() . '/template-parts/single/single-post/related.php');
		return ob_get_clean();
	}


	function scrolling_section()
	{
		ob_start();
		$post_is_global = true;
		include(get_stylesheet_directory() . '/template-parts/modules/_scrolling_section.php');
		return ob_get_clean();
	}

	function brands_slider()
	{
		ob_start();
		include(get_stylesheet_directory() . '/template-parts/global/vendor-slider.php');
		return ob_get_clean();
	}


	function announcement_bar()
	{
		ob_start();
		get_template_part('template-parts/header/header', 'announcement-bar');
		return ob_get_clean();
	}

	function shop_filter()
	{
		if (is_product_category()) {
			$filter_shortocde = get__term_meta(get_queried_object()->term_id, 'filter_shortocde');
			if ($filter_shortocde) {
				return do_shortcode($filter_shortocde);
			} else {
				return do_shortcode('[wpf-filters id=1]');
			}
		} else {
			return do_shortcode('[wpf-filters id=1]');
		}
	}

	function menu($atts)
	{
		extract(
			shortcode_atts(
				array(
					'id' => '',
				),
				$atts
			)
		);
		return wp_nav_menu(
			array(
				'menu' => $id,
				'container'      => false,
				'menu_class'     => '',
				'items_wrap'     => '<ul id="%1$s" class="menu-items list-inline p-0 %2$s">%3$s</ul>',
				'depth'          => 3,
				'echo' => false
			)
		);
	}

	function popup($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'id' => '',
					'is_shortcode' => true
				),
				$atts
			)
		);
		include(get_stylesheet_directory() . '/template-parts/shortcodes/popup.php');
		return ob_get_clean();
	}

	function mini_cart()
	{
		ob_start();
		include(get_stylesheet_directory() . '/template-parts/shortcodes/mini_cart.php');
		return ob_get_clean();
	}

	function search_form()
	{
		ob_start();
		include(get_stylesheet_directory() . '/template-parts/shortcodes/search_form.php');
		return ob_get_clean();
	}

	function mega_menu($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'id' => '',
				),
				$atts
			)
		);
		include(get_stylesheet_directory() . '/template-parts/shortcodes/mega_menu.php');
		return ob_get_clean();
	}

	function post_grid($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'id' => '',
					'class' => '',
					'popup_id' => false,
					'disable_button' => false
				),
				$atts
			)
		);
		include(get_stylesheet_directory() . '/template-parts/shortcodes/post_grid.php');
		return ob_get_clean();
	}
	function brands_grid($atts)
	{
		ob_start();
		extract(
			shortcode_atts(
				array(
					'id' => '',
				),
				$atts
			)
		);
		include(get_stylesheet_directory() . '/template-parts/shortcodes/brands.php');
		return ob_get_clean();
	}
}


$Shortcodes = new Shortcodes;
add_shortcode('brands_grid', array($Shortcodes, 'brands_grid'));
add_shortcode('post_grid', array($Shortcodes, 'post_grid'));
add_shortcode('mega_menu', array($Shortcodes, 'mega_menu'));
add_shortcode('search_form', array($Shortcodes, 'search_form'));
add_shortcode('mini_cart', array($Shortcodes, 'mini_cart'));
add_shortcode('popup', array($Shortcodes, 'popup'));
add_shortcode('announcement_bar', array($Shortcodes, 'announcement_bar'));
add_shortcode('shop_filter', array($Shortcodes, 'shop_filter'));
add_shortcode('menu', array($Shortcodes, 'menu'));
add_shortcode('brands_slider', array($Shortcodes, 'brands_slider'));
add_shortcode('contact_number', array($Shortcodes, 'contact_number'));
add_shortcode('email_address', array($Shortcodes, 'email_address'));
add_shortcode('post_title', array($Shortcodes, 'post_title'));
add_shortcode('post_title_html', array($Shortcodes, 'post_title_html'));
add_shortcode('get_param', array($Shortcodes, 'get_param'));
add_shortcode('svg', array($Shortcodes, 'svg'));
add_shortcode('site_logo', array($Shortcodes, 'site_logo'));
add_shortcode('contact_details', array($Shortcodes, 'contact_details'));
add_shortcode('socials', array($Shortcodes, 'socials'));
add_shortcode('woocommerce_icons', array($Shortcodes, 'woocommerce_icons'));
add_shortcode('address', array($Shortcodes, 'address'));
add_shortcode('opening_times', array($Shortcodes, 'opening_times'));
add_shortcode('site_url', array($Shortcodes, 'site_url'));
add_shortcode('webinar_box', array($Shortcodes, 'webinar_box'));
add_shortcode('webinar_date_time', array($Shortcodes, 'webinar_date_time'));
add_shortcode('login_link', array($Shortcodes, 'login_link'));
add_shortcode('login_button', array($Shortcodes, 'login_button'));
add_shortcode('brands', array($Shortcodes, 'brands'));
add_shortcode('featured_drones', array($Shortcodes, 'featured_drones'));
add_shortcode('terms', array($Shortcodes, 'terms'));
add_shortcode('post_term', array($Shortcodes, 'post_term'));
add_shortcode('login_dropdown', array($Shortcodes, 'login_dropdown'));
add_shortcode('breadcrumbs', array($Shortcodes, 'breadcrumbs'));
add_shortcode('testimonial_carousel', array($Shortcodes, 'testimonial_carousel'));
add_shortcode('featured_blogs', array($Shortcodes, 'featured_blogs'));
add_shortcode('blog_meta', array($Shortcodes, 'blog_meta'));
add_shortcode('social_share', array($Shortcodes, 'social_share'));
add_shortcode('post_link', array($Shortcodes, 'post_link'));
add_shortcode('coptrz_review', array($Shortcodes, 'coptrz_review'));

function add_to_cart_form_shortcode($atts)
{
	if (empty($atts)) {
		return '';
	}

	if (!isset($atts['id']) && !isset($atts['sku'])) {
		return '';
	}

	$args = array(
		'posts_per_page'      => 1,
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
	);

	if (isset($atts['sku'])) {
		$args['meta_query'][] = array(
			'key'     => '_sku',
			'value'   => sanitize_text_field($atts['sku']),
			'compare' => '=',
		);

		$args['post_type'] = array('product', 'product_variation');
	}

	if (isset($atts['id'])) {
		$args['p'] = absint($atts['id']);
	}

	$single_product = new WP_Query($args);

	$preselected_id = '0';


	if (isset($atts['sku']) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type) {

		$variation = new WC_Product_Variation($single_product->post->ID);
		$attributes = $variation->get_attributes();


		$preselected_id = $single_product->post->ID;


		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
			'p'                   => $single_product->post->post_parent,
		);

		$single_product = new WP_Query($args);
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var $variations_form = $('[data-product-page-preselected-id="<?php echo esc_attr($preselected_id); ?>"]').find('form.variations_form');
				<?php foreach ($attributes as $attr => $value) { ?>
					$variations_form.find('select[name="<?php echo esc_attr($attr); ?>"]').val('<?php echo esc_js($value); ?>');
				<?php } ?>
			});
		</script>
	<?php
	}

	$single_product->is_single = true;
	ob_start();
	global $wp_query;

	$previous_wp_query = $wp_query;

	$wp_query = $single_product;

	wp_enqueue_script('wc-single-product');
	while ($single_product->have_posts()) {
		$single_product->the_post()
	?>
		<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr($preselected_id); ?>">
			<?php woocommerce_template_single_add_to_cart(); ?>
		</div>
	<?php
	}

	$wp_query = $previous_wp_query;

	wp_reset_postdata();
	return '<div class="woocommerce">' . ob_get_clean() . '</div>';
}
add_shortcode('add_to_cart_form', 'add_to_cart_form_shortcode');


function catch_that_image()
{
	global $post, $posts;
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $post->post_content, $matches);
	$first_img = $matches[1][0];

	return $first_img;
}
function query_products()
{

	$args = array(

		'posts_per_page' => -1,

		'post_type'      => array('post'),

		'post_status'    => 'any',

	);
	$products = new WP_Query($args);
	echo '<ol>';

	while ($products->have_posts()) {
		$products->the_post();
		if (catch_that_image()) {
			$image = attachment_url_to_postid(catch_that_image());
			$post_thumb = get_the_post_thumbnail_url(get_the_ID());

			if (!$post_thumb) {
				if ($image) {
					echo '<li class="mb-3"> <a href="' . get_permalink() . '">';

					echo '<br>';


					echo get_the_title();

					echo '<br>';

					echo wp_get_attachment_image($image);

					set_post_thumbnail(get_the_ID(), $image);

					echo '</a><hr></li>';
				}
			}
		}
	}

	echo '</ul>';

	wp_reset_postdata();

	return;
}


add_shortcode('query_products', 'query_products');


/**
 * Returns the number of posts for a term in a taxonomy, for a post type
 * @param string $post_type
 * @param string $taxonomy
 * @return int count
 */
function get_post_count_for_term_and_post_type($taxonomy, $term_id, $post_type)
{

	// Build the args
	$args = array(
		'post_type' => $post_type,
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => $taxonomy,
				'field' => 'id',
				'terms' => $term_id,
			)
		)
	);

	// Get the posts
	$posts = get_posts($args);

	// Return the count
	return count($posts);
}

function delete_empty_taxonomy($atts)
{
	ob_start();
	extract(
		shortcode_atts(
			array(
				'taxonomy' => '',
				'post_type' => '',
				'delete' => 'false',
				'recount' => 'false',
			),
			$atts
		)
	);

	$terms = get_terms(array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
	));
	$key = 1;

	foreach ($terms as $term) {
		$count = get_post_count_for_term_and_post_type($taxonomy, $term->term_id, $post_type);

		if ($recount == 'true') {
			wp_update_term_count($term->term_id, $taxonomy);
		}

		if ($count == 0) {
			echo '<li>';
			echo $key;
			echo 'Count: ' . $count;
			echo 'Term: ' . $term->slug;

			if ($delete == 'true') {
				if (wp_delete_term($term->term_id, $taxonomy)) {
					echo $term->name . ' deleted';
				}
			}


			echo '<li>';
		}
		$key++;
	}

	return ob_get_clean();
}

add_shortcode('delete_empty_taxonomy', 'delete_empty_taxonomy');



function get_images_lists()
{
	ob_start();
	?>
	<style>
		.page-numbers {
			display: flex;
			flex-wrap: wrap;
			font-size: 20px;
			list-style: none;
		}

		.page-numbers li {
			margin-left: 30px;
			margin-right: 30px;
		}

		.page-numbers li * {
			font-size: 30px;
		}
	</style>
<?php
	$images_id = array(
		70482, 70481, 70480, 70478, 70476, 70467, 70462, 70461, 70460, 70453, 70452, 70451, 70448, 70447, 70439, 70436, 70437, 70429, 70430, 70420, 70415, 70414, 70362, 70363, 70359, 70357, 70358, 70353, 70354, 70355, 70350, 70352, 70349, 70346, 70344, 70341, 70342, 70337, 70332, 70330, 70329, 70325, 70321, 70315, 70313, 70311, 70310, 70308, 70306, 70304, 70301, 70300, 70299, 70295, 70293, 70291, 70281, 70279, 70277, 70276, 70274, 70272, 70268, 70261, 70262, 70258, 70259, 70254, 70253, 70247, 70243, 70241, 70235, 70233, 70232, 70231, 70229, 70224, 70222, 70217, 70219, 70213, 70210, 70208, 70204, 70201, 70181, 70179, 70172, 70173, 70170, 70169, 70167, 70165, 70163, 70161, 70162, 70159, 70157, 70154, 70153, 70150, 70147, 70145, 70146, 70142, 70141, 70137, 70135, 70134, 70130, 70131, 70129, 70127, 70125, 70123, 70121, 70122, 70120, 70117, 70118, 70115, 70114, 70106, 70108, 70099, 70101, 70102, 70095, 70088, 70086, 70084, 70078, 70080, 70076, 70074, 70070, 70068, 70040, 70038, 70025, 70006, 69984, 69985, 69981, 69982, 69983, 69980, 69978, 69949, 69919, 69917, 69914, 69903, 69898, 69892, 69885, 69884, 69881, 69882, 69877, 69871, 69866, 69865, 69864, 69862, 69863, 69859, 69857, 69858, 69855, 69845, 69846, 69847, 69844, 69840, 69841, 69826, 69822, 69821, 69820, 69814, 69813, 69811, 69812, 69802, 69800, 69798, 69799, 69797, 69795, 69796, 69792, 69793, 69794, 69791, 69784, 69780, 69779, 69777, 69778, 69774, 69776, 69770, 69772, 69768, 69764, 69761, 69760, 69757, 69753, 69755, 69750, 69751, 69747, 69748, 69749, 69745, 69746, 69739, 69741, 69736, 69737, 69732, 69733, 69734, 69722, 69720, 69717, 69718, 69719, 69713, 69714, 69715, 69708, 69709, 69704, 69707, 69702, 69699, 69694, 69695, 69697, 69690, 69691, 69693, 69686, 69687, 69689, 69683, 69685, 69670, 69664, 69619, 69591, 69589, 69590, 69585, 69553, 69544, 69538, 69540, 69541, 69535, 69536, 69537, 69531, 69532, 69533, 69529, 69530, 69521, 69522, 69523, 69518, 69519, 69520, 69516, 69513, 69511, 69504, 69490, 69491, 69492, 69483, 69480, 69481, 69482, 69475, 69478, 69479, 69471, 69473, 69474, 69468, 69469, 69464, 69465, 69466, 69457, 69458, 69459, 69449, 69450, 69452, 69455, 69439, 69441, 69443, 69444, 69448, 69434, 69369, 69364, 69368, 69350, 69353, 69355, 69361, 69345, 69346, 69347, 69348, 69333, 69338, 69339, 69318, 69323, 69324, 69330, 69251, 69245, 69247, 69248, 69244, 69238, 69239, 69234, 69235, 69236, 69223, 69226, 69227, 69230, 69232, 69217, 69220, 69221, 69208, 69209, 69211, 69213, 69112, 69104, 69110, 69098, 69086, 69050, 68990, 68997, 68974, 68977, 68987, 68970, 68954, 68959, 68947, 68932, 68938, 68923, 68878, 68882, 68887, 68868, 68874, 68827, 68813, 68817, 68822, 68809, 68664, 68667, 68660, 68661, 68659, 68646, 68647, 68645, 68644, 68640, 68641, 68637, 68636, 68615, 68616, 68613, 68593, 68594, 68595, 68596, 68590, 68591, 68592, 68586, 68587, 68588, 68584, 68585, 68571, 68568, 68569, 68570, 68565, 68566, 68567, 68561, 68562, 68563, 68557, 68558, 68560, 68556, 68551, 68552, 68553, 68550, 68541, 68537, 68533, 68534, 68529, 68530, 68532, 68515, 68506, 68507, 68508, 68505, 68488, 68489, 68485, 68486, 68482, 68484, 68481, 68476, 68474, 68459, 68456, 68458, 68453, 68455, 68450, 68451, 68449, 68447, 68448, 68445, 68444, 68443, 68437, 68436, 68434, 68431, 68432, 68427, 68428, 68424, 68425, 68426, 68421, 68423, 68420, 68417, 68409, 68410, 68403, 68404, 68402, 68401, 68396, 68397, 68398, 68399, 68394, 68395, 68383, 68375, 68372, 68373, 68374, 68371, 68362, 68360, 68361, 68356, 68325, 68324, 68322, 68308, 68305, 68306, 68307, 68303, 68299, 68300, 68301, 68273, 68274, 68272, 68270, 68271, 68268, 68269, 68265, 68266, 68263, 68264, 68260, 68262, 68253, 68254, 68251, 68252, 68246, 68247, 68244, 68243, 68242, 68241, 68240, 68236, 68237, 68223, 68221, 68222, 68212, 68209, 68208, 68207, 68206, 68198, 68197, 68195, 68196, 68194, 68189, 68185, 68184, 68180, 68173, 68172, 68171
	);
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$query_images_args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => 50,
		'paged' => $paged,
		'post__in' => $images_id
	);

	$query_images = new WP_Query($query_images_args);

	while ($query_images->have_posts()) {
		$query_images->the_post();
		$attched_file = get_post_meta(get_the_ID(), '_wp_attached_file', true);
		$meta_data = get_post_meta(get_the_ID(), '_wp_attachment_metadata', true);
		$attach_file_url = 'https://coptrz.com/wp-content/uploads/' . $attched_file;

		$year_date = explode('/', $attched_file);


		$folder_url = 'https://coptrz.com/wp-content/uploads/' . $year_date[0] . '/' . $year_date[1] . '/';
		$updated_file_base_url = $year_date[0] . '/' . $year_date[1] . '/';

		$folder_url_file = '/home/coptrzcp2/public_html/wp-content/uploads/' . $year_date[0] . '/' . $year_date[1] . '/';


		$new_meta_data = array();

		/*
		if (_url_is_valid($attach_file_url) == false) {
			$large = $folder_url . $meta_data['sizes']['large']['file'];
			if (_url_is_valid($large) == true) {
				$updated_file = $updated_file_base_url . $meta_data['sizes']['large']['file'];
				echo 'large: ' . _url_is_valid($large) . '<br>';
			} else {
				$medium_large = $folder_url . $meta_data['sizes']['medium_large']['file'];
				if (_url_is_valid($medium_large) == true) {
					$updated_file = $updated_file_base_url . $meta_data['sizes']['medium_large']['file'];
					echo 'medium_large: ' . $medium_large . '<br>';
				} else {
					$medium = $folder_url . $meta_data['sizes']['medium']['file'];
					if (_url_is_valid($medium) == true) {
						$updated_file = $updated_file_base_url . $meta_data['sizes']['medium']['file'];
					} else {
						$thumbnail = $folder_url . $meta_data['sizes']['thumbnail']['file'];
						if (_url_is_valid($thumbnail)) {
							$updated_file = $updated_file_base_url . $meta_data['sizes']['thumbnail']['file'];
						}
					}
				}
			}
		}*/

		/*
		if (_url_is_valid($attach_file_url) == false) {
			$large = $folder_url . $meta_data['sizes']['large']['file'];
			if (_url_is_valid($large) == true) {
				rename($folder_url_file, $meta_data['sizes']['large']['file']);
			} else {
				$medium_large = $folder_url_file . $meta_data['sizes']['medium_large']['file'];
				if (_url_is_valid($medium_large) == true) {
					rename($folder_url_file, $meta_data['sizes']['medium_large']['file']);
				} else {
					$medium = $folder_url . $meta_data['sizes']['medium']['file'];
					if (_url_is_valid($medium) == true) {
						rename($folder_url_file, $meta_data['sizes']['medium']['file']);
					} else {
						$thumbnail = $folder_url . $meta_data['sizes']['thumbnail']['file'];
						if (_url_is_valid($thumbnail)) {
							rename($folder_url_file, $meta_data['sizes']['thumbnail']['file']);
						}
					}
				}
			}
		}
*/

		//copy
		/*
		if (_url_is_valid($attach_file_url) == false) {
			$large = $folder_url . $meta_data['sizes']['large']['file'];
			echo 'attach_file_url is invalid' . '<br>';
			if (_url_is_valid($large) == true) {
				$file = $folder_url_file . $meta_data['sizes']['large']['file'];
				$newfile = $folder_url_file . end($year_date);
				_duplicate($file, $newfile);

				echo 'large is valid' . '<br>';
			} else {
				$medium_large = $folder_url . $meta_data['sizes']['medium_large']['file'];
				if (_url_is_valid($medium_large) == true) {
					$file = $folder_url_file . $meta_data['sizes']['medium_large']['file'];
					$newfile = $folder_url_file . end($year_date);
					_duplicate($file, $newfile);

					echo $medium_large . '<br>';
					echo $file . '<br>';
					echo $newfile . '<br>';
					echo 'medium_large is valid' . '<br>';
				} else {
					$medium = $folder_url . $meta_data['sizes']['medium']['file'];
					if (_url_is_valid($medium) == true) {
						$file = $folder_url_file . $meta_data['sizes']['medium']['file'];
						$newfile = $folder_url_file . end($year_date);
						_duplicate($file, $newfile);
					} else {
						$thumbnail = $folder_url . $meta_data['sizes']['thumbnail']['file'];
						if (_url_is_valid($thumbnail)) {
							$file = $folder_url_file . $meta_data['sizes']['thumbnail']['file'];
							$newfile = $folder_url_file . end($year_date);
													_duplicate($file, $newfile, true, get_the_ID());

						} 
					}
				}
			}

			echo 'url is invalid';
		}*/

		$large = $folder_url . $meta_data['sizes']['large']['file'];
		echo 'attach_file_url is invalid' . '<br>';
		if (_url_is_valid($large) == true) {
			$file = $folder_url_file . $meta_data['sizes']['large']['file'];
			$newfile = $folder_url_file . end($year_date);
			_duplicate($file, $newfile);

			echo 'large is valid' . '<br>';
		} else {
			$medium_large = $folder_url . $meta_data['sizes']['medium_large']['file'];
			if (_url_is_valid($medium_large) == true) {
				$file = $folder_url_file . $meta_data['sizes']['medium_large']['file'];
				$newfile = $folder_url_file . end($year_date);
				_duplicate($file, $newfile);

				echo $medium_large . '<br>';
				echo $file . '<br>';
				echo $newfile . '<br>';
				echo 'medium_large is valid' . '<br>';
			} else {
				$medium = $folder_url . $meta_data['sizes']['medium']['file'];
				if (_url_is_valid($medium) == true) {
					$file = $folder_url_file . $meta_data['sizes']['medium']['file'];
					$newfile = $folder_url_file . end($year_date);
					_duplicate($file, $newfile);
				} else {
					$thumbnail = $folder_url . $meta_data['sizes']['thumbnail']['file'];
					if (_url_is_valid($thumbnail)) {
						$file = $folder_url_file . $meta_data['sizes']['thumbnail']['file'];
						$newfile = $folder_url_file . end($year_date);
						_duplicate($file, $newfile, true, get_the_ID());
					}
				}
			}
		}

		echo 'File: ' . $file;
	}

	wp_reset_postdata();


	$big = 999999999; // need an unlikely integer

	echo paginate_links(array(
		'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
		'format' => '?paged=%#%',
		'current' => max(1, get_query_var('paged')),
		'total' => $query_images->max_num_pages,
		'mid_size' => 10,
		'prev_text'    => __('«'),
		'next_text'    => __('»'),
		'type'         => 'list'
	));


	return ob_get_clean();
}

add_shortcode('get_images_lists', 'get_images_lists');

function _duplicate($file, $newfile, $add_meta_if_failed = false, $image_id = false)
{
	ob_start();
	if (!copy($file, $newfile)) {
		echo "failed to copy $file...\n";

		if ($add_meta_if_failed) {
			add_post_meta($image_id, 'image_not_found', true);
		}
	} else {
		echo "copy success <br>";
	}
	return ob_get_clean();
}

function _url_is_valid($url = null)
{
	$code = '';
	if (is_null($url)) {
		return false;
	} else {
		$handle = curl_init($url);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($handle);
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($code == '200') {
			return true;
		} else {
			return false;
		}
		curl_close($handle);
	}
}

function get_empty_images()
{
	ob_start();
	$query_images_args = array(
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'image_not_found',
				'value' => true,
			),
		)
	);

	$query_images = new WP_Query($query_images_args);
	while ($query_images->have_posts()) {
		$query_images->the_post();

		echo get_the_ID() . '<br>';
	}
	return ob_get_clean();
}
add_shortcode('get_empty_images', 'get_empty_images');
