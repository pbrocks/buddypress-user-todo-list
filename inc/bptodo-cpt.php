<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Class to add admin menu to manage general settings
if ( ! class_exists( 'BP_Todo_CPT' ) ) {

	class BP_Todo_CPT {

		// constructor
		function __construct() {
			add_action( 'init', array( $this, 'bptodo_create_cpt' ) );
			add_action( 'init', array( $this, 'bptodo_create_cpt_category' ) );
		}

		// Actions performed on loading init: creating cpt
		function bptodo_create_cpt() {
			$labels = array(
				'name'               => __( 'To-Do Items', 'wb-todo' ),
				'singular_name'      => __( 'To-Do Item', 'wb-todo' ),
				'menu_name'          => __( 'To-Do Items', 'wb-todo' ),
				'name_admin_bar'     => __( 'To-Do Items', 'wb-todo' ),
				'view_item'          => __( 'View To-Do Item', 'wb-todo' ),
				'all_items'          => __( 'All To-Do Items', 'wb-todo' ),
				'search_items'       => __( 'Search To-Do Item', 'wb-todo' ),
				'parent_item_colon'  => __( 'Parent To-Do Item:', 'wb-todo' ),
				'not_found'          => __( 'No To-Do Item Found', 'wb-todo' ),
				'not_found_in_trash' => __( 'No To-Do Item Found In Trash', 'wb-todo' ),
			);

			$args = array(
				'labels'             => $labels,
				'public'             => true,
				'menu_icon'          => 'dashicons-edit',
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array(
					'slug'       => 'todo',
					'with_front' => false,
				),
				'capability_type'    => 'post',
				'capabilities'       => array(
					'create_posts' => false,
					'delete_posts' => false,
					'edit_post'    => false,
				),
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
			);
			register_post_type( 'bp-todo', $args );
			flush_rewrite_rules( false );
		}

		// Actions performed on loading init: creating cpt category
		function bptodo_create_cpt_category() {
			$tax_labels = array(
				'name'              => __( 'To-Do Category', 'wb-todo' ),
				'singular_name'     => __( 'To-Do Category', 'wb-todo' ),
				'search_items'      => __( 'Search To-Do Items Categories', 'wb-todo' ),
				'all_items'         => __( 'All To-Do Items Categories', 'wb-todo' ),
				'parent_item'       => __( 'Parent To-Do Item Category', 'wb-todo' ),
				'parent_item_colon' => __( 'Parent To-Do Item Category:', 'wb-todo' ),
				'edit_item'         => __( 'Edit Category', 'wb-todo' ),
				'update_item'       => __( 'Update Category', 'wb-todo' ),
				'add_new_item'      => __( 'Add To-Do Item Category', 'wb-todo' ),
				'not_found'         => __( 'No To-Do Item Categories Found', 'wb-todo' ),
				'menu_name'         => __( 'Categories', 'wb-todo' ),
			);
			$tax_args   = array(
				'hierarchical'      => true,
				'labels'            => $tax_labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'public'            => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'todo_category' ),
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
