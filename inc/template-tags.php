<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !TEMPLATE TAGS =============================================================================== */
/* ---------------------------------------------------------------------------------------------- */

/**
 * Retrieve term thumbnail ID.
 *
 * @since 1.0.0
 *
 * @param int        $term_taxonomy_id Term term_taxonomy_id.
 * @return int|false The term thumbnail ID on success, false on failure.
 */

function get_term_thumbnail_id( $term_taxonomy_id ) {
	$term_taxonomy_id = absint( $term_taxonomy_id );

	if ( $term_taxonomy_id ) {

		// With "Meta for Taxonomies" plugin.
		if ( sftth_has_term_meta_plugin() ) {
			$thumbnail = $term_taxonomy_id ? get_term_taxonomy_meta( $term_taxonomy_id, '_thumbnail_id', true ) : false;
			return $thumbnail ? absint( $thumbnail ) : false;
		}

		// Without "Meta for Taxonomies" plugin.
		return sftth_get_option( $term_taxonomy_id );
	}

	return false;
}


/**
 * Check if term has a thumbnail attached.
 *
 * @since 1.0.0
 *
 * @param int   $term_taxonomy_id Term term_taxonomy_id.
 * @return bool Whether term has an image attached.
 */

function has_term_thumbnail( $term_taxonomy_id ) {
	return (bool) get_term_thumbnail_id( $term_taxonomy_id );
}


/**
 * Display the term thumbnail.
 *
 * @since 1.0.0
 *
 * @see get_term_thumbnail()
 *
 * @param int $term_taxonomy_id Term term_taxonomy_id.
 * @param string|array $size    Optional. Registered image size to use, or flat array of height
 *                              and width values. Default 'post-thumbnail'.
 * @param string|array $attr    Optional. Query string or array of attributes. Default empty.
 */

function the_term_thumbnail( $term_taxonomy_id, $size = 'post-thumbnail', $attr = '' ) {
	echo get_term_thumbnail( $term_taxonomy_id, $size, $attr );
}


/**
 * Retrieve the term thumbnail.
 *
 * @since 1.0.0
 *
 * @param int $term_taxonomy_id Term term_taxonomy_id.
 * @param string|array $size    Optional. Registered image size to use, or flat array of height
 *                              and width values. Default 'post-thumbnail'.
 * @param string|array $attr    Optional. Query string or array of attributes. Default empty.
 * @return string      The term thumbnail.
 */

function get_term_thumbnail( $term_taxonomy_id, $size = 'post-thumbnail', $attr = '' ) {

	$term_thumbnail_id = get_term_thumbnail_id( $term_taxonomy_id );

	/**
	 * Filter the term thumbnail size.
	 *
	 * @since 1.0.0
	 *
	 * @param string $size The term thumbnail size.
	 */
	$size = apply_filters( 'term_thumbnail_size', $size );

	if ( $term_thumbnail_id ) {

		/**
		 * Fires before fetching the term thumbnail HTML.
		 *
		 * Provides "just in time" filtering of all filters in wp_get_attachment_image().
		 *
		 * @since 1.0.0
		 *
		 * @param string $term_taxonomy_id  The term_taxonomy ID.
		 * @param string $term_thumbnail_id The term thumbnail ID.
		 * @param string $size              The term thumbnail size.
		 */
		do_action( 'begin_fetch_term_thumbnail_html', $term_taxonomy_id, $term_thumbnail_id, $size );

		$html = wp_get_attachment_image( $term_thumbnail_id, $size, false, $attr );

		/**
		 * Fires after fetching the term thumbnail HTML.
		 *
		 * @since 1.0.0
		 *
		 * @param string $term_taxonomy_id  The term_taxonomy ID.
		 * @param string $term_thumbnail_id The term thumbnail ID.
		 * @param string $size              The term thumbnail size.
		 */
		do_action( 'end_fetch_term_thumbnail_html', $term_taxonomy_id, $term_thumbnail_id, $size );

	}
	else {
		$html = '';
	}
	/**
	 * Filter the term thumbnail HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html              The term thumbnail HTML.
	 * @param string $term_taxonomy_id  The term_taxonomy ID.
	 * @param string $term_thumbnail_id The term thumbnail ID.
	 * @param string $size              The term thumbnail size.
	 * @param string $attr              Query string of attributes.
	 */
	return apply_filters( 'term_thumbnail_html', $html, $term_taxonomy_id, $term_thumbnail_id, $size, $attr );
}


/**
 * Set a term thumbnail.
 *
 * @since 1.0.0
 *
 * @param int   $term_taxonomy_id Term term_taxonomy_id where thumbnail should be attached.
 * @param int   $thumbnail_id     Thumbnail to attach.
 * @return bool True on success, false on failure.
 */

function set_term_thumbnail( $term_taxonomy_id, $thumbnail_id ) {
	$term_taxonomy_id = absint( $term_taxonomy_id );
	$thumbnail_id     = absint( $thumbnail_id );

	if ( ! $term_taxonomy_id ) {
		return false;
	}

	// With "Meta for Taxonomies" plugin.
	if ( sftth_has_term_meta_plugin() ) {
		if ( $thumbnail_id && get_post( $thumbnail_id ) && wp_get_attachment_image( $thumbnail_id, 'thumbnail' ) ) {
			return update_term_taxonomy_meta( $term_taxonomy_id, '_thumbnail_id', $thumbnail_id );
		}

		return delete_term_taxonomy_meta( $term_taxonomy_id, '_thumbnail_id' );
	}

	// Without "Meta for Taxonomies" plugin.
	return sftth_update_option( $term_taxonomy_id, $thumbnail_id );
}


/**
 * Remove a term thumbnail.
 *
 * @since 1.0.0
 *
 * @param int   $term_taxonomy_id Term term_taxonomy_id where thumbnail should be removed from.
 * @return bool True on success, false on failure.
 */

function delete_term_thumbnail( $term_taxonomy_id ) {
	return set_term_thumbnail( $term_taxonomy_id, false );
}


/**/