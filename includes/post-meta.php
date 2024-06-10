<?php

use Carbon_Fields\Container;
use Carbon_Fields\Complex_Container;
use Carbon_Fields\Field;


/*-----------------------------------------------------------------------------------*/
/* Theme Options
/*-----------------------------------------------------------------------------------*/

Container::make('theme_options', __('Theme Options'))
	->set_page_parent('themes.php')
	->add_fields(
		array(
			Field::make('html', 'site_logo_html')->set_html('<label> SITE LOGO </label>')->set_classes('seperator '),
			Field::make('image', 'logo', 'Logo')->set_width(33),
			Field::make('image', 'alt_logo', 'Alt Logo')->set_width(33),
			Field::make('html', 'contact_details_html')->set_html('<label> CONTACT DETAILS </label>')->set_classes('seperator '),
			Field::make('text', 'contact_number', 'Contact Number'),
			Field::make('text', 'email_address', 'Email Address'),
			Field::make('image', 'placeholder_image', 'Placeholder Image'),
		)
	);



/*-----------------------------------------------------------------------------------*/
/* Post Options
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Post Options')
	->where('post_type', '=', 'post')
	->set_context('side')
	->add_fields(
		array(
			Field::make('text', 'reading_time', 'Reading Time'),

		)
	);

Container::make('post_meta', 'Tag Options')
	->where('post_type', '=', 'page')
	->or_where('post_type', '=', 'product')
	->or_where('post_type', '=', 'solutions')
	->set_context('side')
	->add_fields(
		array(
			Field::make('checkbox', 'use_microsoft_ads_uet_tag', 'Use Microsoft Ads UET Tag'),
			Field::make('checkbox', 'use_linkedin_insight_tag', 'Use LinkedIn Insight Tag'),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Page Options
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Page Options')
	->where('post_type', '=', 'page')
	->set_context('side')
	->add_fields(
		array(
			Field::make('checkbox', 'hide_header', 'Hide Header'),
			Field::make('checkbox', 'hide_footer', 'Hide Footer'),

		)
	);

/*-----------------------------------------------------------------------------------*/
/* Industry Solution
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Industry Options')
	->or_where('post_type', '=', 'solutions')
	->add_fields(
		array(
			Field::make('checkbox', 'hide_on_list', 'Hide on List'),
			Field::make('image', 'icon', 'Icon'),
			Field::make('textarea', 'short_descr', 'Short Description'),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Testimonial
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Testimonial Content')
	->where('post_type', '=', 'testimonials')
	->add_fields(
		array(
			Field::make('text', 'testimonial_title', 'Testimonial Title'),
			Field::make('textarea', 'testimonial_content', 'Testimonial Content'),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* CSS, Header, Body and Footer Scripts
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Custom CSS / Header Scripts / Body Scripts / Footer Scripts')
	->set_priority('high')
	->where('post_type', '=', 'post')
	->or_where('post_type', '=', 'page')
	->or_where('post_type', '=', 'services')
	->add_fields(
		array(
			Field::make('textarea', 'page_custom_css', 'Custom CSS'),
			Field::make('header_scripts', 'page_header_scripts', __('Header Scripts')),
			Field::make('textarea', 'page_body_scripts', __('Body Scripts')),
			Field::make('footer_scripts', 'page_footer_scripts', __('Footer Scripts')),
		)
	);


/*-----------------------------------------------------------------------------------*/
/* Header, Body and Footer Scripts
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('→Header, Body and Footer Scripts'))
	->set_page_parent('themes.php')
	->add_fields(
		array(
			Field::make('header_scripts', 'header_scripts', __('Header Scripts')),
			Field::make('textarea', 'body_scripts', __('Body Scripts')),
			Field::make('footer_scripts', 'footer_scripts', __('Footer Scripts'))
		)
	);


/*-----------------------------------------------------------------------------------*/
/* Testimonials
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Settings'))
	->set_page_parent('edit.php?post_type=testimonials')
	->add_fields(
		array(
			Field::make('text', 'testimonial_heading', 'Heading'),
			Field::make('text', 'testimonial_rating', 'Rating'),
		)
	);


/*-----------------------------------------------------------------------------------*/
/* Product Attributes
/*-----------------------------------------------------------------------------------*/
Container::make('term_meta', __('Category Properties'))
	->where('term_taxonomy', '=', 'pa_brands')
	->add_fields(
		array(
			Field::make('textarea', 'menu_description', __('Menu Description')),
			Field::make('image', 'menu_icon', 'Menu Icon'),
			Field::make('image', 'image', __('Logo')),
			Field::make('image', 'featured_product_image', 'Featured Product Logo'),

			Field::make('complex', 'featured_boxes', 'Featured Boxes')
				->add_fields(
					array(
						Field::make('text', 'prefix', 'Prefix'),
						Field::make('text', 'heading', 'Heading'),
						Field::make('textarea', 'description', 'Description'),
						Field::make('text', 'link', 'Link'),
						Field::make('image', 'image', 'Image'),

					)
				)
				->set_layout('grid')
				->set_header_template('<%- heading  %>'),
			Field::make('checkbox', 'hide_vendor', 'Hide Vendor On Slider'),
			Field::make('checkbox', 'hide_vendor_on_menu', 'Hide Vendor On Menu'),
			Field::make('checkbox', 'featured_vendor', 'Featured Vendor'),

		)
	);

/*-----------------------------------------------------------------------------------*/
/* Product Category
/*-----------------------------------------------------------------------------------*/
Container::make('term_meta', __('Category Properties'))
	->where('term_taxonomy', '=', 'product_cat')
	->add_fields(
		array(
			Field::make('checkbox', 'display_in_shop', __('Display in Shop Page')),
			Field::make('text', 'menu_order', __('Menu Order'))->set_default_value(0),
			Field::make('text', 'filter_shortocde', __('Filter Shortcode'))
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Product
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Product Components')
	->set_priority('high')
	->where('post_type', '=', 'product')
	->where('post_template', '!=', 'templates/page-training.php')
	->add_tab(
		'Product Options',
		array(
			Field::make('html', 'sep_1')->set_html('<label>ENQUIRE NOW BUTTON SETTINGS</label>')->set_classes('seperator '),
			Field::make('select', 'button_type', 'Button Type')
				->set_options(
					array(
						''                       => 'Default',
						'replace_enquire_button' => 'Replace enquire button text and link',
						'link_to_form'           => 'Link to a form within the page',
					)
				),

			Field::make('text', 'cst_btn_link', 'Button Text')->set_width(50)
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'replace_enquire_button',
						)
					)
				),
			Field::make('text', 'cst_btn_text', 'Button Link')->set_width(50)
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'replace_enquire_button',
						)
					)
				),
			Field::make('textarea', 'enquire_now_form', 'Enquire Now Form')
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'link_to_form',
						)
					)
				),
			Field::make('textarea', 'menu_description', 'Menu Description'),
			Field::make('html', 'sep_2')->set_html('<label>OTHER OPTIONS</label>')->set_classes('seperator '),
			Field::make('checkbox', 'finance_available', 'Finance Available?'),
			Field::make('checkbox', 'business_invoicing', 'Business Invoicing?'),
			Field::make('checkbox', 'hide_product_summary', 'Hide Product Summary'),
			Field::make('checkbox', 'free_shipping', 'Free Shipping'),
			Field::make('text', 'lead_time', 'Lead Time'),
		)
	)
	->add_tab(
		'Featured Video',
		array(
			Field::make('select', 'featured_video_type', __('Featured Video Type'))
				->set_options(
					array(
						'dark-video'  => 'Dark',
						'light-video' => 'Light',
					)
				),
			Field::make('text', 'featured_video_text', __('Featured Video Heading')),
			Field::make('textarea', 'featured_video_description', __('Featured Video Description')),
			Field::make('file', 'featured_video_file', __('Featured Video File')),
		)
	)
	->add_tab(
		'Specifications',
		array(
			Field::make('complex', 'specifications', '')
				->add_fields(
					array(
						Field::make('text', 'heading', 'Heading'),
						Field::make('textarea', 'description', 'Description'),
						Field::make('image', 'icon', 'Icon')
					)
				)
				->set_layout('tabbed-vertical')
				->set_header_template('<%- heading  %>')
		)
	);
