<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Add admin page for price quote settings
if( !class_exists( 'Bptodo_Admin' ) ) {
	class Bptodo_Admin{

		private $plugin_slug = 'user-todo-list-settings',
				$plugin_settings_tabs = array(),
				$post_type = 'bp-todo';

		//constructor
		function __construct() {
			add_action( 'admin_menu', array( $this, 'bptodo_add_menu_page' ) );

			add_action('admin_init', array($this, 'bptodo_register_general_settings'));
			add_action('admin_init', array($this, 'bptodo_register_shortcode_settings'));
			add_action('admin_init', array($this, 'bptodo_register_support_settings'));

			$this->bptodo_save_general_settings();
		}

		//Actions performed on loading admin_menu
		function bptodo_add_menu_page() {
			add_submenu_page( 'edit.php?post_type=bp-todo', __( 'BuddyPress Member To-Do List Settings', BPTODO_TEXT_DOMAIN ), __( 'Settings', BPTODO_TEXT_DOMAIN ), 'manage_options', 'user-todo-list-settings', array( $this, 'bptodo_admin_options_page' ) );
		}

		function bptodo_admin_options_page() {
			$tab = isset($_GET['tab']) ? $_GET['tab'] : 'user-todo-list-settings';
			?>
			<div class="wrap">
				<div class="bptodo-header">
					<h2 class="bptodo-plugin-heading"><?php _e( 'BuddyPress Member To-Do List Settings', BPTODO_TEXT_DOMAIN ); ?></h2>
					<div class="bptodo-extra-actions">
						<button type="button" class="button button-secondary" onclick="window.open('https://wbcomdesigns.com/contact/', '_blank');"><i class="fa fa-envelope" aria-hidden="true"></i> <?php _e( 'Email Support', BPTODO_TEXT_DOMAIN )?></button>
						<button type="button" class="button button-secondary" onclick="window.open('https://wbcomdesigns.com/helpdesk/article-categories/bp-user-to-do-list/', '_blank');"><i class="fa fa-file" aria-hidden="true"></i> <?php _e( 'User Manual', BPTODO_TEXT_DOMAIN )?></button>
						<button type="button" class="button button-secondary" onclick="window.open('https://wordpress.org/support/plugin/bp-user-to-do-list/reviews/', '_blank');"><i class="fa fa-star" aria-hidden="true"></i> <?php _e( 'Rate Us on WordPress.org', BPTODO_TEXT_DOMAIN )?></button>
					</div>
				</div>

				<?php $this->bptodo_plugin_settings_tabs();?>
				<?php do_settings_sections( $tab );?>
			</div>
			<?php
		}

		public function bptodo_plugin_settings_tabs() {

			 $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'user-todo-list-settings';
			echo '<h2 class="nav-tab-wrapper">';
			foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
				echo '<a class="nav-tab ' . $active . '" href="?post_type='. $this->post_type.'&page=' . $this->plugin_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
			}
			echo '</h2>';
		}

		function bptodo_register_general_settings() {
			$this->plugin_settings_tabs['user-todo-list-settings'] = __( 'General', BPTODO_TEXT_DOMAIN );
			register_setting('user-todo-list-settings', 'user-todo-list-settings');
			add_settings_section('section_general', ' ', array(&$this, 'bptodo_general_settings_content'), 'user-todo-list-settings');
		}

		function bptodo_register_support_settings() {
			$this->plugin_settings_tabs['bptodo-support'] = __( 'Support', BPTODO_TEXT_DOMAIN );
			register_setting('bptodo-support', 'bptodo-support');
			add_settings_section('section_support', ' ', array(&$this, 'bptodo_support_settings_content'), 'bptodo-support');
		}

		function bptodo_support_settings_content() {
			if (file_exists(dirname(__FILE__) . '/inc/bptodo-support.php')) {
				require_once( dirname(__FILE__) . '/inc/bptodo-support.php' );
			}
		}

		function bptodo_general_settings_content() {
			if (file_exists(dirname(__FILE__) . '/inc/bptodo-general-settings.php')) {
				require_once( dirname(__FILE__) . '/inc/bptodo-general-settings.php' );
			}
		}

		function bptodo_register_shortcode_settings() {
			$this->plugin_settings_tabs['user-todo-list-shortcodes'] = __( 'Shortcodes', BPTODO_TEXT_DOMAIN );
			register_setting('user-todo-list-shortcodes', 'user-todo-list-shortcodes');
			add_settings_section('section_shortcodes', ' ', array(&$this, 'bptodo_general_shortcodes_content'), 'user-todo-list-shortcodes');
		}

		function bptodo_general_shortcodes_content() {
			if (file_exists(dirname(__FILE__) . '/inc/bptodo-shortcodes-settings.php')) {
				require_once( dirname(__FILE__) . '/inc/bptodo-shortcodes-settings.php' );
			}
		}

		public function bptodo_save_general_settings(){
			if( isset( $_POST['bptodo-save-settings'] ) && wp_verify_nonce( $_POST['bptodo-general-settings-nonce'], 'bptodo' ) ) {

				//Profile menu label
				$settings['profile_menu_label'] = sanitize_text_field( $_POST['bptodo_profile_menu_label'] );
				if( isset( $_POST['bptodo_allow_user_add_category'] ) ) {
					$settings['allow_user_add_category'] = sanitize_text_field( $_POST['bptodo_allow_user_add_category'] );
				}
				if( isset( $_POST['bptodo_send_notification'] ) ) {
					$settings['send_notification'] = sanitize_text_field( $_POST['bptodo_send_notification'] );
				}
				if( isset( $_POST['bptodo_send_mail'] ) ) {
					$settings['send_mail'] = sanitize_text_field( $_POST['bptodo_send_mail'] );
				}
				
				update_option('user_todo_list_settings', $settings);
				echo '<div class="notice notice-success is-dismissible"><p><strong>Settings Saved.</strong></p></div>';
			}
		}
	}
}