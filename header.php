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

		if (get_post_type() ==  'industries' && get_the_ID() != 65092) {

			$sections = get__post_meta_by_id(65092, 'sections');
			$hero_description = get__post_meta_by_id(65092, 'hero_description');
			$hero_height = get__post_meta_by_id(65092, 'hero_height');
			$hero_alignment = get__post_meta_by_id(65092, 'hero_alignment');

			$hero_form_enable = get__post_meta_by_id(65092, 'hero_form_enable');
			$hero_form_image = get__post_meta_by_id(65092, 'hero_form_image');
			$hero_form_heading = get__post_meta_by_id(65092, 'hero_form_heading');
			$hero_form_description = get__post_meta_by_id(65092, 'hero_form_description');
			$hero_form_style = get__post_meta_by_id(65092, 'hero_form_style');
			$hero_form = get__post_meta_by_id(65092, 'hero_form');



			$sections_r = get__post_meta_by_id(get_the_ID(), 'sections');
			if (!$sections_r) {
				carbon_set_post_meta(get_the_ID(), 'sections', $sections);
				carbon_set_post_meta(get_the_ID(), 'hero_description', $hero_description);
				carbon_set_post_meta(get_the_ID(), 'hero_height', $hero_height);
				carbon_set_post_meta(get_the_ID(), 'hero_alignment', $hero_alignment);
				carbon_set_post_meta(get_the_ID(), 'hero_form_enable', $hero_form_enable);
				carbon_set_post_meta(get_the_ID(), 'hero_form_image', $hero_form_image);
				carbon_set_post_meta(get_the_ID(), 'hero_form_heading', $hero_form_heading);
				carbon_set_post_meta(get_the_ID(), 'hero_form_description', $hero_form_description);
				carbon_set_post_meta(get_the_ID(), 'hero_form_style', $hero_form_style);
				carbon_set_post_meta(get_the_ID(), 'hero_form', $hero_form);
			}


			/*
			$sections_after_main = get__post_meta_by_id(65092, 'sections_after_main');

			$sections_after_main_r = get__post_meta_by_id(get_the_ID(), 'sections_after_main');

			if (!$sections_after_main_r) {
				carbon_set_post_meta(get_the_ID(), 'sections_after_main', $sections_after_main);
			}*/

			$data = array(
				'ID' => get_the_ID(),
				'post_content' => '',
			);

			wp_update_post($data);
		}
