<?php
function action_open_header()
{
	echo '<header id="header">';
}
add_action('open_header', 'action_open_header');

function action_close_header()
{
	echo '</header>';
}

add_action('close_header', 'action_close_header');

function action_open_footer()
{
	echo '<footer>';
}
add_action('open_footer', 'action_open_footer');

function action_close_footer()
{
	echo '</footer>';
}

add_action('close_footer', 'action_close_footer');

function action_after_open_main()
{
	if (is_single() || is_page()) {
		$title = get_the_title();
	} else if (is_search()) {
		$title = 'Search';
	} else {
		$title = get_the_archive_title();
	}

	echo '<div class="page-title" role="banner"> <h1>' . $title . '</h1> </div>';
}

add_action('after_open_main', 'action_after_open_main');




//Allow shortcode in menu
add_filter('wp_nav_menu_items', 'do_shortcode');


function se_logout_redirect($redirect_to, $requested_redirect_to, $user)
{
	$requested_redirect_to = get_permalink(get_option('woocommerce_myaccount_page_id'));

	return $requested_redirect_to;
}
add_filter('logout_redirect', 'se_logout_redirect', 10, 3);


/*//Remove Editor
function remove_editor() {
if (isset($_GET['post'])) {
$id = $_GET['post'];
$template = get_post_meta($id, '_wp_page_template', true);
switch ($template) {
case 'templates/modules.php':
// the below removes 'editor' support for 'pages'
// if you want to remove for posts or custom post types as well
// add this line for posts:
// remove_post_type_support('post', 'editor');
// add this line for custom post types and replace 
// custom-post-type-name with the name of post type:
// remove_post_type_support('custom-post-type-name', 'editor');
remove_post_type_support('page', 'editor');
break;
default :
// Don't remove any other template.
break;
}
}
}
add_action('init', 'remove_editor');*/


function action_wp_head()
{
	$page_header_scripts = get__post_meta('page_header_scripts');
	$page_custom_css = get__post_meta('page_custom_css');

	if ($page_header_scripts) {
		echo $page_header_scripts;
	}
	if ($page_custom_css) {
		echo '<style id="page-custom-css">' . $page_custom_css . '</style>';
	}
?>
	<script>
		jQuery(document).ready(function() {
			announcement_bar();
		});

		function setCookie(cname, cvalue, exdays) {
			const d = new Date();
			d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
			let expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function getCookie(cname) {
			let name = cname + "=";
			let ca = document.cookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}

		function announcement_bar() {
			$announcement_bar = getCookie('announcement_bar');

			if ($announcement_bar != 'false') {
				jQuery('.announcement-bar').addClass('show');
			}
			jQuery('.exit-announcement-bar').click(function(e) {
				$top_bar = jQuery('.top-bar');
				jQuery('.announcement-bar').remove();
				jQuery('header').css('--top-bar-height', $top_bar.outerHeight() + 'px');
				setCookie('announcement_bar', 'false', 1);
			});
		}

		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');

				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
			return false;
		};
	</script>
	<?php
}

add_action('wp_head', 'action_wp_head', 99999);


function action_admin_head()
{
	if (isset($_GET['post'])) {
		$id = $_GET['post'];
		$template = get_post_meta($id, '_wp_page_template', true);
		$post_type = get_post_type($id);
		if ($template == 'templates/modules.php') {
	?>

			<style>
				.edit-post-header-toolbar {
					display: none !important;
				}

				.edit-post-sidebar__panel-tabs ul li:last-child {
					display: none;
				}

				.block-editor-block-list__layout {
					display: none !important;
				}

				.editor-post-text-editor {
					display: none !important;
				}

				.components-dropdown-menu__menu .components-menu-group:first-child {
					display: none;
				}
			</style>


		<?php } ?>

		<script>
			jQuery(document).ready(function($) {
				setTimeout(function() {
					jQuery('body').removeClass('is-fullscreen-mode');
				});
			});
		</script>
	<?php } ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js" integrity="sha512-NqYds8su6jivy1/WLoW8x1tZMRD7/1ZfhWG/jcRQLOzV1k1rIODCpMgoBnar5QXshKJGV7vi0LXLNXPoFsM5Zg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<?php
}

