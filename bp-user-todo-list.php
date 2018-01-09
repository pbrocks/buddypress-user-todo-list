<?php
/**
 * Plugin Name: BuddyPress Member To-Do List
 * Plugin URI: https://wbcomdesigns.com/contact/
 * Description: This plugin allows users to create to do items in their profile section and manage them accordingly.
 * Version: 1.0.1
 * Author: Wbcom Designs
 * Author URI: http://wbcomdesigns.com
 * License: GPLv2+
 * Text Domain: wb-todo
 */
defined('ABSPATH') || exit; // Exit if accessed directly

//Load plugin textdomain ( @since 1.0.0 )
add_action( 'init', 'bptodo_load_textdomain' );
function bptodo_load_textdomain() {
	$domain = "wb-todo";
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	
	load_textdomain( $domain, 'languages/'.$domain.'-' . $locale . '.mo' );
	$var = load_plugin_textdomain( $domain, false, plugin_basename( dirname(__FILE__) ) . '/languages' );
}

//Constants used in the plugin
if ( !defined( 'BPTODO_PLUGIN_PATH' ) ) {
	define( 'BPTODO_PLUGIN_PATH', plugin_dir_path(__FILE__) );
}

if ( !defined( 'BPTODO_PLUGIN_URL' ) ) {
	define( 'BPTODO_PLUGIN_URL', plugin_dir_url(__FILE__) );
}

if ( !defined( 'BPTODO_TEXT_DOMAIN' ) ) {
	define( 'BPTODO_TEXT_DOMAIN', 'wb-todo' );
}

if ( !defined( 'BP_ENABLE_MULTIBLOG' ) ) {
	define( 'BP_ENABLE_MULTIBLOG', false );
}

if ( !defined( 'BP_ROOT_BLOG' ) ) {
	define( 'BP_ROOT_BLOG', 1 );
}
global $bptodo;

//Include needed files
function run_wp_bptodo_list() {
	$include_files = array(
		'inc/bptodo-scripts.php',
		'inc/bptodo-ajax.php',
		'inc/bptodo-cpt.php',
		'inc/bptodo-globals.php',
		'inc/bptodo-hooks.php',
		'admin/bptodo-admin.php',
	);
	foreach ($include_files  as $include_file) include $include_file;

	//Initialize admin class
	new Bptodo_Admin();
	
	//Initialize globals class
	global $bptodo;
	$bptodo = new Bptodo_Globals();
}

//Settings link for this plugin
function bptodo_admin_page_link( $links ) {
	$bptodo_links = array(
		'<a href="'.admin_url('edit.php?post_type=bp-todo&page=user-todo-list-settings').'">'.__( 'Settings', BPTODO_TEXT_DOMAIN ).'</a>',
		'<a href="https://wbcomdesigns.com/contact/" target="_blank">'.__( 'Support', BPTODO_TEXT_DOMAIN ).'</a>'
	);
	return array_merge( $links, $bptodo_links );
}

/**
 * Check plugin requirement on plugins loaded
 * this plugin requires buddypress to be installed and active
 */
add_action('plugins_loaded', 'bptodo_plugin_init');
	function bptodo_plugin_init() {
	if ( is_multisite() ) {
		// Makes sure the plugin is defined before trying to use it
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active_for_network( 'buddypress/bp-loader.php' ) === false ) {
			add_action('network_admin_notices', 'bptodo_network_plugin_admin_notice');
		} else {
			run_wp_bptodo_list();
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bptodo_admin_page_link' );
			add_action( 'bp_include', 'bptodo_create_profile_menu' );
		}
	} else {
		$bp_active = in_array('buddypress/bp-loader.php', get_option('active_plugins'));
		if ( current_user_can('activate_plugins') && $bp_active !== true ) {
			add_action('admin_notices', 'bptodo_plugin_admin_notice');
		} else {
			run_wp_bptodo_list();
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bptodo_admin_page_link' );
			add_action( 'bp_include', 'bptodo_create_profile_menu' );
		}
	}
}

/**
 * Plugin notice - activate buddypress - single site
 */
function bptodo_plugin_admin_notice() {
	$bptodo_plugin = __( 'BuddyPress Member To-Do List', BPTODO_TEXT_DOMAIN );
	$bp_plugin = __( 'BuddyPress', BPTODO_TEXT_DOMAIN );

	echo '<div class="error"><p>' . sprintf(__('%1$s is ineffective now as it requires %2$s to be installed and active.', BPTODO_TEXT_DOMAIN), '<strong>' . esc_html($bptodo_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>') . '</p></div>';
	if (isset($_GET['activate'])) unset($_GET['activate']);
}

/**
 * Plugin notice - activate buddypress - multisite
 */
function bptodo_network_plugin_admin_notice() {
	$bptodo_plugin = __( 'BuddyPress Member To-Do List', BPTODO_TEXT_DOMAIN );
	$bp_plugin = __( 'BuddyPress', BPTODO_TEXT_DOMAIN );

	echo '<div class="error"><p>' . sprintf(__('%1$s is ineffective now as it requires %2$s to be installed and active.', BPTODO_TEXT_DOMAIN), '<strong>' . esc_html($bptodo_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>') . '</p></div>';
	if (isset($_GET['activate'])) unset($_GET['activate']);
}

function bptodo_create_profile_menu(){
	include 'inc/bptodo-profile-menu.php';
}