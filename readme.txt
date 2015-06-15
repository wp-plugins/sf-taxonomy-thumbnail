=== Taxonomy Thumbnail ===

Contributors: GregLone
Tags: thumbnail, image, taxonomy, category, dev
Requires at least: 3.5
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv3
License URI: http://www.screenfeed.fr/gpl-v3.txt

Add a thumbnail to your taxonomy terms.


== Description ==

This plugin is meant for developers, it allows to attach a thumbnail to taxonomy terms.

= UI for setting the thumbnails =

The thumbnail can be added on term creation or later on the term edition page.  
The terms list has a column displaying the current thumbnail (so far, no specific action here).  
The plugin uses the "new" media window (the one used since WP 3.5), not the old thickbox.  
I made some extra efforts to enhance accessibility. I'm not an a11y expert but the UI is not only a "Add thumbnail" button. For instance, the new `wp.a11y.speak()` is used when available. Please give me feedback if you think it can be improved.  
Works with or without JavaScript.  
If JavaScript is enabled, thumbnails are set via ajax, no need to update the terms.  
By default, the UI is displayed for all public taxonomies, but this can be filtered that way:

	add_filter( 'sftth_taxonomies', 'my_taxonomies_with_thumbnail' );

	function my_taxonomies_with_thumbnail( $taxonomies ) {
		unset( $taxonomies['post_tag'] );
		$taxonomies['my_custom_tax'] = 'my_custom_tax';
		return $taxonomies;
	}

= Template tags =

Find them in `inc/template-tags.php`.  
All of them use `term_taxonomy_id`, not `term_id`. This way we don't need to specify the taxonomy. I tried to mimic the post thumbnail functions.

Retrieve term thumbnail ID:

	get_term_thumbnail_id( $term_taxonomy_id )

Check if term has a thumbnail attached:

	has_term_thumbnail( $term_taxonomy_id )

Display the term thumbnail:

	the_term_thumbnail( $term_taxonomy_id, $size = 'post-thumbnail', $attr = '' )

Retrieve the term thumbnail:

	get_term_thumbnail( $term_taxonomy_id, $size = 'post-thumbnail', $attr = '' )

Set a term thumbnail:

	set_term_thumbnail( $term_taxonomy_id, $thumbnail_id )

Remove a term thumbnail:

	delete_term_thumbnail( $term_taxonomy_id )

= Store the data =

There are two ways to store the thumbnail IDs:

1. Use term metas with the plugin [Meta for Taxonomies](https://wordpress.org/plugins/meta-for-taxonomies/).
1. Use an option (an array association of `term_taxonomy_id` => `thumbnail_id` integers). The option name can be customized by defining the constant `SFTTH_OPTION_NAME` in `wp-config.php`.

Side note: there is no upgrade system to switch from one to the other.

= Get terms =

Use `get_terms()` with a specific parameter to retrieve only terms with a thumbnail:

	$terms = get_terms( array(
		'with_thumbnail' => true,
	) );

If you use the plugin *Meta for Taxonomies*, you should always cache thumbnails:

	$terms = get_terms( array(
		'with_thumbnail' => false,
	) );

Summary:

1. `'with_thumbnail' => true`: cache thumbnails, retrieve only terms with a thumbnail.
1. `'with_thumbnail' => false`: cache thumbnails, retrieve all terms.

= Uninstall =

When uninstalling the plugin, you can decide to not delete the thumbnails, simply define a constant in `wp-config.php`:

	define( 'SFTTH_KEEP_DATA', true );


= Translations =

* US English
* French

= Requirements =

Should work starting from WP 3.5, but tested only in WP 4.2.2 so far.

= Credits =

Photo used for the banner by [Nicolas Janik](https://www.flickr.com/photos/n1colas/2598073727/) ([CC BY 2.0](https://creativecommons.org/licenses/by/2.0/)).


== Installation ==

1. Extract the plugin folder from the downloaded ZIP file.
1. Upload the `sf-taxonomy-thumbnail` folder to your `/wp-content/plugins/` directory.
1. Activate the plugin from the "Plugins" page.
1. If you want to use term metas instead of an option, install the plugin [Meta for Taxonomies](https://wordpress.org/plugins/meta-for-taxonomies/).


== Frequently Asked Questions ==

= Do I need to cache thumbnails in `get_terms()` if I don't use term metas? =

No, the option is already cached by WordPress.

= Why should I use the plugin Meta for Taxonomies? =

I think it's the proper way to store this kind of data. Post thumbnails are stored in post metas, right? So term thumbnails should be stored in term metas.  
But it requires a plugin, WordPress does not provide a term metas system yet. And I don't want to force you to use a dependency, so I wanted my plugin to also work without it.

= Any plan for a settings page where I can choose the taxonomies? =

Nope, it will not happen, there is no point to do so.

= Other questions? =

Eventually, try the [WordPress support forum](http://wordpress.org/support/plugin/sf-move-login).


== Screenshots ==

1. Create a new category an assign a thumbnail.
2. Change or remove the thumbnail from the category.


== Changelog ==

= 1.0 =

* 2015/06/11
* Initial release.


== Upgrade Notice ==

First release.
