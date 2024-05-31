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
/* Industry Solution
/*-----------------------------------------------------------------------------------*/
Container::make('post_meta', 'Industry Options')
	->where('post_type', '=', 'solutions')
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
						'dark-video' => 'Dark',
						'light-video'      => 'Light',
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
	->add_tab('CTA', 	array(
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
	));


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
				->set_types(array(
					array(
						'type'      => 'post',
						'post_type' => 'post',
					)
				)),
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
				->set_types(array(
					array(
						'type'      => 'post',
						'post_type' => 'guides',
					)
				)),
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
				->set_types(array(
					array(
						'type'      => 'post',
						'post_type' => 'webinars',
					)
				)),
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
				->set_types(array(
					array(
						'type'      => 'post',
						'post_type' => 'casestudies',
					)
				)),
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
