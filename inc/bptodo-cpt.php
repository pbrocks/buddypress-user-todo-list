<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

//Class to add admin menu to manage general settings
if (!class_exists('BP_Todo_CPT')) {
	class BP_Todo_CPT {

		//constructor
		function __construct() {
			add_action('init', array($this, 'bptodo_create_cpt'));
			add_action('init', array($this, 'bptodo_create_cpt_category'));
		}

		//Actions performed on loading init: creating cpt
		function bptodo_create_cpt() {
			$labels = array(
				'name' => 'Todo Items',
				'singular_name' => 'Todo Item',
				'menu_name' => 'Todo Items',
				'name_admin_bar' => 'Todo Items',
				'view_item' => 'View Todo Item',
				'all_items' => 'All Todo Items',
				'search_items' => 'Search Todo Item',
				'parent_item_colon' => 'Parent Todo Item:',
				'not_found' => 'No Todo Item Found',
				'not_found_in_trash' => 'No Todo Item Found In Trash',
			);
			$icon_url = BPTODO_PLUGIN_URL.'admin/assets/images/todo-list.png';
			$args = array(
				'labels' => $labels,
				'public' => true,
				'menu_icon' => $icon_url,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array('slug' => 'todo', 'with_front' => false),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title', 'editor', 'author', 'thumbnail'),
			);
			register_post_type('bp-todo', $args);
		}

		//Actions performed on loading init: creating cpt category
		function bptodo_create_cpt_category() {
			$tax_labels = array(
				'name' => 'Category',
				'singular_name' => 'Todo Item Category',
				'search_items' => 'Search Todo Items Categories',
				'all_items' => 'All Todo Items Categories',
				'parent_item' => 'Parent Todo Item Category',
				'parent_item_colon' => 'Parent Todo Item Category:',
				'edit_item' => 'Edit Category',
				'update_item' => 'Update Category',
				'add_new_item' => 'Add Todo Item Category',
				'not_found' => 'No Todo Item Categories Found',
				'menu_name' => 'Todo Item Category',
				
			);
			$tax_args = array(
				'hierarchical' => true,
				'labels' => $tax_labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'public' => true,
				'query_var' => true,
				'rewrite' => array('slug' => 'todo_category'),
			);
			register_taxonomy('todo_category', array('bp-todo'), $tax_args);
		}
	}
	new BP_Todo_CPT();
}