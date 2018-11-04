<?php
// Register Post Type blocks
function post_type_block() {
	$labels = array(
		'name'                  => _x( 'Theme options', 'Post Type General Name', Config::$domain_name ),
		'singular_name'         => _x( 'Block', 'Post Type Singular Name', Config::$domain_name ),
		'menu_name'             => __( 'Theme options', Config::$domain_name ),
		'name_admin_bar'        => __( 'Theme options', Config::$domain_name ),
		'archives'              => __( 'Blocks Archives', Config::$domain_name ),
		'attributes'            => __( 'Blocks Attributes', Config::$domain_name ),
		'parent_item_colon'     => __( 'Parent block:', Config::$domain_name ),
		'all_items'             => __( 'All blocks', Config::$domain_name ),
		'add_new_item'          => __( 'Add new block', Config::$domain_name ),
		'add_new'               => __( 'Add new block', Config::$domain_name ),
		'new_item'              => __( 'New block', Config::$domain_name ),
		'edit_item'             => __( 'Modifier le block', Config::$domain_name ),
		'update_item'           => __( 'Mettre à jour le block', Config::$domain_name ),
		'view_item'             => __( 'Display block', Config::$domain_name ),
		'view_items'            => __( 'Display all blocks', Config::$domain_name ),
		'search_items'          => __( 'Find block', Config::$domain_name ),
		'not_found'             => __( 'No block found', Config::$domain_name ),
		'not_found_in_trash'    => __( 'No block found in trash', Config::$domain_name ),
		'featured_image'        => __( 'Image block (375x250)', Config::$domain_name ),
		'set_featured_image'    => __( 'Set image', Config::$domain_name ),
		'remove_featured_image' => __( 'Delete image', Config::$domain_name ),
		'use_featured_image'    => __( 'Use this image', Config::$domain_name ),
		'insert_into_item'      => __( 'Add', Config::$domain_name ),
		'uploaded_to_this_item' => __( 'Upload to this post', Config::$domain_name ),
		'items_list'            => __( 'Items list', Config::$domain_name ),
		'items_list_navigation' => __( 'Navigation', Config::$domain_name ),
		'filter_items_list'     => __( 'List items filter', Config::$domain_name ),
	);

	$args = array(
		'label'                 => __( 'Blocks', Config::$domain_name ),
		'description'           => __( 'Blocks Description', Config::$domain_name ),
		'labels'                => $labels,
		'supports' 				=> array('title'),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 2,
		'menu_icon'         	=> 'dashicons-admin-generic',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'    => array('post', 'posts'),
		'map_meta_cap'       => true
	);

	register_post_type( 'blocks', $args );
}

add_action( 'wp_loaded', 'post_type_block', 0 );

/**
 * Partner categories
 * @uses  Inserts new taxonomy object into the list
 * @uses  Adds query vars
 * @param string  Name of taxonomy object
 * @param array|string  Name of the object type for the taxonomy object.
 * @param array|string  Taxonomy arguments
 * @return null|WP_Error WP_Error if errors, otherwise null.
 */

function blocks_categories() {

	$taxTitle = "blocks Categories"; 
	$labels = array(
		'name'                       => _x( $taxTitle, 'Taxonomy General Name', Config::$domain_name ),
		'singular_name'              => _x( $taxTitle, 'Taxonomy Singular Name', Config::$domain_name ),
		'menu_name'                  => __( $taxTitle, Config::$domain_name ),
		'all_items'                  => __( 'Toutes les categories', Config::$domain_name ),
		'parent_item'                => __( 'Categorie parente', Config::$domain_name ),
		'parent_item_colon'          => __( 'Categorie parente', Config::$domain_name ),
		'new_item_name'              => __( 'Nouvelle Categorie ', Config::$domain_name ),
		'add_new_item'               => __( 'Nouvelle Categorie ', Config::$domain_name ),
		'edit_item'                  => __( 'Modifier', Config::$domain_name ),
		'update_item'                => __( 'Mettre à jour', Config::$domain_name ),
		'view_item'                  => __( 'Afficher', Config::$domain_name ),
		'separate_items_with_commas' => __( 'Separer les élements par points-virgules', Config::$domain_name ),
		'add_or_remove_items'        => __( 'Aajouter ou supprimer des éléments', Config::$domain_name ),
		'choose_from_most_used'      => __( 'Choisissez parmi les plus utilisés', Config::$domain_name ),
		'popular_items'              => __( 'Éléments populaires', Config::$domain_name ),
		'search_items'               => __( 'Trouver une categorie', Config::$domain_name ),
		'not_found'                  => __( 'Pas trouvé', Config::$domain_name ),
		'no_terms'                   => __( 'Aucune catégorie trouvée', Config::$domain_name ),
		'items_list'                 => __( 'Liste des élements ', Config::$domain_name ),
		'items_list_navigation'      => __( 'Navigation', Config::$domain_name ),
	);

	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'block_ui'                    => true,
		'block_admin_column'          => true,
		'block_in_nav_menus'          => true,
		'block_tagcloud'              => true,
		'capabilities' 				 => array (
								            'manage_terms' => 'manage_categories', //by default only admin
								            'edit_terms' => 'manage_blocks_tax',
								            'delete_terms' => 'manage_blocks_tax',
								            'assign_terms' => 'edit_blocks_tax'
								        ),
	);
	register_taxonomy( 'blocks_tax', array( 'blocks' ), $args );
}

// add_action( 'wp_loaded', 'blocks_categories', 0 );

# Adding meta boxes

/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function basetheme_register_blocks_metabox() {
	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$block_meta = new_cmb2_box( array(
		'id'            => Config::$prefix . 'metabox',
		'title'         => esc_html__( 'Block fields generator', Config::$domain_name ),
		'object_types'  => array( 'blocks' ), // Post type
		// 'block_on_cb' => 'yourprefix_block_if_front_page', // function should return a bool value
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Block field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
		// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
	) );

	$block_meta->add_field( array(
		'name'       => esc_html__( 'Add block fields', Config::$domain_name ),
		'desc'       => esc_html__( 'Fields block', Config::$domain_name ),
		'id'         => Config::$prefix . 'repeatable_group',
		'type'       => 'repeatable_group',
		'repeatable'      => false,
		'column'          => false, // Display field value in the admin post-listing columns
		'attributes' => array(
			'required'            => true, // Will be required only if visible.
		)
	) );

}

add_action( 'cmb2_admin_init', 'basetheme_register_blocks_metabox' );