<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

//Class to add custom scripts and styles
if( !class_exists( 'BP_Todo_Scripts_Styles' ) ) {
	class BP_Todo_Scripts_Styles{

		//constructor
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'bptodo_custom_variables' ) );
		}

		/**
		 * Actions performed on loading admin_menu
		 */
		function bptodo_custom_variables() {
			$curr_url = $_SERVER['REQUEST_URI'];
			if( strpos( $curr_url, 'todo' ) !== false ) {
				wp_enqueue_script('bptodo-js-date','//cdn.jsdelivr.net/webshim/1.14.5/polyfiller.js', array('jquery'));
				wp_enqueue_script('bptodo-js-front',BPTODO_PLUGIN_URL.'assets/js/bptodo-front.js', array('jquery'));
				wp_localize_script( 'bptodo-js-front', 'bptodo_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
				wp_enqueue_style('bptodo-front-css', BPTODO_PLUGIN_URL.'assets/css/bptodo-front.css');
			}
		}
	}
	new BP_Todo_Scripts_Styles();
}