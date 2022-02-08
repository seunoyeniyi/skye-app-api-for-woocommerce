<?php 
register_rest_route(SKYE_API_NAMESPACE_V2, '/update-user-shipping-address/(?P<user>.*?)', array(
    'methods' => 'POST',
    'permission_callback' => function () {
        return true;
    },
    'callback' => function ($data) {

        $user_id = $data['user'];
            $customer = new WC_Customer($user_id);
            if (isset($data['first_name'])) $customer->set_shipping_first_name($data['first_name']);
            if (isset($data['first_name'])) update_user_meta( $user_id, "first_name", $data['first_name']);
            if (isset($data['last_name'])) $customer->set_shipping_last_name($data['last_name']);
            if (isset($data['last_name'])) update_user_meta( $user_id, "last_name", $data['last_name']);
            if (isset($data['company'])) $customer->set_shipping_company($data['company']);
            if (isset($data['country'])) $customer->set_shipping_country($data['country']);
            if (isset($data['state'])) $customer->set_shipping_state($data['state']);
            if (isset($data['postcode'])) $customer->set_shipping_postcode($data['postcode']);
            if (isset($data['city'])) $customer->set_shipping_city($data['city']);
            if (isset($data['address_1'])) $customer->set_shipping_address($data['address_1']);
            if (isset($data['address_2'])) $customer->set_shipping_address_2($data['address_2']);
            //since there is no method to set phone and email, let set it into billing
            if (isset($data['email'])) $customer->set_billing_email($data['email']);
            if (isset($data['phone'])) $customer->set_billing_phone($data['phone']);

            //update the user cart shipping and calculate cost
            if (isset($data['selected_country']) && isset($data['selected_state'])) { //use country and state code
                //update user cart shipping
                sk_update_cart_shipping_v2($user_id, $data['selected_country'], 
                    $data['selected_state'], 
                    (isset($data['postcode'])) ? $data['postcode'] : "", 
                    (isset($data['shipping_provider'])) ? $data['shipping_provider'] : "woocommerce",
                    (isset($data['shipping_provider_cost'])) ? $data['shipping_provider_cost'] : 0
                );
            } else { //use name of the shipping country and state
                sk_update_cart_shipping_by_name_v2(
                    $user_id,
                    (isset($data['country'])) ? $data['country'] : "",
                    (isset($data['state'])) ? $data['state'] : "",
                    (isset($data['postcode'])) ? $data['postcode'] : "",
                    (isset($data['shipping_provider'])) ? $data['shipping_provider'] : "woocommerce",
                    (isset($data['shipping_provider_cost'])) ? $data['shipping_provider_cost'] : 0
                );
            }

            //update date of birth, gender and picture
            if (isset($data["gender"])) { //gender
                update_user_meta( $user_id, 'gender_field', sanitize_text_field($data['gender']) );
            }
            if (isset($data["birthday"])) { //date of birth
                update_user_meta( $user_id, 'birthday_field', sanitize_text_field($data['birthday']) );
            }
            if (isset($data['other_phone'])) {
                update_user_meta( $user_id, 'other_phone_field', sanitize_text_field($data['other_phone']) );
            }
            if (isset($_FILES["image"])) { //profile image
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                require_once( ABSPATH . 'wp-admin/includes/media.php' );

                $attachment_id = media_handle_upload( 'image', 0 );

                if ( is_wp_error( $attachment_id ) ) {
                    update_user_meta( $user_id, 'image', $_FILES['image'] . ": " . $attachment_id->get_error_message() );
                } else {
                    update_user_meta( $user_id, 'image', $attachment_id );
                }
            }
            
            $array = array();
            if ($customer->save()) {
                $array['code'] = "saved";
            } else {
                $array['code'] = "not-saved";
            }


            $array['data'] = sk_get_user_shipping_address($user_id);


            return $array;

    }
));
register_rest_route(SKYE_API_NAMESPACE_V2, '/change-cart-shipping-method/(?P<user_id>.*?)/(?P<shipping_method>.*?)', array(
    'methods' => 'POST',
    'permission_callback' => function() {return true; },
    'callback' => function($data) {
        
        $user_id = $data['user_id'];
        $method = $data['shipping_method'];

        sk_change_cart_shipping_method_v2($user_id, $method);
        
        return json_decode(sk_get_cart_value($user_id), true);
    }
));

