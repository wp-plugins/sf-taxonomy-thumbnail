<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !UTILITIES =================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

/*
 * Get taxonomies where the thumbnail UI will be displayed.
 *
 * @return array An array like `array( "category" => "category", "post_tag" => "post_tag" )`.
 */

function sftth_get_taxonomies() {
	$taxonomies = get_taxonomies( array(
		'public'  => true,
		'show_ui' => true,
	) );

	return apply_filters( 'sftth_taxonomies', $taxonomies );
}


// !Tells if the plugin "Meta for Taxonomies" is activated.

function sftth_has_term_meta_plugin() {
	static $has_term_meta_plugin;

	if ( ! isset( $has_term_meta_plugin ) ) {
		$has_term_meta_plugin = function_exists( 'add_term_taxonomy_meta' );
	}

	return $has_term_meta_plugin;
}


// !Meh

if ( ! function_exists( 'doing_ajax' ) ) :
function doing_ajax() {
	return defined( 'DOING_AJAX' ) && DOING_AJAX && is_admin();
}
endif;


/**/