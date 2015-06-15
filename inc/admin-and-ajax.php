<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !I18N ======================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

add_action( 'init', 'sftth_lang_init' );

function sftth_lang_init() {
	load_plugin_textdomain( 'sf-taxonomy-thumbnail', false, basename( dirname( SFTTH_FILE ) ) . '/languages/' );
}


/* !--------------------------------------------------------------------------------------------- */
/* !UPDATE ON FORM SUBMIT ======================================================================= */
/* ---------------------------------------------------------------------------------------------- */

/*
 * Used in the following cases:
 * - when we add a new term,
 * - if JavaScript is disabled while editing a term,
 * - if the update via ajax failed while editing a term.
 */

add_action( 'created_term', 'sftth_update_term_thumbnail_on_form_submit', 10, 3 );
add_action( 'edited_term',  'sftth_update_term_thumbnail_on_form_submit', 10, 3 );

function sftth_update_term_thumbnail_on_form_submit( $term_id, $term_taxonomy_id, $taxonomy ) {
	// The thumbnail is already set via ajax (or hasn't changed).
	if ( ! empty( $_POST['term-thumbnail-updated'] ) || ! isset( $_POST['thumbnail'] ) ) {
		return;
	}

	if ( empty( $_POST['action'] ) || ( $_POST['action'] !== 'add-tag' && $_POST['action'] !== 'editedtag' ) ) {
		return;
	}

	$thumbnail_id = absint( $_POST['thumbnail'] );

	if ( $thumbnail_id ) {
		set_term_thumbnail( $term_taxonomy_id, $thumbnail_id );
	}
	else {
		delete_term_thumbnail( $term_taxonomy_id );
	}
}


/* !--------------------------------------------------------------------------------------------- */
/* !TABLES COLUMN =============================================================================== */
/* ---------------------------------------------------------------------------------------------- */

add_action( 'admin_init', 'sftth_add_columns', 5 );
add_action( 'wp_ajax_add-tag', 'sftth_add_columns', 5 );

function sftth_add_columns() {
	global $taxnow;

	$taxonomy   = doing_ajax() && ! empty( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : $taxnow;
	$taxonomies = sftth_get_taxonomies();

	if ( $taxonomy && in_array( $taxonomy, $taxonomies ) ) {
		add_filter( 'manage_edit-' . $taxonomy . '_columns', 'sftth_add_column_header' );
		add_filter( 'manage_' . $taxonomy . '_custom_column', 'sftth_add_column_content', 10, 3 );
	}
}


function sftth_add_column_header( $columns ) {
	$out = array();

	if ( ! empty( $columns ) ) {
		foreach ( $columns as $id => $column ) {
			$out[ $id ] = $column;

			if ( $id === 'cb' ) {
				$out['term-thumbnail'] = __( 'Thumbnail' );
				break;
			}
		}

		$out = array_merge( $out, $columns );
	}

	return $out;
}


function sftth_add_column_content( $content, $column_name, $term_id ) {
	global $taxnow;

	if ( $column_name !== 'term-thumbnail' ) {
		return $content;
	}

	$taxonomy         = doing_ajax() && ! empty( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : $taxnow;
	$term             = get_term( $term_id, $taxonomy );
	$term_taxonomy_id = absint( $term->term_taxonomy_id );
	$thumbnail_id     = get_term_thumbnail_id( $term_taxonomy_id );

	return $thumbnail_id ? get_term_thumbnail( $term_taxonomy_id, array( 80, 60 ) ) : '';
}

/**/