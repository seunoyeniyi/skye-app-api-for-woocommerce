<?php
add_filter('manage_edit-shop_order_columns', 'skye_add_driver_order_column_to_admin_table');

function skye_add_driver_order_column_to_admin_table($columns)
{
    if (!SKYE_ALLOW_DELIVERY_DRIVER) return $columns;
    $reordered_columns = array();
    foreach ($columns as $key => $column) {
        $reordered_columns[$key] = $column;
        if ($key ==  'order_status') {
            // Inserting after "Status" column
            $reordered_columns['skye_driver'] = __('Driver', 'skye_domain');
        }
    }
    return $reordered_columns;
}
add_action('manage_shop_order_posts_custom_column', 'skye_add_driver_order_column_to_admin_table_content');

function skye_add_driver_order_column_to_admin_table_content($column)
{

    global $post;

    if ('skye_driver' === $column) {

        $order = wc_get_order($post->ID);
        $driver_id = $order->get_meta("skye_order_driver");
        if ($driver_id) {
            $user = get_user_by('ID', $driver_id);
            if ($user) {
                echo "<a href='user-edit.php?user_id=" . $user->ID . "'><b>" . $user->display_name . "</b></a>";
            }
        }
    }
}

// Adding Meta container admin shop_order pages
add_action('add_meta_boxes', 'skye_add_driver_meta_boxes');
if (!function_exists('skye_add_driver_meta_boxes')) {
    function skye_add_driver_meta_boxes()
    {
        if (!SKYE_ALLOW_DELIVERY_DRIVER) return;
        add_meta_box('skye_other_fields', __('Driver', 'woocommerce'), 'skye_add_driver_fields_for_packaging', 'shop_order', 'side', 'high');
    }
}

// Adding Meta field in the meta container admin shop_order pages
if (!function_exists('skye_add_driver_fields_for_packaging')) {
    function skye_add_driver_fields_for_packaging()
    {
        if (!SKYE_ALLOW_DELIVERY_DRIVER) return;
        
        global $post;

        //if status is either on-hold or processing
        $order = new WC_Order($post->ID);
        if ($order->has_status('completed') || $order->has_status('pending')) {
            echo "Order " . $order->get_status();
        } else {
            $meta_field_data = get_post_meta($post->ID, 'skye_order_driver', true) ? get_post_meta($post->ID, 'skye_order_driver', true) : '';
            $sk_drivers = get_users(array('role' => 'skye_delivery_driver'));
        ?>
        <input type="hidden" name="skye_order_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">

            <label>Driver</label> <br>
            <select name="skye_order_drivers_input" id="skye_order_drivers_input" style="width: 100%;">
                <option value="">Assign a Driver</option>
                <?php foreach($sk_drivers as $driver) { ?>
                    <option value="<?php echo $driver->ID; ?>" <?php echo ($driver->ID == $meta_field_data) ? "selected" : ""; ?>><?php echo $driver->display_name; ?></option>
                <?php } ?>
            </select>
            <?php
        }
    }
}

// Save the data of the Meta field
add_action('save_post', 'skye_save_wc_order_other_fields', 10, 1);
if (!function_exists('skye_save_wc_order_other_fields')) {

    function skye_save_wc_order_other_fields($post_id)
    {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if (!isset($_POST['skye_order_meta_field_nonce'])) {
            return $post_id;
        }
        $nonce = $_REQUEST['skye_order_meta_field_nonce'];

        //Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce)) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {

            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        $driver_id = $_POST['skye_order_drivers_input'];
        if (empty($driver_id)) {
            delete_post_meta( $post_id, 'skye_order_driver');
        } else {
            $order = new WC_Order($post_id);
            if (!$order->has_status('completed')) {
                update_post_meta($post_id, 'skye_order_driver', $driver_id);
                update_post_meta($post_id, 'skye_order_delivery_status', '');
                //notify driver
                $driver = get_user_by( 'ID', $driver_id);
                if ($driver) {
                    if ($driver->user_email) {
                        $email = $driver->user_email;
                        $message = "<h3>You have new order to deliver</h3>
                        <h4>Order: #" . $post_id . "</h4>
                        <p>Open your delivery app to view details.</p>";
                        $headers = array('Content-Type: text/html; charset=UTF-8');
                        $subject = get_bloginfo( 'name') . ' - You have new order to deliver';
                        wp_mail( $email, $subject, $message, $headers);
                    }
                }
            }

        }
    }
}
