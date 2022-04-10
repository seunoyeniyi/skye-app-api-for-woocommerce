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

    add_submenu_page("skye-app", "Push Notification", "Push Notification", "manage_options", "skye-app-push-notification", function() { include(plugin_dir_path(__FILE__) . 'pages/push-notification.php'); }, 1);


    if (SKYE_ALLOW_SLIDE_BANNER) add_submenu_page("skye-app", "Slide Banners", "Slide Banners", "manage_options", "skye-app-banner-slides", function() { $banner_type = "slide"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 2);
    add_submenu_page(null, "Edit Slide Banners", "Edit Slide Banners", "manage_options", "skye_edit_banner", function() { include(plugin_dir_path(__FILE__) . 'pages/banners/banner-edit.php'); }, 2); //hidden for edit
    
    if (SKYE_ALLOW_BIG_BANNER) add_submenu_page("skye-app", "Big Banners", "Big Banners", "manage_options", "skye-app-banner-big", function() { $banner_type = "big"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 3);
    
    if (SKYE_ALLOW_CAROUSEL_BANNER) add_submenu_page("skye-app", "Carousel Banners", "Carousel Banners", "manage_options", "skye-app-banner-carousel", function() { $banner_type = "carousel"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 4);
    
    if (SKYE_ALLOW_THIN_BANNER) add_submenu_page("skye-app", "Thin Banners", "Thin Banners", "manage_options", "skye-app-banner-thin", function() { $banner_type = "thin"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 5);
    
    // add_submenu_page("skye-app", "Sale Banners", "Sale Banners", "manage_options", "skye-app-banner-sale", function() { $banner_type = "sale"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 6);
    
    if (SKYE_ALLOW_GRID_BANNER) add_submenu_page("skye-app", "Grid Banners", "Grid Banners", "manage_options", "skye-app-banner-grid", function() { $banner_type = "grid"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 7);
    
    if (SKYE_ALLOW_VIDEO_BANNER) add_submenu_page("skye-app", "Video Banners", "Video Banners", "manage_options", "skye-app-banner-video", function() { $banner_type = "video"; include(plugin_dir_path(__FILE__) . 'pages/banners/banners.php'); }, 9);
    
    if (SKYE_ALLOW_DELIVERY_DRIVER) add_submenu_page("skye-app", "Delivery Drivers", "Delivery Drivers", "manage_options", "skye-app-delivery-drivers", function() { include(plugin_dir_path(__FILE__) . 'pages/delivery-drivers.php'); }, 10);

    if (SKYE_ALLOW_RESOURCES) add_submenu_page("skye-app", "Images", "Image Resources", "manage_options", "skye-app-resources", function() { include(plugin_dir_path(__FILE__) . 'pages/resources.php'); }, 11);

    if (SKYE_ALLOW_API_TOKEN_KEY) add_submenu_page("skye-app", "Secure TOKEN KEY", "Secure TOKEN KEY", "manage_options", "skye-app-token-key", function() { include(plugin_dir_path(__FILE__) . 'pages/token-key-page.php'); }, 12);

});





// Add Styles and Scripts to Settings page
add_action('admin_enqueue_scripts', 'skye_apps_scripts_func');
function skye_apps_scripts_func($hook)
{
    wp_enqueue_style("skye_app_css", plugin_dir_url(__DIR__) . "css/admin-style.css");
    wp_enqueue_script('skye_app_script', plugin_dir_url(__DIR__) . "js/admin-script.js");
}


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
    }?>
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



if (!function_exists('sk_push_notification')) {
    function sk_push_notification($to, $data){

        $api_key= get_option( "sk_push_api_key", "");
        $url="https://fcm.googleapis.com/fcm/send";
        $fields=json_encode(array('registration_ids'=>$to, 'notification'=>$data));
    
        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($fields));
    
        $headers = array();
        $headers[] = 'Authorization: key ='.$api_key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $result = curl_exec($ch);
    
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            return false;
        }
        curl_close($ch);
        return true;
    }
}





//FOR SLIDE BANNERS SETUP
include(plugin_dir_path(__FILE__) . 'hooks/slide-banners-hook.php');







//FOR DELIVERY API
include(plugin_dir_path(__FILE__) . 'hooks/delivery-api-hook.php');






add_action( 'woocommerce_admin_order_data_after_shipping_address', 'sk_custom_field_display_admin_order_meta', 10, 1 );
function sk_custom_field_display_admin_order_meta( $order ){
    $user_id = $order->get_user_id();
    $other_phone = get_user_meta( $user_id, 'other_phone_field', true);
    
    if (empty($other_phone)) {
        $other_phone = "nil";
    }

    echo '<p><strong>'.__('Alternative Phone', 'woocommerce').': </strong> ' . $other_phone . '</p>';
}