add_action('admin_head', 'action_admin_head', 999999999);

function action_body_scripts()
{
	$page_body_scripts = get__post_meta('page_body_scripts');
	$body_scripts = get__theme_option('body_scripts');
	if ($page_body_scripts) {
		echo $page_body_scripts;
	}

	if ($body_scripts) {
		echo $body_scripts;
	}
}

add_action('wp_body_open', 'action_body_scripts');



add_filter('body_class', 'action_body_class');
function action_body_class($classes)
{
	global $theme_options;
	if (is_product()) {
		$terms = get_the_terms(get_the_ID(), 'product_cat');
		foreach ($terms as $term) {
			$classes[] = 'product-cat-' . $term->slug;
		}
	}
	if (!is_404()) {

		if ($theme_options['announcement_bar']) {
			$classes[] = 'has-annoucement';
		}
	}

	return $classes;
}


add_filter('wpcf7_autop_or_not', '__return_false');

add_filter('get_the_archive_title', function ($title) {
	if (is_category()) {
		$title = single_cat_title('', false);
	} elseif (is_tag()) {
		$title = single_tag_title('', false);
	} elseif (is_author()) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif (is_tax()) { //for custom post types
		$title = sprintf(__('%1$s'), single_term_title('', false));
	} elseif (is_post_type_archive()) {
		$title = post_type_archive_title('', false);
	}
	return $title;
});



function modify_cpt_slug($args, $post_type)
{

	if ($post_type == 'career-paths' || $post_type == 'events' || $post_type == 'webinars') {
		$args['rewrite'] = array('with_front' => false);
	} else if ($post_type == 'casestudies') {
		$args['rewrite'] = array('with_front' => false, 'slug' => 'case-studies');
	} else if ($post_type == 'solutions') {
		$args['rewrite'] = array('with_front' => false, 'slug' => 'industry-solutions');
	} else if ($post_type == 'careers') {
		$args['rewrite'] = array('with_front' => false, 'slug' => 'careers/job-vacancies');
	}

	return $args;
}
add_filter('register_post_type_args', 'modify_cpt_slug', 10, 2);


function modify_taxonomy_slug($args, $taxonomy)
{
	if ($taxonomy == 'case_study_category') {
		$args['rewrite'] = array('with_front' => false, 'slug' => 'case-study-category');
	}
	return $args;
}
add_filter('register_taxonomy_args', 'modify_taxonomy_slug', 10, 2);


function action_wp_footer_scripts()
{
	$ms_tag = get__post_meta('use_microsoft_ads_uet_tag');
	$linkedin_tag = get__post_meta('use_linkedin_insight_tag');
	if ($ms_tag) {
	?>
		<script>
			(function(w, d, t, r, u) {
				var f, n, i;
				w[u] = w[u] || [], f = function() {
					var o = {
						ti: "17167185"
					};
					o.q = w[u], w[u] = new UET(o), w[u].push("pageLoad")
				}, n = d.createElement(t), n.src = r, n.async = 1, n.onload = n.onreadystatechange = function() {
					var s = this.readyState;
					s && s !== "loaded" && s !== "complete" || (f(), n.onload = n.onreadystatechange = null)
				}, i = d.getElementsByTagName(t)[0], i.parentNode.insertBefore(n, i)
			})(window, document, "script", "//bat.bing.com/bat.js", "uetq");
		</script>
	<?php
	}
	if ($linkedin_tag) {
	?>
		<script type="text/javascript">
			_linkedin_partner_id = "498138";
			window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
			window._linkedin_data_partner_ids.push(_linkedin_partner_id);
		</script>
		<script type="text/javascript">
			(function() {
				var s = document.getElementsByTagName("script")[0];
				var b = document.createElement("script");
				b.type = "text/javascript";
				b.async = true;
				b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
				s.parentNode.insertBefore(b, s);
			})();
		</script>
		<noscript>
			<img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid=498138&fmt=gif" />
		</noscript>
	<?php
	}
	?>
<?php

}

add_action('wp_footer', 'action_wp_footer_scripts');


function costumer_type_not_logged_in()
{
	$announcement_bar = get__theme_option('announcement_bar');
	$hide_on_mobile = get__theme_option('hide_on_mobile');

	global $theme_options;

	$theme_options = array(
		'announcement_bar' => $announcement_bar,
		'hide_on_mobile' => $hide_on_mobile,
	);
}

