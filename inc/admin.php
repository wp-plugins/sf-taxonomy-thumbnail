<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}


/* ---------------------------------------------------------------------------------------------- */
/* !THE FIELD =================================================================================== */
/* ---------------------------------------------------------------------------------------------- */

// !Add the field to the term form.

add_action( 'load-edit-tags.php', 'sftth_add_field' );

function sftth_add_field() {
	global $taxnow;

	$taxonomies = sftth_get_taxonomies();

	if ( $taxnow && in_array( $taxnow, $taxonomies ) ) {
		// Add new term.
		add_action( $taxnow . '_add_form_fields', 'sftth_new_term_field', 20 );
		// Edit term.
		add_action( $taxnow . '_edit_form', 'sftth_edit_term_field', 20, 2 );
		// Styles and JavaScript
		add_action( 'admin_enqueue_scripts', 'sftth_styles_and_scripts' );
	}
}


// !Add new term.

function sftth_new_term_field( $taxonomy ) {
	?>
	<div class="form-field term-thumbnail">
		<label for="thumbnail"><?php _e( 'Thumbnail' ); ?></label>
		<div id="wp-thumbnail-wrap" class="wp-thumbnail-wrap wp-editor-wrap hide-if-js">
			<input type="number" name="thumbnail" value="" id="thumbnail" autocomplete="off" title="<?php esc_attr_e( 'Indicate an image ID', 'sf-taxonomy-thumbnail' ); ?>" />
		</div>

		<p id="thumbnail-field-wrapper" class="thumbnail-field-wrapper hide-if-no-js" aria-hidden="true">
			<button type="button" class="add-term-thumbnail button button-secondary button-large" id="thumbnail-button"><?php _e( 'Set a thumbnail', 'sf-taxonomy-thumbnail' ); ?></button>
		</p>
	</div>
	<?php
}


// !Edit term.

function sftth_edit_term_field( $term, $taxonomy ) {
	$term_taxonomy_id = absint( $term->term_taxonomy_id );
	$thumbnail_id     = get_term_thumbnail_id( $term_taxonomy_id );
	$thumbnail        = '';

	if ( $thumbnail_id ) {
		$thumbnail = get_term_thumbnail( $term_taxonomy_id, 'medium', array( 'title' => trim( strip_tags( get_the_title( $thumbnail_id ) ) ), ) );

		if ( ! $thumbnail ) {
			$thumbnail_id = '';
		}
	}
	?>
	<table class="form-table">
		<tbody>
			<tr class="form-field term-thumbnail-wrap">
				<th scope="row">
					<label for="thumbnail"><?php _e( 'Thumbnail' ); ?></label>
				</th>
				<td>
					<div id="wp-thumbnail-wrap" class="wp-thumbnail-wrap wp-editor-wrap hide-if-js">
						<input type="number" name="thumbnail" value="<?php echo $thumbnail_id; ?>" id="thumbnail" autocomplete="off" title="<?php esc_attr_e( 'Indicate an image ID', 'sf-taxonomy-thumbnail' ); ?>" /><br/>
						<?php echo $thumbnail; ?>
					</div>

					<div id="thumbnail-field-wrapper" class="thumbnail-field-wrapper hide-if-no-js" aria-hidden="true" data-tt-id="<?php echo $term_taxonomy_id; ?>">
						<?php
						if ( $thumbnail ) {
							$orientation = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
							$orientation = $orientation[1] >= $orientation[2] ? 'landscape' : 'portrait';
							echo '<button type="button" class="change-term-thumbnail add-term-thumbnail attachment" id="thumbnail-button" title="' . esc_attr__( 'Change thumbnail', 'sf-taxonomy-thumbnail' ) . '">';
								echo '<span class="attachment-preview type-image ' . $orientation . '"><span class="thumbnail"><span class="centered">' . $thumbnail . '</span></span></span>';
							echo '</button><br/>';
							echo '<button type="button" class="remove-term-thumbnail button button-secondary button-large delete">' . __( 'Remove thumbnail', 'sf-taxonomy-thumbnail' ) . '</button>';
						}
						else {
							echo '<button type="button" class="add-term-thumbnail button button-secondary button-large" id="thumbnail-button">' . __( 'Set a thumbnail', 'sf-taxonomy-thumbnail' ) . '</button>';
						}
						?>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}


// !Styles and scripts

function sftth_styles_and_scripts() {
	$dir = plugin_dir_url( SFTTH_FILE );
	$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$ver = $min ? SFTTH_VERSION : time();

	// CSS
	wp_enqueue_style( 'sf-taxonomy-thumbnail', $dir . 'res/css/style' . $min . '.css', false, $ver, 'all' );

	// JS
	wp_enqueue_media();

	$dependencies = array( 'jquery', 'media-editor' );
	if ( version_compare( $GLOBALS['wp_version'], '4.2', '>=' ) ) {
		$dependencies[] = 'wp-a11y';
	}
	wp_enqueue_script( 'sf-taxonomy-thumbnail', $dir . 'res/js/script' . $min . '.js', $dependencies, $ver, true );

	$i18n = array(
		'setImage'       => __( 'Set a thumbnail',  'sf-taxonomy-thumbnail' ),
		'changeImage'    => __( 'Change thumbnail', 'sf-taxonomy-thumbnail' ),
		'removeImage'    => __( 'Remove thumbnail', 'sf-taxonomy-thumbnail' ),
		'chooseImage'    => __( 'Choose Thumbnail', 'sf-taxonomy-thumbnail' ),
		'selectImage'    => __( 'Select thumbnail', 'sf-taxonomy-thumbnail' ),
		'loading'        => __( 'Loading&hellip;',  'sf-taxonomy-thumbnail' ),
		'successSet'     => __( 'Thumbnail successfully set to this term.', 'sf-taxonomy-thumbnail' ),
		'successRemoved' => __( 'Thumbnail successfully removed from this term.', 'sf-taxonomy-thumbnail' ),
		'errorSet'       => __( 'An error occurred, the thumbnail could not be set to this term. Try to update the term manually.', 'sf-taxonomy-thumbnail' ),
		'errorRemoved'   => __( 'An error occurred, the thumbnail could not be removed from this term. Try to update the term manually.', 'sf-taxonomy-thumbnail' ),
	);
	wp_localize_script( 'sf-taxonomy-thumbnail', 'sftth', $i18n );
}


/**/