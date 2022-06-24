<?php 
register_rest_route(SKYE_API_NAMESPACE_V2, '/update-user-shipping-address/(?P<user>.*?)', array(
    'methods' => 'POST',
    'permission_callback' => 'sk_api_security_check',
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
            if (isset($data['email'])) { $customer->set_billing_email($data['email']); $customer->set_email($data['email']); }
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
    'permission_callback' => 'sk_api_security_check',
    'callback' => function($data) {
        
        $user_id = $data['user_id'];
        $method = $data['shipping_method'];

        sk_change_cart_shipping_method_v2($user_id, $method);
        
        return json_decode(sk_get_cart_value($user_id), true);
    }
));

register_rest_route(SKYE_API_NAMESPACE_V2, '/update-cart-shipping/(?P<user>.*?)', array(
    'methods' => 'POST',
    'permission_callback' => 'sk_api_security_check',
    'callback' => function ($data) {

        $user_id = $data['user']; //could be the hash id
        
           
            $changed = false;

            //update the user cart shipping and calculate cost
            if (isset($data['selected_country']) && isset($data['selected_state'])) { //use country and state code
                //update user cart shipping
                $changed = sk_update_cart_shipping_v2($user_id, $data['selected_country'], 
                    $data['selected_state'], 
                    (isset($data['postcode'])) ? $data['postcode'] : "", 
                    (isset($data['shipping_provider'])) ? $data['shipping_provider'] : "woocommerce",
                    (isset($data['shipping_provider_cost'])) ? $data['shipping_provider_cost'] : 0
                );
            } else { //use name of the shipping country and state
                $changed = sk_update_cart_shipping_by_name_v2(
                    $user_id,
                    (isset($data['country'])) ? $data['country'] : "",
                    (isset($data['state'])) ? $data['state'] : "",
                    (isset($data['postcode'])) ? $data['postcode'] : "",
                    (isset($data['shipping_provider'])) ? $data['shipping_provider'] : "woocommerce",
                    (isset($data['shipping_provider_cost'])) ? $data['shipping_provider_cost'] : 0
                );
            }

        
            
            $array = array();

            if ($changed) {
                $array['code'] = "saved";
            } else {
                $array['code'] = "not-saved";
            }


           


            return $array;

    }
));

