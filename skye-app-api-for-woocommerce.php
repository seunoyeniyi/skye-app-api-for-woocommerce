<?php
/**
 * Plugin Name: Skye App API For WooCommerce
 * Plugin URI: https://github.com/seunoyeniyi/skye-app-api-for-woocommerce
 * Description: API to use for your APP development either Mobile, Desktop, or Web APP.
 * Version: 1.0
 * Author: Seun Oyeniyi
 * Author URI: https://instagram.com/seun_oyeniyi
 */
if(!defined('ABSPATH')) { exit; }

define("SKYE_API_NAMESPACE_V1", "skye-api/v1");
define("SKYE_API_NAMESPACE_V2", "skye-api/v2");
define("SKYE_ALLOW_SECURITY", false);
define("SKYE_ALLOW_API_TOKEN_KEY", false);
define("SKYE_ALLOW_DELIVERY_DRIVER", false);
define("SKYE_ALLOW_SLIDE_BANNER", true);
define("SKYE_ALLOW_BIG_BANNER", true);
define("SKYE_ALLOW_CAROUSEL_BANNER", true);
define("SKYE_ALLOW_THIN_BANNER", true);
define("SKYE_ALLOW_GRID_BANNER", true);
define("SKYE_ALLOW_VIDEO_BANNER", false);
define("SKYE_ALLOW_RESOURCES", true);
define("SKYE_ENABLE_COUPON_FIRST_ORDER", true);
define("SKYE_FIRST_ORDER_COUPON_CODE","NEWCUSTOMER");


function find_matching_product_variation_id($product_id, $attributes)
{
    return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
        new \WC_Product($product_id),
        $attributes
    );
}


include(plugin_dir_path( __FILE__ ) . 'includes/functions.php');
include(plugin_dir_path( __FILE__ ) . 'includes/skye-api.php');
include(plugin_dir_path( __FILE__ ) . 'includes/hooks.php');

register_activation_hook( __FILE__, 'skye_activated');


//for whatsdown
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );