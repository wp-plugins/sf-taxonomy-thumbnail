<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !SAVE TERM THUMBNAIL VIA AJAX ================================================================ */
/* ---------------------------------------------------------------------------------------------- */

// !Set a thumbnail to the given term.

add_action( 'wp_ajax_set-term-thumbnail', 'sftth_set_term_thumbnail_ajax' );

function sftth_set_term_thumbnail_ajax() {
	if ( empty( $_POST['term_ID'] ) || empty( $_POST['tt_ID'] ) || empty( $_POST['taxonomy'] ) || empty( $_POST['id'] ) ) {
		wp_send_json_error();
	}

	check_ajax_referer( 'update-tag_' . $_POST['term_ID'], '_wpnonce' );

	$taxonomy = esc_attr( $_POST['taxonomy'] );
	$taxonomy = get_taxonomy( $taxonomy );

	if ( ! $taxonomy || is_wp_error( $taxonomy ) || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		wp_send_json_error();
	}

	$term_taxonomy_id = absint( $_POST['tt_ID'] );
	$thumbnail_id     = absint( $_POST['id'] );

	if ( $term_taxonomy_id && $thumbnail_id ) {
		if ( get_term_thumbnail_id( $term_taxonomy_id ) === $thumbnail_id ) {
			wp_send_json_success();
		}
		if ( set_term_thumbnail( $term_taxonomy_id, $thumbnail_id ) ) {
			wp_send_json_success();
		}
	}

	wp_send_json_error();
}


// !Remove the thumbnail from the given term.

add_action( 'wp_ajax_delete-term-thumbnail', 'sftth_delete_term_thumbnail_ajax' );

function sftth_delete_term_thumbnail_ajax() {
	if ( empty( $_POST['term_ID'] ) || empty( $_POST['tt_ID'] ) || empty( $_POST['taxonomy'] ) ) {
		wp_send_json_error();
	}

	check_ajax_referer( 'update-tag_' . $_POST['term_ID'], '_wpnonce' );

	$taxonomy = esc_attr( $_POST['taxonomy'] );
	$taxonomy = get_taxonomy( $taxonomy );

	if ( ! $taxonomy || is_wp_error( $taxonomy ) || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		wp_send_json_error();
	}

	$term_taxonomy_id = absint( $_POST['tt_ID'] );

	if ( $term_taxonomy_id && ( ! get_term_thumbnail_id( $term_taxonomy_id ) || delete_term_thumbnail( $term_taxonomy_id ) ) ) {
		wp_send_json_success();
	}

	wp_send_json_error();
}

/**/