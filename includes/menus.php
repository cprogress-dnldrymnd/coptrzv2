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

	if (current_user_can('administrator')) {
		echo '<pre>';
		var_dump($header_menu);
		echo '</pre>';
	}

	$html = '<nav class="navbar text-white p-0">';
	$html .= '<ul class="navbar-nav flex-row me-auto mb-2 mb-lg-0">';

	foreach ($header_menu as $menu) {
		$title = $menu->title;
		$url = $menu->url;
		$menu_item_parent = $menu->menu_item_parent;
		if ($menu_item_parent == 0) {
			$html_submenu = '';
			$html .= '<li class="nav-item">';
			$html .= "<a class='nav-link text-white' href='$url'>$title</a>";
			$html .= '</li>';
		} else {
		}
	}

	$html .= '</ul>';
	$html .= '</nav>';
	return $html;
}
