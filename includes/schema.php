<?php
/**
 * Primary hook function to inject Course JSON-LD schema into the document head.
 * Evaluates the current query context to ensure it only fires on the correct product types.
 *
 * @return void
 */
function dd_inject_dynamic_schema() {
	// Restrict to singular views
	if ( ! is_singular() ) {
		return;
	}

	global $post;
	$schema_payloads = array();

	// Evaluate Course Schema for WooCommerce products in the 'training' category OR specific pages
	$is_training_product = is_singular( 'product' ) && has_term( 'training', 'product_cat' );
	$is_course_page      = is_page() && get_post_meta( $post->ID, '_is_course_page', true ); // Custom field trigger for standard pages

	if ( $is_training_product || $is_course_page ) {
		$schema_payloads[] = dd_generate_course_schema( $post );
	}

	// Output the schema payload to the DOM
	if ( ! empty( $schema_payloads ) ) {
		$final_json = count( $schema_payloads ) === 1 ? $schema_payloads[0] : $schema_payloads;
		
		echo "\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $final_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'dd_inject_dynamic_schema', 99 );

/**
 * Constructs the Course schema object for WooCommerce training products and specific pages.
 *
 * @param WP_Post $post The current global post object.
 * @return array The structured schema array.
 */
function dd_generate_course_schema( $post ) {
	// Fallback logic for description: use excerpt if available, otherwise trim the post content.
	$course_description = has_excerpt( $post->ID ) ? get_the_excerpt( $post->ID ) : wp_trim_words( $post->post_content, 25 );
	$provider_name      = get_bloginfo( 'name' );

	return array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Course',
		'name'        => get_the_title( $post->ID ),
		'description' => wp_strip_all_tags( $course_description ),
		'provider'    => array(
			'@type' => 'Organization',
			'name'  => $provider_name,
			'sameAs'=> home_url(),
		),
	);
}