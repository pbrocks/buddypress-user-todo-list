<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

$name = bp_get_displayed_user_username();

//Delete a todo item
if (isset($_POST['delete'])) {
	$todo_id = $_POST['todo_id'];
	wp_delete_post($todo_id, true);
}

$class = "todo-completed";
//Mark complete a todo item
if (isset($_POST['complete'])) {
	$todo_id = $_POST['todo_id'];
	update_post_meta($todo_id, 'todo_status', 'complete');
}

//Edit a todo item
if (isset($_POST['edit'])) {
	$todo_id = $_POST['todo_id'];
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
<table class="list-todo-block">
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="25%">Task</th>
			<th width="40%">Action</th>
			<th width="30%">Due Date</th>
		</tr>
	</thead>

	<tbody>
		<?php if (empty($todos)) {?>
			<tr>
				<td colspan="3">
					No tasks added!
				</td>
			</tr>
		<?php } else {?>
			<?php $count = 1; ?>
			<?php foreach ($todos as $todo) {?>
				<?php $todo_status = get_post_meta($todo->ID, 'todo_status', true); ?>
				<tr>
					<td width="5%" class="<?php if ($todo_status == 'complete') echo $class; ?>">
						<?php echo $count++; ?>
					</td>
					<td width="25%" class="<?php if ($todo_status == 'complete') echo $class; ?>">
						<?php echo $todo->post_title; ?>
					</td>
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
					<td width="30%">
						<?php 
						$curr_date = date_create(date('Y-m-d'));
						$due_date = date_create(get_post_meta($todo->ID, 'todo_due_date', true));
						$diff = date_diff($curr_date, $due_date);
						$diff_days = $diff->format("%R%a");
						if ($diff_days < 0) {
							$str = 'Expired '.abs($diff_days).' days ago!';
							?>
							<p style="color: red;"><?php echo $str; ?></p>
							<?php
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
							$str = 'Today is the last day to complete. Hurry Up!';
							?>
							<p style="color: red;"><?php echo $str; ?></p>
							<?php
						} else {
							echo abs($diff_days).' days left to complete the task!';
						}
						?>
					</td>
				</tr>
			<?php }?>
		<?php }?>
	</tbody>
</table>