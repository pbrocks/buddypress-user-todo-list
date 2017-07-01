<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

//Class to add custom scripts and styles
if( !class_exists( 'BP_Todo_Scripts_Styles' ) ) {
	class BP_Todo_Scripts_Styles{

		//constructor
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'bptodo_custom_variables' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bptodo_admin_variables' ) );
		}

		//Actions performed for enqueuing scripts and styles for front end.
		function bptodo_custom_variables() {
			global $bptodo;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			if( strpos( $_SERVER['REQUEST_URI'], $profile_menu_slug ) !== false ) {
				//jQuery UI Datepicker
				wp_enqueue_script('bptodo-js-ui', BPTODO_PLUGIN_URL.'assets/js/jquery-ui.min.js', array('jquery'));
				wp_enqueue_style('bptodo-css-ui', BPTODO_PLUGIN_URL.'assets/css/jquery-ui.min.css');

				wp_enqueue_script('bptodo-js-front',BPTODO_PLUGIN_URL.'assets/js/bptodo-front.js', array('jquery'));
				wp_enqueue_style('bptodo-front-css', BPTODO_PLUGIN_URL.'assets/css/bptodo-front.css');
			}
		}

		//Actions performed for enqueuing scripts and styles for admin panel.
		function bptodo_admin_variables() {
			if( strpos( $_SERVER['REQUEST_URI'], 'bp-todo' ) !== false ) {
				//jQuery UI Datepicker
				wp_enqueue_script('bptodo-js-date', BPTODO_PLUGIN_URL.'assets/js/jquery-ui.min.js', array('jquery'));
				wp_enqueue_style('bptodo-front-css', BPTODO_PLUGIN_URL.'assets/css/jquery-ui.min.css');

				wp_enqueue_style('bptodo-css-admin', BPTODO_PLUGIN_URL.'admin/assets/css/bptodo-admin.css');
				wp_enqueue_script('bptodo-js-admin',BPTODO_PLUGIN_URL.'admin/assets/js/bptodo-admin.js', array('jquery'));
			}
		}
	}
	new BP_Todo_Scripts_Styles();
}