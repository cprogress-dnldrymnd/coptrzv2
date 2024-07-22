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
	$data = $_POST['data'];
	$query = $_POST['query'];
	$s = isset($_POST['s']) ? $_POST['s'] : false;
	$events_type = isset($_POST['s']) ? $_POST['events_type'] : false;

	$posts_per_page = isset($_GET['posts_per_page']) ? $_GET['posts_per_page'] : false;
	$data_val = json_decode(stripslashes($data), true);
	$query_val = json_decode(stripslashes($query), true);
	if ($posts_per_page) {
		$args['posts_per_page'] = $posts_per_page;
	}

	$args = $query_val;

	if ($events_type) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'events_type',
				'field' => 'term_id',
				'terms' => $events_type,
			),
		);
	}

	$args['post_type'] = $data_val['post_type'];

	if ($s) {
		$args['s'] = $s;
	}

	$the_query = new WP_Query($args);
	echo '<div class="row g-4 same-image-height">';
	echo '<pre>';
	var_dump($args);
	echo '</pre>';
	if ($the_query->have_posts()) {
		while ($the_query->have_posts()) {
			$the_query->the_post();
			$data_val['id'] = get_the_ID();
			if (get_post_type() == 'events') {
				$data_val['additional_content'] = _events_additional_content(get_the_ID());
			}
			echo __post_box($data_val);
		}
	} else {
		echo '<div class="col-12 text-center -margin-top">';
		echo "<h2>No results found for $s</h2>";
		echo '</div>';
	}
	echo '</div>';

	wp_reset_postdata();


	die();
}


add_action('wp_ajax_nopriv_training_ajax', 'training_ajax'); // for not logged in users
add_action('wp_ajax_training_ajax', 'training_ajax');
function training_ajax()
{
	$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : 'online-self-paced';
	$delivery_method = isset($_POST['delivery_method']) ? $_POST['delivery_method'] : 'online-self-paced';
	$location = isset($_POST['location']) ? $_POST['location'] : false;
	custom_product_variation_training($product_id, $delivery_method, $sortby, $location);
	die();
}
