<?php
/**
 * Plugin Name: Skye App API For WooCommerce
 * Plugin URI: https://github.com/seunoyeniyi/skye-app-api-for-woocommerce
 * Description: API to use for your APP development either Mobile, Desktop, or Web APP. eg: https://yourwebsite.com/wp-json/skye-api/v1/product=1
 * Version: 1.0
 * Author: Seun Oyeniyi
 * Author URI: https://instagram.com/seun_oyeniyi
 */
if(!defined('ABSPATH')) { exit; }

define("SKYE_API_NAMESPACE_V1", "skye-api/v1");

include(plugin_dir_path( __FILE__ ) . 'includes/functions.php');
include(plugin_dir_path( __FILE__ ) . 'includes/skye-api.php');
include(plugin_dir_path( __FILE__ ) . 'includes/hooks.php');

register_activation_hook( __FILE__, 'skye_activated');