<?php
/**
 * Search results template.
 *
 * Dedicated list layout (not the archive AJAX grid). Each hit shows a
 * thumbnail (featured image or theme placeholder), title, post-type badge,
 * and excerpt.
 *
 * @package DigitallyDisruptive
 */

get_header();

$search_query = get_search_query();
$post_type    = function_exists('coptrz_current_search_post_type') ? coptrz_current_search_post_type() : '';
$found        = (int) $GLOBALS['wp_query']->found_posts;

$types = function_exists('coptrz_searchable_post_types') ? coptrz_searchable_post_types() : array();
?>

<main class="search-results-page" id="main">
	<section class="search-results-hero">
		<div class="container">
			<h1 class="search-results-hero__title">
				<?php
				if ($search_query) {
					printf(
						/* translators: %s: search query */
						esc_html__('Search results for “%s”', 'coptrz-theme'),
						esc_html($search_query)
					);
				} else {
					esc_html_e('Search', 'coptrz-theme');
				}
				?>
			</h1>
			<p class="search-results-hero__count">
				<?php
				printf(
					/* translators: %d: number of results */
					esc_html(_n('%d result', '%d results', $found, 'coptrz-theme')),
					$found
				);
				?>
			</p>
			<?php
			if (function_exists('coptrz_search_form_html')) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helpers escape.
				echo coptrz_search_form_html(
					array(
						's'          => $search_query,
						'post_type'  => $post_type,
						'id_prefix'  => 'coptrz-results-pt',
						'input_id'   => 'coptrz-search-results-input',
						'form_class' => 'coptrz-search-form--results',
					)
				);
			}
			?>
		</div>
	</section>

	<section class="search-results-list">
		<div class="container">
			<?php if (have_posts()) : ?>
				<div class="search-results-items">
					<?php
					while (have_posts()) :
						the_post();
						$post_id   = get_the_ID();
						$type_slug = get_post_type($post_id);
						$type_obj  = get_post_type_object($type_slug);
						$type_label = isset($types[ $type_slug ])
							? $types[ $type_slug ]
							: ( $type_obj && ! empty($type_obj->labels->singular_name)
								? $type_obj->labels->singular_name
								: $type_slug );
						$permalink = get_permalink($post_id);
						$title     = get_the_title($post_id);
						?>
						<article <?php post_class('search-result-item-wrap'); ?>>
							<a class="search-result-item" href="<?php echo esc_url($permalink); ?>">
								<div class="search-result-item__media">
									<?php
									if (function_exists('__image')) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- __image escapes.
										echo __image(
											array(
												'featured_image' => $post_id,
												'size'           => 'medium',
												'placeholder'    => true,
												'class'          => _attribute('class', array('image-box', 'search-result-item__image')),
											)
										);
									} elseif (has_post_thumbnail($post_id)) {
										echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'search-result-item__image'));
									}
									?>
								</div>
								<div class="search-result-item__body">
									<span class="search-result-item__type"><?php echo esc_html($type_label); ?></span>
									<h2 class="search-result-item__title"><?php echo esc_html($title); ?></h2>
									<?php
									$excerpt = get_the_excerpt($post_id);
									if ($excerpt) :
										?>
										<p class="search-result-item__excerpt"><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p>
									<?php endif; ?>
								</div>
							</a>
						</article>
					<?php endwhile; ?>
				</div>

				<nav class="search-results-pagination" aria-label="<?php esc_attr_e('Search results pages', 'coptrz-theme'); ?>">
					<?php
					echo paginate_links(
						array(
							'total'     => (int) $GLOBALS['wp_query']->max_num_pages,
							'current'   => max(1, (int) get_query_var('paged')),
							'type'      => 'plain',
							'prev_text' => __('Previous', 'coptrz-theme'),
							'next_text' => __('Next', 'coptrz-theme'),
						)
					);
					?>
				</nav>
			<?php else : ?>
				<div class="search-results-empty">
					<h2><?php esc_html_e('No results found', 'coptrz-theme'); ?></h2>
					<p><?php esc_html_e('Try a different search term or content type filter.', 'coptrz-theme'); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
