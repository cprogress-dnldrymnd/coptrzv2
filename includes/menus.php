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


	$html = '<nav class="navbar position-static text-white p-0">';
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
		$ID = $menu['ID'];
		$menu_item_parent = $menu['menu_item_parent'];
		if ($menu_item_parent == 0) {
			$html .= '<li class="nav-item">';
			$html .= '<a class="nav-link text-white" href="' . $menu['url'] . '">' . $menu['title'] . '</a>';
			$submenus1 = array_filter($menus_array, function ($var) use ($ID) {
				return ($var['menu_item_parent'] == $ID);
			});

			if ($submenus1) {
				$html .= '<div class="submenu">';
				$html .= '<ul class="list-inline d-flex p-0">';
				foreach ($submenus1 as $submenu1) {
					$html .= '<li>';
					$html .= '<a  href="' . $submenu1['url'] . '">' . $submenu1['title'] . '</a>';
					$html .= '</li>';
				}
				$html .= '</ul>';
				$html .= '</div>';
			}

			$html .= '</li>';
		}
	}

	$html .= '</ul>';
	$html .= '</nav>';
	return $html;
}
