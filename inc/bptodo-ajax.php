<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

//Class to serve AJAX Calls
if (!class_exists('BPTodoAjax')) {
	class BPTodoAjax {

		//Constructor
		function __construct() {
			//Export My Tasks
			add_action('wp_ajax_bptodo_export_my_tasks', array($this, 'bptodo_export_my_tasks'));
			add_action('wp_ajax_nopriv_bptodo_export_my_tasks', array($this, 'bptodo_export_my_tasks'));

			//Add BP Todo Category
			add_action('wp_ajax_bptodo_add_todo_category_front', array($this, 'bptodo_add_todo_category_front'));
			add_action('wp_ajax_nopriv_bptodo_add_todo_category_front', array($this, 'bptodo_add_todo_category_front'));
		}

		//Actions Performed To Export My Tasks
		function bptodo_export_my_tasks() {
			if (isset($_POST['action']) && $_POST['action'] === 'bptodo_export_my_tasks') {
				$args = array(
					'post_type' => 'bp-todo',
					'post_status' => 'publish',
					'author' => get_current_user_id(),
					'posts_per_page' => -1,
				);
				$result = new WP_Query($args);
				$todos = $result->posts;
				$tasks = array();
				if (!empty($todos)) {
					foreach ($todos as $key => $todo) {
						$temp = array();
						$temp['Task ID'] = $todo->ID;
						$temp['Task Title'] = $todo->post_title;
						$temp['Task Summary'] = $todo->post_content;
						$temp['Task Due Date'] = get_post_meta($todo->ID, 'todo_due_date', true);
						$temp['Task Status'] = get_post_meta($todo->ID, 'todo_status', true);
						$tasks[$key] = $temp;
					}
				}
				echo json_encode($tasks);
				die;
			}
		}

		//Actions Performed To Add BP Todo Category
		function bptodo_add_todo_category_front() {
			if (isset($_POST['action']) && $_POST['action'] === 'bptodo_add_todo_category_front') {
				$term = $_POST['name'];
				$taxonomy = 'todo_category';
				$termExists = term_exists($term, $taxonomy);
				if ($termExists === 0 || $termExists === null) {
					wp_insert_term($term, $taxonomy);
				}
				echo 'todo-category-added';
				die;
			}
		}
	}
	new BPTodoAjax();
}