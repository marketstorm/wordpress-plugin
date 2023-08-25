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


define('MARKETSTORM_PREFIX', 'marketstorm');
define('MARKETSTORM_BASENAME', plugin_basename(__FILE__));
define('MARKETSTORM_OPTION_NAME', MARKETSTORM_PREFIX . '_options');
define('MARKETSTORM_OPTION_GROUP', MARKETSTORM_PREFIX . '_settings');
define('MARKETSTORM_DISPLAY_NAME', 'Marketstorm');
define('MARKETSTORM_REQUIRED_PHP_VERSION', '5.3');
define('MARKETSTORM_TESTED_WP_VERSION', '5.3');
define('MARKETSTORM_REQUIRED_WP_VERSION',  '3.1');
define('MARKETSTORM_REQUIRED_PERMISSIONS', 'manage_options');
define('MARKETSTORM_MATOMO_URI', 'https://cdn.matomo.cloud/marketstormai.matomo.cloud');
define('MARKETSTORM_GITHUB_USERNAME', 'marketstorm-ai');
define('MARKETSTORM_GITHUB_REPOSITORY', 'wordpress-plugin');


function marketstorm_requirements_met() {
	global $wp_version;

	if (version_compare(PHP_VERSION, MARKETSTORM_REQUIRED_PHP_VERSION, '<')) return false;
	if (version_compare($wp_version, MARKETSTORM_REQUIRED_WP_VERSION, '<'))  return false;

	return true;
}


function marketstorm_requirements_error() {
	global $wp_version;
	require_once(dirname(__FILE__) . '/requirements-error.php');
}


if(marketstorm_requirements_met()) {
	require_once(__DIR__ . '/controller.php');
	require_once(__DIR__ . '/updater.php');
	require_once(__DIR__ . '/injector.php');
	require_once(__DIR__ . '/injector-matomo.php');

	if(class_exists('Marketstorm\Controller')) {
		$GLOBALS[MARKETSTORM_PREFIX] = MarketStorm\Controller::getInstance();
		register_activation_hook(__FILE__, array($GLOBALS[MARKETSTORM_PREFIX], 'activate'));
		register_deactivation_hook(__FILE__, array($GLOBALS[MARKETSTORM_PREFIX], 'deactivate'));
	}
} else {
	add_action('admin_notices', 'marketstorm_requirements_error');
}


add_action('init', 'github_updater');
function github_updater() {
	$updater = new MarketStorm\Updater(__FILE__, MARKETSTORM_GITHUB_USERNAME, MARKETSTORM_GITHUB_REPOSITORY);
	$updater->initialize();
}
