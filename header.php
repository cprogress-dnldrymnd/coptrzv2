<!DOCTYPE html>
<html <?php language_attributes(); ?> class="html">

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="">
	<meta name="format-detection" content="telephone=no">
	<?php if (!is_404() && !is_search()) { ?>
		<link rel="canonical" href="<?= canonical() ?>" />
	<?php } ?>
	<title>
		<?php wp_title('') ?>
	</title>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
	<?php
	$hide_header = false;
	if (is_singular()) {
		$header_post_id = get_the_ID() ?: get_queried_object_id();
		if ($header_post_id) {
			$hide_header = get__post_meta_by_id($header_post_id, 'hide_header');
		}
	}
	// A `layouts` post can be flagged Display Location = Header (includes/post-meta.php,
	// enforced unique at save time in includes/hooks.php) to take over the banner +
	// <header> below entirely, editable without a deploy. Falls back to the hardcoded
	// markup when no layout is flagged.
	$header_layout_id = function_exists('coptrz_get_header_layout_id') ? coptrz_get_header_layout_id() : 0;
	if (!$hide_header) {
		if (function_exists('coptrz_render_header_location_layouts')) {
			coptrz_render_header_location_layouts('before_header');
		}
		if ($header_layout_id) {
			global $layouts_global;
			$layouts_global[] = $header_layout_id;
			echo do_shortcode('[layouts id="' . $header_layout_id . '"]');
		} else { ?>
		<?php if (!is_404()) { ?>
			<!--
		<div
			class="top-bar rounded-corner bg-primary mx-20px mt-20px d-flex align-items-center justify-content-center text-center">
			<div class="container" style="color: #ffffff; text-transform: uppercase;">
				Call now - <a href="tel:03301117177">0330 111 7177</a>
			</div>
		</div>
		-->
			<div
				class="banner-topbar rounded-corner bg-primary mx-20px mt-20px d-flex align-items-center justify-content-center text-center">
				<a href="https://shop.coptrz.com/collections/rpc-l1-part-a-summer-sale?utm_source=coptrz.com&utm_medium=email&utm_campaign=2026_website_summer-sale_coptrz-rpc-l1-part-a-courses_b2c" target="_blank" class="desktop-only">
					<img src="https://coptrz.com/wp-content/uploads/2026/06/announcement-bar-desktop-2.svg" alt="RPC Promo Banner">
				</a>
				<a href="https://shop.coptrz.com/collections/rpc-l1-part-a-summer-sale?utm_source=coptrz.com&utm_medium=email&utm_campaign=2026_website_summer-sale_coptrz-rpc-l1-part-a-courses_b2c" target="_blank" class="mobile-only d-none">
					<img src="https://coptrz.com/wp-content/uploads/2026/06/announcement-bar-mobile.svg" alt="RPC Promo Banner">
				</a>
			</div>
		<?php } ?>

		<header class="header small-text overflow-hidden">
			<div class="container">
				<div class="header-inner mt-20px rounded-10px">
					<div class="row justify-content-between">
						<?php get_template_part('template-parts/header/header-left') ?>
						<?php get_template_part('template-parts/header/header-menu') ?>
						<?php get_template_part('template-parts/header/header-right') ?>
					</div>
				</div>
			</div>
		</header>
		<?php }
		if (function_exists('coptrz_render_header_location_layouts')) {
			coptrz_render_header_location_layouts('after_header');
		}
	} ?>
	<?php wp_body_open(); ?>
	<?php
	$class = '';

	if (!is_404()) {
		$class = 'mt-20px';
	}
	global $popups_id;
	$popups_id[] = 268179;
	if (is_product()) {
		$popups_id[] = 299743;
	}

	?>
	<div class="site-wrapper position-relative">
		<?php
		if (function_exists('coptrz_render_header_location_layouts')) {
			coptrz_render_header_location_layouts('before_main');
		}
		?>
	<main class="<?= $class ?>">