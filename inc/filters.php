<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !FILTERS ===================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

/*
 * !When using `get_terms()`, add the parameter `"with_thumbnail" => true` to return only terms with a thumbnail.
 * Side note: `"with_thumbnail" => false` will return ALL terms, not only the ones without thumbnail.
 *
 * This filter adds `WHERE` and/or `JOIN` clauses.
 */

add_filter( 'terms_clauses', 'sftth_terms_clauses_filter', 10, 3 );

function sftth_terms_clauses_filter( $clauses, $taxonomies, $args ) {
	global $wpdb;

	if ( empty( $args['with_thumbnail'] ) ) {
		return $clauses;
	}

	// With "Meta for Taxonomies" plugin.
	if ( sftth_has_term_meta_plugin() ) {
		$clauses[ 'join' ]   = ! empty( $clauses[ 'join' ] ) ? $clauses[ 'join' ] : '';
		$clauses[ 'join' ]  .= " INNER JOIN $wpdb->term_taxometa AS ttm ON t.term_id = ttm.term_taxo_id";

		$clauses[ 'where' ]  = ! empty( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
		$clauses[ 'where' ] .= " AND ttm.meta_key = '_thumbnail_id' AND CAST( ttm.meta_value AS SIGNED ) > 0";
	}
	// Without "Meta for Taxonomies" plugin.
	else {
		$option = sftth_get_option();
		$option = implode( ',', array_keys( $option ) );

		$clauses[ 'where' ]  = ! empty( $clauses[ 'where' ] ) ? $clauses[ 'where' ] : '';
		$clauses[ 'where' ] .= " AND tt.term_taxonomy_id IN ( $option )";
	}

	return $clauses;
}


/*
 * !When using `get_terms()`, set the parameter `with_thumbnail` to put thumbnail IDs in cache.
 * Hint: `"with_thumbnail" => false` will cache thumbnails and return ALL terms.
 * Will work only if the `fields` parameter is set to `all` and "Meta for Taxonomies" is active.
 *
 * This filter simply update the "termmeta" cache on `get_terms()` output.
 */

add_filter( 'get_terms', 'sftth_get_terms_cache_filter', 10, 3 );

function sftth_get_terms_cache_filter( $terms, $taxonomies, $args ) {

	if ( ! sftth_has_term_meta_plugin() ) {
		// Remove the filter if we don't use term metas.
		remove_filter( 'get_terms', 'sftth_get_terms_cache_filter' );
	}
	elseif ( $terms && 'all' === $args['fields'] && isset( $args['with_thumbnail'] ) ) {
		update_termmeta_cache( wp_list_pluck( $terms, 'term_taxonomy_id' ) );
	}

	return $terms;
}

/**/