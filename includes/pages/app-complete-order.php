<?php

$error_msg = "Can't proceed your order!";
$error_title = "Order denied!";
$is_error = true;

if (!isset($_GET['user'])) {
    $is_error = true;
    $error_msg = $error_msg . " #USER-404";
    the_page();
    return;
} else {

    $user_id = $_GET['user'];
    $allow_guest = (isset($_GET['allow_guest']));
    $status = 'wc-pending';
    $order_note = 'Ordered from App';
    $payment_method = (isset($data['payment_method'])) ? $data['payment_method'] : null;

    //to add address to url: url?billing_address%Bfirst_name%5D=SEUN&billing_address%5Blast_name%5D=OYENIYI....
    //%5B is to select variable array with they key eg: address%5Bfirst_name
    //%5D is to add the value to the key eg:address%5Bfirst_name%5D=SEUN
    $billing_address = (isset($data['billing_address'])) ? $data['billing_address'] : null;
    $shipping_address = (isset($data['shipping_address'])) ? $data['shipping_address'] : null;

    //is user cart exists or empty
    if (!sk_user_cart_exists($user_id)) {
        $is_error = true;
        $error_msg = "Your cart is empty, please go back and pick your desired products!";
        the_page();
        return;
    }

    //CART JSON
    $cart = json_decode(sk_get_cart_value($user_id), true);

    //cart empty?
    $empty = is_null($cart) ? true : $cart['is_empty'];
    if ($empty) {
        $is_error = true;
        $error_msg = "Your cart is empty, please go back and pick your desired products!";
        the_page();
        return;
    }

    //user exists or allow guest is set?
    if (!sk_user_exists($user_id) && !$allow_guest) {
        $is_error = true;
        $error_msg = "Access denied! User not exists!";
        $error_title = "Access denied!";
        the_page();
        return;
    }


    //CREATE ORDER
    $order = wc_create_order(array(
        'customer_id' => (sk_user_exists($user_id)) ? $user_id : null,
        'user_id' => (sk_user_exists($user_id)) ? $user_id : null,
        // 'status' => null,
        // 'customer_note' => null,
        // 'parent' => null,
        // 'created_via' => null,
    ));
    //add products in cart by looping the items
    foreach ($cart['items'] as $item) {
        $order->add_product(get_product($item['ID']), $item['quantity']);
    }
    //add coupon if any
    if ($cart['has_coupon']) {
        $order->apply_coupon($cart['coupon']);
    }

    

    //set address
    $customer = new WC_Customer($user_id);
    $address = array(
        'first_name' => $customer->get_first_name(),
        'last_name'  => $customer->get_last_name(),
        'company'    => $customer->get_shipping_company(),
        'email'      => $customer->get_billing_email(),
        'phone'      => $customer->get_billing_phone(),
        'address_1'  => $customer->get_shipping_address_1(),
        'address_2'  => $customer->get_shipping_address_2(),
        'city'       => $customer->get_shipping_city(),
        'state'      => $customer->get_shipping_state(),
        'postcode'   => $customer->get_shipping_postcode(),
        'country'    => $customer->get_shipping_country()
    );

    if (!is_null($billing_address)) {
        $order->set_address($billing_address, 'billing');
    } else { //use profiel shipping address
        $order->set_address($address, 'billing');
    }

    if (!is_null($shipping_address)) {
        $order->set_address($shipping_address, 'shipping');
    } else { //use the profile shipping address
        $order->set_address($address, 'shipping');
    }

    // set payment gateways
    $payment_gateways = WC()->payment_gateways->payment_gateways();
    if (!is_null($payment_method)) $order->set_payment_method($payment_gateways[$payment_method]);

    //set shipping cost
    $item = new WC_Order_Item_Shipping();
    $method_title = "Flat rate"; //default
    if ($cart['shipping_method'] == "local_pickup") $method_title = "Local pickup";
    if ($cart['shipping_method'] == "free_shipping") $method_title = "Free Shipping";
    if ($cart['shipping_method'] == "by_printful") $method_title = "Printful Shipping";
    $item->set_method_title($method_title);
    // $item->set_method_id( "amazon_flat_rate:17" );
    $item->set_total($cart['shipping_cost']);
    $order->add_item($item);


     //deduct reward discount if applied
     if ($cart['apply_reward']) {
        global $wc_points_rewards;

        $discount_code = sprintf( 'wc_points_redemption_%s_%s', $order->get_user_id(), date( 'Y_m_d_h_i', current_time( 'timestamp' ) ) );
        $discount_amount = $cart['reward_discount'];
        if ($discount_amount > 0) {
            $points_redeemed = WC_Points_Rewards_Manager::calculate_points_for_discount($discount_amount);
            WC_Points_Rewards_Manager::decrease_points($order->get_user_id(), $points_redeemed, 'order-redeem', array('discount_code' => $discount_code, 'discount_amount' => $discount_amount), $order->get_id());
            update_post_meta($order->get_id(), '_wc_points_redeemed', $points_redeemed);
            sk_wc_order_add_discount($order->get_id(), __("Points Redeemed"), $discount_amount);
            //order note
            $order->add_order_note(sprintf(__('%d %s redeemed for a %s discount.', 'wc_points_rewards'), $points_redeemed, $wc_points_rewards->get_points_label($points_redeemed), wc_price($discount_amount)));
            sk_remove_reward($user_id);
        }
    }


    $order->calculate_totals();
    //PAYMENT STATUS
    //wc-processing - for payed but order not completed
    //wc-completed - for payed and completed
    //wc-pending - for payment pending
    //wc-on-hold - for holding order without processing the payment
    //wc-cancelled - for order cancelled
    //wc-refunded - for order to refund
    //wc-failed - for failed order
    $order->update_status($status, $order_note, true);
    if ($order->save()) {
        if (isset($_GET['clear_cart'])) sk_delete_user_cart($user_id);
        
        $checkout_url =  str_replace('localhost', '192.168.43.11', $order->get_checkout_payment_url());
        $checkout_url .= "&sk-web-payment=1&sk-user-checkout=" . $user_id;
        wp_redirect($checkout_url);
        // header("Location: " . $checkout_url);
    } else {
        $is_error = true;
        $error_msg = "Unable to create order!";
        the_page();
        return;
    }

}



?>



<?php function the_page()
{
    global $is_error, $error_msg, $error_title; 
    
    ?>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php $error_title; ?></title>
        <style>
            .alert {
                padding: 20px;
                background-color: #f44336;
                color: white;
            }

            .closebtn {
                margin-left: 15px;
                color: white;
                font-weight: bold;
                float: right;
                font-size: 22px;
                line-height: 20px;
                cursor: pointer;
                transition: 0.3s;
            }

            .closebtn:hover {
                color: black;
            }
        </style>

    </head>

    <body>

        <?php if ($is_error) { ?>
            <div class="alert">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <strong>Opps!</strong> <?php echo $error_msg; ?>
            </div>
        <?php } ?>

    </body>

    </html>

<?php } ?>