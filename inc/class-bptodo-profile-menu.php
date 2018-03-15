<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-user-todo-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bptodo_Profile_Menu' ) ) {
	/**
	 * Class to add admin menu to manage general settings.
	 *
	 * @package bp-user-todo-list
	 * @author  wbcomdesigns
	 * @since   1.0.0
	 */
	class Bptodo_Profile_Menu {

		/**
		 * Define hook.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {
			add_action( 'bp_setup_nav', array( $this, 'bptodo_member_profile_todo_tab' ) );
		}

		/**
		 * Actions performed on loading init: creating profile menu tab.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_member_profile_todo_tab() {
			if ( bp_is_my_profile() ) {
				global $bp, $bptodo;

				$profile_menu_label        = $bptodo->profile_menu_label;
				$profile_menu_label_plural = $bptodo->profile_menu_label_plural;
				$profile_menu_slug         = $bptodo->profile_menu_slug;
				$my_todo_items             = $bptodo->my_todo_items;

				$displayed_uid  = bp_displayed_user_id();
				$parent_slug    = $profile_menu_slug;
				$todo_menu_link = bp_core_get_userlink( $displayed_uid, false, true ) . $parent_slug;

				$name     = bp_get_displayed_user_username();
				$tab_args = array(
					'name'                    => __( $profile_menu_label . ' <span class="count">' . $my_todo_items . '</span>', 'wb-todo' ),
					'slug'                    => $profile_menu_slug,
					'screen_function'         => array( $this, 'todo_tab_function_to_show_screen' ),
					'position'                => 75,
					'default_subnav_slug'     => 'list',
					'show_for_displayed_user' => true,
				);
				bp_core_new_nav_item( $tab_args );

				/** Add subnav add new todo item. */
				bp_core_new_subnav_item(
					array(
						'name'            => __( 'Add', 'wb-todo' ),
						'slug'            => 'add',
						'parent_url'      => $todo_menu_link . '/',
						'parent_slug'     => $parent_slug,
						'screen_function' => array( $this, 'bptodo_add_todo_show_screen' ),
						'position'        => 200,
						'link'            => $todo_menu_link . '/add',
					)
				);

				/** Add subnav todo list items. */
				bp_core_new_subnav_item(
					array(
						'name'            => __( $profile_menu_label_plural, 'wb-todo' ),
						'slug'            => 'list',
						'parent_url'      => $todo_menu_link . '/',
						'parent_slug'     => $parent_slug,
						'screen_function' => array( $this, 'bpchk_todo_list_show_screen' ),
						'position'        => 100,
						'link'            => $todo_menu_link . '/list',
					)
				);
			}
		}

		/**
		 * Screen function for add todo menu item.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_add_todo_show_screen() {
			add_action( 'bp_template_title', array( $this, 'add_todo_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'add_todo_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Screen function for add todo menu item.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function add_todo_tab_function_to_show_title() {
			global $bptodo;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			if ( isset( $_GET['args'] ) ) {
				$todo_id = sanitize_text_field( wp_unslash( $_GET['args'] ) );
				$todo    = get_post( $todo_id );
				printf( esc_html__( ' Edit %1s : %2s ', 'wb-todo' ), $profile_menu_slug, $todo->post_title );
			} else {
				printf( esc_html__( ' Add a new %s in your list ', 'wb-todo' ), $profile_menu_slug );
			}
		}

		/**
		 * Screen function for add todo menu item.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function add_todo_tab_function_to_show_content() {
			if ( isset( $_GET['args'] ) ) {
				include 'todo/edit.php';
			} else {
				include 'todo/add.php';
			}
		}

		/**
		 * Screen function for todo list menu item.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bpchk_todo_list_show_screen() {
			add_action( 'bp_template_title', array( $this, 'list_todo_tab_function_to_show_title' ) );
			add_action( 'bp_template_content', array( $this, 'list_todo_tab_function_to_show_content' ) );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}

		/**
		 * Screen function for todo list title.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function list_todo_tab_function_to_show_title() {
			global $bptodo;
			$profile_menu_label_plural = $bptodo->profile_menu_label_plural;
			echo '<h4>';
			echo esc_html( $profile_menu_label_plural, 'wb-todo' );
			esc_html_e( ' List', 'wp-todo' );
			$args  = array(
				'post_type'      => 'bp-todo',
				'author'         => bp_displayed_user_id(),
				'post_staus'     => 'publish',
				'posts_per_page' => -1,
			);
			$todos = get_posts( $args );
			if ( 0 !== count( $todos ) ) {
				?>
				<?php $todo_export_nonce = wp_create_nonce( 'bptodo-export-todo' ); ?>
				<input type="hidden" id="bptodo-export-todo-nonce" value="<?php echo esc_html( $todo_export_nonce ); ?>">
				<a href="javascript:void(0);" id="export_my_tasks"><i class="fa fa-download" aria-hidden="true"></i></a>
				<?php
			}
			echo '</h4>';
		}

		/**
		 * Screen function for todo list content.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function list_todo_tab_function_to_show_content() {
			include 'todo/list.php';
		}
	}
	new Bptodo_Profile_Menu();
}
