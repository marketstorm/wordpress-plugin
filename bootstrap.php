<?php

/*
Plugin Name: MarketStorm
Plugin URI:  https://github.com/marketstorm-ai/wordpress-plugin
Description: A pixel injection plugin for MarketStorm clients
Version:     0.0.2
Author:      MarketStorm
Author URI:  marketstorm.ai
*/


if (! defined('ABSPATH')) {
	die('Access denied.');
}


define('PREFIX', 'marketstorm');
define('PLUGIN_BASENAME', plugin_basename(__FILE__));
define('OPTION_NAME', PREFIX . '_options');
define('OPTION_GROUP', PREFIX . '_settings');
define('DISPLAY_NAME', 'Marketstorm');
define('REQUIRED_PHP_VERSION', '5.3');
define('TESTED_WP_VERSION', '5.3');
define('REQUIRED_WP_VERSION',  '3.1');
define('REQUIRED_PERMISSIONS', 'manage_options');
define('MATOMO_URI', 'https://cdn.matomo.cloud/marketstormai.matomo.cloud');
define('GITHUB_USERNAME', 'marketstorm-ai');
define('GITHUB_REPOSITORY', 'wordpress-plugin');


function requirements_met() {
	global $wp_version;

	if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<')) return false;
	if (version_compare($wp_version, REQUIRED_WP_VERSION, '<'))  return false;

	return true;
}


function requirements_error() {
	global $wp_version;
	require_once(dirname(__FILE__) . '/requirements-error.php');
}


if(requirements_met()) {
	require_once(__DIR__ . '/controller.php');
	require_once(__DIR__ . '/updater.php');
	require_once(__DIR__ . '/injector.php');
	require_once(__DIR__ . '/injector-matomo.php');

	if(class_exists('Marketstorm\Controller')) {
		$GLOBALS[PREFIX] = MarketStorm\Controller::getInstance();
		register_activation_hook(__FILE__, array($GLOBALS[PREFIX], 'activate'));
		register_deactivation_hook(__FILE__, array($GLOBALS[PREFIX], 'deactivate'));
	}
} else {
	add_action('admin_notices', 'requirements_error');
}


add_action('init', 'github_updater');
function github_updater() {
	$updater = new MarketStorm\Updater(__FILE__, GITHUB_USERNAME, GITHUB_REPOSITORY);
	$updater->initialize();
}
