<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

$name = bp_get_displayed_user_username();

//Delete a todo item
if (isset($_POST['delete'])) {
	$todo_id = sanitize_text_field( $_POST['todo_id'] );
	wp_delete_post($todo_id, true);
	$str = '';
	$str .= '<div id="message" class="updated">';
	$str .= '<p>'.__( 'Todo item deleted successfully.', 'wb-todo' ).'</p>';
	$str .= '</div>';
	echo $str;
}

$class = "todo-completed";
//Mark complete a todo item
if (isset($_POST['complete'])) {
	$todo_id = sanitize_text_field( $_POST['todo_id'] );
	update_post_meta($todo_id, 'todo_status', 'complete');
	$str = '';
	$str .= '<div id="message" class="updated">';
	$str .= '<p>'.__( 'Todo item marked completed successfully.', 'wb-todo' ).'</p>';
	$str .= '</div>';
	echo $str;
}

//Edit a todo item
if (isset($_POST['edit'])) {
	$todo_id = sanitize_text_field( $_POST['todo_id'] );
	$url = home_url('/members/'.$name.'/todo/add?args='.$todo_id);
	?>
	<script>
		var url = '<?php echo $url; ?>';
		window.location.href = url;
	</script>
	<?php
}

//List of Todo Items
$args = array(
	'post_type' => 'bp-todo',
	'post_status' => 'publish',
	'author' => get_current_user_id(),
	'posts_per_page' => -1,
);
$result = new WP_Query($args);
$todos = $result->posts;
?>

<?php if (empty($todos)) {?>
<div id="message" class="info">
	<p><?php _e( 'Sorry, No todo item found.', 'wb-todo' ); ?></p>
</div>
<?php } else {?>
<table class="list-todo-block">
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="25%">Task</th>
			<th width="30%">Due Date</th>
			<th width="40%">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php $count = 1; ?>
		<?php foreach ($todos as $todo) {?>
			<?php
			$todo_status = get_post_meta($todo->ID, 'todo_status', true);

			$due_date_str = $due_date_td_class = '';
			$curr_date = date_create(date('Y-m-d'));
			$due_date = date_create(get_post_meta($todo->ID, 'todo_due_date', true));
			$diff = date_diff($curr_date, $due_date);
			$diff_days = $diff->format("%R%a");
			if ($diff_days < 0) {
				$due_date_str = 'Expired '.abs($diff_days).' days ago!';
				$due_date_td_class = 'bptodo-expired';
			} else if ($diff_days == 0) {

				//Send mail
				if (get_post_meta($todo->ID, 'todo_last_day_mail_sent', true) === 'no') {
					$author_id = $todo->post_author;
					$author = get_userdata($author_id);
					$author_email = $author->data->user_email;
					$subject = 'BP Task - Wordpress';
					$messsage = 'Your task: '.$todo->post_title.' is going to exipre tomorrow. Kindly finish it up! Thanks!';
					wp_mail($author_email, $subject, $messsage);
					update_post_meta($todo->ID, 'todo_last_day_mail_sent', 'yes');
				}
				$due_date_str = 'Today is the last day to complete. Hurry Up!';
				$due_date_td_class = 'bptodo-expires-today';
			} else {
				$due_date_str = abs($diff_days).' days left to complete the task!';
			}

			if( $todo_status == 'complete' ) {
				$due_date_str = 'Completed!';
				$due_date_td_class = '';
			}
			?>

			<tr>
				<td width="5%" class="<?php if ($todo_status == 'complete') echo $class; ?>">
					<?php echo $count++; ?>
				</td>
				<td width="25%" class="<?php if ($todo_status == 'complete') echo $class; ?>">
					<?php echo $todo->post_title; ?>
				</td>
				<td width="30%" class="<?php echo $due_date_td_class;?>"><?php echo $due_date_str;?></td>
				<td width="40%">
					<form action="" method="post">
						<input type="hidden" name="todo_id" value="<?php echo $todo->ID; ?>">
						<input type="submit" name="delete" value="Delete">
						<?php if ($todo_status !== 'complete') {?>
							<input type="submit" name="edit" value="Edit">
							<input type="submit" name="complete" value="Mark Complete">
						<?php }?>
					</form>
				</td>
			</tr>
		<?php }?>
	</tbody>
</table>
<?php }?>