add_action('init', 'costumer_type_not_logged_in');
/**
 * Adds a submenu page under a custom post type parent.
 */
add_action('admin_menu', 'vendors_submenu');

function vendors_submenu()
{
	add_submenu_page(
		'edit.php?post_type=product',
		__('Brands', 'textdomain'),
		__('Brands', 'textdomain'),
		'manage_options',
		'/edit-tags.php?taxonomy=pa_brands&post_type=product',
	);
}



function custom_login_redirect()
{

	return '/my-account';
}

add_filter('woocommerce_login_redirect', 'custom_login_redirect');


function utm_parameters()
{
?>
	<script>
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');

				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
		};


		jQuery(document).ready(function($) {
			$utm_campaign = getUrlParameter('utm_campaign');
			$utm_source = getUrlParameter('utm_source');
			$utm_medium = getUrlParameter('utm_medium');
			$utm_term = getUrlParameter('utm_term');
			$utm_content = getUrlParameter('utm_content');
			$gclid = getUrlParameter('gclid');
			$dclid = getUrlParameter('dclid');

			setcookieVal('utm_campaign', $utm_campaign);
			setcookieVal('utm_source', $utm_source);
			setcookieVal('utm_medium', $utm_medium);
			setcookieVal('utm_term', $utm_term);
			setcookieVal('utm_content', $utm_content);
			setcookieVal('gclid', $gclid);
			setcookieVal('dclid', $dclid);


			setinputVal('form_fields[utm_campaign]', getCookie('utm_campaign'));
			setinputVal('form_fields[utm_source]', getCookie('utm_source'));
			setinputVal('form_fields[utm_medium]', getCookie('utm_medium'));
			setinputVal('form_fields[utm_term]', getCookie('utm_term'));
			setinputVal('form_fields[utm_content]', getCookie('utm_content'));
			setinputVal('form_fields[gclid]', getCookie('gclid'));
			setinputVal('form_fields[dclid]', getCookie('dclid'));
		});

		function setcookieVal($cname, $cvalue) {
			if ($cvalue) {
				setCookie($cname, $cvalue, 30);
			}
		}

		function setinputVal($name, $value) {
			if ($value) {
				jQuery('input[name="' + $name + '"]').val($value);
			}
		}
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split('=');

				if (sParameterName[0] === sParam) {
					return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
			return false;
		};

		function setCookie(cname, cvalue, exdays) {
			const d = new Date();
			d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
			let expires = "expires=" + d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		}

		function getCookie(cname) {
			let name = cname + "=";
			let ca = document.cookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
	</script>
<?php
}
add_action('wp_footer', 'utm_parameters');



/**
 * Disable the emoji's
 */
function disable_emojis()
{
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
	add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'disable_emojis');

/**
 * Filter function used to remove the tinymce emoji plugin.
 * 
 * @param array $plugins 
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce($plugins)
{
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch($urls, $relation_type)
{
	if ('dns-prefetch' == $relation_type) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

		$urls = array_diff($urls, array($emoji_svg_url));
	}

	return $urls;
}

add_filter('litespeed_ucss_per_pagetype', '__return_true');


add_action('admin_init', function () {
	// Redirect any user trying to access comments page
	global $pagenow;

	if ($pagenow === 'edit-comments.php') {
		wp_safe_redirect(admin_url());
		exit;
	}

	// Remove comments metabox from dashboard
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

	// Disable support for comments and trackbacks in post types
	foreach (get_post_types() as $post_type) {
		if (post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
	remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
});


function action_popups()
{
?>
	<?= do_shortcode('[popup id=268179]') ?>
	<script>
		jQuery(document).ready(function () {
			jQuery('button[data-bs-toggle="modal"]').each(function (index, element) {
				$target = jQuery(this).attr('data-bs-target');
				if(jQuery($target).length ==0) {
					$html = jQuery(jQuery.trim('<?= do_shortcode('[popup id=268179]') ?>'));

					console.log($html);
				}
			});
		});
	</script>
<?php
}

add_action('wp_footer', 'action_popups');