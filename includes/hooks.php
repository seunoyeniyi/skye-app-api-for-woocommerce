<?php

// ON Activating plugin
//setup database
if (!function_exists('skye_activated')) {
    function skye_activated() {
    set_transient( 'skye-api-activated', true, 5);

    // setup database
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $cart_table = $wpdb->prefix . "skye_carts";
    
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $sql = "CREATE TABLE IF NOT EXISTS " . $cart_table . " (
                ID INT NOT NULL AUTO_INCREMENT,
                user VARCHAR(2000),
                cart_value LONGTEXT,
                session_expiry DATETIME,
                PRIMARY KEY (ID)
            ) " . $charset_collate . ";";
    dbDelta($sql);
    
    }
}
//welcome message
add_action( 'admin_notices', function() {
   if (get_transient( 'skye-api-activated' )) { ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using Skye App API... You are on your way creating your <strong>awesome apps</strong>.</p>
        </div>
   <?php 
   //to only display this once
   delete_transient( 'skye-api-activated' );
   }
});