/*-----------------------------------------------------------------------------------*/
/* Product - Training
/*-----------------------------------------------------------------------------------*/

Container::make('theme_options', __('Training Settings'))
	->set_page_parent('edit.php?post_type=product')
	->add_tab(
		'Why chose us',
		array(
			Field::make('complex', 'why_choose_us', 'Why choose us')
				->add_fields(
					array(
						Field::make('image', 'icon', 'Icon'),
						Field::make('text', 'heading', 'Heading'),
						Field::make('textarea', 'description', 'Description'),
					)
				)
				->set_header_template('<%- heading  %>')
				->set_layout('tabbed-vertical')
		)
	)
	->add_tab(
		'Customer Slider',
		array(
			Field::make('select', 'image_source', 'Image Source')
				->set_options(
					array(
						'select-from-gallery' => 'Select from Gallery',
						'custom-gallery'      => 'Custom Images',
					)
				),
			Field::make('text', 'custom_heading', 'Custom Heading')
				->set_conditional_logic(
					array(
						array(
							'field' => 'image_source',
							'value' => 'select-from-gallery',
						)
					)
				),
			Field::make('text', 'gallery', 'Gallery ID')

				->set_conditional_logic(
					array(
						array(
							'field' => 'image_source',
							'value' => 'select-from-gallery',
						)
					)
				),
			Field::make('media_gallery', 'custom_gallery', 'Images')
				->set_conditional_logic(
					array(
						array(
							'field' => 'image_source',
							'value' => 'custom-gallery'
						)
					)
				),
		)
	)
	->add_tab(
		'CTA',
		array(
			Field::make('text', 'training_cta_heading', 'Heading'),
			Field::make('textarea', 'training_cta_description', 'Description'),
			Field::make('file', 'training_cta_background', 'Background')->set_width(20)
				->set_help_text('Select Image/Video Background'),
			Field::make('select', 'training_cta_style', 'Style')
				->set_options(
					array(
						'style-1' => 'Style 1',
						'style-2' => 'Style 2',
					)
				)
		)
	);


Container::make('post_meta', 'Training Components')
	->set_priority('high')
	->where('post_template', '=', 'templates/page-training.php')
	->add_tab(
		'Training Information',
		array(
			Field::make('rich_text', 'how_to_take', 'How to take this course'),
			Field::make('rich_text', 'what_do_i_get', 'What do I get from this course'),
			Field::make('rich_text', 'self_paced', 'Delivery Method Self-Paced Description'),
		)

	)
	->add_tab(
		'Product Options',
		array(
			Field::make('html', 'sep_1')->set_html('<label>ENQUIRE NOW BUTTON SETTINGS</label>')->set_classes('seperator '),
			Field::make('select', 'button_type', 'Button Type')
				->set_options(
					array(
						''                       => 'Default',
						'replace_enquire_button' => 'Replace enquire button text and link',
						'link_to_form'           => 'Link to a form within the page',
					)
				),

			Field::make('text', 'cst_btn_link', 'Button Text')->set_width(50)
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'replace_enquire_button',
						)
					)
				),
			Field::make('text', 'cst_btn_text', 'Button Link')->set_width(50)
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'replace_enquire_button',
						)
					)
				),
			Field::make('textarea', 'enquire_now_form', 'Enquire Now Form')
				->set_conditional_logic(
					array(
						array(
							'field' => 'button_type',
							'value' => 'link_to_form',
						)
					)
				),
			Field::make('textarea', 'menu_description', 'Menu Description'),
			Field::make('html', 'sep_2')->set_html('<label>OTHER OPTIONS</label>')->set_classes('seperator '),
			Field::make('checkbox', 'finance_available', 'Finance Available?'),
			Field::make('checkbox', 'business_invoicing', 'Business Invoicing?'),
			Field::make('checkbox', 'hide_product_summary', 'Hide Product Summary'),
			Field::make('checkbox', 'cpd_maker', 'CPD Maker'),
			Field::make('checkbox', 'tquk_logo', 'Show TQUK Logo'),
		)
	)
	->add_tab(
		'Reviews',
		array(
			Field::make('complex', 'reviews', 'Reviews')
				->add_fields(
					array(
						Field::make('text', 'author', 'Author'),
						Field::make('textarea', 'review_content', 'Review Content'),
					)
				)
				->set_header_template('<%- author  %>')
				->set_layout('tabbed-vertical')
		)
	)

	->add_tab(
		'Use Cases',
		array()
	)
	->add_tab(
		'Suitable Industry',
		array(
			Field::make('text', 'suitable_industry', 'Suggested Articles')
		)
	)
	->add_tab(
		'FAQs',
		array(
			Field::make('complex', 'faqs', 'FAQs')
				->add_fields(
					array(
						Field::make('text', 'heading', 'Heading'),
						Field::make('textarea', 'description', 'Description'),
					)
				)
				->set_header_template('<%- heading  %>')
				->set_layout('tabbed-vertical')
		)
	);





/*-----------------------------------------------------------------------------------*/
/* Gallery
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Gallery')
	->set_priority('high')
	->or_where('post_type', '=', 'galleries')
	->add_fields(
		array(
			Field::make('media_gallery', 'media_gallery', ''),
		)
	);


/*-----------------------------------------------------------------------------------*/
/* Blogs
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Settings'))
	->set_page_parent('edit.php')
	->add_fields(
		array(
			Field::make('association', 'suggested_articles', 'Featured Posts(Blog)')
				->set_types(
					array(
						array(
							'type'      => 'post',
							'post_type' => 'post',
						)
					)
				),
		)
	);



/*-----------------------------------------------------------------------------------*/
/* Guides
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Settings'))
	->set_page_parent('edit.php?post_type=guides')
	->add_fields(
		array(
			Field::make('association', 'featured_guides', 'Featured Guides')
				->set_types(
					array(
						array(
							'type'      => 'post',
							'post_type' => 'guides',
						)
					)
				),
		)
	);

Container::make('post_meta', 'Guide Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'guides')
	->add_fields(
		array(
			Field::make('checkbox', 'hide_on_list', 'Hide on List'),

		)
	);


/*-----------------------------------------------------------------------------------*/
/* Guides
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Settings'))
	->set_page_parent('edit.php?post_type=webinars')
	->add_fields(
		array(
			Field::make('association', 'featured_webinars', 'Featured Webinars')
				->set_types(
					array(
						array(
							'type'      => 'post',
							'post_type' => 'webinars',
						)
					)
				),
		)
	);





/*-----------------------------------------------------------------------------------*/
/* Blogs
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Settings'))
	->set_page_parent('edit.php?post_type=casestudies')
	->add_fields(
		array(
			Field::make('association', 'featured_case_studies', 'Featured Case Studies')
				->set_types(
					array(
						array(
							'type'      => 'post',
							'post_type' => 'casestudies',
						)
					)
				),
		)
	);




/*-----------------------------------------------------------------------------------*/
/* Events
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Event Details')
	->set_priority('high')
	->or_where('post_type', '=', 'events')
	->add_fields(
		array(
			Field::make('date', 'crb_event_start_date', __('Event Start Date'))
				->set_storage_format('d/m/Y'),
			Field::make('time', 'crb_event_start_time', 'Event Start Time')
				->set_storage_format('g:i a'),

		)
	);


/*-----------------------------------------------------------------------------------*/
/* Events Category
/*-----------------------------------------------------------------------------------*/
Container::make('term_meta', __('Category Properties'))
	->where('term_taxonomy', '=', 'events_category')
	->add_fields(
		array(
			Field::make('text', 'menu_order', __('Menu Order')),
			Field::make('html', 'html_1')
				->set_html('<label>CTA</label>')
				->set_classes('seperator '),
			Field::make('text', 'heading', __('Heading')),
			Field::make('text', 'link_text', __('Link Text')),
			Field::make('text', 'link_url', __('Link URL')),

		)
	);

