<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Class to serve AJAX Calls
if ( ! class_exists( 'BPTodoAjax' ) ) {
	class BPTodoAjax {

		// Constructor
		function __construct() {
			// Export My Tasks
			add_action( 'wp_ajax_bptodo_export_my_tasks', array( $this, 'bptodo_export_my_tasks' ) );

			// Remove a task
			add_action( 'wp_ajax_bptodo_remove_todo', array( $this, 'bptodo_remove_todo' ) );

			// Complete a task
			add_action( 'wp_ajax_bptodo_complete_todo', array( $this, 'bptodo_complete_todo' ) );

			// Undo complete a task
			add_action( 'wp_ajax_bptodo_undo_complete_todo', array( $this, 'bptodo_undo_complete_todo' ) );

			// Add BP Todo Category
			add_action( 'wp_ajax_bptodo_add_todo_category_front', array( $this, 'bptodo_add_todo_category_front' ) );
		}

		// Actions Performed To Export My Tasks
		function bptodo_export_my_tasks() {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'bptodo_export_my_tasks' ) {
				check_ajax_referer( 'bptodo-export-todo', 'security_nonce' );
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
				echo json_encode( $tasks );
				die;
			}
		}

		// Actions performed to delete a todo
		function bptodo_remove_todo() {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'bptodo_remove_todo' ) {
				$tid = sanitize_text_field( $_POST['tid'] );
				wp_delete_post( $tid, true );
				echo 'todo-removed';
				die;
			}
		}

		// Actions performed to complete a todo
		function bptodo_complete_todo() {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'bptodo_complete_todo' ) {
				$tid = sanitize_text_field( $_POST['tid'] );
				update_post_meta( $tid, 'todo_status', 'complete' );
				$completed_todos = sanitize_text_field( $_POST['completed'] );
				$all_todo        = sanitize_text_field( $_POST['all_todo'] );
				(int) $completed_todos ++;
				$avg_percentage = ( $completed_todos * 100 ) / $all_todo;

				/*** Add html of completed todo */

				$todo          = get_post( $tid );
				$todo_title    = $todo->post_title;
				$todo_edit_url = bp_core_get_userlink( bp_displayed_user_id(), false, true ) . $profile_menu_slug . '/add?args=' . $tid;

				$todo_status   = get_post_meta( $todo->ID, 'todo_status', true );
				$todo_priority = get_post_meta( $todo->ID, 'todo_priority', true );
				$due_date_str  = $due_date_td_class    = '';
				$curr_date     = date_create( date( 'Y-m-d' ) );
				$due_date      = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
				$diff          = date_diff( $curr_date, $due_date );
				$diff_days     = $diff->format( '%R%a' );
				if ( $diff_days < 0 ) {
					$due_date_str      = 'Expired ' . abs( $diff_days ) . ' days ago!';
					$due_date_td_class = 'bptodo-expired';
				} elseif ( $diff_days == 0 ) {
					$due_date_str      = 'Today is the last day to complete. Hurry Up!';
					$due_date_td_class = 'bptodo-expires-today';
					$all_remaining_todo++;
				} else {
					$due_date_str = abs( $diff_days ) . ' days left to complete the task!';
					$all_remaining_todo++;
				}
				if ( $todo_status == 'complete' ) {
					$due_date_str      = 'Completed!';
					$due_date_td_class = '';
					$all_completed_todo++;
				}
				if ( ! empty( $todo_priority ) ) {
					if ( $todo_priority == 'critical' ) {
						$priority_class = 'bptodo-priority-critical';
					} elseif ( $todo_priority == 'high' ) {
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
				<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="' . $tid . '" title="' . __( 'Remove: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-times"></i></a></li>';
				if ( $todo_status !== 'complete' ) {

					$completed_html .= '<li><a href="' . $todo_edit_url . '" title="' . __( 'Edit: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-edit"></i></a></li>
					<li id="bptodo-complete-li-' . $tid . '"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="' . $tid . '" title="' . __( 'Complete: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-check"></i></a></li>';
				} else {
					$completed_html .= '<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="' . $tid . '" title="' . __( 'Undo Complete: ' . $todo_title, 'wb-todo' ) . '"><i class="fa fa-undo"></i></a></li>';
				}
				$completed_html .= '</ul></td></tr>';
				/*** End of html of completed todo */

				$response = array(
					'result'         => 'todo-completed',
					'completed_todo' => $completed_todos,
					'completed_html' => $completed_html,
					'avg_percentage' => $avg_percentage,
				);
				echo json_encode( $response );
				die;
			}
		}

		// Actions performed to undo complete a todo
		function bptodo_undo_complete_todo() {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'bptodo_undo_complete_todo' ) {
				$tid = sanitize_text_field( $_POST['tid'] );
				update_post_meta( $tid, 'todo_status', 'incomplete' );
				echo 'todo-undo-completed';
				die;
			}
		}

		// Actions Performed To Add BP Todo Category
		function bptodo_add_todo_category_front() {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'bptodo_add_todo_category_front' ) {
				check_ajax_referer( 'bptodo-add-todo-category', 'security_nonce' );
				$term       = sanitize_text_field( $_POST['name'] );
				$taxonomy   = 'todo_category';
				$termExists = term_exists( $term, $taxonomy );
				if ( $termExists === 0 || $termExists === null ) {
					wp_insert_term( $term, $taxonomy );
				}
				echo 'todo-category-added';
				die;
			}
		}
	}
	new BPTodoAjax();
}
