<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
$DisplayData = new DisplayData;
?>
<?php if (is_shop()) { ?>
	<?php
	get_template_part('template-parts/woocommerce/archive', 'banner');
	get_template_part('template-parts/global/vendor-slider');
	echo do_shortcode('[elementor-template id="106737"]');
	?>


	<?php
	//$post_is_global = true;
	//include(locate_template('/template-parts/modules/_cta.php'));
	?>
	<?= product_display(true); ?>

	<section class="two-columns two-columns-v2 xl-padding-bottom">
		<div class="container">
			<div class="row-holder">
				<div class="row gx-7 background-white">
					<div class="col-lg-6 col-image">
						<div class="column-holder h-100">
							<div class="image-box h-100">
								<img src="<?= content_url() ?>/uploads/2022/12/police-drone.png" alt="">
							</div>
						</div>
					</div>
					<div class="col-lg-6 d-flex align-items-center">
						<div class="column-holder column-text content-margin pt-3 pb-3">
							<div class="heading-box ">
								<span class="prefix">COPTRZ ACADEMY</span>
								<h2>
									<strong> Expertly created e-learning drone courses </strong>
								</h2>
							</div>
							<div class="description-box">
								<p>
									Studying with us gives you the qualifications, tools and continual development you need
									to be the best drone operator in your industry.
								</p>
							</div>

							<div class="button-group-box d-flex flex-wrap">
								<div class="button-box button-accent">
									<a href="https://coptrz.com/product-category/e-learning-courses/">EXPLORE COURSE</a>
								</div>
								<div class="button-box button-bordered d-none">
									<a href="">FIND OUT MORE</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php } else { ?>
	<?php
	get_template_part('template-parts/woocommerce/archive', 'banner');
	if (is_product_category()) {
		echo product_display_category(true);
	} else {
		echo product_display(false);
	}
	echo do_shortcode('[elementor-template id="106737"]');
	echo do_shortcode('[elementor-template id="126927"]');
	?>
<?php } ?>

<?php get_footer('shop');
