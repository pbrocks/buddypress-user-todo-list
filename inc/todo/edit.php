<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

$todo_id = $_GET['args'];
$add_img = BPTODO_PLUGIN_URL.'assets/images/add.png';

//Save todo items
if( isset( $_POST['todo_update'] ) && wp_verify_nonce( $_POST['save_update_todo_data_nonce'], 'wp-bp-todo' ) ) {
	$cat = sanitize_text_field( $_POST['todo_cat'] );

	$title = sanitize_text_field( $_POST['todo_title'] );
	$summary = sanitize_text_field( $_POST['todo_summary'] );
	$due_date = sanitize_text_field( $_POST['todo_due_date'] );

	$taxonomy = 'todo_category';
	$args = array(
		'ID' => $_GET['args'],
		'post_type' => 'bp-todo',
		'post_status' => 'publish',
		'post_title' => $title,
		'post_content' => $summary,
		'post_author' => get_current_user_id(),
	);
	$post_id = wp_update_post( $args );

	update_post_meta( $post_id, 'todo_status', 'incomplete' );
	update_post_meta( $post_id, 'todo_due_date', $due_date );

	wp_set_object_terms( $post_id, $cat, $taxonomy );
}

$todo_cats = get_terms( 'todo_category', 'orderby=name&hide_empty=0' );
$todo = get_post( $todo_id );
$todo_cat = wp_get_object_terms( $todo_id, 'todo_category' );
$todo_cat_id =0;
if (!empty($todo_cat) && is_array($todo_cat))
	$todo_cat_id = $todo_cat[0]->term_id;
$todo_due_date = get_post_meta( $todo_id, 'todo_due_date', true );
?>
<form action="" method="post">
	<table class="add-todo-block">
		<tr>
			<td width="20%">
				<?php _e('Todo Category', 'wb-todo');?>
			</td>
			<td width="80%">
				<select name="todo_cat" id="bp_todo_categories" required>
					<option value=""><?php _e('--Select--', 'bptodo');?></option>
					<?php if( isset( $todo_cats ) ) {?>
						<?php foreach( $todo_cats as $todo_cat ) {?>
							<option <?php if( $todo_cat_id == $todo_cat->term_id ) echo 'selected="selected"';?> value="<?php echo esc_html( $todo_cat->name );?>">
								<?php echo esc_html( $todo_cat->name );?>
							</option>
						<?php }?>
					<?php }?>
				</select>
				<a href="javascript:void(0);" class="add-todo-category">
					<img src="<?php echo esc_url( $add_img );?>">
				</a>
			</td>
		</tr>

		<!-- Add Todo Category -->
		<tr class="add-todo-cat-row">
			<td>
				<input type="text" id="todo-category-name" placeholder="<?php _e('Todo category', 'wb-todo');?>">
			</td>
			<td>
				<input type="button" id="add-todo-cat" value="<?php _e('Add', 'wb-todo');?>">
				<input type="button" value="<?php _e('Close', 'wb-todo');?>" id="todo-cat-close">
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Todo Title', 'wb-todo');?>
			</td>
			<td width="80%">
				<input value="<?php echo esc_html( $todo->post_title );?>" type="text" placeholder="<?php _e('Todo Title', 'wb-todo');?>" name="todo_title" required>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Todo Summary', 'wb-todo');?>
			</td>
			<td width="80%">
				<textarea placeholder="<?php _e('Todo Summary', 'wb-todo');?>" name="todo_summary"><?php echo esc_textarea( $todo->post_content ); ?></textarea>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e('Todo Due Date', 'wb-todo');?>
			</td>
			<td width="80%">
				<input type="date" class="todo_due_date" name="todo_due_date" value="<?php echo esc_html( $todo_due_date );?>" required>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="80%">
				<?php wp_nonce_field( 'wp-bp-todo', 'save_update_todo_data_nonce'); ?>
				<input type="submit" name="todo_update" value="<?php _e('Update Todo', 'wb-todo');?>">
			</td>
		</tr>
	</table>
</form>