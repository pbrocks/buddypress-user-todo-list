<?php
/*
Plugin Name: BP User Todo List
Plugin URI: https://wbcomdesigns.com/contact/
Description: This plugin allows users to create to do items in their profile section and manage them accordingly.
Version: 1.0.0
Author: Wbcom Designs
Author URI: http://wbcomdesigns.com
License: GPLv2+
Text Domain: wb-todo
*/

// Exit if accessed directly
defined('ABSPATH') || exit;

//Constants used in the plugin
define('BPTODO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BPTODO_PLUGIN_URL', plugin_dir_url(__FILE__));

//Include needed files
function run_wp_bptodo_list() {
	$include_files = array(
		'admin/bptodo-metabox.php',
		'inc/bptodo-scripts.php',
		'inc/bptodo-ajax.php',
		'inc/bptodo-cpt.php',
		'inc/bptodo-profile-menu.php'
	);
	foreach ($include_files  as $include_file) include $include_file;
}

//Settings link for this plugin
function bptodo_admin_page_link($links) {
	$page_link = array('<a href="'.admin_url('edit.php?post_type=bp-todo').'">CPT</a>');
	return array_merge($links, $page_link);
}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires buddypress to be installed and active
 */
add_action('plugins_loaded', 'bptodo_plugin_init');
function bptodo_plugin_init() {
  // If BuddyPress is NOT active
  $bp_active = in_array('buddypress/bp-loader.php', get_option('active_plugins'));

  if ( current_user_can('activate_plugins') && $bp_active !== true ) {
    add_action('admin_notices', 'bptodo_plugin_admin_notice');
  } else {
    if (!defined('BPTODO_PLUGIN_BASENAME')) {
        define('BPTODO_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }
    run_wp_bptodo_list();
    //Settings link for this plugin
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bptodo_admin_page_link' );
  }
}

function bptodo_plugin_admin_notice() {
	$bptodo_plugin = __( 'BP User Todo List', 'wb-todo' );
	$bp_plugin = __( 'BuddyPress', 'wb-todo' );

	echo '<div class="error"><p>'
	. sprintf(__('%1$s requires %2$s to function correctly. Please activate %2$s before activating %1$s.', 'wb-todo'), '<strong>' . esc_html($bptodo_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>')
	. '</p></div>';
	if (isset($_GET['activate'])) unset($_GET['activate']);
}