<?php
/*-----------------------------------------------------------------------------------*/
/* Register main menu for Wordpress use
/*-----------------------------------------------------------------------------------*/
function menu_locations()
{
	register_nav_menus(
		array(
			'header-menu'	=>	__('Header Menu'),
		)
	);
}

add_action('init', 'menu_locations');


function header_menu()
{
	$menuLocations = get_nav_menu_locations(); // Get our nav locations (set in our theme, usually functions.php)
	// This returns an array of menu locations ([LOCATION_NAME] = MENU_ID);

	$menuID = $menuLocations['header-menu']; // Get the *primary* menu ID

	$args = array(
		'post_parent' => 0
	);
	$header_menu = wp_get_nav_menu_items($menuID, $args); // Get the array


	$html = '<nav class="navbar text-white p-0">';
	$html .= '<ul class="navbar-nav flex-row me-auto mb-2 mb-lg-0">';
	$menus_array = array();
	foreach ($header_menu as $menu) {
		$title = $menu->title;
		$ID = $menu->ID;
		$url = $menu->url;
		$menu_item_parent = $menu->menu_item_parent;
		$menus_array[] = array(
			'menu_item_parent' => $menu_item_parent,
			'title' => $title,
			'ID' => $ID,
			'url' => $url,
		);
	}

	foreach ($menus_array as $menu) {
		$title = $menu['title'];
		$ID = $menu['ID'];
		$url = $menu['url'];
		$menu_item_parent = $menu['menu_item_parent'];
		if ($menu_item_parent == 0) {
			$html .= '<li class="nav-item">';
			$html .= "<a class='nav-link text-white' href='$url'>$title</a>";
		}

		$found_key = array_search($ID, array_column($menus_array, 'menu_item_parent'));

		$html .= var_dump($found_key);

		if ($menu_item_parent == 0) {
			$html .= '</li>';
		}
	}

	$html .= '</ul>';
	$html .= '</nav>';
	return $html;
}
