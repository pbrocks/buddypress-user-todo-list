<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Metabox to show the due date of todo item
add_action('add_meta_boxes','bptodo_due_date_metabox');
function bptodo_due_date_metabox() {
	add_meta_box( 'bptodo-metabox', 'Todo Due Date', 'bptodo_due_date_content', 'bp-todo', 'side', 'high', null );
}

function bptodo_due_date_content() {
	global $post;
	$due_date = get_post_meta( $post->ID, 'todo_due_date', true );
	?>
	<input type="date" name="todo_due_date" required value="<?php echo $due_date;;?>">
	<?php
}

//Save due date on save post
add_action( 'save_post', 'bptodo_save_due_date' );
function bptodo_save_due_date( $post_id ) {
	$post = get_post( $post_id );
	if( $post->post_type == 'bp-todo' ) {
		if( isset( $_POST['todo_due_date'] ) ) {
			update_post_meta( $post_id, 'todo_due_date', $_POST['todo_due_date'] );
		}
	}
}