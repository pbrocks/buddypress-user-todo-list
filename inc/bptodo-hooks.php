<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
* Class to add custom hooks for this plugin
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if( !class_exists( 'Bptodo_Custom_Hooks' ) ) {
	class Bptodo_Custom_Hooks{

		/**
		* Constructor.
		*
		* @since    1.0.0
		* @access   public
		* @author   Wbcom Designs
		*/
		public function __construct() {
			add_action( 'bp_member_header_actions', array( $this, 'bupr_add_review_button_on_member_header' ) );
			add_filter('manage_bp-todo_posts_columns', array( $this, 'bptodo_due_date_column_heading' ), 10);
			add_action('manage_bp-todo_posts_custom_column', array( $this, 'bptodo_due_date_column_content' ), 10, 2);
		}

		/**
		 * Actions performed to add a review button on member header
		 */
		public function bupr_add_review_button_on_member_header() {
			if( bp_displayed_user_id() === bp_loggedin_user_id() ) {
				$todo_add_url = bp_core_get_userlink( bp_displayed_user_id(), false, true ).'todo/add';
				?>
				<div id="bptodo-add-todo-btn" class="generic-button">
					<a href="<?php echo $todo_add_url;?>" class="add-todo"><?php _e( 'Add Todo', 'wb-todo' );?></a>
				</div>
				<?php
			}
		}

		public function bptodo_due_date_column_heading( $defaults ) {
			$defaults['due_date'] = __( 'Due Date', 'wb-todo' );
			return $defaults;
		}

		public function bptodo_due_date_column_content( $column_name, $post_id ) {
			if( $column_name == 'due_date' ) {
				$due_date = get_post_meta( $post_id, 'todo_due_date', true );
				echo date("F jS, Y", strtotime($due_date));
			}
		}
	}
	new Bptodo_Custom_Hooks();
}