<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

//Class to add admin menu to manage general settings
if ( !class_exists( 'BP_Todo_CPT' ) ) {

	class BP_Todo_CPT {

		//constructor
		function __construct() {
			add_action( 'init', array( $this, 'bptodo_create_cpt' ) );
			add_action( 'init', array( $this, 'bptodo_create_cpt_category' ) );
		}

		//Actions performed on loading init: creating cpt
		function bptodo_create_cpt() {
			$labels = array(
				'name'				 => __( 'Todo Items', BPTODO_TEXT_DOMAIN ),
				'singular_name'		 => __( 'Todo Item', BPTODO_TEXT_DOMAIN ),
				'menu_name'			 => __( 'Todo Items', BPTODO_TEXT_DOMAIN ),
				'name_admin_bar'	 => __( 'Todo Items', BPTODO_TEXT_DOMAIN ),
				'view_item'			 => __( 'View Todo Item', BPTODO_TEXT_DOMAIN ),
				'all_items'			 => __( 'All Todo Items', BPTODO_TEXT_DOMAIN ),
				'search_items'		 => __( 'Search Todo Item', BPTODO_TEXT_DOMAIN ),
				'parent_item_colon'	 => __( 'Parent Todo Item:', BPTODO_TEXT_DOMAIN ),
				'not_found'			 => __( 'No Todo Item Found', BPTODO_TEXT_DOMAIN ),
				'not_found_in_trash' => __( 'No Todo Item Found In Trash', BPTODO_TEXT_DOMAIN ),
			);

			$args = array(
				'labels'			 => $labels,
				'public'			 => true,
				'menu_icon'			 => 'dashicons-edit',
				'publicly_queryable' => true,
				'show_ui'			 => true,
				'show_in_menu'		 => true,
				'query_var'			 => true,
				'rewrite'			 => array( 'slug' => 'todo', 'with_front' => false ),
				'capability_type'	 => 'post',
				'capabilities'		 => array(
					'create_posts'	 => false,
					'delete_posts'	 => false,
					'edit_post'		 => false,
				),
				'has_archive'		 => true,
				'hierarchical'		 => false,
				'menu_position'		 => null,
				'supports'			 => array( 'title', 'editor', 'author', 'thumbnail' ),
			);
			register_post_type( 'bp-todo', $args );
			flush_rewrite_rules( FALSE );
		}

		//Actions performed on loading init: creating cpt category
		function bptodo_create_cpt_category() {
			$tax_labels	 = array(
				'name'				 => __( 'Todo Category', BPTODO_TEXT_DOMAIN ),
				'singular_name'		 => __( 'Todo Category', BPTODO_TEXT_DOMAIN ),
				'search_items'		 => __( 'Search Todo Items Categories', BPTODO_TEXT_DOMAIN ),
				'all_items'			 => __( 'All Todo Items Categories', BPTODO_TEXT_DOMAIN ),
				'parent_item'		 => __( 'Parent Todo Item Category', BPTODO_TEXT_DOMAIN ),
				'parent_item_colon'	 => __( 'Parent Todo Item Category:', BPTODO_TEXT_DOMAIN ),
				'edit_item'			 => __( 'Edit Category', BPTODO_TEXT_DOMAIN ),
				'update_item'		 => __( 'Update Category', BPTODO_TEXT_DOMAIN ),
				'add_new_item'		 => __( 'Add Todo Item Category', BPTODO_TEXT_DOMAIN ),
				'not_found'			 => __( 'No Todo Item Categories Found', BPTODO_TEXT_DOMAIN ),
				'menu_name'			 => __( 'Categories', BPTODO_TEXT_DOMAIN ),
			);
			$tax_args	 = array(
				'hierarchical'		 => true,
				'labels'			 => $tax_labels,
				'show_ui'			 => true,
				'show_admin_column'	 => true,
				'public'			 => true,
				'query_var'			 => true,
				'rewrite'			 => array( 'slug' => 'todo_category' ),
			);
			register_taxonomy( 'todo_category', array( 'bp-todo' ), $tax_args );

			$term = term_exists( 'Uncategorized', 'todo_category' );
			if ( empty( $term ) ) {
				wp_insert_term(
					'Uncategorized',
					'todo_category'
				);
			}
		}
	}

	new BP_Todo_CPT();
}