/*-----------------------------------------------------------------------------------*/
/* Events
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Product Modal Description')
	->set_priority('high')
	->or_where('post_type', '=', 'product')
	->add_fields(
		array(
			Field::make('rich_text', 'product_modal_description', __(''))
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Webinar
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Webinar Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'webinars')
	->add_fields(
		array(
			Field::make('text', 'alt_title', __('Alt Title')),
			Field::make('textarea', 'description', __('Description')),
			Field::make('oembed', 'video', __('Video')),
			Field::make('text', 'minutes', __('Minutes')),
			Field::make('date', 'date', __('Date')),
			Field::make('time', 'time', __('Time')),
			Field::make('text', 'form_title', __('Form Title'))->set_help_text('Default is SAVE YOUR SEAT'),
			Field::make('textarea', 'form', __('Form')),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Nira 3D
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Nira 3D Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'nira3d')
	->add_fields(
		array(
			Field::make('text', '3d_url', __('URL')),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Partners
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Partner Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'partners')
	->add_fields(
		array(
			Field::make('text', 'tagline', __('Tagline')),
			Field::make('text', 'website', __('Website')),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Team
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Team Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'team')
	->add_fields(
		array(
			Field::make('text', 'position', __('Position')),
			Field::make('text', 'linkedin', __('Linkedin')),
			Field::make('text', 'calendly', __('Calendly')),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Careers
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Career Settings')
	->set_priority('high')
	->or_where('post_type', '=', 'careers')
	->add_fields(
		array(
			Field::make('text', 'salary_and_location', __('Salary and Location')),
			Field::make('text', 'duration', __('Full Time')),
		)
	);

/*-----------------------------------------------------------------------------------*/
/* Vendor Settings
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Brand Settings'))
	->set_page_parent('edit.php?post_type=product')
	->add_fields(
		array(
			Field::make('rich_text', 'vendor_description', 'Brand Description'),
			Field::make('html', 'html_0')->set_html('<label>FEATURED BRANDS</label>')->set_classes('seperator '),
			Field::make('text', 'featured_brands', __(''))->set_help_text('Please input brand id seperated by comma')


		)
	);


/*-----------------------------------------------------------------------------------*/
/* Announcement Bar Settings
/*-----------------------------------------------------------------------------------*/
Container::make('theme_options', __('Announcement Bar'))
	->add_fields(
		array(
			Field::make('rich_text', 'announcement_bar', 'Content'),
			Field::make('checkbox', 'hide_on_mobile', 'Hide on Mobile'),

		)
	);

/*-----------------------------------------------------------------------------------*/
/* Page Banner
/*-----------------------------------------------------------------------------------*/

Container::make('post_meta', 'Download Guide')
	->where('post_template', '=', 'templates/page-download-guide.php')
	->set_priority('high')
	->add_fields(
		array(
			Field::make('image', 'image', __('Image')),
			Field::make('complex', 'guides', __('Guides'))
				->add_fields(
					array(
						Field::make('textarea', 'guide_text', __('Guide Text')),
					)
				)
		)
	);




/*-----------------------------------------------------------------------------------*/
/* Mega Menus
/*-----------------------------------------------------------------------------------*/
$menu_locations = get_nav_menu_locations();

// 2. Store Menus in Array (Basic)
$menus = array();
foreach ($menu_locations as $location => $menu_id) {
	$menu_items = wp_get_nav_menu_items($menu_id);
	$menus[$location] = $menu_items;
}

Container::make('post_meta', 'Mega Menu Items')
	->where('post_type', '=', 'megamenus')
	->set_priority('high')
	->add_fields(
		array(
			Field::make('complex', 'menu_items', __('Menu Items'))
				->add_fields(
					array(
						Field::make('select', 'menu_type', __('Menu Type'))
							->set_options(
								array(
									'page'   => 'Page',
									'custom' => 'Custom',
								)
							),
						Field::make('text', 'menu_text', __('Menu Text')),
						Field::make('text', 'menu_custom_url', __('Custom Url'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'menu_type',
										'value' => 'custom',
									)
								)
							),
						Field::make('association', 'menu_item_page', __('Page'))
							->set_max(1)
							->set_types(
								array(
									array(
										'type'      => 'post',
										'post_type' => 'page',
									),
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'menu_type',
										'value' => 'page',
									)
								)
							),
						Field::make('complex', 'submenu', __('Submenu'))
							->add_fields(
								'menu_items',
								array(
									Field::make('select', 'width', __('Width'))
										->set_options(
											array(
												'col-12'    => '100%',
												'col-lg-11' => '91.67%',
												'col-lg-10' => '83.33%',
												'col-lg-9'  => '75%',
												'col-lg-7'  => '58.33%',
												'col-lg-6'  => '50%',
												'col-lg-5'  => '67%',
												'col-lg-4'  => '33.33%',
												'col-lg-3'  => '25%',
												'col-lg-2'  => '16.67%',
												'col-lg-1'  => '8.33%',
											)
										),
									Field::make('text', 'menu_text', __('Column Text')),
									Field::make('select', 'menu', __('Menu'))
										->set_options(get_menu_list_array())
								)
							)
							->add_fields(
								'images',
								array(
									Field::make('select', 'width', __('Width'))
										->set_options(
											array(
												'col-12'    => '100%',
												'col-lg-11' => '91.67%',
												'col-lg-10' => '83.33%',
												'col-lg-9'  => '75%',
												'col-lg-7'  => '58.33%',
												'col-lg-6'  => '50%',
												'col-lg-5'  => '67%',
												'col-lg-4'  => '33.33%',
												'col-lg-3'  => '25%',
												'col-lg-2'  => '16.67%',
												'col-lg-1'  => '8.33%',
											)
										),
									Field::make('text', 'menu_text', __('Column Text')),
									Field::make('complex', 'menu_images', __('Images'))
										->add_fields(
											array(
												Field::make('image', 'image', __('Image')),
												Field::make('text', 'url', __('URL'))->set_classes('field-url'),
											)
										)
										->set_layout('tabbed-vertical')

								)
							)
							->set_layout('tabbed-vertical')

					)
				)
				->set_layout('tabbed-vertical')
				->set_header_template('<%- menu_text  %>'),

		)
	);


