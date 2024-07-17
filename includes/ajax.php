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

	$s = isset($_POST['s']) ? $_POST['s'] : false;
	$posts_per_page = isset($_GET['posts_per_page']) ? $_GET['posts_per_page'] : false;


	if (is_home()) {
		$args['post_type'] = 'post';
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
		}
	}

	if ($s) {
		$args['s'] = $s;
	}



	$the_query = new WP_Query($args);


	die();
}
