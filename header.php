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
	<?php if (!is_404()) { ?>
		<div
			class="top-bar rounded-corner bg-primary mx-20px mt-20px d-flex align-items-center justify-content-center text-center">
			<div class="container" style="color: #ffffff; text-transform: uppercase;">
				Free training course with every drone purchase
			</div>
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
	<?php wp_body_open(); ?>
	<?php
	if (!is_404()) {
		$class = 'mt-20px';
	}

	?>
	<main class="<?= $class ?>">