/*-----------------------------------------------------------------------------------*/
/* Modules
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Modules')
	->where('post_template', '=', 'templates/page-modules.php')
	->set_priority('high')
	->add_fields(
		array(
			Field::make('complex', 'modules', __('Modules'))
				->setup_labels(
					array(
						'plural_name'   => 'Modules',
						'singular_name' => 'Module',
					)
				)
				//CTA Fields
				->add_fields(
					'cta',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),

						//Heading Settings
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20),
						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20),
						Field::make('select', 'size', __('Heading Size'))->set_width(20)
							->set_options(
								array(
									'' => 'Default',
									'big-heading' => 'Big Heading',
									'medium-heading' => 'Medium Heading',
									'small-heading' => 'Small Heading',
								)
							),
						//End of Heading Settings
						Field::make('textarea', 'description', __('Description')),
						Field::make('select', 'button_type', __('Button Type'))->set_width(20)->set_classes('trigger-selector')
							->set_options(
								array(
									''          => 'Select Button Type',
									'page'      => 'Page',
									'product'      => 'Product',
									'guides'      => 'Guides',
									'casestudies'      => 'Case Studies',
									'post'      => 'Post',
									'solutions' => 'Solution',
									'popups'    => 'Popup',
									'custom'    => 'Custom',
								)
							),
						Field::make('text', 'button_text', __('Button Text'))->set_width(20),
						Field::make('text', 'button_url', __('Page ID'))->set_width(20)->set_classes('field-url')
							->set_conditional_logic(
								array(
									array(
										'field'   => 'button_type',
										'value'   => 'custom',
										'compare' => '!='
									)
								)
							),
						Field::make('html', 'html')->set_width(20)
							->set_html('<div class="page-selector">  </div>'),
						Field::make('text', 'button_url_custom', __('Button URL'))->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'button_type',
										'value' => 'custom',
									)
								)
							),
						Field::make('select', 'button_style', __('Button Style'))->set_width(20)
							->set_options(
								array(
									'button-accent'      => 'Accent',
									'button-primary'      => 'Primary',
									'button-secondary' => 'Secondary',
									'button-white' => 'White',
									'button-bordered'    => 'Bordered',
								)
							),
					)
				)
				->set_header_template('Call to Action <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of CTA Fields
				//Contact Form Fields
				->add_fields(
					'contact_form',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20),
						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20),
						Field::make('select', 'size', __('Heading Size'))->set_width(20)
							->set_options(
								array(
									'' => 'Default',
									'big-heading' => 'Big Heading',
									'medium-heading' => 'Medium Heading',
									'small-heading' => 'Small Heading',
								)
							),
						Field::make('textarea', 'description', __('Description')),
						Field::make('text', 'form_heading', __('Form Heading')),
						Field::make('text', 'contact_form_shortcode', __('Contact Form Shortcode'))->set_classes('field-contact-form'),
					)
				)
				->set_header_template('Contact Form <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Contact Form Fields
				//Columns Fields
				->add_fields(
					'columns',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),

						Field::make('checkbox', 'same_height_images', __('Same Height Images'))->set_width(20),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(20),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('checkbox', 'numbered_boxes', __('Numbered Boxes'))->set_width(20),
						Field::make('text', 'image_size_ratio', __('Image Size Ratio'))->set_width(100)
							->set_help_text('Default is 30%')
							->set_conditional_logic(
								array(
									array(
										'field' => 'same_height_images',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//End of Heading Settings
						Field::make('select', 'text_align', 'Text Align')
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
									'text-justify'                => 'Justify',
								)
							)
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'size', __('Heading Size'))
							->set_options(
								array(
									'' => 'Default',
									'big-heading' => 'Big Heading',
									'medium-heading' => 'Medium Heading',
									'small-heading' => 'Small Heading',
								)
							)
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce')
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//Columns Stylesheet
						Field::make('complex', 'columns', __('Columns'))
							->setup_labels(
								array(
									'plural_name'   => 'Columns',
									'singular_name' => 'Column',
								)
							)
							->add_fields(
								array(
									Field::make('text', 'label', __('Label')),
									Field::make('select', 'button_type', __('URL Type'))->set_classes('trigger-selector')
										->set_options(
											array(
												''          => 'None',
												'page'      => 'Page',
												'product'      => 'Product',
												'guides'      => 'Guides',
												'casestudies'      => 'Case Studies',
												'post'      => 'Post',
												'solutions' => 'Solution',
												'popups'    => 'Popup',
												'custom'     => 'Custom',
											)
										),
									Field::make('html', 'button_text', __('HTML'))->set_html(''),
									Field::make('text', 'button_url', __('Column URL'))->set_classes('field-url')
										->set_conditional_logic(
											array(
												array(
													'field'   => 'button_type',
													'value'   => 'custom',
													'compare' => '!='
												)
											)
										),
									Field::make('html', 'html')
										->set_html('<div class="page-selector">  </div>'),
									Field::make('text', 'button_url_custom', __('Button URL'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'button_type',
													'value' => 'custom',
												)
											)
										),
									Field::make('complex', 'styles', __('Styles'))
										->set_duplicate_groups_allowed(false)
										->add_fields(
											'background_color',
											array(
												Field::make('select', 'background_color', 'Background Color')
													->set_options(
														array(
															'background-primary'   => 'Primary',
															'background-secondary' => 'Secondary',
															'background-accent'    => 'Accent',
															'background-white'     => 'White',
															'background-light-gray'     => 'Light Gray',
															'background-body-color'     => 'Body',
															'background-custom'    => 'Custom',
														)
													),
												Field::make('color', 'background_color_custom', __('Background Color'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'background_color',
																'value' => 'background-custom',
															)
														)
													),
											)
										)
										->add_fields(
											'padding',
											array(
												Field::make('select', 'padding_top', 'Padding Top')
													->set_options(
														array(
															''                => 'No Padding',
															'xl-padding-top'  => 'Extra Large',
															'lg-padding-top'  => 'Large',
															'md-padding-top'  => 'Medium',
															'sm-padding-top'  => 'Small',
															'xxs-padding-top' => 'Extra Small',
														)
													),
												Field::make('select', 'padding_bottom', 'Padding Bottom')
													->set_options(
														array(
															''                   => 'No Padding',
															'xl-padding-bottom'  => 'Extra Large',
															'lg-padding-bottom'  => 'Large',
															'md-padding-bottom'  => 'Medium',
															'sm-padding-bottom'  => 'Small',
															'xxs-padding-bottom' => 'Extra Small',
														)
													),
												Field::make('select', 'padding_left', 'Padding left')
													->set_options(
														array(
															''                 => 'No Padding',
															'xl-padding-left'  => 'Extra Large',
															'lg-padding-left'  => 'Large',
															'md-padding-left'  => 'Medium',
															'sm-padding-left'  => 'Small',
															'xxs-padding-left' => 'Extra Small',
														)
													),
												Field::make('select', 'padding_right', 'Padding right')
													->set_options(
														array(
															''                  => 'No Padding',
															'xl-padding-right'  => 'Extra Large',
															'lg-padding-right'  => 'Large',
															'md-padding-right'  => 'Medium',
															'sm-padding-right'  => 'Small',
															'xxs-padding-right' => 'Extra Small',
														)
													),
												Field::make('checkbox', 'remove_image_padding', 'Remove Image Padding')

											)
										)
										->add_fields(
											'margin',
											array(
												Field::make('select', 'margin_top', 'margin Top')
													->set_options(
														array(
															''               => 'No margin',
															'xl-margin-top'  => 'Extra Large',
															'lg-margin-top'  => 'Large',
															'md-margin-top'  => 'Medium',
															'sm-margin-top'  => 'Small',
															'xxs-margin-top' => 'Extra Small',
														)
													),
												Field::make('select', 'margin_bottom', 'margin Bottom')
													->set_options(
														array(
															''                  => 'No margin',
															'xl-margin-bottom'  => 'Extra Large',
															'lg-margin-bottom'  => 'Large',
															'md-margin-bottom'  => 'Medium',
															'sm-margin-bottom'  => 'Small',
															'xxs-margin-bottom' => 'Extra Small',
														)
													),
												Field::make('select', 'margin_left', 'margin left')
													->set_options(
														array(
															''                => 'No margin',
															'xl-margin-left'  => 'Extra Large',
															'lg-margin-left'  => 'Large',
															'md-margin-left'  => 'Medium',
															'sm-margin-left'  => 'Small',
															'xxs-margin-left' => 'Extra Small',
														)
													),
												Field::make('select', 'margin_right', 'margin right')
													->set_options(
														array(
															''                 => 'No margin',
															'xl-margin-right'  => 'Extra Large',
															'lg-margin-right'  => 'Large',
															'md-margin-right'  => 'Medium',
															'sm-margin-right'  => 'Small',
															'xxs-margin-right' => 'Extra Small',
														)
													),
											)
										)
										->add_fields(
											'border_radius',
											array(
												Field::make('text', 'border_radius', 'Border Radius')
											)
										)

										->add_fields(
											'alignment',
											array(
												Field::make('select', 'align_items', 'Align Items')
													->set_options(
														array(
															''               => 'Default',
															'align-items-start'  => 'Start',
															'align-items-center'  => 'Center',
															'align-items-end'  => 'End',
														)
													),
												Field::make('select', 'justify_content', 'Justify Content')
													->set_options(
														array(
															''                  => 'Default',
															'justify-content-start'  => 'Start',
															'justify-content-center'  => 'Center',
															'justify-content-end'  => 'End',
															'justify-content-between'  => 'Between',
														)
													),
												Field::make('select', 'text_align', 'Text Align')
													->set_options(
														array(
															''                => 'Default',
															'text-start'                => 'Left',
															'text-center'                => 'Center',
															'text-end'                => 'Right',
															'text-justify'                => 'Justify',
														)
													),
											)
										)
										->add_fields(
											'custom_class',
											array(
												Field::make('text', 'custom_class', 'Custom Class')
											)
										)
										->add_fields(
											'max_width',
											array(
												Field::make('text', 'max_width', 'Max Width')
											)
										)
										->add_fields(
											'column_width',
											array(
												Field::make('select', 'column_width', __('Column Width Desktop'))
													->set_options(
														array(
															'col-lg' 	=> 'Default',
															'col-12'    => '100.00%',
															'col-lg-11' => '91.67%',
															'col-lg-10' => '83.33%',
															'col-lg-9'  => '75.00%',
															'col-lg-8'  => '67.00%',
															'col-lg-7'  => '58.33%',
															'col-lg-6'  => '50.00%',
															'col-lg-5'  => '41.67%',
															'col-lg-4'  => '33.33%',
															'col-lg-3'  => '25.00%',
															'col-lg-2'  => '16.67%',
															'col-lg-1'  => '08.33%',
														)
													),
												Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
													->set_options(
														array(
															'' 	=> 'Default',
															'col-md-12'    => '100.00%',
															'col-md-11' => '91.67%',
															'col-md-10' => '83.33%',
															'col-md-9'  => '75.00%',
															'col-md-8'  => '67.00%',
															'col-md-7'  => '58.33%',
															'col-md-6'  => '50.00%',
															'col-md-5'  => '41.67%',
															'col-md-4'  => '33.33%',
															'col-md-3'  => '25.00%',
															'col-md-2'  => '16.67%',
															'col-md-1'  => '08.33%',
														)
													),
												Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
													->set_options(
														array(
															'' 	=> 'Default',
															'col-12' => '100%',
															'col-11' => '91.67%',
															'col-10' => '83.33%',
															'col-9'  => '75.00%',
															'col-8'  => '67.00%',
															'col-7'  => '58.33%',
															'col-6'  => '50.00%',
															'col-5'  => '41.67%',
															'col-4'  => '33.33%',
															'col-3'  => '25.00%',
															'col-2'  => '16.67%',
															'col-1'  => '08.33%',
														)
													),
											)
										)
										->set_layout('tabbed-vertical'),
									Field::make('complex', 'items', __('Items'))
										->add_fields(
											'heading',
											array(
												Field::make('text', 'heading', __('Heading')),
												Field::make('text', 'prefix', __('Prefix')),
												Field::make('text', 'suffix', __('Suffix')),
												Field::make('select', 'tag', __('Tag'))
													->set_options(
														array(
															'h1' => 'h1',
															'h2' => 'h2',
															'h3' => 'h3',
															'h4' => 'h4',
															'h5' => 'h5',
															'h6' => 'h6',
														)
													)
													->set_default_value('h2'),
												Field::make('select', 'size', __('Heading Size'))
													->set_options(
														array(
															'' => 'Default',
															'big-heading' => 'Big Heading',
															'medium-heading' => 'Medium Heading',
															'small-heading' => 'Small Heading',
														)
													),
												Field::make('select', 'text_color', 'Text Color')
													->set_options(
														array(
															''   => 'Default',
															'text-primary'   => 'Primary',
															'text-secondary' => 'Secondary',
															'text-accent'    => 'Accent',
															'text-white'     => 'White',
															'text-light-gray'     => 'Light Gray',
															'text-body-color'     => 'Body',
															'text-custom'    => 'Custom',
														)
													),
												Field::make('color', 'text_color_custom', __('Text Color'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'text_color',
																'value' => 'text-custom',
															)
														)
													),
											)
										)
										->add_fields(
											'description',
											array(
												Field::make('textarea', 'description', __('Description'))->set_classes('activate-tinymce'),
											)
										)
										->add_fields(
											'embed',
											array(
												Field::make('oembed', 'embed', __('Embed')),
											)
										)
										->add_fields(
											'button',
											array(
												Field::make('select', 'button_type', __('Button Type'))->set_classes('trigger-selector')
													->set_options(
														array(
															''          => 'Select Button Type',
															'page'      => 'Page',
															'product'      => 'Product',
															'guides'      => 'Guides',
															'casestudies'      => 'Case Studies',
															'post'      => 'Post',
															'solutions' => 'Solution',
															'popups'    => 'Popup',
															'custom'     => 'Custom',
														)
													),
												Field::make('text', 'button_text', __('Button Text')),
												Field::make('text', 'button_url', __('Button URL'))->set_classes('field-url')
													->set_conditional_logic(
														array(
															array(
																'field'   => 'button_type',
																'value'   => 'custom',
																'compare' => '!='
															)
														)
													),
												Field::make('html', 'html')
													->set_html('<div class="page-selector">  </div>'),
												Field::make('text', 'button_url_custom', __('Button URL'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'button_type',
																'value' => 'custom',
															)
														)
													),
												Field::make('select', 'button_style', __('Button Style'))
													->set_options(
														array(
															'button-accent'      => 'Accent',
															'button-primary'      => 'Primary',
															'button-secondary' => 'Secondary',
															'button-white' => 'White',
															'button-bordered'    => 'Bordered',
														)
													),

											)
										)
										->add_fields(
											'image',
											array(
												Field::make('image', 'image', __('Image')),
												Field::make('select', 'size', __('Size'))
													->set_options(
														array(
															''          => 'Default',
															'full'      => 'Full',
															'large'      => 'Large',
															'medium' => 'Medium',
															'thumbnail'    => 'Thumbnail',
														)
													),
												Field::make('checkbox', 'custom_size', __('Custom Size')),
												Field::make('text', 'image_width', __('Custom Image Width'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'custom_size',
																'value' => true,
															)
														)
													),
												Field::make('text', 'image_height', __('Custom Image Height'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'custom_size',
																'value' => true,
															)
														)
													),
												Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
												Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
													->set_conditional_logic(
														array(
															array(
																'field' => 'rounded_corners',
																'value' => true,
															)
														)
													),
											)
										)
										->add_fields(
											'icon',
											array(
												Field::make('file', 'icon', __('Icon'))
													->set_type(array('image/svg+xml')),
												Field::make('select', 'icon_color', 'Text Color')
													->set_options(
														array(
															'text-primary'   => 'Primary',
															'text-secondary' => 'Secondary',
															'text-accent'    => 'Accent',
															'text-white'     => 'White',
															'text-light-gray'     => 'Light Gray',
															'text-body-color'     => 'Body',
															'text-custom'    => 'Custom',
														)
													),
												Field::make('color', 'icon_color_custom', __('Text Color'))
													->set_conditional_logic(
														array(
															array(
																'field' => 'icon_color',
																'value' => 'text-custom',
															)
														)
													),
												Field::make('text', 'icon_width', __('Custom Icon Width')),
												Field::make('text', 'icon_height', __('Custom Icon Height'))

											)
										)
										->add_fields(
											'accordion',
											array(
												Field::make('checkbox', 'open_first_item', __('Open First Item')),
												Field::make('select', 'accordion_source', __('Accordion Source'))
													->set_options(
														array(
															''      => 'Custom',
															'faqs'      => 'FAQs Select Manually',
															'faqs_category'      => 'FAQs by Category',
														)
													),
												Field::make('complex', 'accordion', __('Accordion'))
													->setup_labels(
														array(
															'plural_name'   => 'Accordions',
															'singular_name' => 'Accordion',
														)
													)
													->add_fields(
														array(
															Field::make('text', 'heading', __('Heading')),
															Field::make('textarea', 'description', __('Description')),
														)
													)
													->set_layout('tabbed-vertical')
													->set_header_template('<%- heading  %>')
													->set_conditional_logic(
														array(
															array(
																'field' => 'accordion_source',
																'value' => '',
																'comapre' => '='
															)
														)
													),
												Field::make('association', 'faqs', 'Select FAQs')
													->set_types(
														array(
															array(
																'type'      => 'post',
																'post_type' => 'faq',
															)
														)
													)
													->set_conditional_logic(
														array(
															array(
																'field' => 'accordion_source',
																'value' => 'faqs',
																'comapre' => '='
															)
														)
													),
												Field::make('association', 'faqs_category', 'Select FAQs Category')
													->set_types(
														array(
															array(
																'type'      => 'term',
																'taxonomy' => 'faqs_category',
															)
														)
													)
													->set_conditional_logic(
														array(
															array(
																'field' => 'accordion_source',
																'value' => 'faqs_category',
																'comapre' => '='
															)
														)
													),
											)
										)
										->add_fields(
											'custom_html',
											array(
												Field::make('textarea', 'custom_html', __('Custom HTML')),
											)
										)
										->add_fields(
											'number_counters',
											array(
												Field::make('complex', 'number_counters', __('Custom HTML'))
													->add_fields(array(
														Field::make('text', 'number', __('Number'))
															->set_attribute('type', 'number'),
														Field::make('text', 'prefix', __('Prefix')),
														Field::make('text', 'suffix', __('Suffix')),
														Field::make('textarea', 'description', __('Description')),
														Field::make('image', 'icon', __('Icon')),
													))
													->set_layout('tabbed-vertical')
													->set_header_template('<%- number %>')
											)
										)
										->set_layout('tabbed-vertical')
								)
							)
							->set_layout('tabbed-vertical')
							->set_header_template('<%- label  %>'),
					)
				)
				->set_header_template('Columns <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Columns Fields
				//Logo Slider Fields
				->add_fields(
					'logo_slider',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),

						//Heading Settings Logo Slider
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'text_align', 'Text Align')->set_width(20)
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//End of Heading Settings
						Field::make('textarea', 'description', __('Description'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'number_of_slides', __('Number of Slides Desktop'))
							->set_default_value(4)
							->set_required(true)
							->set_attribute('type', 'number'),
						Field::make('text', 'number_of_slides_tablet', __('Number of Slides Table'))
							->set_attribute('type', 'number'),
						Field::make('text', 'number_of_slides_mobile', __('Number of Slides Mobile'))
							->set_attribute('type', 'number'),
						Field::make('media_gallery', 'images', __('Image')),
					)
				)
				->set_header_template('Logo Slider <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Logo Slider Fields
				//Custom HTML Fields
				->add_fields(
					'custom_html',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//End of Heading Settings
						Field::make('textarea', 'custom_html', __('Custom HTML')),
					)
				)
				->set_header_template('Custom HTML <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Custom HTML
				//WYSIWYG Fields
				->add_fields(
					'wysiwyg',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'max_width',
								array(
									Field::make('text', 'max_width', 'Max Width'),
									Field::make('checkbox', 'centred', 'Centred'),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),

						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'text_align', 'Text Align')->set_width(20)
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//End of Heading Settings
						Field::make('rich_text', 'wysiwyg', __('WYSIWYG')),
					)
				)
				->set_header_template('WYSIWYG <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of WYSIWYG Fields
				//Accordion Fields
				->add_fields(
					'accordion',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'text_align', 'Text Align')
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						//End of Heading Settings
						Field::make('textarea', 'description', __('Description'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('checkbox', 'open_first_item', __('Open First Item')),
						Field::make('select', 'accordion_source', __('Accordion Source'))
							->set_options(
								array(
									''      => 'Custom',
									'faqs'      => 'FAQs Select Manually',
									'faqs_category'      => 'FAQs by Category',
								)
							),
						Field::make('complex', 'accordion', __('Accordion'))
							->setup_labels(
								array(
									'plural_name'   => 'Accordions',
									'singular_name' => 'Accordion',
								)
							)
							->add_fields(
								array(
									Field::make('text', 'heading', __('Heading')),
									Field::make('textarea', 'description', __('Description')),
								)
							)
							->set_layout('tabbed-vertical')
							->set_header_template('<%- heading  %>')
							->set_conditional_logic(
								array(
									array(
										'field' => 'accordion_source',
										'value' => '',
										'comapre' => '='
									)
								)
							),
						Field::make('association', 'faqs', 'Select FAQs')
							->set_types(
								array(
									array(
										'type'      => 'post',
										'post_type' => 'faq',
									)
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'accordion_source',
										'value' => 'faqs',
										'comapre' => '='
									)
								)
							),
						Field::make('association', 'faqs_category', 'Select FAQs Category')
							->set_types(
								array(
									array(
										'type'      => 'term',
										'taxonomy' => 'faqs_category',
									)
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'accordion_source',
										'value' => 'faqs_category',
										'comapre' => '='
									)
								)
							),
					)
				)
				->set_header_template('Accordion <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Accordion Fields
				//Post Tabs Fields
				->add_fields(
					'post_tabs',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(33)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('textarea', 'description', __('Description'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'popup_id', __('Popup ID')),

						Field::make('text', 'post_type_key', __('Post Type Key')),
						Field::make('text', 'taxonomy_key', __('Taxonomy Key')),
					)
				)
				->set_header_template('Post Tabs <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//End of Post Grid Fields
				->add_fields(
					'post_grid',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'text_align', 'Text Align')->set_width(20)
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('textarea', 'description', __('Description'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('complex', 'post_box_styles', __('Post Box Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),
									Field::make('checkbox', 'remove_image_padding', 'Remove Image Padding')

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
												'text-justify'                => 'Justify',
											)
										),
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->add_fields(
								'max_width',
								array(
									Field::make('text', 'max_width', 'Max Width')
								)
							)
							->add_fields(
								'column_width',
								array(
									Field::make('select', 'column_width', __('Column Width Desktop'))
										->set_options(
											array(
												'col-lg' 	=> 'Default',
												'col-12'    => '100.00%',
												'col-lg-11' => '91.67%',
												'col-lg-10' => '83.33%',
												'col-lg-9'  => '75.00%',
												'col-lg-8'  => '67.00%',
												'col-lg-7'  => '58.33%',
												'col-lg-6'  => '50.00%',
												'col-lg-5'  => '41.67%',
												'col-lg-4'  => '33.33%',
												'col-lg-3'  => '25.00%',
												'col-lg-2'  => '16.67%',
												'col-lg-1'  => '08.33%',
											)
										),
									Field::make('select', 'column_width_tablet', __('Column Width Tablet'))
										->set_options(
											array(
												'' 	=> 'Default',
												'col-md-12'    => '100.00%',
												'col-md-11' => '91.67%',
												'col-md-10' => '83.33%',
												'col-md-9'  => '75.00%',
												'col-md-8'  => '67.00%',
												'col-md-7'  => '58.33%',
												'col-md-6'  => '50.00%',
												'col-md-5'  => '41.67%',
												'col-md-4'  => '33.33%',
												'col-md-3'  => '25.00%',
												'col-md-2'  => '16.67%',
												'col-md-1'  => '08.33%',
											)
										),
									Field::make('select', 'column_width_mobile', __('Column Width Mobile'))
										->set_options(
											array(
												'' 	=> 'Default',
												'col-12' => '100%',
												'col-11' => '91.67%',
												'col-10' => '83.33%',
												'col-9'  => '75.00%',
												'col-8'  => '67.00%',
												'col-7'  => '58.33%',
												'col-6'  => '50.00%',
												'col-5'  => '41.67%',
												'col-4'  => '33.33%',
												'col-3'  => '25.00%',
												'col-2'  => '16.67%',
												'col-1'  => '08.33%',
											)
										),
								)
							)
							->set_layout('tabbed-vertical'),
						Field::make('complex', 'post_elements', 'Post Elements')
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'post_title',
								array(
									Field::make('select', 'size', __('Heading Size'))
										->set_options(
											array(
												'' => 'Default',
												'big-heading' => 'Big Heading',
												'medium-heading' => 'Medium Heading',
												'small-heading' => 'Small Heading',
											)
										),
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												''   => 'Default',
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'featured_image',
								array(
									Field::make('select', 'size', __('Size'))
										->set_options(
											array(
												''          => 'Default',
												'full'      => 'Full',
												'large'      => 'Large',
												'medium' => 'Medium',
												'thumbnail'    => 'Thumbnail',
											)
										),
									Field::make('checkbox', 'rounded_corners', __('Rounder Corners')),
									Field::make('text', 'border_radius', __('Border Radius'))->set_help_text('Custom border radius')
										->set_conditional_logic(
											array(
												array(
													'field' => 'rounded_corners',
													'value' => true,
												)
											)
										),
								)
							)
							->add_fields(
								'post_excerpt',
								array(
									Field::make('text', 'excerpt_length', 'Custom Excerpt Length')
								)
							)
							->add_fields(
								'permalink',
								array(
									Field::make('text', 'button_text', 'Button Text'),
									Field::make('select', 'button_style', __('Button Style'))->set_width(20)
										->set_options(
											array(
												'button-accent'      => 'Accent',
												'button-primary'      => 'Primary',
												'button-secondary' => 'Secondary',
												'button-white' => 'White',
												'button-bordered'    => 'Bordered',
											)
										),
								)
							)
							->add_fields(
								'custom_field_1',
								array(
									Field::make('text', 'custom_field_key', __('Custom Field Key')),
									Field::make('text', 'custom_field_type', __('Custom Field Type')),
									Field::make('text', 'custom_field_class', __('Wrapper Class')),
								)
							)
							->set_header_template('Custom Field: <%- custom_field_key  %>')
							->add_fields(
								'custom_field_2',
								array(
									Field::make('text', 'custom_field_key', __('Custom Field Key')),
									Field::make('text', 'custom_field_type', __('Custom Field Type')),
									Field::make('text', 'custom_field_class', __('Wrapper Class')),
								)
							)
							->set_header_template('Custom Field: <%- custom_field_key  %>')
							->add_fields(
								'custom_field_3',
								array(
									Field::make('text', 'custom_field_key', __('Custom Field Key')),
									Field::make('text', 'custom_field_type', __('Custom Field Type')),
									Field::make('text', 'custom_field_class', __('Wrapper Class')),
								)
							)
							->set_header_template('Custom Field: <%- custom_field_key  %>')
							->add_fields(
								'custom_field_4',
								array(
									Field::make('text', 'custom_field_key', __('Custom Field Key')),
									Field::make('text', 'custom_field_type', __('Custom Field Type')),
									Field::make('text', 'custom_field_class', __('Wrapper Class')),
								)
							)
							->set_header_template('Custom Field: <%- custom_field_key  %>')
							->add_fields(
								'custom_field_5',
								array(
									Field::make('text', 'custom_field_key', __('Custom Field Key')),
									Field::make('text', 'custom_field_type', __('Custom Field Type')),
									Field::make('text', 'custom_field_class', __('Wrapper Class')),
								)
							)
							->set_header_template('Custom Field: <%- custom_field_key  %>')
							->set_layout('tabbed-vertical'),
						Field::make('complex', 'post_type', 'Post Type')
							->set_duplicate_groups_allowed(false)
							->set_max(1)
							->add_fields(
								'post',
								array(
									Field::make('hidden', 'taxonomy_key', '')->set_default_value('category'),
									Field::make('select', 'source', __('Source'))
										->set_options(
											array(
												'all'      => 'Select All',
												'manually'      => 'Select Manually',
												'category'      => 'Select by Category',
											)
										),

									Field::make('association', 'post', 'Select Post')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'post',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'manually',
												)
											)
										),
									Field::make('association', 'category', 'Select Post Categories')
										->set_types(
											array(
												array(
													'type'      => 'term',
													'taxonomy' => 'category',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'category',
													'comapre' => '='
												)
											)
										),
								)
							)

							->add_fields(
								'team',
								array(
									Field::make('hidden', 'taxonomy_key', '')->set_default_value('team_category'),
									Field::make('select', 'source', __('Source'))
										->set_options(
											array(
												'all'      => 'Select All',
												'manually'      => 'Select Manually',
												'category'      => 'Select by Category',
											)
										),

									Field::make('association', 'post', 'Select Post')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'team',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'manually',
												)
											)
										),
									Field::make('association', 'category', 'Select Team Categories')
										->set_types(
											array(
												array(
													'type'      => 'term',
													'taxonomy' => 'team_category',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'category',
													'comapre' => '='
												)
											)
										),
								)
							)

							->add_fields(
								'product',
								array(
									Field::make('hidden', 'taxonomy_key', '')->set_default_value('product_cat'),
									Field::make('select', 'source', __('Source'))
										->set_options(
											array(
												'all'      => 'Select All',
												'manually'      => 'Select Manually',
												'category'      => 'Select by Category',
											)
										),

									Field::make('association', 'post', 'Select Products')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'product',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'manually',
												)
											)
										),
									Field::make('association', 'category', 'Select Product Categories')
										->set_types(
											array(
												array(
													'type'      => 'term',
													'taxonomy' => 'category',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'category',
													'comapre' => '='
												)
											)
										),

								)
							)
							->add_fields(
								'guides',
								array(
									Field::make('association', 'guides', 'Select guides')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'guides',
												)
											)
										)
										->set_conditional_logic(
											array(
												array(
													'field' => 'source',
													'value' => 'guides',
												)
											)
										),
								)
							)

							->add_fields(
								'casestudies',
								array(

									Field::make('association', 'casestudies', 'Select casestudies')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'casestudies',
												)
											)
										)

								)
							)
							->add_fields(
								'solutions',
								array(


									Field::make('association', 'solutions', 'Select solutions')
										->set_types(
											array(
												array(
													'type'      => 'post',
													'post_type' => 'solutions',
												)
											)
										)


								)
							)
							->set_layout('tabbed-vertical')





					)
				)
				->set_header_template('Post Grid <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//Enf of Post Grid Fields
				//End of Testimonial Fields
				->add_fields(
					'testimonials',
					array(
						//Module Settings
						Field::make('text', 'title', __('Module Title'))->set_width(33),
						Field::make('text', 'module_id', __('Module ID'))->set_width(33),
						Field::make('checkbox', 'disable_module', __('Disable Module'))->set_width(33),
						//End of Module Settings
						Field::make('complex', 'styles', __('Styles'))
							->set_duplicate_groups_allowed(false)
							->add_fields(
								'background_color',
								array(
									Field::make('select', 'background_color', 'Background Color')
										->set_options(
											array(
												'background-primary'   => 'Primary',
												'background-secondary' => 'Secondary',
												'background-accent'    => 'Accent',
												'background-white'     => 'White',
												'background-light-gray'     => 'Light Gray',
												'background-body-color'     => 'Body',
												'background-custom'    => 'Custom',
											)
										),
									Field::make('color', 'background_color_custom', __('Background Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_color',
													'value' => 'background-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'background_image',
								array(
									Field::make('image', 'background_image', 'Background Image'),
									Field::make('select', 'background_size', 'Background Size')
										->set_options(
											array(
												'background-cover' => 'Cover',
												'background-contain'  => 'Contain',
											)
										),
									Field::make('select', 'background_attachment', 'Background Attachment')
										->set_options(
											array(
												'background-scroll'    => 'Scroll',
												'background-fixed'  => 'Fixed',
											)
										),
									Field::make('select', 'background_repeat', 'Background Repeat')
										->set_options(
											array(
												'background-no-repeat'    => 'No Repeat',
												'background-repeat'  => 'No Repeat',
											)
										),
								)
							)
							->add_fields(
								'background_overlay',
								array(
									Field::make('select', 'background_overlay_type', 'Background Overlay Type')
										->set_options(
											array(
												'default'    => 'Default',
												'image'  => 'Image',
												'custom'  => 'Custom',
											)
										),
									Field::make('image', 'background_overlay_image', 'Image Background Overlay')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('text', 'background_overlay_image_opacity', 'Image Background Overlay Opacity')
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'image',
												)
											)
										),
									Field::make('color', 'background_overlay_custom', 'Custom Background Overlay')
										->set_alpha_enabled(true)
										->set_conditional_logic(
											array(
												array(
													'field' => 'background_overlay_type',
													'value' => 'custom',
												)
											)
										),
								)
							)
							->add_fields(
								'text_color',
								array(
									Field::make('select', 'text_color', 'Text Color')
										->set_options(
											array(
												'text-primary'   => 'Primary',
												'text-secondary' => 'Secondary',
												'text-accent'    => 'Accent',
												'text-white'     => 'White',
												'text-light-gray'     => 'Light Gray',
												'text-body-color'     => 'Body',
												'text-custom'    => 'Custom',
											)
										),
									Field::make('color', 'text_color_custom', __('Text Color'))
										->set_conditional_logic(
											array(
												array(
													'field' => 'text_color',
													'value' => 'text-custom',
												)
											)
										),
								)
							)
							->add_fields(
								'padding',
								array(
									Field::make('select', 'padding_top', 'Padding Top')
										->set_options(
											array(
												''                => 'No Padding',
												'xl-padding-top'  => 'Extra Large',
												'lg-padding-top'  => 'Large',
												'md-padding-top'  => 'Medium',
												'sm-padding-top'  => 'Small',
												'xxs-padding-top' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_bottom', 'Padding Bottom')
										->set_options(
											array(
												''                   => 'No Padding',
												'xl-padding-bottom'  => 'Extra Large',
												'lg-padding-bottom'  => 'Large',
												'md-padding-bottom'  => 'Medium',
												'sm-padding-bottom'  => 'Small',
												'xxs-padding-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_left', 'Padding left')
										->set_options(
											array(
												''                 => 'No Padding',
												'xl-padding-left'  => 'Extra Large',
												'lg-padding-left'  => 'Large',
												'md-padding-left'  => 'Medium',
												'sm-padding-left'  => 'Small',
												'xxs-padding-left' => 'Extra Small',
											)
										),
									Field::make('select', 'padding_right', 'Padding right')
										->set_options(
											array(
												''                  => 'No Padding',
												'xl-padding-right'  => 'Extra Large',
												'lg-padding-right'  => 'Large',
												'md-padding-right'  => 'Medium',
												'sm-padding-right'  => 'Small',
												'xxs-padding-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'margin',
								array(
									Field::make('select', 'margin_top', 'margin Top')
										->set_options(
											array(
												''               => 'No margin',
												'xl-margin-top'  => 'Extra Large',
												'lg-margin-top'  => 'Large',
												'md-margin-top'  => 'Medium',
												'sm-margin-top'  => 'Small',
												'xxs-margin-top' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_bottom', 'margin Bottom')
										->set_options(
											array(
												''                  => 'No margin',
												'xl-margin-bottom'  => 'Extra Large',
												'lg-margin-bottom'  => 'Large',
												'md-margin-bottom'  => 'Medium',
												'sm-margin-bottom'  => 'Small',
												'xxs-margin-bottom' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_left', 'margin left')
										->set_options(
											array(
												''                => 'No margin',
												'xl-margin-left'  => 'Extra Large',
												'lg-margin-left'  => 'Large',
												'md-margin-left'  => 'Medium',
												'sm-margin-left'  => 'Small',
												'xxs-margin-left' => 'Extra Small',
											)
										),
									Field::make('select', 'margin_right', 'margin right')
										->set_options(
											array(
												''                 => 'No margin',
												'xl-margin-right'  => 'Extra Large',
												'lg-margin-right'  => 'Large',
												'md-margin-right'  => 'Medium',
												'sm-margin-right'  => 'Small',
												'xxs-margin-right' => 'Extra Small',
											)
										),

								)
							)
							->add_fields(
								'alignment',
								array(
									Field::make('select', 'align_items', 'Align Items')
										->set_options(
											array(
												''               => 'Default',
												'align-items-start'  => 'Start',
												'align-items-center'  => 'Center',
												'align-items-end'  => 'End',
											)
										),
									Field::make('select', 'justify_content', 'Justify Content')
										->set_options(
											array(
												''                  => 'Default',
												'justify-content-start'  => 'Start',
												'justify-content-center'  => 'Center',
												'justify-content-end'  => 'End',
												'justify-content-between'  => 'Between',
											)
										),
									Field::make('select', 'text_align', 'Text Align')
										->set_options(
											array(
												''                => 'Default',
												'text-start'                => 'Left',
												'text-center'                => 'Center',
												'text-end'                => 'Right',
											)
										),
								)
							)
							->add_fields(
								'container_width',
								array(
									Field::make('select', 'container_width', 'Container Width')
										->set_options(
											array(
												''               => 'Default',
												'large-container'  => 'Large',
												'medium-container'  => 'Medium',
												'small-container'  => 'Small',
												'custom-container'  => 'Custom',
											)
										),
									Field::make('text', 'custom_container_width', 'Custom Container Width')
										->set_conditional_logic(
											array(
												array(
													'field' => 'container_width',
													'value' => 'custom-container',
												)
											)
										),
								)
							)
							->add_fields(
								'border_radius',
								array(
									Field::make('text', 'border_radius', 'Border Radius')
								)
							)
							->add_fields(
								'custom_class',
								array(
									Field::make('text', 'custom_class', 'Custom Class')
								)
							)
							->set_layout('tabbed-vertical'),
						//Heading Settings
						Field::make('checkbox', 'display_heading_description', __('Display Section Heading and Description'))->set_width(33),
						Field::make('checkbox', 'heading_with_line', __('Heading with prefix on left with line'))->set_width(67)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_prefix', __('Prefix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading_suffix', __('Suffix'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('text', 'heading', __('Heading'))
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),

						Field::make('select', 'tag', __('Tag'))
							->set_options(
								array(
									'h1' => 'h1',
									'h2' => 'h2',
									'h3' => 'h3',
									'h4' => 'h4',
									'h5' => 'h5',
									'h6' => 'h6',
								)
							)
							->set_default_value('h2')
							->set_width(20)
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'text_align', 'Text Align')
							->set_width(20)
							->set_options(
								array(
									''                => 'Default',
									'text-start'                => 'Left',
									'text-center'                => 'Center',
									'text-end'                => 'Right',
								)
							),
						Field::make('textarea', 'description', __('Description'))
							->set_conditional_logic(
								array(
									array(
										'field' => 'display_heading_description',
										'value' => true,
									)
								)
							),
						Field::make('select', 'testimonial_source', __('Testimonial Source'))
							->set_options(
								array(
									'testimonial'      => 'Select Manually',
									'testimonial_category'      => 'Select by Category',
								)
							),

						Field::make('association', 'testimonials', 'Select Testimonials')
							->set_types(
								array(
									array(
										'type'      => 'post',
										'post_type' => 'testimonials',
									)
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'testimonial_source',
										'value' => 'testimonial',
										'comapre' => '='
									)
								)
							),
						Field::make('association', 'testimonial_category', 'Select Testimonial Categories')
							->set_types(
								array(
									array(
										'type'      => 'term',
										'taxonomy' => 'testimonial_category',
									)
								)
							)
							->set_conditional_logic(
								array(
									array(
										'field' => 'testimonial_source',
										'value' => 'testimonial_category',
										'comapre' => '='
									)
								)
							),

					)
				)
				->set_header_template('Testimonials <% if (title) { %>[Title: <%- title %>] <% } %> <% if (module_id) { %>[Module ID: <%- module_id %>]<% } %>')
				//Enf of Testimonial Fields
		)
	);


/*-----------------------------------------------------------------------------------*/
/* 3D Model Library
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', '3D Model Library Settings')
	->set_priority('high')
	->or_where('post_type', '=', '3dmodellibraries')
	->add_fields(
		array(
			Field::make('image', 'captured_by', 'Captured by (Logo)'),
		)
	);



/*-----------------------------------------------------------------------------------*/
/* 3D Model Library
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Popup Settings')
	->set_priority('high')
	->set_context('side')
	->or_where('post_type', '=', 'popups')
	->add_fields(
		array(
			Field::make('select', 'popup_layout', 'Popup Layout')
				->set_options(
					array(
						''				=> 'Default',
						'contact_form' 	=> 'Contact Form',
					)
				),
			Field::make('select', 'popup_max_width', 'Popup Max Width')
				->set_options(
					array(
						'popup-default'	=> 'Default',
						'popup-small' 	=> 'Small',
						'popup-medium' 	=> 'Medium',
						'popup-large' 	=> 'Large',
					)
				),
			Field::make('select', 'background_color', 'Background Color')
				->set_options(
					array(
						''   => 'None',
						'background-primary'   => 'Primary',
						'background-secondary' => 'Secondary',
						'background-accent'    => 'Accent',
						'background-white'     => 'White',
						'background-light-gray'     => 'Light Gray',
						'background-body-color'     => 'Body',
					)
				),

		)
	);

/*-----------------------------------------------------------------------------------*/
/* 3D Model Category
/*-----------------------------------------------------------------------------------*/
Container::make('term_meta', __('Category Properties'))
	->where('term_taxonomy', '=', 'Model3d_Category')
	->add_fields(
		array(
			Field::make('text', 'menu_order', 'Menu Order')
				->set_attribute('type', 'number')
				->set_required(true)
				->set_default_value(0),
		)
	);
