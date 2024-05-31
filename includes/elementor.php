<?php
function register_new_widgets($widgets_manager)
{
	require_once(__DIR__ . '/elementor-widgets/brands-slider/brands-slider.php');
	$widgets_manager->register(new \Elementor_Brands_Slider());
}
add_action('elementor/widgets/register', 'register_new_widgets');



function add_elementor_widget_categories($elements_manager)
{

	$elements_manager->add_category(
		'Coptrz',
		[
			'title' => esc_html__('Coptrz', 'textdomain'),
			'icon'  => 'fa fa-plug',
		]
	);
}
add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');



function sectors_query($query)
{

	// Get current meta Query
	$meta_query = $query->get('meta_query');

	// If there is no meta query when this filter runs, it should be initialized as an empty array.
	if (!$meta_query) {
		$meta_query = [];
	}

	// Append our meta query
	$meta_query[] = [
		'key' => '_hide_on_list',
		'value' => 'yes',
		'compare' => 'NOT IN',
	];

	$query->set('meta_query', $meta_query);
}
add_action('elementor/query/sectors', 'sectors_query');
