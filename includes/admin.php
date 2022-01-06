<?php



//ALLOW WP list table class
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}






//Skye App settings pages
add_action("admin_menu", function () {
    add_menu_page(
        "Skye App",
        "Skye App",
        "manage_options",
        "skye-app",
        function() { include(plugin_dir_path(__FILE__) . 'pages/general.php'); },
        plugin_dir_url(__DIR__) . "assets/icons8_iphone_20px.png",
        60
    );

    add_submenu_page("skye-app", "Slide Banners", "Slide Banners", "manage_options", "skye-app-banner-slides", function() { $banner_type = "slide"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 1);
    add_submenu_page(null, "Edit Slide Banners", "Edit Slide Banners", "manage_options", "skye_edit_banner", function() { include(plugin_dir_path(__FILE__) . 'pages/banners/banner-edit.php'); }, 2); //hidden for edit
    
    add_submenu_page("skye-app", "Big Banners", "Big Banners", "manage_options", "skye-app-banner-big", function() { $banner_type = "big"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 2);
    
    add_submenu_page("skye-app", "Carousel Banners", "Carousel Banners", "manage_options", "skye-app-banner-carousel", function() { $banner_type = "carousel"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 3);
    
    add_submenu_page("skye-app", "Thin Banners", "Thin Banners", "manage_options", "skye-app-banner-thin", function() { $banner_type = "thin"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 5);
    
    add_submenu_page("skye-app", "Sale Banners", "Sale Banners", "manage_options", "skye-app-banner-sale", function() { $banner_type = "sale"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 6);
    
    add_submenu_page("skye-app", "Categories Banners", "Categories Banners", "manage_options", "skye-app-banner-categories", function() { $banner_type = "categories"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 7);
    
    add_submenu_page("skye-app", "Video Banners", "Video Banners", "manage_options", "skye-app-banner-video", function() { $banner_type = "video"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 9);
    
    add_submenu_page("skye-app", "Delivery Drivers", "Delivery Drivers", "manage_options", "skye-app-delivery-drivers", function() { include(plugin_dir_path(__FILE__) . 'pages/delivery-drivers.php'); }, 10);

});





// Add Styles and Scripts to Settings page
add_action('admin_enqueue_scripts', 'skye_apps_scripts_func');
function skye_apps_scripts_func($hook)
{
    wp_enqueue_style("skye_app_css", plugin_dir_url(__DIR__) . "css/admin-style.css");
    wp_enqueue_script('skye_app_script', plugin_dir_url(__DIR__) . "js/admin-script.js");
}





//FOR SLIDE BANNERS SETUP
include(plugin_dir_path(__FILE__) . 'hooks/slide-banners-hook.php');







//FOR DELIVERY API
include(plugin_dir_path(__FILE__) . 'hooks/delivery-api-hook.php');