//create order page
register_rest_route( SKYE_API_NAMESPACE_V2, '/create-order/(?P<user>.*?)', array(
    'methods' => 'POST',
        'permission_callback' => 'sk_api_security_check',
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

        $_POST['user'] = $user_id; //to be able to access user id from hook
        $_GET['user'] = $user_id; //to be able to access user id from hook
        $_REQUEST['user'] = $user_id; //to be able to access user id from hook

        $_POST['from_create_order_api'] = $user_id; //to be able to know from the hook that adding to cart is from create order route
        $_GET['from_create_order_api'] = $user_id; //to be able to know from the hook that adding to cart is from create order route
        $_REQUEST['from_create_order_api'] = $user_id; //to be able to know from the hook that adding to cart is from create order route
        
        $allow_guest = (isset($data['allow_guest']));
        $status = (isset($data['status'])) ? sanitize_text_field($data['status']) : 'wc-pending';
        $order_note = (isset($data['order_note'])) ? sanitize_text_field($data['order_note']) : 'Ordered from API';
        $payment_method = (isset($data['payment_method'])) ? $data['payment_method'] : null;
        $remove_tax = isset($data["remove_tax"]);
    
        $return_array = array();
        
        $billing_address = array(
            'first_name' => (isset($data['billing_first_name'])) ? $data['billing_first_name']: null,
            'last_name'  => (isset($data['billing_last_name'])) ? $data['billing_last_name']: null,
            'company'    => (isset($data['billing_company'])) ? $data['billing_company']: null,
            'email'      => (isset($data['billing_email'])) ? $data['billing_email']: null,
            'phone'      => (isset($data['billing_phone'])) ? $data['billing_phone']: null,
            'address_1'  => (isset($data['billing_address_1'])) ? $data['billing_address_1']: null,
            'address_2'  => (isset($data['billing_address_2'])) ? $data['billing_address_2']: null, 
            'city'       => (isset($data['billing_city'])) ? $data['billing_city']: null,
            'state'      => (isset($data['billing_state'])) ? $data['billing_state']: null,
            'postcode'   => (isset($data['billing_postcode'])) ? $data['billing_postcode']: null,
            'country'    => (isset($data['billing_country'])) ? $data['billing_country']: null,
        );
        $shipping_address = array(
            'first_name' => (isset($data['shipping_first_name'])) ? $data['shipping_first_name']: null,
            'last_name'  => (isset($data['shipping_last_name'])) ? $data['shipping_last_name']: null,
            'company'    => (isset($data['shipping_company'])) ? $data['shipping_company']: null,
            'email'      => (isset($data['shipping_email'])) ? $data['shipping_email']: null,
            'phone'      => (isset($data['shipping_phone'])) ? $data['shipping_phone']: null,
            'address_1'  => (isset($data['shipping_address_1'])) ? $data['shipping_address_1']: null,
            'address_2'  => (isset($data['shipping_address_2'])) ? $data['shipping_address_2']: null, 
            'city'       => (isset($data['shipping_city'])) ? $data['shipping_city']: null,
            'state'      => (isset($data['shipping_state'])) ? $data['shipping_state']: null,
            'postcode'   => (isset($data['shipping_postcode'])) ? $data['shipping_postcode']: null,
            'country'    => (isset($data['shipping_country'])) ? $data['shipping_country']: null,
        );
        
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
            if ($remove_tax) {
				$woocommerce->cart->add_to_cart($item['ID'], $item['quantity'], 0, array(), array( 'sk_cart_remove_tax' => true));
			} else {
				$woocommerce->cart->add_to_cart($item['ID'], $item['quantity']);
			}
        }

       

        $address = array();
            
        if (sk_user_exists($user_id)) {
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
        }

        //twist shipping and billing address
        if (!is_null($billing_address['email'])) { //use billing for shipping if shipping not set - using email
            if (is_null($shipping_address['email'])) {
                $shipping_address = $billing_address;
            }
        }
        if (!is_null($shipping_address['email'])) { //use shipping for billing if billing not set - using email
            if (is_null($billing_address['email'])) {
                $billing_address = $shipping_address;
            }
        }

        //BILLING EMAIL - required
        $billing_email = null;
        if (!is_null($billing_address['email'])) {
            $billing_email = isset($billing_address['email']) ? $billing_address['email'] : null;
        } else {
            $billing_email = isset($address['email']) ? $address['email'] : null;
        }

        // return $billing_address;

        //CREATE ORDER - FROM CART
        $checkout = WC()->checkout();
        $order_id = $checkout->create_order(array(
            'billing_email' => $billing_email,
            'payment_method' => $payment_method
        ));

        $order = wc_get_order($order_id);

        //ADD USER TO THE ORDER
        if (sk_user_exists($user_id)) {
            update_post_meta($order->id, '_customer_user', (sk_user_exists($user_id)) ? $user_id : null);
            $order->set_customer_id((sk_user_exists($user_id)) ? $user_id : null);
        }
        
        //add coupon if any
        if ($cart['has_coupon']) {
            $order->apply_coupon($cart['coupon']);
        }
        
        //set address
        if (!is_null($billing_address['email'])) {
            $order->set_address($billing_address, 'billing');
        } else { //use profile shipping address
            $order->set_address( $address, 'billing' );
        }

        if (!is_null($shipping_address['email'])) {
            $order->set_address($shipping_address, 'shipping');
        } else { //use the profile shipping address
            $order->set_address( $address, 'shipping' );

        }

        // set payment gateways
        $payment_gateways = WC()->payment_gateways->payment_gateways();
        if (!is_null($payment_method)) $order->set_payment_method($payment_gateways[$payment_method]);

        //####### remove shipping if already been added ########
        $ship_items    = (array) $order->get_items('shipping');
        if ( sizeof( $ship_items ) > 0 ) {
            // Loop through shipping items
            foreach ( $ship_items as $item_id => $item ) {
                $order->remove_item( $item_id );
            }
        }
        //########## remove shipping if already been added #######
        
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
        if ($cart['apply_reward'] && sk_user_exists($user_id)) {
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
    'permission_callback' => 'sk_api_security_check',
    'callback' => function ($data) {

       ////
        
    }
));

