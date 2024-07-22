<!DOCTYPE html>
<html <?php language_attributes(); ?> class="html">

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<meta name="author" content="">
	<meta name="format-detection" content="telephone=no">
	<title>
		<?php bloginfo('name'); // show the blog name, from settings 
		?> |
		<?php is_front_page() ? bloginfo('description') : wp_title(''); // if we're on the home page, show the description, from the site's settings - otherwise, show the title of the post or page 
		?>
	</title>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
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
	<?php wp_body_open(); ?>
	<?php
	if (!is_404()) {
		$class = 'mt-20px';
	}
	?>
	<main class="<?= $class ?>">

		<?php


		$args = array(
			'numberposts' => -1,
			'post_type'   => 'product',
			'fields' => 'ids',
		);

		$products = get_posts($args);

		foreach ($products as $product) {
		}

		$sections = get__post_meta_by_id(61453, 'sections');
		$sections_after_main = get__post_meta_by_id(61453, 'sections_after_main');

		carbon_set_post_meta(61610, 'sections', $sections);
		carbon_set_post_meta(61610, 'sections_after_main', $sections_after_main);

		$data = array(
			'ID' => 61610,
			'post_content' => '',
		);

		wp_update_post($data);
