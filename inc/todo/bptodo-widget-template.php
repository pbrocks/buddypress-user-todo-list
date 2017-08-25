<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<section id="bptodo-todo-calendar" class="widget">
	<h2 class="widget-title"><?php echo $show_title;?></h2>
	<div id="bptodo-calender">
		<?php
		//List of Todo Items
		$args = array(
			'post_type' => 'bp-todo',
			'post_status' => 'publish',
			'author' => get_current_user_id(),
			'posts_per_page' => -1,
		);
		$result = new WP_Query($args);
		$todos = $result->posts;
		if (!empty($todos)) {
			$str = '';
			$i = 0;
			foreach ($todos as $todo) {
				$due_date = get_post_meta($todo->ID, 'todo_due_date', true);
				$date = date_create($due_date);
				$str .= '"' . date_format($date, "Y/n/j") . '": jQuery("<div>' . esc_attr( $todo->post_title ) . '</div>")';
				if ($i != count( $todos ) - 1) {
					$str .= ',';
				}
			}
			$i++;
		}
		?>
		<div id="bptodo-tempust"></div>
		<div id="bptodo-output"></div>
		<script>
			jQuery("#bptodo-tempust").tempust({
				date: new Date(),
				offset: 1,
				events: { <?php echo $str;?> }
			});
		</script>
	</div>
</section>