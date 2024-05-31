<?php

/**
 * The template for displaying any single post.
 *
 */

get_header(); // This fxn gets the header.php file and renders it 
?>
<?php if (have_posts()):
	// Do we have any posts in the databse that match our query?
	?>

	<?php while (have_posts()):
		the_post();
		// If we have a post to show, start a loop that will display it
		?>
		<?php
		if (!get__post_meta('hide_page_banner') && get_post_type() != 'webinars' && get_post_type() != 'careers') {
			get_template_part('template-parts/section/content', 'banner');
		}

		if (get_post_type() == 'elementor_library') {
			the_content();
		}
		else {
			get_template_part('template-parts/single/content-post', get_post_type());
		}
		?>


	<?php endwhile; // OK, let's stop the post loop once we've displayed it 
	?>

	<?php
	// If comments are open or we have at least one comment, load up the default comment template provided by Wordpress
	/*if ( comments_open() || '0' != get_comments_number() )
			comments_template( '', true );*/
	?>


<?php else: // Well, if there are no posts to display and loop through, let's apologize to the reader (also your 404 error) 
	?>

	<article class="post error">
		<h1 class="404">Nothing has been posted like that yet</h1>
	</article>

<?php endif; // OK, I think that takes care of both scenarios (having a post or not having a post to show) 
?>
<?php get_footer(); // This fxn gets the footer.php file and renders it 
?>