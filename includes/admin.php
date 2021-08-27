<?php

//Skye App settings pages
// add_action( "admin_menu", function() {
//     add_menu_page( 
//         "Skye App Settings", 
//         "Skye App Settings", 
//         "manage_options", 
//         "skye-app", "skye_app_func", 
//         plugin_dir_url( __DIR__ ) . "assets/icons8_iphone_20px.png", 
//         60
//     );
//     if (ENABLE_SLIDING_BANNER)
//         add_submenu_page( "skye-app", "App Banner Slides", "Banner Slides", "manage_options", "skye-app-banner-slides", "skye_app_banner_slides", 1);
//     if (ENABLE_SCROLLING_BANNER)
//         add_submenu_page( "skye-app", "App Banner Scrolls", "Small Banner Scrolls", "manage_options", "skye-app-banner-scrolls", "skye_app_banner_scrolls", 2);
    
// });


function skye_app_func() { include(plugin_dir_path( __FILE__ ) . 'pages/general.php'); }
function skye_app_banner_slides() { include(plugin_dir_path( __FILE__ ) . 'pages/banner-slides.php'); }
function skye_app_banner_scrolls() { include(plugin_dir_path( __FILE__ ) . 'pages/banner-scrolls.php'); }




// Add Styles and Scripts to Settings page
add_action( 'admin_enqueue_scripts', 'skye_apps_scripts_func' );
function skye_apps_scripts_func($hook) {
    //load only on ?page=skye-app
    // wp_die($hook);
    if ($hook != "toplevel_page_skye-app" && $hook != "skye-app-settings_page_skye-app-banners") return;
    wp_enqueue_style( "skye_app_css", plugin_dir_url( __DIR__ ) . "css/admin-style.css");
    wp_enqueue_script( 'skye_app_script', plugin_dir_url( __DIR__ ) . "js/admin-script.js");
}