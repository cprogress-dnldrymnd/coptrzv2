<?php
add_action('wp_ajax_nopriv_buy_now_ajax', 'buy_now_ajax'); // for not logged in users
add_action('wp_ajax_buy_now_ajax', 'buy_now_ajax');
function buy_now_ajax()
{
	$buy_now_id = $_POST['buy_now_id'];
	if ($buy_now_id) {
		global $woocommerce;
		$woocommerce->cart->empty_cart();
		$woocommerce->cart->add_to_cart($buy_now_id);
	}
	die();
}


add_action('wp_ajax_nopriv_archive_ajax', 'archive_ajax'); // for not logged in users
add_action('wp_ajax_archive_ajax', 'archive_ajax');
function archive_ajax()
{

	$args['post_type'] = $post_type;
	$args['posts_per_page'] = $posts_per_page;
	$args['post_status'] = 'publish';


	if ($terms || $terms_category) {
		if ($taxonomy != 'category') {
			$args['tax_query'] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $terms . $terms_category,
				),
			);
		} else {
			$args['cat'] = $terms . $terms_category;
		}
	}

	if ($s) {
		$args['s'] = $s;
	}



	$the_query = new WP_Query($args);


	die();
}
