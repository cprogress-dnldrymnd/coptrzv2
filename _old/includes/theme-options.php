<?php
class Theme_Options extends Helpers
{
	public static function logo()
	{
		return '<a class="site-logo full-logo" href="' . get_site_url() . '"> <img src="' . wp_get_attachment_image_url(get__theme_option('logo'), 'medium') . '" alt="' . get_bloginfo('name') . '"> </a>';
	}

	public static function alt_logo()
	{
		return '<a class="site-logo alt-logo" href="' . get_site_url() . '"> <img src="' . wp_get_attachment_image_url(get__theme_option('alt_logo'), 'large') . '" alt="' . get_bloginfo('name') . '"></a>';
	}


	public static function contact_number_url()
	{
		return 'tel:' . get__theme_option('contact_number');
	}


	public static function email_address_url()
	{
		return 'tel:' . get__theme_option('email_address');
	}

	
	public static function contact_number()
	{
		return '<a href="' . Theme_Options::contact_number_url() . '">' . get__theme_option('contact_number') . '</a>';
	}
	public static function email_address()
	{
		return '<a href="' . Theme_Options::email_address_url() . '">' . get__theme_option('email_address') . '</a>';
	}
}
