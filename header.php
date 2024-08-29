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
			<div class="container">
				<a href="tel:03301659412" class="text-white">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone"
						viewBox="0 0 16 16">
						<path
							d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z">
						</path>
					</svg>
					Call us now on <u>0330 165 9412</u>
				</a>
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
		<?php
		$copy_from = $_GET['copy_from'];
		$copy_after = $_GET['copy_after'];
		$training = $_GET['training'];
		$software = $_GET['software'];
		$accessories = $_GET['accessories'];
		$related_casestudies = $_GET['related_casestudies'];
		$drones = $_GET['drones'];
		if ($copy_from) {
			$sections = get__post_meta_by_id($copy_from, 'sections');
			carbon_set_post_meta(get_the_ID(), 'sections', $sections);
			update_post_meta(get_the_ID(), '_sections_html', '');

			if ($copy_after == 'true') {
				$sections_after_main = get__post_meta_by_id($copy_from, 'sections_after_main');
				carbon_set_post_meta(get_the_ID(), 'sections_after_main', $sections_after_main);
				update_post_meta(get_the_ID(), '_sections_after_main_html', '');
			}

			if ($training == 'true') {
				$related_training = get__post_meta_by_id($copy_from, 'related_training');
				carbon_set_post_meta(get_the_ID(), 'related_training', $related_training);
			}

			if ($software == 'true') {
				$related_software = get__post_meta_by_id($copy_from, 'softwares');
				carbon_set_post_meta(get_the_ID(), 'softwares', $related_software);
			}

			if ($drones == 'true') {
				$drones = get__post_meta_by_id($copy_from, 'drones');
				carbon_set_post_meta(get_the_ID(), 'drones', $drones);
			}

			if ($accessories == 'true') {
				$accessories = get__post_meta_by_id($copy_from, 'accessories');
				carbon_set_post_meta(get_the_ID(), 'accessories', $accessories);
			}
			if ($related_casestudies == 'true') {
				$related_casestudies = get__post_meta_by_id($copy_from, 'related_casestudies');
				carbon_set_post_meta(get_the_ID(), 'related_casestudies', $related_casestudies);
			}

		}