//create order page
register_rest_route( SKYE_API_NAMESPACE_V2, '/create-order/(?P<user>.*?)', array(
    'methods' => 'POST',
        'permission_callback' => function() {return true; },
    'callback' => function($data) {
        require_once(WC_ABSPATH . 'includes/wc-cart-functions.php');
        require_once(WC_ABSPATH . 'includes/wc-notice-functions.php');

        global $wpdb, $woocommerce;

        $woocommerce = WC();
        $woocommerce->session = new WC_Session_Handler();
        $woocommerce->session->init();
        $woocommerce->customer = new WC_Customer();
        $woocommerce->cart = new WC_Cart();

        $woocommerce->cart->empty_cart();


        $user_id = $data['user'];
        $allow_guest = (isset($data['allow_guest']));
        $status = (isset($data['status'])) ? sanitize_text_field($data['status']) : 'wc-pending';
        $order_note = (isset($data['order_note'])) ? sanitize_text_field($data['order_note']) : 'Ordered from API';
        $payment_method = (isset($data['payment_method'])) ? $data['payment_method'] : null;
    
        $return_array = array();
        //to add address to url: url?billing_address%Bfirst_name%5D=SEUN&billing_address%5Blast_name%5D=OYENIYI....
        //%5B is to select variable array with they key eg: address%5Bfirst_name
        //%5D is to add the value to the key eg:address%5Bfirst_name%5D=SEUN
        $billing_address = (isset($data['billing_address'])) ? $data['billing_address']: null;
        $shipping_address = (isset($data['shipping_address'])) ? $data['shipping_address']: null;

        //cart exists?
        $return_array['cart_exists'] = sk_user_cart_exists($user_id);
        if (!sk_user_cart_exists($user_id))
            return $return_array;
        //cart empty?
        $cart = json_decode(sk_get_cart_value($user_id), true);
        $return_array['cart_empty'] = is_null($cart) ? true : $cart['is_empty'];
        if ($return_array['cart_empty'])
            return $return_array;
        //user exists or allow guest is set?
        $return_array['user_exists'] = sk_user_exists($user_id);
        if (!sk_user_exists($user_id) && !$allow_guest)
            return $return_array;
        


         //CREATE CART FROM WITH ITEMS
         foreach ($cart['items'] as $item) {
             if (isset($item['wooco_ids'])) {
                 $_POST['wooco_ids'] = $item['wooco_ids'];
             } else {
                 if (isset( $_POST['wooco_ids'])) { unset($_POST['wooco_ids']); }
             }
            $woocommerce->cart->add_to_cart($item['ID']);
        }
            
        //GET CUSTOMER
        $customer = new WC_Customer( $user_id );
        
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

        //BILLING EMAIL - required
            $billing_email = null;
        if (!is_null($billing_address)) {
            $billing_email = $billing_address['email'];
        } else {
            $billing_email = $address['email'];
        }

        //CREATE ORDER - FROM CART
        $checkout = WC()->checkout();
        $order_id = $checkout->create_order(array(
            'billing_email' => $billing_email,
            'payment_method' => $payment_method
        ));

        $order = wc_get_order($order_id);

        //ADD USER TO THE ORDER
        update_post_meta($order->id, '_customer_user', (sk_user_exists($user_id)) ? $user_id : null);
        $order->set_customer_id((sk_user_exists($user_id)) ? $user_id : null);
        
        //add coupon if any
        if ($cart['has_coupon']) {
            $order->apply_coupon($cart['coupon']);
        }
        
        //set address
        
        if (!is_null($billing_address)) {
            $order->set_address($billing_address, 'billing');
        } else { //use profile shipping address
            $order->set_address( $address, 'billing' );
        }

        if (!is_null($shipping_address)) {
            $order->set_address($shipping_address, 'shipping');
        } else { //use the profile shipping address
            $order->set_address( $address, 'shipping' );

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

        foreach ($cart['shipping_methods'] as $method) {
            if ($method['rate_id'] == $cart['shipping_method']) {
                $method_title = $method['title'];
            }
        }

        $item->set_method_title($method_title);
        // $item->set_method_id( "amazon_flat_rate:17" );
        $item->set_total( $cart['shipping_cost'] );
        // $item->calculate_taxes(array(
        //     'country' => $country_code,
        //     'state' => '', // Can be set (optional)
        //     'postcode' => '', // Can be set (optional)
        //     'city' => '', // Can be set (optional)
        // ));
        $order->add_item( $item );

        //deduct reward discount if applied
        if ($cart['apply_reward']) {
            global $wc_points_rewards;

            $discount_code = sprintf('wc_points_redemption_%s_%s', $order->get_user_id(), date('Y_m_d_h_i', current_time('timestamp')));
            $discount_amount = $cart['reward_discount'];
            if ($discount_amount > 0) {
                $points_redeemed = WC_Points_Rewards_Manager::calculate_points_for_discount($discount_amount);
                WC_Points_Rewards_Manager::decrease_points($order->get_user_id(), $points_redeemed, 'order-redeem', array('discount_code' => $discount_code, 'discount_amount' => $discount_amount), $order->get_id());
                update_post_meta($order->get_id(), '_wc_points_redeemed', $points_redeemed);
                sk_wc_order_add_discount($order, __("Points Redeemed"), $discount_amount);
                //order note
                $order->add_order_note(sprintf(__('%d %s redeemed for a %s discount.', 'wc_points_rewards'), $points_redeemed, $wc_points_rewards->get_points_label($points_redeemed), wc_price($discount_amount)));
                sk_remove_reward($user_id);
            }
        }


        //tell where it was ordered from
        $order->update_meta_data( 'ordered_from', 'api' );
        
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
        $order->save();

        //clear cart if set
        if (isset($data['clear_cart'])) sk_delete_user_cart($user_id);

        $return_array['order_created'] = true;
        $return_array['info'] = sk_order_info($order->get_id());

        return $return_array;
    }
));




register_rest_route(SKYE_API_NAMESPACE_V2, '/test', array(
    'methods' => 'GET',
    'permission_callback' => function () {
        return true;
    },
    'callback' => function ($data) {

       ////
        
    }
));