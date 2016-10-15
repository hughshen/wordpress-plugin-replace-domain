<?php
/**
 * @package Replace Domain
 * @version 1.0
 */
/*
Plugin Name: Replace Domain
Plugin URI: https://github.com/hughshen/wordpress-plugin-replace-domain
Description: This is a simple plugin for replace domain that widgets vanished when migratin wordpress domains.
Author: Hugh Shen
Version: 1.0
Author URI: https://github.com/hughshen
License: MIT
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	exit;
}

define('REPLACE_DOMAIN_DIR', plugin_dir_path(__FILE__));
define('REPLACE_DOMAIN_CLASS_NAME', 'ReplaceDomain');

register_activation_hook( __FILE__, array(REPLACE_DOMAIN_CLASS_NAME, 'plugin_activation'));
register_deactivation_hook( __FILE__, array(REPLACE_DOMAIN_CLASS_NAME, 'plugin_deactivation'));

if (is_admin() || (defined('WP_CLI') && WP_CLI)) {
	require_once(REPLACE_DOMAIN_DIR . 'class.replace-domain.php' );
	add_action('init', array(REPLACE_DOMAIN_CLASS_NAME, 'init'));
}
