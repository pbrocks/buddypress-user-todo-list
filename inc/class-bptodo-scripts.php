<?php
/**
 * Exit if accessed directly.
 *
 * @package bp-user-todo-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Bptodo_Scripts' ) ) {

	/**
	 * Class to add custom scripts and styles.
	 *
	 * @package bp-user-todo-list
	 * @author  wbcomdesigns
	 * @since   1.0.0
	 */
	class Bptodo_Scripts {

		/**
		 * Constructor.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'bptodo_custom_variables' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bptodo_admin_variables' ) );
		}

		/**
		 * Actions performed for enqueuing scripts and styles for front end.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_custom_variables() {
			global $bptodo, $post;
			$profile_menu_slug = $bptodo->profile_menu_slug;
			if ( ( strpos( $_SERVER['REQUEST_URI'], $profile_menu_slug ) !== false ) || isset( $post->post_content ) && has_shortcode( $post->post_content, 'bptodo_by_category' ) ) {
				/** JQuery UI Datepicker CSS. */
				wp_enqueue_style( 'bptodo-css-ui', BPTODO_PLUGIN_URL . 'assets/css/jquery-ui.min.css', array(), BPTODO_VERSION );
				wp_enqueue_style( 'bptodo-css-fa', BPTODO_PLUGIN_URL . 'assets/css/font-awesome.min.css', array(), BPTODO_VERSION );

				if ( ! wp_script_is( 'jquery' ) ) {
					wp_enqueue_script( 'jquery' );
				}

				if ( ! wp_script_is( 'jquery-ui-tabs' ) ) {
					wp_enqueue_script( 'jquery-ui-tabs' );
				}

				if ( ! wp_script_is( 'datepicker.min.js' ) ) {
					wp_enqueue_script( 'jquery-ui-datepicker' );
				}

				wp_enqueue_script( 'bptodo-js-front', BPTODO_PLUGIN_URL . 'assets/js/bptodo-front.js', array( 'jquery' ), BPTODO_VERSION );
				wp_localize_script(
					'bptodo-js-front', 'todo_ajax_object', array(
						'ajax_url'   => admin_url( 'admin-ajax.php' ),
						'ajax_nonce' => wp_create_nonce( 'bptodo-todo-nonce' )
					)
				);
				wp_enqueue_style( 'bptodo-front-css', BPTODO_PLUGIN_URL . 'assets/css/bptodo-front.css', array(), BPTODO_VERSION );
			}
			wp_enqueue_script( 'bptodo-js-tempust', BPTODO_PLUGIN_URL . 'assets/js/tempust.js', array( 'jquery' ), BPTODO_VERSION );
		}

		/**
		 * Actions performed for enqueuing scripts and styles for admin panel.
		 *
		 * @author  wbcomdesigns
		 * @since   1.0.0
		 * @access  public
		 */
		public function bptodo_admin_variables() {
			if ( strpos( $_SERVER['REQUEST_URI'], 'bp-todo' ) !== false ) {
				wp_enqueue_style( 'bptodo-css-fa', BPTODO_PLUGIN_URL . 'admin/assets/css/font-awesome.min.css', array(), BPTODO_VERSION );
				wp_enqueue_style( 'bptodo-css-admin', BPTODO_PLUGIN_URL . 'admin/assets/css/bptodo-admin.css', array(), BPTODO_VERSION );
				wp_enqueue_script( 'bptodo-js-admin', BPTODO_PLUGIN_URL . 'admin/assets/js/bptodo-admin.js', array( 'jquery' ), BPTODO_VERSION );
			}
		}
	}
	new Bptodo_Scripts();
}
