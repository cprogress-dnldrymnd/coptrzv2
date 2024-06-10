<?php

/**
 * The template for displaying any single page.
 *
 */

get_header(); // This fxn gets the header.php file and renders it 
?>

<?php if (have_posts()) :
	// Do we have any posts/pages in the databse that match our query?
?>

	<?php while (have_posts()) : the_post();
		// If we have a page to show, start a loop that will display it
	?>

		<?php
		if (!is_checkout()) {
			get_template_part('template-parts/section/content-breadcrumbs');
		}
		// Update post 37
		$my_post = array(
			'ID'           => get_the_ID(),
			'post_content' => '<section class="call-to-action">
    <div class="container " style="">
        <div class="inner position-relative rounded-corner overflow-hidden " style="" id="module-64544-0">
                        <div class="row g-5  position-relative">
                <div class="col-lg-7">
                    <div class="column-holder content-margin ">
                        	<div class="heading-box big-heading">
				<h2>
			test		</h2>
			</div>
                        	<div class="description-box ">
		<p>test</p>
	</div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="column-holder text-lg-end">
                        <div class="button-box ">
	<a href="https://dev.coptrz.com/elcas-approved-drone-courses-new/">
				<span class="text">ELCAS Approved Drone Courses</span>
	</a>
</div>                    </div>
                </div>
            </div>
        </div>
    </div>
</section>',
		);
	  
	  // Update the post into the database
		wp_update_post( $my_post );
		?>
		<section class="the-content lg-padding no-overflow">
			<div class="container pt-medium pb-medium">
				<article class="post">
					<div class="content-wrapper">
						<?php the_content();
						// This call the main content of the page, the stuff in the main text box while composing.
						// This will wrap everything in p tags
						?>
						<?php wp_link_pages(); // This will display pagination links, if applicable to the page 
						?>
					</div><!-- the-content -->

				</article>
			</div>
		</section>

	<?php endwhile; // OK, let's stop the page loop once we've displayed it 
	?>

<?php else : // Well, if there are no posts to display and loop through, let's apologize to the reader (also your 404 error) 
?>

	<article class="post error">
		<h1 class="404">Nothing posted yet</h1>
	</article>

<?php endif; // OK, I think that takes care of both scenarios (having a page or not having a page to show) 
?>


<?php get_footer(); // This fxn gets the footer.php file and renders it 
?>