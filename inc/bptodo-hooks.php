<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
* Class to add custom hooks for this plugin
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if( !class_exists( 'Bptodo_Custom_Hooks' ) ) {
	class Bptodo_Custom_Hooks{

		/**
		* Constructor.
		*
		* @since    1.0.0
		* @access   public
		* @author   Wbcom Designs
		*/
		public function __construct() {
			add_action( 'bp_member_header_actions', array( $this, 'bptodo_add_review_button_on_member_header' ) );
			add_action( 'bp_setup_admin_bar', array( $this, 'bptodo_setup_admin_bar' ), 80 );
			add_filter( 'manage_bp-todo_posts_columns', array( $this, 'bptodo_due_date_column_heading' ), 10);
			add_action( 'manage_bp-todo_posts_custom_column', array( $this, 'bptodo_due_date_column_content' ), 10, 2);
		}

		/**
		 * Actions performed to add a review button on member header
		 */
		public function bptodo_add_review_button_on_member_header() {
			global $bptodo;
			$profile_menu_label = $bptodo->profile_menu_label;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			if( bp_displayed_user_id() === bp_loggedin_user_id() ) {
				$todo_add_url = bp_core_get_userlink( bp_displayed_user_id(), false, true ).$profile_menu_slug.'/add';
				?>
				<div id="bptodo-add-todo-btn" class="generic-button">
					<a href="<?php echo $todo_add_url;?>" class="add-todo"><?php _e( 'Add '.$profile_menu_label, BPTODO_TEXT_DOMAIN );?></a>
				</div>
				<?php
			}
		}

		/**
		 *
		 */
		public function bptodo_setup_admin_bar( $wp_admin_nav = array() ) {
			global $wp_admin_bar, $bptodo;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			$profile_menu_label_plural = $bptodo->profile_menu_label_plural;

			$base_url = bp_loggedin_user_domain().$profile_menu_slug;
			$todo_add_url = $base_url.'/add';
			$todo_list_url = $base_url.'/list';
			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-buddypress',
					'id' => 'my-account-'.$profile_menu_slug,
					'title' => __( $profile_menu_label_plural, BPTODO_TEXT_DOMAIN ),
					'href' => trailingslashit( $todo_list_url )
				) );

				// Add add-new submenu
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-'.$profile_menu_slug,
					'id'     => 'my-account-'.$profile_menu_slug.'-'.'list',
					'title'  => __( 'List', BPTODO_TEXT_DOMAIN ),
					'href'   => trailingslashit( $todo_list_url )
				) );

				// Add add-new submenu
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-'.$profile_menu_slug,
					'id'     => 'my-account-'.$profile_menu_slug.'-'.'add',
					'title'  => __( 'Add', BPTODO_TEXT_DOMAIN ),
					'href'   => trailingslashit( $todo_add_url )
				) );
			}
		}

		public function bptodo_due_date_column_heading( $defaults ) {
			$defaults['due_date'] = __( 'Due Date', BPTODO_TEXT_DOMAIN );
			$defaults['status'] = __( 'Status', BPTODO_TEXT_DOMAIN );
			return $defaults;
		}

		public function bptodo_due_date_column_content( $column_name, $post_id ) {
			if( $column_name == 'due_date' ) {
				$due_date = get_post_meta( $post_id, 'todo_due_date', true );
				echo date("F jS, Y", strtotime($due_date));
			}

			if( $column_name == 'status' ) {
				$todo_status = get_post_meta($post_id, 'todo_status', true);

				$due_date_str = $due_date_td_class = '';
				$curr_date = date_create(date('Y-m-d'));
				$due_date = date_create(get_post_meta($post_id, 'todo_due_date', true));
				$diff = date_diff($curr_date, $due_date);
				$diff_days = $diff->format("%R%a");
				if ($diff_days < 0) {
					$due_date_str = 'Expired '.abs($diff_days).' days ago!';
				} else if ($diff_days == 0) {
					$due_date_str = 'Today is the last day to complete. Hurry Up!';
				} else {
					$due_date_str = abs($diff_days).' days left to complete the task!';
				}

				if( $todo_status == 'complete' ) {
					$due_date_str = 'Completed!';
				}

				echo $due_date_str;
			}
		}
	}
	new Bptodo_Custom_Hooks();
}