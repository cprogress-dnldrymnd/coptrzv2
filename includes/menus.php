<?php 
/*-----------------------------------------------------------------------------------*/
/* Register main menu for Wordpress use
/*-----------------------------------------------------------------------------------*/
function menu_locations() {

	register_nav_menus(
		array(

			'header-menu'	=>	__( 'Header Menu'),
			'header-menu-2'  => __('Header Menu 2'),
			'mobile-menu'	=>	__( 'Mobile Menu'), 
			'footer-links'	=>	__( 'Footer Links'), 
		)

	);

}

add_action( 'init', 'menu_locations' );