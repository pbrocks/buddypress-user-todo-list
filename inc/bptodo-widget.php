<?php
defined('ABSPATH') || exit; // Exit if accessed directly
if( !class_exists( 'Bptodo_Widget_Todo_Calendar' ) ) {
	class Bptodo_Widget_Todo_Calendar extends WP_Widget {
		function __construct() {
			parent::__construct('Bptodo_Widget_Todo_Calendar',
				__('Todo Calendar'),
				array(
					'description' => __( 'This widget helps to get a calendar in the sidebar to reflect the todo marked in the calendar.', BPTODO_TEXT_DOMAIN )
				)
			);
		}

		public function widget( $args, $instance ) {
			global $wp_widget_factory;
			$title = $instance['title'];
			if( !empty( $title ) ) {
				$show_title = strip_tags( $title );
			}
			include 'todo/bptodo-widget-template.php';
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			return $instance;
		}

		public function form( $instance ) {
			$title = __('New Title');
			if( isset( $instance['title'] ) ) {
				$title = $instance['title'];
			}
			//Creating the form at the admin side for setting the title
			?>
			<p>
			<label for="<?php echo $this->get_field_id('title')?>"><?php _e('Title')?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" type="text" value="<?php echo esc_attr($title);?>">
			</p>
			<?php
		}
	} //class LocationFilter closed
}