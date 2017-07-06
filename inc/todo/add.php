<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$todo_cats = get_terms( 'todo_category', 'orderby=name&hide_empty=0' );
$add_img = BPTODO_PLUGIN_URL.'assets/images/add.png';

global $bptodo;
$profile_menu_label = $bptodo->profile_menu_label;
$profile_menu_slug = $bptodo->profile_menu_slug;

//Save todo items
if( isset( $_POST['todo_create'] ) && wp_verify_nonce( $_POST['save_new_todo_data_nonce'], 'wp-bp-todo' ) ) {

	$cat = sanitize_text_field( $_POST['todo_cat'] );

	$title = sanitize_text_field( $_POST['todo_title'] );
	$summary = sanitize_text_field( $_POST['todo_summary'] );
	$due_date = sanitize_text_field( $_POST['todo_due_date'] );
	
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

	$str = '';
	$str .= '<div id="message" class="updated">';
	$str .= '<p>'.__( 'Todo item created successfully.', BPTODO_TEXT_DOMAIN ).'</p>';
	$str .= '</div>';
	echo $str;

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
<form action="" method="post">
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
					<a href="javascript:void(0);" class="add-todo-category"><img src="<?php echo esc_url( $add_img );?>"></a>
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
				<input type="submit" name="todo_create" value="<?php _e('Submit '.$profile_menu_label, BPTODO_TEXT_DOMAIN);?>">
			</td>
		</tr>
	</table>
</form>