<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

global $bptodo;
$profile_menu_label	 = $bptodo->profile_menu_label;
$profile_menu_slug	 = $bptodo->profile_menu_slug;
$name				 = bp_get_displayed_user_username();
$url				 = home_url( "/members/$name/$profile_menu_slug" );
$todo_id			 = sanitize_text_field( $_GET[ 'args' ] );

$todo_cats		 = get_terms( 'todo_category', 'orderby=name&hide_empty=0' );
$todo			 = get_post( $todo_id );
$todo_cat		 = wp_get_object_terms( $todo_id, 'todo_category' );
$todo_cat_id	 = 0;
if ( !empty( $todo_cat ) && is_array( $todo_cat ) )
	$todo_cat_id	 = $todo_cat[ 0 ]->term_id;
$todo_due_date	 = get_post_meta( $todo_id, 'todo_due_date', true );
?>
<!-- The Modal -->
<div id="myModal" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<div class="modal-header"></div>
		<div class="modal-body">
			<p style="margin:15px;">To Do Updated Successfully!</p>
		</div>
		<div class="modal-footer"></div>
	</div>
</div>
<form action="<?php echo $url; ?>" method="post">
	<table class="add-todo-block">
		<tr>
			<td width="20%">
				<?php _e( 'Category', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<div>
					<select name="todo_cat" id="bp_todo_categories" required>
						<option value=""><?php _e( '--Select--', 'bptodo' ); ?></option>
						<?php if ( isset( $todo_cats ) ) { ?>
							<?php foreach ( $todo_cats as $todo_cat ) { ?>
								<option <?php if ( $todo_cat_id == $todo_cat->term_id ) echo 'selected="selected"'; ?> value="<?php echo esc_html( $todo_cat->name ); ?>">
									<?php echo esc_html( $todo_cat->name ); ?>
								</option>
							<?php } ?>
						<?php } ?>
					</select>
					<a href="javascript:void(0);" class="add-todo-category"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
				</div>
				<div class="add-todo-cat-row">
					<input type="text" id="todo-category-name" placeholder="<?php _e( $profile_menu_label . ' category', BPTODO_TEXT_DOMAIN ); ?>">
					<input type="button" id="add-todo-cat" value="<?php _e( 'Add', BPTODO_TEXT_DOMAIN ); ?>">
				</div>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Title', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<input value="<?php echo esc_html( $todo->post_title ); ?>" type="text" placeholder="<?php _e( 'Title', BPTODO_TEXT_DOMAIN ); ?>" name="todo_title" required class="bptodo-text-input">
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Summary', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<textarea placeholder="<?php _e( 'Summary', BPTODO_TEXT_DOMAIN ); ?>" name="todo_summary" class="bptodo-text-input"><?php echo esc_textarea( $todo->post_content ); ?></textarea>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Due Date', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<input type="text" placeholder="<?php _e( 'Due Date', BPTODO_TEXT_DOMAIN ); ?>" class="todo_due_date bptodo-text-input" name="todo_due_date" value="<?php echo esc_html( $todo_due_date ); ?>" required>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="80%">
				<?php wp_nonce_field( 'wp-bp-todo', 'save_update_todo_data_nonce' ); ?>
				<input type="submit" id="todo_update" name="todo_update" value="<?php _e( 'Update ' . $profile_menu_label, BPTODO_TEXT_DOMAIN ); ?>">
			</td>
		</tr>
	</table>
</form>