//cart info page
register_rest_route( SKYE_API_NAMESPACE_V2, '/cart/(?P<user>.*?)', array(
    'methods' => 'GET',
        'permission_callback' => 'sk_api_security_check',
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
        

        $cart_table = $wpdb->prefix . "skye_carts";
        $user_id = $data['user']; //user real ID or hash for unknown user
        
        if (!sk_user_cart_exists($user_id)) return array("user-cart-not-exists");

        $return_array = array();

        $return_array = json_decode(sk_get_cart_value($user_id), true);
        // $coupon = new WC_Coupon("44gb97sb");
        // return $coupon->get_discount_type();
        // return $coupon->get_amount();

        //RESET REWARDS CALCULATIONS
    if (class_exists("WC_Points_Rewards_Manager")) {
        $current_user_points = WC_Points_Rewards_Manager::get_users_points($user_id); //points
        $current_user_points_value = WC_Points_Rewards_Manager::get_users_points_value($user_id); //price
        $return_array['user_points'] = $current_user_points;
        $return_array['user_points_value'] = $current_user_points_value; //in price
        //calculate reward discount
        $cart_subtotal = $return_array['subtotal']; //total items price - $subtotal has been declared at the top
        $cart_subtotal_points = WC_Points_Rewards_Manager::calculate_points_for_discount($cart_subtotal); //there is a difference btw calculate_poitns() and calculate_points_for_discount
        if ($current_user_points >= $cart_subtotal_points) {
            $return_array['reward_discount_points'] = $cart_subtotal_points;
            $return_array['reward_discount'] = WC_Points_Rewards_Manager::calculate_points_value($cart_subtotal_points);
        } else {
            $return_array['reward_discount_points'] = $current_user_points;
            $return_array['reward_discount'] = $current_user_points_value;
        }
        //update in database
        $wpdb->update($cart_table, array(
            'cart_value' => json_encode($return_array),
        ), array(
            'user' => $user_id
        ));
    }

    if (class_exists('VTPRD_Controller')) { //whic means if the plugin is installed
        foreach($return_array['items'] as $item) {
            $woocommerce->cart->add_to_cart($item['ID'], $item['quantity']);
        }

        //global $vtprd_cart
        global $vtprd_cart;
        
        //loop cart items and fidn out which product rules is applied to
        foreach($vtprd_cart->cart_items as $item) {
            
            if ($item->yousave_total_amt > 0 && $item->discount_price !== "") { //rules applied
                // $save_amount = $item->yousave_total_amt;
                //now find this item in db cart json
                
                foreach($return_array['items'] as $index => $json_item) {
                        if ($json_item["ID"] == $item->product_id) { //found

                            $return_array['items'][$index]['subtotal'] = $item->discount_price;
                            $return_array['items'][$index]['rules_applied'] = true;

                        }
                }
            } else {
                //now find this item in db cart json, if previous has rules applied
                foreach($return_array['items'] as $index => $json_item) {
                        if ($json_item["rules_applied"] && $json_item['ID'] == $item->product_id) { //found
                            $return_array['items'][$index]['subtotal'] = $json_item["price"] * $json_item['quantity'];
                            $return_array['items'][$index]['rules_applied'] = false;

                        }
                }
            } 

        }



        $return_array['subtotal'] = sk_cart_subtotal($return_array['items']); //re-calculate subtotal
        $return_array['total'] = ($return_array['subtotal'] + $return_array['shipping_cost']) - $return_array['coupon_discount'];

        //update in database
        $wpdb->update($cart_table, array(
            'cart_value' => json_encode($return_array),
        ), array(
            'user' => $user_id
        ));
    }
    


            $update_db = false;
            //check for out of stock products
            foreach ($return_array['items'] as $index => $item) {
                $product = wc_get_product($item['ID']);
                if ($product->managing_stock() && $item['quantity'] > $product->get_stock_quantity()) {
                    $return_array['items'][$index]['quantity'] = $product->get_stock_quantity();
                    $return_array['items'][$index]['subtotal'] = $product->get_price() * $product->get_stock_quantity();
                    $update_db = true;
                }
            }

            if ($update_db) {
                $wpdb->update($cart_table, array(
                    'cart_value' => json_encode($return_array),
                ), array(
                    'user' => $user_id
                ));
            }

            //we don't need error message from perivous cart error
            if (isset($return_array['code'])) unset($return_array['code']);
            if (isset($return_array['msg'])) unset($return_array['msg']);
    
    
        
        return $return_array;
    }
));