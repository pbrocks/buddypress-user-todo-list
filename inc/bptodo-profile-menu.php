<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

//Class to add admin menu to manage general settings
if( !class_exists( 'BP_Profile_Todo' ) ) {
	class BP_Profile_Todo{

		//constructor
		function __construct() {
			add_action( 'bp_setup_nav', array( $this, 'bptodo_member_profile_todo_tab' ) );
		}

		//Actions performed on loading init: creating profile menu tab
		function bptodo_member_profile_todo_tab() {
			global $bp;

			if( bp_is_my_profile() ) {
				$name = bp_get_displayed_user_username();
				$tab_args = array(
					'name' => 'Todo',
					'slug' => 'todo',
					'screen_function' => array( $this, 'todo_tab_function_to_show_screen' ),
					'position' => 75,
					'default_subnav_slug' => 'todo_sub',
					'show_for_displayed_user' => true,
				);
				bp_core_new_nav_item( $tab_args );

				$parent_slug = 'todo';

				//Add subnav add new todo item
				bp_core_new_subnav_item(
					array(
						'name' => 'Add',
						'slug' => 'add',
						'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
						'parent_slug' => $parent_slug,
						'screen_function' => array( $this, 'bptodo_add_todo_show_screen' ),
						'position' => 100,
						'link' => site_url()."/members/$name/$parent_slug/add/",
					)
				);

				//Add subnav todo list items
				bp_core_new_subnav_item(
					array(
						'name' => 'Todo List',
						'slug' => 'list',
						'parent_url' => $bp->loggedin_user->domain . $parent_slug.'/',
						'parent_slug' => $parent_slug,
						'screen_function' => array( $this, 'bpchk_todo_list_show_screen' ),
						'position' => 100,
						'link' => site_url()."/members/$name/$parent_slug/list/",
					)
				);
			}
		}

		//Screen function for todo menu item
		function todo_tab_function_to_show_screen() {
			$name = bp_get_displayed_user_username();
			$myTodoURL = home_url().'/members/'.$name.'/todo/add';
			header( 'Location: '.$myTodoURL );
		}

		//Screen function for add todo menu item
		function bptodo_add_todo_show_screen() {
			add_action( 'bp_template_title', array( $this, 'add_todo_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'add_todo_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		function add_todo_tab_function_to_show_title(){
			if( isset( $_GET['args'] ) ) {
				echo 'Edit Your Todo Item';
			} else {
				echo 'Add Your Todo Item';
			}
		}

		function add_todo_tab_function_to_show_content(){
			if( isset( $_GET['args'] ) ) {
				include 'todo/edit.php';
			} else {
				include 'todo/add.php';
			}
		}

		//Screen function for todo list menu item
		function bpchk_todo_list_show_screen() {
			add_action( 'bp_template_title', array( $this, 'list_todo_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'list_todo_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		function list_todo_tab_function_to_show_title(){
			echo 'List Of Todo Items';
			?>
			<input type="submit" value="Export My Tasks" id="export_my_tasks">
			<?php
		}

		function list_todo_tab_function_to_show_content(){
			include 'todo/list.php';
		}
	}
	new BP_Profile_Todo();
}