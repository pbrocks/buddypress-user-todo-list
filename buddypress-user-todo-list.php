<?php
/*
Plugin Name: BP User To Do List
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
$include_files = array(
	'admin/bptodo-metabox.php',
	'inc/bptodo-scripts.php',
	'inc/bptodo-ajax.php',
	'inc/bptodo-cpt.php',
	'inc/bptodo-profile-menu.php'
);
foreach ($include_files  as $include_file) {
	include $include_file;
}

//BP Todo list Plugin Activation
register_activation_hook(__FILE__, 'bptodo_plugin_activation');
function bptodo_plugin_activation() {
	//Check if "Buddypress" plugin is active or not
	if (!in_array('buddypress/bp-loader.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		deactivate_plugins(plugin_basename(__FILE__));
		wp_die('The <b>BP Todo List</b> plugin requires <b>Buddypress</b> plugin to be installed and active. Return to <a href="'.admin_url('plugins.php').'">Plugins</a>');
	}
}

//Settings link for this plugin
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'bptodo_admin_page_link');
function bptodo_admin_page_link($links) {
	$page_link = array('<a href="'.admin_url('edit.php?post_type=bp-todo').'">CPT</a>');
	return array_merge($links, $page_link);
}
