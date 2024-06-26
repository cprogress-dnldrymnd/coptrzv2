<?php
/*-----------------------------------------------------------------------------------*/
/* Register main menu for Wordpress use
/*-----------------------------------------------------------------------------------*/
function menu_locations()
{

	register_nav_menus(
		array(

			'header-menu'	=>	__('Header Menu'),
			'header-menu-2'  => __('Header Menu 2'),
			'mobile-menu'	=>	__('Mobile Menu'),
			'footer-links'	=>	__('Footer Links'),
		)

	);
}

add_action('init', 'menu_locations');

function get_menu_list_array()
{
	$menu_array = array();
	$menus = wp_get_nav_menus(); // Get all registered navigation menus

	foreach ($menus as $menu) {
		$menu_array[$menu->term_id] = $menu->name; // Store ID as key, name as value
	}

	return $menu_array;
}
