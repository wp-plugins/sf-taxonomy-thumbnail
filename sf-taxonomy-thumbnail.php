<?php
/*
 * Plugin Name: SF Taxonomy Thumbnail
 * Plugin URI: http://www.screenfeed.fr/taxthumb/
 * Description: Add a thumbnail to your taxonomy terms.
 * Version: 1.0
 * Author: Grégory Viguier
 * Author URI: http://www.screenfeed.fr/
 * License: GPLv3
 * License URI: http://www.screenfeed.fr/gpl-v3.txt
 * Text Domain: sf-taxonomy-thumbnail
 * Domain Path: /languages/
 */


/*
 * @See `inc/template-tags.php`.
 * @See `inc/filters.php`.
 *
 * Use the filter `sftth_taxonomies` to show/hide the thumbnail UI on your taxonomy edition pages. See `sftth_get_taxonomies()` in `inc/utilities.php`.
 * All public taxonomies are included by default.
 *
 * If the plugin "Meta for Taxonomies" is used, "SF Taxonomy Thumbnail" will use term metas instead of an option.
 * Grab it on Github: https://github.com/herewithme/meta-for-taxonomies,
 * or the official repository: https://wordpress.org/plugins/meta-for-taxonomies/.
 */


if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}
if ( version_compare( $GLOBALS['wp_version'], '3.5', '<' ) ) {
	return;
}

/* ---------------------------------------------------------------------------------------------- */
/* !CONSTANTS =================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

define( 'SFTTH_VERSION', '1.0' );
define( 'SFTTH_FILE',    __FILE__ );

if ( ! defined( 'SFTTH_OPTION_NAME' ) ) {
	define( 'SFTTH_OPTION_NAME', 'sftth_terms_thumbnail' );
}


/* !--------------------------------------------------------------------------------------------- */
/* !INCLUDES ==================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

add_action( 'plugins_loaded', 'sftth_includes', 1 );

function sftth_includes() {
	$dir = plugin_dir_path( SFTTH_FILE );

	if ( ! function_exists( 'add_term_taxonomy_meta' ) ) {
		include( $dir . 'inc/option.php' );
	}

	include( $dir . 'inc/utilities.php' );
	include( $dir . 'inc/template-tags.php' );
	include( $dir . 'inc/filters.php' );

	if ( doing_ajax() ) {
		include( $dir . 'inc/admin-and-ajax.php' );
		include( $dir . 'inc/ajax.php' );
	}
	elseif ( is_admin() ) {
		include( $dir . 'inc/admin-and-ajax.php' );
		include( $dir . 'inc/admin.php' );
	}

}


/**/