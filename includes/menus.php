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
		'post_parent' => 270611
	);
	$header_menu = wp_get_nav_menu_items($menuID, $args); // Get the array


	$html = '<nav class="navbar text-white p-0">';
	$html .= '<ul class="navbar-nav flex-row me-auto mb-2 mb-lg-0">';

	foreach ($header_menu as $menu) {
		$title = $menu->title;
		$ID = $menu->ID;
		$url = $menu->url;
		$menu_item_parent = $menu->menu_item_parent;
		if ($menu_item_parent == 0) {
			$html .= '<li class="nav-item">';
			$html .= "<a class='nav-link text-white' href='$url'>$title</a>";
		}

		if ($menu_item_parent == $ID) {
			$html .= '<div class="submenu-level-1">';

			$html .= '</div>';
		}

		if ($menu_item_parent == 0) {
			$html .= '</li>';
		}
	}

	$html .= '</ul>';
	$html .= '</nav>';
	return $html;
}
