<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

if ( defined( 'SFTTH_KEEP_DATA' ) && SFTTH_KEEP_DATA ) {
	return;
}

if ( ! defined( 'SFTTH_OPTION_NAME' ) ) {
	define( 'SFTTH_OPTION_NAME', 'sftth_terms_thumbnail' );
}

delete_option( SFTTH_OPTION_NAME );

if ( function_exists( 'delete_term_taxonomy_meta' ) ) {
	delete_term_taxonomy_meta( false, '_thumbnail_id', false, true );
}

/**/