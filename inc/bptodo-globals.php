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
				$profile_menu_slug;
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
	}
}