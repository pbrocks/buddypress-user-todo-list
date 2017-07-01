<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Add admin page for price quote settings
if( !class_exists( 'bptodo_AdminPage' ) ) {
	class bptodo_AdminPage{

		private $plugin_slug = 'user-todo-list-settings',
				$plugin_settings_tabs = array(),
				$post_type = 'bp-todo';

		//constructor
		function __construct() {
			add_action( 'admin_menu', array( $this, 'bptodo_add_menu_page' ) );

			add_action('admin_init', array($this, 'bptodo_register_general_settings'));
			add_action('admin_init', array($this, 'bptodo_register_support_settings'));
		}

		//Actions performed on loading admin_menu
		function bptodo_add_menu_page() {
			add_submenu_page( 'edit.php?post_type=bp-todo', __( 'User Todo List Settings', BPTODO_TEXT_DOMAIN ), __( 'Settings', BPTODO_TEXT_DOMAIN ), 'manage_options', 'user-todo-list-settings', array( $this, 'bptodo_admin_options_page' ) );
		}

		function bptodo_admin_options_page() {
			$tab = isset($_GET['tab']) ? $_GET['tab'] : 'user-todo-list-settings';
			?>
			<div class="wrap">
				<h2><?php _e('User Todo List Settings - BuddyPress Members', BPTODO_TEXT_DOMAIN); ?></h2>
				<p><?php _e('This plugin will allow the site owner to manage todo items created by the site members.', BPTODO_TEXT_DOMAIN); ?></p>
				<?php $this->bptodo_plugin_settings_tabs(); ?>
				<form action="" method="POST" id="<?php echo $tab;?>-settings-form">
					<?php do_settings_sections( $tab );?>
				</form>
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
	}
	new bptodo_AdminPage();
}