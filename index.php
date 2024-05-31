<?php

/**
 * The template for displaying the home/index page.
 * This template will also be called in any case where the Wordpress engine 
 * doesn't know which template to use (e.g. 404 error)
 */
get_header(); // This fxn gets the header.php file and renders it 
?>

<?php get_template_part('template-parts/section/content-breadcrumbs'); ?>
<?php
if (is_search()) {
	get_template_part('template-parts/archive/archive', 'search');
} else {
	get_template_part('template-parts/archive/archive', get_post_type());
}
?>

<?php
/*
if (current_user_can('administrator')) {
	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status '   => 'any'

	);

	$posts = get_posts($args);
	echo '<table class="table" style="width: 800px; margin-left: auto; margin-right: auto">';
	foreach ($posts as $post) {

		$featured_video_text = get__post_meta_by_id($post->ID, 'featured_video_text');
		$modules = get__post_meta_by_id($post->ID, 'modules');

		if ($featured_video_text || $modules) {
			echo '<tr>';
			echo '<td>';
			echo '<a href="' . get_permalink($post->ID) . '">';
			echo $post->post_title;
			echo '</a>';
			echo '</td>';

			echo '<td>';
			if ($featured_video_text) {
				echo 'Has video';
			}
			echo '</td>';

			echo '<td>';
			if ($modules) {
				echo 'Has modules';
			}
			echo '</td>';

			echo '</tr>';
		}
	}
	echo '</table>';
}
*/
?>



<?php get_footer(); // This fxn gets the footer.php file and renders it 
?>