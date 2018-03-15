<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-user-todo-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Bptodo_Ajax' ) ) {
	/**
	 * Class to serve AJAX Calls.
	 *
	 * @package bp-user-todo-list
	 * @author  wbcomdesigns
	 * @since   1.0.0
	 */
	class Bptodo_Ajax {

		/**
		 * Define hook.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {
			/** Export My Tasks. */
			add_action( 'wp_ajax_bptodo_export_my_tasks', array( $this, 'bptodo_export_my_tasks' ) );

			/** Remove a task. */
			add_action( 'wp_ajax_bptodo_remove_todo', array( $this, 'bptodo_remove_todo' ) );

			/** Complete a task. */
			add_action( 'wp_ajax_bptodo_complete_todo', array( $this, 'bptodo_complete_todo' ) );

			/** Undo complete a task. */
			add_action( 'wp_ajax_bptodo_undo_complete_todo', array( $this, 'bptodo_undo_complete_todo' ) );

			/** Add BP Todo Category. */
			add_action( 'wp_ajax_bptodo_add_todo_category_front', array( $this, 'bptodo_add_todo_category_front' ) );
		}

		/**
		 * Actions Performed To Export My Tasks.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_export_my_tasks() {
			check_ajax_referer( 'bptodo-export-todo', 'security_nonce' );
			if ( isset( $_POST['action'] ) && 'bptodo_export_my_tasks' == $_POST['action'] ) {
				$args   = array(
					'post_type'      => 'bp-todo',
					'post_status'    => 'publish',
					'author'         => get_current_user_id(),
					'posts_per_page' => -1,
				);
				$result = new WP_Query( $args );
				$todos  = $result->posts;
				$tasks  = array();
				if ( ! empty( $todos ) ) {
					foreach ( $todos as $key => $todo ) {
						$temp                  = array();
						$temp['Task ID']       = $todo->ID;
						$temp['Task Title']    = $todo->post_title;
						$temp['Task Summary']  = $todo->post_content;
						$temp['Task Due Date'] = get_post_meta( $todo->ID, 'todo_due_date', true );
						$temp['Task Status']   = get_post_meta( $todo->ID, 'todo_status', true );
						$tasks[ $key ]         = $temp;
					}
				}
				echo wp_json_encode( $tasks );
				die;
			}
		}

		/**
		 * Actions performed to delete a todo.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_remove_todo() {
			check_ajax_referer( 'bptodo-todo-nonce', 'ajax_nonce' );
			if ( isset( $_POST['action'] ) && 'bptodo_remove_todo' == $_POST['action'] ) {
				if ( isset( $_POST['tid'] ) ) {
					$tid = sanitize_text_field( wp_unslash( $_POST['tid'] ) );
				}
				wp_delete_post( $tid, true );
				esc_html_e( 'todo-removed', 'wb-todo' );
				die;
			}
		}

		/**
		 * Actions performed to complete a todo.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_complete_todo() {
			check_ajax_referer( 'bptodo-todo-nonce', 'ajax_nonce' );
			if ( isset( $_POST['action'] ) && 'bptodo_complete_todo' == $_POST['action'] ) {
				$due_date_str      = '';
				$due_date_td_class = '';
				$tid = sanitize_text_field( wp_unslash( $_POST['tid'] ) );
				update_post_meta( $tid, 'todo_status', 'complete' );
				$completed_todos = sanitize_text_field( wp_unslash( $_POST['completed'] ) );
				$all_todo        = sanitize_text_field( wp_unslash( $_POST['all_todo'] ) );
				(int) $completed_todos ++;
				$avg_percentage = ( $completed_todos * 100 ) / $all_todo;

				/** Add html of completed todo. */
				$todo          = get_post( $tid );
				$todo_title    = $todo->post_title;
				$todo_edit_url = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

				$todo_status   = get_post_meta( $todo->ID, 'todo_status', true );
				$todo_priority = get_post_meta( $todo->ID, 'todo_priority', true );
				$curr_date     = date_create( date( 'Y-m-d' ) );
				$due_date      = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
				$diff          = date_diff( $curr_date, $due_date );
				$diff_days     = $diff->format( '%R%a' );
				if ( $diff_days < 0 ) {
					$due_date_str      = 'Expired ' . abs( $diff_days ) . ' days ago!';
					$due_date_td_class = 'bptodo-expired';
				} elseif ( 0 == $diff_days ) {
					$due_date_str      = 'Today is the last day to complete. Hurry Up!';
					$due_date_td_class = 'bptodo-expires-today';
					$all_remaining_todo++;
				} else {
					$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
					$all_remaining_todo++;
				}
				if ( 'complete' == $todo_status ) {
					$due_date_str      = 'Completed!';
					$due_date_td_class = '';
					$all_completed_todo++;
				}
				if ( ! empty( $todo_priority ) ) {
					if ( 'critical' == $todo_priority ) {
						$priority_class = 'bptodo-priority-critical';
					} elseif ( 'high' == $todo_priority ) {
						$priority_class = 'bptodo-priority-high';
					} else {
						$priority_class = 'bptodo-priority-normal';
					}
				}
				$completed_html  = '';
				$completed_html .= '<tr id="bptodo-row-' . $tid . '">
				<td class="bptodo-priority"><span class="' . $priority_class . '">' . $todo_priority . '</span></td>
				<td class="todo-completed">' . $todo_title . '</td>
				<td class="bp-to-do-actions">
				<ul>
				<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="' . esc_attr( $tid ) . '" title="' . esc_attr( 'Remove: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-times"></i></a></li>';
				if ( 'complete' !== $todo_status ) {

					$completed_html .= '<li><a href="' . esc_attr( $todo_edit_url ) . '" title="' . esc_attr( 'Edit: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-edit"></i></a></li>
					<li id="bptodo-complete-li-' . esc_attr( $tid ) . '"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="' . esc_attr( $tid ) . '" title="' . esc_attr( 'Complete: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-check"></i></a></li>';
				} else {
					$completed_html .= '<li><a href="" class="bptodo-undo-complete-todo" data-tid="' . $tid . '" title="' . esc_attr( 'Undo Complete: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-undo"></i></a></li>';
				}
				$completed_html .= '</ul></td></tr>';
				/** End of html of completed todo. */
				$response = array(
					'result'         => 'todo-completed',
					'completed_todo' => $completed_todos,
					'completed_html' => $completed_html,
					'avg_percentage' => $avg_percentage,
				);
				echo wp_json_encode( $response );
				die;
			}
		}

		/**
		 * Actions performed to undo complete a todo.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_undo_complete_todo() {
			check_ajax_referer( 'bptodo-todo-nonce', 'ajax_nonce' );
			if ( isset( $_POST['action'] ) && 'bptodo_undo_complete_todo' === $_POST['action'] ) {
				if ( isset( $_POST['tid'] ) ) {
					$tid = sanitize_text_field( $_POST['tid'] );
				}
				update_post_meta( $tid, 'todo_status', 'incomplete' );
				echo 'todo-undo-completed';
				die;
			}
		}

		/**
		 * Actions Performed To Add BP Todo Category.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_add_todo_category_front() {
			check_ajax_referer( 'bptodo-add-todo-category', 'security_nonce' );
			if ( isset( $_POST['action'] ) && 'bptodo_add_todo_category_front' === $_POST['action'] ) {
				if ( isset( $_POST['name'] ) ) {
					$term = sanitize_text_field( wp_unslash( $_POST['name'] ) );
				}
				$taxonomy    = 'todo_category';
				$term_exists = term_exists( $term, $taxonomy );
				if ( 0 === $term_exists || null === $term_exists ) {
					wp_insert_term( $term, $taxonomy );
				}
				echo 'todo-category-added';
				die;
			}
		}
	}
	new Bptodo_Ajax();
}
