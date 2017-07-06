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
			add_action( 'init', array( $this, 'bptodo_manage_todo_due_date' ) );
			add_action( 'bp_member_header_actions', array( $this, 'bptodo_add_review_button_on_member_header' ) );
			add_action( 'bp_setup_admin_bar', array( $this, 'bptodo_setup_admin_bar' ), 80 );
			add_filter( 'manage_bp-todo_posts_columns', array( $this, 'bptodo_due_date_column_heading' ), 10);
			add_action( 'manage_bp-todo_posts_custom_column', array( $this, 'bptodo_due_date_column_content' ), 10, 2);

			add_filter( 'bp_notifications_get_registered_components', array( $this, 'bptodo_due_date_notifications_component' ) );
			add_filter( 'bp_notifications_get_notifications_for_user', array( $this, 'bptodo_format_due_date_notifications' ), 10, 5 );
		}

		/**
		 * Actions performed to send mails and notifications
		 * whose due date has arrived
		 */
		public function bptodo_manage_todo_due_date() {
			global $bptodo;
			$args = array(
				'post_type' => 'bp-todo',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'order_by' => 'name',
				'order' => 'ASC'
			);
			$todo_items = get_posts( $args );
			if( !empty( $todo_items ) ) {
				foreach ( $todo_items as $key => $todo ) {
					$author_id = $todo->post_author;
					$curr_date = date_create(date('Y-m-d'));
					$due_date = date_create(get_post_meta($todo->ID, 'todo_due_date', true));
					$diff = date_diff($curr_date, $due_date);
					$diff_days = $diff->format("%R%a");

					//Check if mail sending is allowed
					if( !empty( $bptodo->send_mail ) && $bptodo->send_mail == 'yes' ) {
						//If today is the due date
						if( $diff_days == 0 ) {
							//If the mail is not sent already
							$due_date_mail_sent = get_post_meta($todo->ID, 'todo_last_day_mail_sent', true);
							if ( $due_date_mail_sent == 'no' ) {
								$author = get_userdata($author_id);
								$author_email = $author->data->user_email;
								$subject = 'BP Task - Wordpress';
								$messsage = 'Your task: '.$todo->post_title.' is going to exipre today. Kindly finish it up! Thanks!';
								wp_mail($author_email, $subject, $messsage);
								update_post_meta($todo->ID, 'todo_last_day_mail_sent', 'yes');
							}
						}
					}// end if sending mail is allowed

					//Check if notification sending is allowed
					if( !empty( $bptodo->send_notification ) && $bptodo->send_notification == 'yes' ) {
						//If today is the due date
						if( $diff_days == 0 ) {
							//If the mail is not sent already
							$due_date_notification_sent = get_post_meta($todo->ID, 'todo_last_day_notification_sent', true);
							if ( $due_date_notification_sent == 'no' ) {

								//Send notification for appectance
								bp_notifications_add_notification( array(
									'user_id'           => $author_id,
									'item_id'           => $todo->ID,
									'secondary_item_id' => get_current_user_id(),
									'component_name'    => 'bptodo_due_date',
									'component_action'  => 'bptodo_due_date_action',
									'date_notified'     => bp_core_current_time(),
									'is_new'            => 1,
								) );
								update_post_meta($todo->ID, 'todo_last_day_notification_sent', 'yes');
							}
						}
					}// end if sending mail is allowed

				} // end foreach loop of todo list
			}
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
			$my_todo_items = $bptodo->my_todo_items;

			$base_url = bp_loggedin_user_domain().$profile_menu_slug;
			$todo_add_url = $base_url.'/add';
			$todo_list_url = $base_url.'/list';
			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu( array(
					'parent' => 'my-account-buddypress',
					'id' => 'my-account-'.$profile_menu_slug,
					'title' => __( $profile_menu_label_plural.' <span class="count">'.$my_todo_items.'</span>', BPTODO_TEXT_DOMAIN ),
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
			$defaults['todo_id'] = __( 'ID', BPTODO_TEXT_DOMAIN );
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

			if( $column_name == 'todo_id' ) {
				echo $post_id;
			}
		}

		/**
		 * Actions performed for adding component for due date of todo list
		 */
		function bptodo_due_date_notifications_component( $component_names = array() ) {
			if ( ! is_array( $component_names ) ) {
				$component_names = array();
			}
			array_push( $component_names, 'bptodo_due_date' );
			return $component_names;
		}

		/**
		 * Actions performed for formatting the notifications of bptodo due date
		 */
		function bptodo_format_due_date_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
			
			global $bptodo;
			$profile_menu_label = $bptodo->profile_menu_label;

			if ( 'bptodo_due_date_action' === $action ) {
				$todo = get_post( $item_id );
				$todo_title = $todo->post_title;
				$todo_link = get_permalink( $item_id );
				
				$custom_title = $profile_menu_label.' due date arrived for task: '.$todo_title;
				$custom_link  = $todo_link;
				$custom_text = 'Your task: '.$todo_title.' is due today. Please complete it as soon as possible.';

				// WP Toolbar
				if ( 'string' === $format ) {
					$return = apply_filters( 'bptodo_due_date_filter', '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>', $custom_text, $custom_link );
				// Deprecated BuddyBar
				} else {
					$return = apply_filters( 'bptodo_due_date_filter', array( 'text' => $custom_text, 'link' => $custom_link ), $custom_link, (int) $total_items, $custom_text, $custom_title );
				}

				return $return;
			}
		}
	}
	new Bptodo_Custom_Hooks();
}