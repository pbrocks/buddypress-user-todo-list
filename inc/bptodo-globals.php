<?php
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
* Class to define global variable for this plugin
*
* @since    1.0.0
* @author   Wbcom Designs
*/
if( !class_exists( 'Bptodo_Globals' ) ) {
	class Bptodo_Globals{

		public  $profile_menu_label,
				$profile_menu_label_plural,
				$profile_menu_slug,
				$send_mail,
				$send_notification,
				$my_todo_items;
		/**
		* Constructor.
		*
		* @since    1.0.0
		* @access   public
		* @author   Wbcom Designs
		*/
		public function __construct() {
			$this->setup_globals();
		}

		/**
		 *
		 */
		public function setup_globals() {
			global $bptodo;
			$settings = get_option( 'user_todo_list_settings' );

			//Profile menu label
			$this->profile_menu_label = 'Todo';
			if( isset( $settings['profile_menu_label'] ) ) {
				$this->profile_menu_label = $settings['profile_menu_label'];
			}
			$this->profile_menu_label_plural = $this->pluralize( $this->profile_menu_label );
			$this->profile_menu_slug = str_replace( ' ', '-', strtolower( $this->profile_menu_label ) );

			//Send Notification
			$this->send_notification = 'no';
			if( !empty( $settings['send_notification'] ) ) {
				$this->send_notification = 'yes';
			}

			//Send Mail
			$this->send_mail = 'no';
			if( !empty( $settings['send_mail'] ) ) {
				$this->send_mail = 'yes';
			}

			//Count my todo items
			$this->my_todo_items = $this->bptodo_count_my_todo_items();
		}

		public static function pluralize($singular, $plural=null) {
			if($plural!==null) return $plural;

			$last_letter = strtolower($singular[strlen($singular)-1]);
			switch($last_letter) {
				case 'y':
					return substr($singular,0,-1).'ies';
				case 's':
					return $singular.'es';
				default:
					return $singular.'s';
			}
		}

		private function bptodo_count_my_todo_items(){
			$args = array(
				'post_type' => 'bp-todo',
				'author'    => bp_displayed_user_id(),
				'post_staus'=> 'publish',
				'posts_per_page' => -1
			);
			$todos = get_posts($args);
			return count( $todos );
		}
	}
}