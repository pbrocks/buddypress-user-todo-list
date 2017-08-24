<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

$todo_cats = get_terms( 'todo_category', 'orderby=name&hide_empty=0' );

global $bptodo;
$profile_menu_label	 = $bptodo->profile_menu_label;
$profile_menu_slug	 = $bptodo->profile_menu_slug;
$name				 = bp_get_displayed_user_username();

$displayed_uid = bp_displayed_user_id();
$form_post_link = bp_core_get_userlink( $displayed_uid, false, true ).$profile_menu_slug;
?>
<form action="<?php echo $form_post_link;?>" method="post" id="myForm">
	<table class="add-todo-block">
		<tr>
			<td width="20%">
				<?php _e( 'Category', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<div>
					<select name="todo_cat" id="bp_todo_categories" required>
						<option value=""><?php _e( '--Select--', BPTODO_TEXT_DOMAIN ); ?></option>
						<?php if ( isset( $todo_cats ) ) { ?>
							<?php foreach ( $todo_cats as $todo_cat ) { ?>
								<option value="<?php echo $todo_cat->name; ?>"><?php echo $todo_cat->name; ?></option>
							<?php } ?>
						<?php } ?>
					</select>
					<?php if( $bptodo->allow_user_add_category == 'yes' ) {?>
						<a href="javascript:void(0);" class="add-todo-category"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
					<?php }?>
				</div>
				<?php if( $bptodo->allow_user_add_category == 'yes' ) {?>
					<div class="add-todo-cat-row">
						<input type="text" id="todo-category-name" placeholder="<?php _e( $profile_menu_label . ' category', BPTODO_TEXT_DOMAIN ); ?>">
						<input type="button" id="add-todo-cat" value="<?php _e( 'Add', BPTODO_TEXT_DOMAIN ); ?>">
					</div>
				<?php }?>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Title', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<input type="text" placeholder="<?php _e( 'Title', BPTODO_TEXT_DOMAIN ); ?>" name="todo_title" required class="bptodo-text-input">
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Summary', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<textarea placeholder="<?php _e( 'Summary', BPTODO_TEXT_DOMAIN ); ?>" name="todo_summary" class="bptodo-text-input"></textarea>
			</td>
		</tr>

		<tr>
			<td width="20%">
				<?php _e( 'Due Date', BPTODO_TEXT_DOMAIN ); ?>
			</td>
			<td width="80%">
				<input type="text" placeholder="<?php _e( 'Due Date', BPTODO_TEXT_DOMAIN ); ?>" name="todo_due_date" class="todo_due_date bptodo-text-input" required>
			</td>
		</tr>

		<tr>
			<td width="20%"></td>
			<td width="80%">
				<?php wp_nonce_field( 'wp-bp-todo', 'save_new_todo_data_nonce' ); ?>
				<input id="bp-add-new-todo" type="submit" name="todo_create" value="<?php _e( 'Submit ' . $profile_menu_label, BPTODO_TEXT_DOMAIN ); ?>">
			</td>
		</tr>
	</table>
</form>