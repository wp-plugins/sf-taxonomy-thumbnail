jQuery(document).ready( function( $ ){
	var file_frame = {};

	// !Add new/Change thumbnail.
	$( "body" ).on( "click", ".add-term-thumbnail", function sftthOpenLibrary( e ) {
		var editor = $(this).parent( ".thumbnail-field-wrapper" ).attr( "id" ).slice( 0, -14 );

		window.wpActiveEditor = editor;

		// If the media frame already exists, reopen it.
		if ( typeof( file_frame[ editor ] ) !== "undefined" ) {
			file_frame[ editor ].open();
			return;
		}

		// Create the media frame.
		file_frame[ editor ] = window.wp.media.frames.file_frame = window.wp.media( {
			title: window.sftth.chooseImage,
			button: {
				text: window.sftth.selectImage
			},
			library: {
				type: "image"
			},
			multiple: false
		} );

		// If the input has some value, preselect the image.
		file_frame[ editor ].on( "open", function sftthPreselectCurrentThumbnail() {
			var preselect = Number( document.getElementById( editor ).value ),
				attachment, selection;

			if ( ! preselect ) {
				return;
			}

			attachment = wp.media.attachment( preselect );

			if ( ! attachment ) {
				return;
			}

			attachment.fetch();
			selection = file_frame[ editor ].state().get( "selection" );

			selection.add( [ attachment ] );
		} );

		// When an image is selected, fill the input and create the image preview.
		file_frame[ editor ].on( "select", function sftthSelectCurrentThumbnail() {
			var attachment   = file_frame[ editor ].state().get( "selection" ).first().toJSON(),
				ActiveEditor = document.getElementById( editor ),
				$output_wrap = $( "#" + editor + "-field-wrapper" ),
				tt_ID        = $output_wrap.data( "tt-id" ),
				$image       = $( "<img />" ),
				orientation;

			// Input value
			ActiveEditor.value = attachment.id;

			// The image
			if ( typeof( attachment.sizes.medium ) === "object" ) {
				$image.attr( { "src": attachment.sizes.medium.url, "height": attachment.sizes.medium.height, "width": attachment.sizes.medium.width, "class": "attachment-thumbnail" } );
				orientation = attachment.sizes.medium.orientation;
			}
			else if ( typeof( attachment.sizes.thumbnail ) === "object" ) {
				$image.attr( { "src": attachment.sizes.thumbnail.url, "height": attachment.sizes.thumbnail.height, "width": attachment.sizes.thumbnail.width, "class": "attachment-thumbnail" } );
				orientation = attachment.sizes.thumbnail.orientation;
			}
			else {
				$image.attr( { "src": attachment.url, "height": attachment.height, "width": attachment.width, "class": "attachment-full" } );
				orientation = attachment.orientation;
			}

			$image.attr( { "alt": attachment.alt, "title": attachment.title } );

			// Button wrapping the image
			$image = $image.wrap( "<button type=\"button\" class=\"change-term-thumbnail add-term-thumbnail attachment\" id=\"thumbnail-button\" title=\"" + window.sftth.changeImage + "\"><span class=\"attachment-preview type-image\"><span class=\"thumbnail\"><span class=\"centered\"></span></span></span></button>" ).parents( ".attachment-preview" ).addClass( orientation ).parents( ".change-term-thumbnail" );

			// Insert all the things
			$output_wrap.text( "" ).append( $image ).append( "<br/>" ).append( "<button type=\"button\" class=\"remove-term-thumbnail button button-secondary button-large delete\">" + window.sftth.removeImage + "</button>" );

			// Set the thumbnail via ajax.
			if ( typeof tt_ID !== "undefined" && tt_ID ) {
				$output_wrap
					.find( ".add-term-thumbnail" ).attr( { "disabled": "disabled", "aria-disabled": "true", "title": window.sftth.loading } ).focus()
					.siblings( ".remove-term-thumbnail" ).after( "<span class=\"spinner is-active\"></span>" );

				wp.media.ajax( "set-term-thumbnail", {
					data: {
						id: attachment.id,
						tt_ID: Number( tt_ID ),
						term_ID: $( "[name=\"tag_ID\"]" ).val(),
						taxonomy: $( "[name=\"taxonomy\"]" ).val(),
						_wpnonce: document.getElementById( "_wpnonce" ).value
					}
				} )
				.done( function() {
					// Prevent updating the term thumbnail on form submit (it's useless).
					$output_wrap
						.find( ".add-term-thumbnail" ).removeAttr( "disabled aria-disabled" ).attr( "title", window.sftth.changeImage )
						.siblings( ".spinner" ).replaceWith( "<span class=\"dashicons dashicons-yes\"></span><input type=\"hidden\" name=\"term-thumbnail-updated\" value=\"1\" />" );

					if ( wp.a11y && wp.a11y.speak ) {
						wp.a11y.speak( window.sftth.successSet );
					}
				} )
				.fail( function() {
					$output_wrap
						.find( ".add-term-thumbnail" ).removeAttr( "disabled aria-disabled" ).attr( "title", window.sftth.changeImage )
						.siblings( ".spinner" ).replaceWith( "<div class=\"error-message\">" + window.sftth.errorSet + "</div>" );

					if ( wp.a11y && wp.a11y.speak ) {
						wp.a11y.speak( window.sftth.errorSet );
					}
				} );
			}
		} );

		// Finally, open the modal
		file_frame[ editor ].open();
	} );

	// !Remove thumbnail
	$("body").on( "click", ".remove-term-thumbnail", function sftthOpenLibrary( e ) {
		var editor = $(this).parent( ".thumbnail-field-wrapper" ).attr( "id" ).slice( 0, -14 ),
			$output_wrap = $( "#" + editor + "-field-wrapper" ),
			tt_ID        = $output_wrap.data( "tt-id" );

		// Input value
		document.getElementById( editor ).value = '';

		// Remove the wrapper content and insert the button.
		$output_wrap.text( "" ).append( "<button type=\"button\" class=\"add-term-thumbnail button button-secondary button-large\" id=\"thumbnail-button\">" + window.sftth.setImage + "</button>" );

		// Unset the thumbnail via ajax.
		if ( typeof tt_ID !== "undefined" && tt_ID ) {
			$output_wrap
				.find( ".add-term-thumbnail" ).attr( { "disabled": "disabled", "aria-disabled": "true", "title": window.sftth.loading } ).focus()
				.after( "<span class=\"spinner is-active\"></span>" );

			wp.media.ajax( "delete-term-thumbnail", {
				data: {
					tt_ID: Number( tt_ID ),
					term_ID: $( "[name=\"tag_ID\"]" ).val(),
					taxonomy: $( "[name=\"taxonomy\"]" ).val(),
					_wpnonce: document.getElementById( "_wpnonce" ).value
				}
			} )
			.done( function() {
				// Prevent updating the term thumbnail on form submit (it's useless).
				$output_wrap
					.find( ".add-term-thumbnail" ).removeAttr( "disabled aria-disabled" ).attr( "title", window.sftth.changeImage )
					.siblings( ".spinner" ).replaceWith( "<span class=\"dashicons dashicons-yes\"></span><input type=\"hidden\" name=\"term-thumbnail-updated\" value=\"1\" />" );

				if ( wp.a11y && wp.a11y.speak ) {
					wp.a11y.speak( window.sftth.successRemoved );
				}
			} )
			.fail( function() {
				$output_wrap
					.find( ".add-term-thumbnail" ).removeAttr( "disabled aria-disabled title" )
					.siblings( ".spinner" ).replaceWith( "<div class=\"error-message\">" + window.sftth.errorRemoved + "</div>" );

				if ( wp.a11y && wp.a11y.speak ) {
					wp.a11y.speak( window.sftth.errorRemoved );
				}
			} );
		}
	} );

	// !Change the attribute "for".
	$( "[for=\"thumbnail\"]" ).attr( "for", "thumbnail-button" );

	// !Deal with aria-hidden.
	$( ".wp-thumbnail-wrap" ).attr( "aria-hidden", "true" );
	$( ".thumbnail-field-wrapper" ).removeAttr( "aria-hidden" );

	// !If the thumbnail is not changed, these inputs will stay in place and prevent a useless thumbnail update.
	$( ".add-term-thumbnail, .remove-term-thumbnail" ).after( "<input type=\"hidden\" name=\"term-thumbnail-updated\" value=\"1\" />" );

	// !The "New tag" form is submitted via ajax: let's empty the field value after submition.
	if ( $( "#addtag" ).length ) {
		$.ajaxPrefilter( function( options ) {
			var data = "&" + options.data + "&";
			if ( data.indexOf( "&action=add-tag&" ) !== -1 ) {
				$( ".remove-term-thumbnail" ).click();
			}
		} );
	}

} );