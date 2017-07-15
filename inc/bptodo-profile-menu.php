<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

//Class to add admin menu to manage general settings
if (!class_exists('BP_Profile_Todo')) {
	class BP_Profile_Todo {

		//constructor
		function __construct() {
			add_action( 'bp_setup_nav', array( $this, 'bptodo_member_profile_todo_tab' ) );
		}

		//Actions performed on loading init: creating profile menu tab
		function bptodo_member_profile_todo_tab() {
			if (bp_is_my_profile()) {
				global $bp, $bptodo;
				
				$profile_menu_label = $bptodo->profile_menu_label;
				$profile_menu_label_plural = $bptodo->profile_menu_label_plural;
				$profile_menu_slug = $bptodo->profile_menu_slug;
				$my_todo_items = $bptodo->my_todo_items;

				$name = bp_get_displayed_user_username();
				$tab_args = array(
					'name' => __( $profile_menu_label.' <span class="count">'.$my_todo_items.'</span>', BPTODO_TEXT_DOMAIN ),
					'slug' => $profile_menu_slug,
					'screen_function' => array($this, 'todo_tab_function_to_show_screen'),
					'position' => 75,
					'default_subnav_slug' => 'list',
					'show_for_displayed_user' => true,
				);
				bp_core_new_nav_item($tab_args);

				$parent_slug = $profile_menu_slug;

				//Add subnav add new todo item
				bp_core_new_subnav_item(
					array(
						'name' => __( 'Add', BPTODO_TEXT_DOMAIN ),
						'slug' => 'add',
						'parent_url' => $bp->loggedin_user->domain.$parent_slug.'/',
						'parent_slug' => $parent_slug,
						'screen_function' => array($this, 'bptodo_add_todo_show_screen'),
						'position' => 200,
						'link' => site_url()."/members/$name/$parent_slug/add/",
					)
				);

				//Add subnav todo list items
				bp_core_new_subnav_item(
					array(
						'name' => __( $profile_menu_label_plural, BPTODO_TEXT_DOMAIN ),
						'slug' => 'list',
						'parent_url' => $bp->loggedin_user->domain.$parent_slug.'/',
						'parent_slug' => $parent_slug,
						'screen_function' => array($this, 'bpchk_todo_list_show_screen'),
						'position' => 100,
						'link' => site_url()."/members/$name/$parent_slug/list/",
					)
				);
			}
		}

		//Screen function for add todo menu item
		function bptodo_add_todo_show_screen() {
			add_action('bp_template_title', array($this, 'add_todo_tab_function_to_show_title'));
			add_action('bp_template_content', array($this, 'add_todo_tab_function_to_show_content'));
			bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
		}

		function add_todo_tab_function_to_show_title() {
			global $bptodo;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			if (isset($_GET['args'])) {
				$todo_id = sanitize_text_field( $_GET['args'] );
				$todo = get_post( $todo_id );
				echo __( 'Edit '.$profile_menu_slug.': '.$todo->post_title, BPTODO_TEXT_DOMAIN );
			} else {
				echo __( 'Add a new '.$profile_menu_slug.' in your list', BPTODO_TEXT_DOMAIN );
			}
		}

		function add_todo_tab_function_to_show_content() {
			if (isset($_GET['args'])) {
				include 'todo/edit.php';
			} else {
				include 'todo/add.php';
			}
		}

		//Screen function for todo list menu item
		function bpchk_todo_list_show_screen() {
			add_action('bp_template_title', array($this, 'list_todo_tab_function_to_show_title'));
			add_action('bp_template_content', array($this, 'list_todo_tab_function_to_show_content'));
			bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
		}

		function list_todo_tab_function_to_show_title() {
			global $bptodo;
			$profile_menu_label_plural = $bptodo->profile_menu_label_plural;
			echo __( $profile_menu_label_plural.' List', BPTODO_TEXT_DOMAIN );
			$args = array(
				'post_type' => 'bp-todo',
				'author'    => bp_displayed_user_id(),
				'post_staus'=> 'publish',
				'posts_per_page' => -1
			);
			$todos = get_posts($args);
			if( count( $todos ) != 0 ) {
				?>
				<a href="javascript:void(0);" id="export_my_tasks"><i class="fa fa-download" aria-hidden="true"></i></a>
				<?php
			}
		}

		function list_todo_tab_function_to_show_content() {
			include 'todo/list.php';
		}
	}
	new BP_Profile_Todo();
}