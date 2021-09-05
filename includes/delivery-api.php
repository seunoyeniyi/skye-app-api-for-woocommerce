<?php

//confirm driver login
register_rest_route( SKYE_API_NAMESPACE_V1, '/driver-authenticate', array(
    'methods' => 'POST',
    'callback' => function($data) {
        $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
        $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
        $password = (isset($data['password'])) ? $data['password'] : null;
  
        
        $auth = wp_authenticate( (!is_null($username)) ? $username : $email, $password);
        
        if (!isset($auth->errors)) {
            $roles = $auth->roles;
            if (in_array("skye_delivery_driver", $roles)) { // is a driver
                return $auth;
            } else {
                return array(
                    'code' => 'user-not-a-driver',
                    'message' => 'This user is not a driver',
                    'data' => null
                );
            }
        }
        return $auth;
    }
));

//register driver
register_rest_route( SKYE_API_NAMESPACE_V1, '/register-driver', array(
    'methods' => 'POST',
    'callback' => function($data) {
        $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
        $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
        $password = (isset($data['password'])) ? $data['password'] : null;
        $replace_cart_user = (isset($data['replace_cart_user'])) ? $data['replace_cart_user'] : null;
        $tron_wallet = (isset($data['tron_wallet'])) ? $data['tron_wallet'] : null;
        
        
        $user_id = wp_create_user($username, $password, $email);
        if (!is_null($tron_wallet)) { //add tron wallet to user profile
            update_user_meta( $user_id, 'tron_wallet', $tron_wallet );
        }



        if (is_numeric($user_id)) {
            if (!is_null($replace_cart_user)) {
                        if (sk_user_cart_exists($replace_cart_user))
                            sk_change_cart_user_id($replace_cart_user, $user_id);
            }
            $u = new WP_User($user_id);
            $u->set_role( 'skye_delivery_driver' );

            return sk_get_user_info($user_id);
        } else {
            return $user_id;
        }
    
        
    }
));

//driver orders
register_rest_route( SKYE_API_NAMESPACE_V1, '/driver-orders/(?P<user>.*?)', array(
    'methods' => 'GET',
    'callback' => function($data) {
        $user_id = $data['user'];
        $default_status = array('wc-processing', 'wc-on-hold', 'wc-completed');
        $status = (isset($data['status'])) ? $data['status'] : $default_status;
        $date_completed = (isset($data['date_completed'])) ? $data['date_completed'] : null; //value eg: 2018-10-01...2018-10-10
        $paged = isset($data['paged']) ? $data['paged'] : 1;
        $post_per_page = isset($data['per_page']) ? $data['per_page'] : 50;

        $args = array(
            'limit' => $post_per_page,
            // 'orderby' => 'date',
            // 'order' => 'DESC',
            // 'customer_id' => $user_id,
            // 'user_id' => $user_id,
            'return' => 'ids',
            'status' => $status,
            // 'type' => //'shop_order' or 'shop_order_refund'
            // 'created_via' => 'checkout',
            'paged' => $paged,
            'paginate' => true,
            // 'payment_method' => 'cheque',
            // 'date_paid' => '2016-02-2',
            // 'date_created' => '2016-02-2',
            'date_completed' => $date_completed,
            'meta_key' => 'skye_order_driver',
            'meta_value' => $user_id,            
            'meta_compare' => 'EXISTS'
        );
        if ($status == "new" || $status == "picked" || $status == "pending") {
            $args['status'] = array('wc-processing', 'wc-on-hold');  
        }


        $query = new WC_Order_Query($args);

        $orders = $query->get_orders();
        $order_array = array();
        $order_array['orders'] = array();
        $loop_count = 0;
        foreach($orders->orders as $order_id) { //i use $orders->orders bcos paginate is set true
            if ($status == "new") { 
                $order = wc_get_order($order_id);
                if ($order->get_meta("skye_order_delivery_status") != "picked") {
                    $order_array['orders'][] = sk_order_info($order_id);
                    $loop_count++;
                }
                continue;
            } else if ($status == "picked" || $status == "pending") {
                $order = wc_get_order($order_id);
                if ($order->get_meta("skye_order_delivery_status") == "picked") {
                    $order_array['orders'][] = sk_order_info($order_id);
                    $loop_count++;
                }
                continue;
            }
            $order_array['orders'][] = sk_order_info($order_id);
            $loop_count++;
        }
        
        $order_array['total'] = $orders->total;
        $order_array['loop_total'] = $loop_count;
        $order_array['max_num_pages'] = $orders->max_num_pages;

        return $order_array;
    }
));

//update order
register_rest_route( SKYE_API_NAMESPACE_V1, '/update-driver-order/(?P<order_id>.*?)/(?P<user_id>.*?)', array(
    'methods' => 'POST',
    'callback' => function($data) {
        $user_id = $data['user_id'];
        $order_id  = $data['order_id'];
        $status  = isset($data['status']) ? sanitize_text_field($data['status']) : null;
        $return_array = array();
    
        $order = new WC_Order($order_id);

        //order exists
        $return_array['order_exists'] = sk_order_exists($order_id);
        if (!sk_order_exists($order_id))
            return $return_array;
        //user exists
        $return_array['user_exists'] = sk_user_exists($user_id);
        if (!sk_user_exists($user_id))
            return $return_array;
        //is user deriver
        $return_array['is_driver'] = sk_is_user_driver($user_id);
        if (!sk_is_user_driver($user_id))
            return $return_array;
        //if this order is assign to this driver
        if ($order->get_meta("skye_order_driver") != $user_id) {
            $return_array['assigned_to_this_driver'] = false;
            return $return_array;
        } else {
            $return_array['assigned_to_this_driver'] = true;
        }
        //UPDATE ORDER
        
        //update status
        if (!is_null($status)) {
            if ($status == "picked") {
                $order->update_meta_data('skye_order_delivery_status', 'picked');
                $order->save();
            } else {
                $order->update_status($status);
            }
            $return_array['order_status_updated'] = true;
        }

        //order info
        $return_array['order_info'] = sk_order_info($order_id);

        return $return_array;
    }
));

//update current driver location
register_rest_route( SKYE_API_NAMESPACE_V1, '/update-driver-location/(?P<user>.*?)', array(
    'methods' => 'POST',
    'callback' => function($data) {
        $user_id = $data['user'];
        $address = isset($data['address']) ? $data['address'] : '';
        $latitude = isset($data['latitude']) ? $data['latitude'] : '';
        $longitude = isset($data['longitude']) ? $data['longitude'] : '';

        $return = array();

        $location = array(
            'address' => $address,
            'latitude' => $latitude,
            'longitude' => $longitude
        );
        $location_encode = json_encode($location);

        $args = array(
            'return' => 'ids',
            'status' => array('wc-processing', 'wc-on-hold'),
            'meta_key' => 'skye_order_driver',
            'meta_value' => $user_id,       
            'meta_compare' => 'EXISTS'
        );

        $query = new WC_Order_Query($args);
        $orders = $query->get_orders();
        $updated_count = 0;
        foreach($orders as $order_id) { //use $orders only since paginate is not set as true
            $order = wc_get_order($order_id);
            if ($order->get_meta("skye_order_delivery_status") == "picked") {
                $order->update_meta_data("skye_order_driver_location", $location_encode);
                $order->save();
                $updated_count++;
            }
        }
        if ($updated_count > 0) {
            $return['code'] = 'updated';
            $return['location'] = $location;
        } else {
            $return['code'] = 'no-order';
            $return['location'] = $location;
        }
        return $return;
    }
));