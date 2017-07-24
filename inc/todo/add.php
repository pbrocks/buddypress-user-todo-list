<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$todo_cats = get_terms( 'todo_category', 'orderby=name&hide_empty=0' );

global $bptodo;
$profile_menu_label = $bptodo->profile_menu_label;
$profile_menu_slug = $bptodo->profile_menu_slug;

//Save todo items
if( isset( $_POST['todo_create'] ) && wp_verify_nonce( $_POST['save_new_todo_data_nonce'], 'wp-bp-todo' ) ) {

	$cat = sanitize_text_field( $_POST['todo_cat'] );

	$title = sanitize_text_field( $_POST['todo_title'] );
	$due_date = sanitize_text_field( $_POST['todo_due_date'] );
	$curr_timestamp = time();
	$due_date_timestamp = strtotime($due_date);
	$secs_difference = $due_date_timestamp - $curr_timestamp;

	$curr_user = get_userdata( get_current_user_id() );
	$curr_user_email = $curr_user->data->user_email;
	
	wp_schedule_single_event( time() + $secs_difference, $title.'_event_mail' );
	add_action( $title.'_event_mail', function() use ( $title ){
		$curr_user_email = $curr_user->data->user_email;
		$subject = 'BP Task - Wordpress';
		$messsage = 'Your task: '.$title.' is going to exipre today. Kindly finish it up! Thanks!';
		wp_mail($curr_user_email, $subject, $messsage);
	});

	$summary = sanitize_text_field( $_POST['todo_summary'] );
	
	
	$taxonomy = 'todo_category';
	$args = array(
		'post_type' => 'bp-todo',
		'post_status' => 'publish',
		'post_title' => $title,
		'post_content' => $summary,
		'post_author' => get_current_user_id(),
	);
	$post_id = wp_insert_post( $args );

	update_post_meta( $post_id, 'todo_status', 'incomplete' );
	update_post_meta( $post_id, 'todo_due_date', $due_date );
	update_post_meta( $post_id, 'todo_last_day_mail_sent', 'no' );
	update_post_meta( $post_id, 'todo_last_day_notification_sent', 'no' );

	wp_set_object_terms( $post_id, $cat, $taxonomy );

	$name = bp_get_displayed_user_username();
	$url = home_url( "/members/$name/$profile_menu_slug/list/" );
	?>
	<script>
		var url = '<?php echo $url; ?>';
		window.location.href = url;
	</script>
	<?php
}
?>
<!-- The Modal -->
<div id="myModal" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<div class="modal-header"></div>
		<div class="modal-body">
			<p style="margin:15px;">To Do added Successfully!</p>
		</div>
		<div class="modal-footer"></div>
	</div>
</div>

<form action="" method="post" id="myForm">
	<table class="add-todo-block">
		<tr>
			<td width="20%">
				<?php _e('Category', BPTODO_TEXT_DOMAIN);?>
			</td>
			<td width="80%">
				<div>
					<select name="todo_cat" id="bp_todo_categories" required>
						<option value=""><?php _e('--Select--', BPTODO_TEXT_DOMAIN);?></option>
						<?php if( isset( $todo_cats ) ) {?>
							<?php foreach( $todo_cats as $todo_cat ) {?>
								<option value="<?php echo $todo_cat->name;?>"><?php echo $todo_cat->name;?></option>
							<?php }?>
						<?php }?>
					</select>
					<a href="javascript:void(0);" class="add-todo-category"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
				</div>
				<div class="add-todo-cat-row">
					<input type="text" id="todo-category-name" placeholder="<?php _e( $profile_menu_label.' category', BPTODO_TEXT_DOMAIN);?>">
					<input type="button" id="add-todo-cat" value="<?php _e('Add', BPTODO_TEXT_DOMAIN);?>">
				</div>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Title', BPTODO_TEXT_DOMAIN);?>
			</td>
			<td width="80%">
				<input type="text" placeholder="<?php _e('Title', BPTODO_TEXT_DOMAIN);?>" name="todo_title" required class="bptodo-text-input">
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Summary', BPTODO_TEXT_DOMAIN);?>
			</td>
			<td width="80%">
				<textarea placeholder="<?php _e('Summary', BPTODO_TEXT_DOMAIN);?>" name="todo_summary" class="bptodo-text-input"></textarea>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Due Date', BPTODO_TEXT_DOMAIN);?>
			</td>
			<td width="80%">
				<input type="text" placeholder="<?php _e('Due Date', BPTODO_TEXT_DOMAIN);?>" name="todo_due_date" class="todo_due_date bptodo-text-input" required>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="80%">
				<?php wp_nonce_field( 'wp-bp-todo', 'save_new_todo_data_nonce'); ?>
				<input id="bp-add-new-todo" type="submit" name="todo_create" value="<?php _e('Submit '.$profile_menu_label, BPTODO_TEXT_DOMAIN);?>">
			</td>
		</tr>
	</table>
</form>