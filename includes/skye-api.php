<?php
add_action( 'rest_api_init', function() {
    //SITE INFO page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/site-info', array(
            'methods' => 'GET',
            'callback' => function($data) {
                $arr = array();
                $arr['name'] = get_bloginfo('name'); // – Site title (set in Settings > General)
                $arr['description'] = get_bloginfo('description'); // – Site tagline (set in Settings > General)
                $arr['wpurl'] = get_bloginfo('wpurl'); // – The WordPress address (URL) (set in Settings > General)
                $arr['url'] = get_bloginfo('url'); // – The Site address (URL) (set in Settings > General)
                $arr['admin_email'] = get_bloginfo('admin_email'); // – Admin email (set in Settings > General)
                $arr['charset'] = get_bloginfo('charset'); // – The "Encoding for pages and feeds" (set in Settings > Reading)
                $arr['version'] = get_bloginfo('version'); // – The current WordPress version
                $arr['html_type'] = get_bloginfo('html_type'); // – The content-type (default: "text/html"). Themes and plugins can override the default value using the ‘pre_option_html_type’ filter
                $arr['text_direction'] = get_bloginfo('text_direction'); // – The text direction determined by the site’s language. is_rtl() should be used instead
                $arr['language'] = get_bloginfo('language'); // – Language code for the current site
                $arr['stylesheet_url'] = get_bloginfo('stylesheet_url'); // – URL to the stylesheet for the active theme. An active child theme will take precedence over this value
                $arr['stylesheet_directory'] = get_bloginfo('stylesheet_directory'); // – Directory path for the active theme. An active child theme will take precedence over this value
                $arr['template_url'] = get_bloginfo('template_url'); // / ‘template_directory’ – URL of the active theme’s directory. An active child theme will NOT take precedence over this value
                $arr['pingback_url'] = get_bloginfo('pingback_url'); // – The pingback XML-RPC file URL (xmlrpc.php)
                $arr['atom_url'] = get_bloginfo('atom_url'); // – The Atom feed URL (/feed/atom)
                $arr['rdf_url'] = get_bloginfo('rdf_url'); // – The RDF/RSS 1.0 feed URL (/feed/rdf)
                $arr['rss_url'] = get_bloginfo('rss_url'); // – The RSS 0.92 feed URL (/feed/rss)
                $arr['rss2_url'] = get_bloginfo('rss2_url'); // – The RSS 2.0 feed URL (/feed)
                $arr['comments_atom_url'] = get_bloginfo('comments_atom_url'); // – The comments Atom feed URL (/comments/feed)
                $arr['comments_rss2_url'] = get_bloginfo('comments_rss2_url'); // – The comments RSS 2.0 feed URL (/comments/feed)
                return $arr;
            }
    ));
    //register user
    register_rest_route( SKYE_API_NAMESPACE_V1, '/register', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
            $password = (isset($data['password'])) ? $data['password'] : null;

            //return ID of the newly registerd user
            return wp_create_user($username, $password, $email);
        }
    ));
    //confirm user login
    register_rest_route( SKYE_API_NAMESPACE_V1, '/authenticate', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
            $password = (isset($data['password'])) ? $data['password'] : null;

            return wp_authenticate( (!is_null($username)) ? $username : $email, $password);
        }
    ));
    //update user billing address
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-billing-address/(?P<user>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user'];
            
            $address_keys = array(
                'first_name',
                'last_name',
                 'company',
                 'email',
                 'phone',
                 'address_1', //street address
                 'address_2', //appartment
                 'city',
                 'state',
                 'postcode',
                 'country'
            );
            foreach ($address_keys as $key) {
                if (isset($data[$key]))
                    update_user_meta( $user_id, 'billing_' . $key, sanitize_text_field($data[$key]));
            }

            return array(
                'code' => 'data-submitted',
                'message' => "Data submitted - check if updated",
                'data' => null
            );
        }
    ));
    //update user shipping address
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-shipping-address/(?P<user>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user'];
            
            $address_keys = array(
                'first_name',
                'last_name',
                 'company',
                 'email',
                 'phone',
                 'address_1', //street address
                 'address_2', //appartment
                 'city',
                 'state',
                 'postcode',
                 'country'
            );
            foreach ($address_keys as $key) {
                if (isset($data[$key]))
                    update_user_meta( $user_id, 'shipping_' . $key, sanitize_text_field($data[$key]));
            }

            return array(
                'code' => 'data-submitted',
                'message' => "Data submitted - check if updated",
                'data' => null
            );
        }
    ));
    //get user info
    register_rest_route( SKYE_API_NAMESPACE_V1, '/user-info/(?P<user>.*?)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $user_id = $data['user'];
            $user = get_userdata($user_id);
            return $user;
        }
    ));
    //update user info
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-user-info/(?P<user>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user'];
            //keys allowed to change
            $user_keys = array(
                'display_name',
                'first_name',
                'last_name',
                'description'
            );
            $array_to_change = array(
                'ID' => $user_id
            );
            foreach($user_keys as $key) {
                if (isset($data[$key]))
                    $array_to_change[$key] = sanitize_text_field($data[$key]);
            }
            //return id of the updated user
            return wp_update_user($array_to_change);
        }
    ));
     //change user password
     register_rest_route( SKYE_API_NAMESPACE_V1, '/change-password/(?P<user_id>.*?)/(?P<old_password>.*?)/(?P<new_password>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user_id'];
            $old_password = $data['old_password'];
            $new_password = $data['new_password'];
            $user = get_userdata($user_id);
            if ($user) {
                $username = $user->data->user_login;
                //confirm old password
                $authenticate =  wp_authenticate($username, $old_password);
                if (!is_null($authenticate->data)) {
                    wp_set_password( $new_password, $user_id );
                    //check if password change
                    $check = wp_authenticate($username, $new_password);
                    if (!is_null($check->data)) {
                        return array(
                            'success' => true
                        );
                    } else {
                        return array(
                            'code' => 'password-not-changed',
                            'message' => "Unable to change password!",
                            'data' => null
                        );
                    }
                } else {
                    return $authenticate;
                }
            } else {
                return array(
                    'code' => 'user-not-exists',
                    'message' => "User ID does not exists",
                    'data' => null
                );
            }
        }
    ));
    //reset user password
    register_rest_route( SKYE_API_NAMESPACE_V1, '/reset-user-password/(?P<user_id>.*?)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $user_id = $data['user_id'];
            $user = new WP_User(intval($user_id));
            $reset_key = get_password_reset_key( $user );
            $wc_emails = WC()->mailer()->get_emails();
            return $wc_emails['WC_Email_Customer_Reset_Password']->trigger($user->user_login, $reset_key);
            return null;
        }
    ));

    //products page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/products', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $paged = isset($data['paged']) ? $data['paged'] : 1;
            $post_per_page = isset($data['per_page']) ? $data['per_page'] : 20;
            $product_cat = isset($data['cat']) ? $data['cat'] : null;
            $post_in = isset($data['ids']) ? explode(',', rtrim($data['ids'], ',')) : null;
            $search = isset($data['search']) ? $data['search'] : null;

            $query = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => $post_per_page,
                'paged' => $paged,
                'product_cat' => $product_cat,
                'post__in' => $post_in,
                's' => $search,
            ));

            if (!$query->have_posts())
            return null;

            $product_array = array();

            while($query->have_posts()) {
                $query->the_post();
                $product_array["results"][] = sk_get_product_array(get_the_ID());
                }
    
            // add pagination
            $product_array['paged'] = $paged;
            $product_array['pagination'] = sk_numeric_pagination($query, $data);
            wp_reset_query();
            return $product_array;
        }
    ));

    //single product page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/product/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $product_id = $data['id'];
            $related_product_per_page = isset($data['related_product_per_page']) ? $data['related_product_per_page'] : 10;

            $info_array = sk_get_product_array($product_id);

            $info_array['related_products'] = array();
            $related_ids  = wc_get_related_products($product_id, $related_product_per_page);
            foreach($related_ids as $id) {
                $info_array['related_products'][] = sk_get_product_array($id);
            }
                

            return $info_array;
        }
    ));

    //add to cart page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/add-to-cart/(?P<product_id>\d+)', array(
        'methods' => 'POST',
        'callback' => function($data) {
 
            $user_id = null;

            //confirm if data['user'] isset
            if (isset($data['user'])) {
                $user_id = $data['user'];
                //if is a registered user
                if (!sk_user_exists($user_id)) {
                    //if user id exists in cart if not registered user
                    if (!sk_user_cart_exists($user_id)) 
                        return array('user-cart-not-exists');
                }
            } else {
                $user_id = sk_generate_new_user_hash_id();
            }


            $return_array = array();

            //check if user cat exists
            if (sk_user_cart_exists($user_id)) {
                //update
                $return_array['user_cart_exists'] = true;
                $return_array['product_added'] = sk_update_cart_value($user_id, $data);
            } else {
                //insert
                $return_array['user_cart_exists'] = false;
                $return_array['product_added'] = sk_insert_cart($user_id, $data);
            }

            //add user id or new generate id hash
            $return_array['user'] = $user_id;

            //merget the cart value
            return array_merge($return_array, json_decode(sk_get_cart_value($user_id), true));
        }
    ));

    //cart info page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/cart/(?P<user>.*?)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $user_id = $data['user']; //user real ID or hash for unknown user
            
            if (!sk_user_cart_exists($user_id)) return array("user-cart-not-exists");

            $return_array = array();

            $return_array = json_decode(sk_get_cart_value($user_id, true));

            //CART INFORMATION TO COPY
            // // cat conditional if
            // $return_array['is_empty'] = WC()->cart->is_empty();
            // $return_array['needs_payment'] = WC()->cart->needs_payment();
            // $return_array['show_shipping'] = WC()->cart->show_shipping();
            // $return_array['needs_shipping'] = WC()->cart->needs_shipping();
            // $return_array['display_prices_including_tax'] = WC()->cart->display_prices_including_tax();

            // // Get cart totals
            // $return_array['contents_count'] = WC()->cart->get_cart_contents_count();
            // $return_array['cart_subtotal'] = WC()->cart->get_cart_subtotal();
            // $return_array['subtotal_ex_tax'] = WC()->cart->subtotal_ex_tax;
            // $return_array['subtotal'] = WC()->cart->subtotal;
            // $return_array['displayed_subtotal'] = WC()->cart->get_displayed_subtotal();
            // $return_array['taxes_total'] = WC()->cart->get_taxes_total();
            // $return_array['shipping_total'] = WC()->cart->get_shipping_total();
            // $return_array['coupons'] = WC()->cart->get_coupons();
            // // $return_array['coupon_discount_amount'] = WC()->cart->get_coupon_discount_amount( 'coupon_code' );
            // $return_array['fees'] = WC()->cart->get_fees();
            // $return_array['discount_total'] = WC()->cart->get_discount_total();
            // $return_array['total'] = WC()->cart->get_total();
            // $return_array['total'] = WC()->cart->total;
            // $return_array['tax_totals'] = WC()->cart->get_tax_totals();
            // $return_array['cart_contents_tax'] = WC()->cart->get_cart_contents_tax();
            // $return_array['fee_tax'] = WC()->cart->get_fee_tax();
            // $return_array['discount_tax'] = WC()->cart->get_discount_tax();
            // $return_array['shipping_total'] = WC()->cart->get_shipping_total();
            // $return_array['shipping_taxes'] = WC()->cart->get_shipping_taxes();

            // // Loop over $cart items
            // $return_array['items'] = array();
            // foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            //     $product = $cart_item['data'];
            //     $return_array['items'][] = array(
            //         'ID' => $cart_item['product_id'],
            //         'quantity' => $cart_item['quantity'],
            //         'price' => WC()->cart->get_product_price( $product ),
            //         'subtotal' => WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ),
            //         'link' => $product->get_permalink( $cart_item ),
            //         'attributes' =>  $product->get_attributes(),
            //         // 'whatever_attribute' => $product->get_attribute( 'whatever' ),
            //         // 'whatever_attribute_tax' => $product->get_attribute( 'pa_whatever' ),
            //         // 'any_attribute' => $cart_item['variation']['attribute_whatever'],
            //         'meta' => wc_get_formatted_cart_item_data( $cart_item ),
            //         // more single product info
            //         // 'product_info' => sk_get_product_array($cart_item['product_id']),

            //     );
            //  }

            //  // Get $cart customer billing / shipping
            // $return_array['billing_first_name'] = WC()->cart->get_customer()->get_billing_first_name();
            // $return_array['billing_last_name'] = WC()->cart->get_customer()->get_billing_last_name();
            // $return_array['billing_company'] = WC()->cart->get_customer()->get_billing_company();
            // $return_array['billing_email'] = WC()->cart->get_customer()->get_billing_email();
            // $return_array['billing_phone'] = WC()->cart->get_customer()->get_billing_phone();
            // $return_array['billing_country'] = WC()->cart->get_customer()->get_billing_country();
            // $return_array['billing_state'] = WC()->cart->get_customer()->get_billing_state();
            // $return_array['billing_postcode'] = WC()->cart->get_customer()->get_billing_postcode();
            // $return_array['billing_city'] = WC()->cart->get_customer()->get_billing_city();
            // $return_array['billing_address'] = WC()->cart->get_customer()->get_billing_address();
            // $return_array['billing_address_2'] = WC()->cart->get_customer()->get_billing_address_2();
            // $return_array['shipping_first_name'] = WC()->cart->get_customer()->get_shipping_first_name();
            // $return_array['shipping_last_name'] = WC()->cart->get_customer()->get_shipping_last_name();
            // $return_array['shipping_company'] = WC()->cart->get_customer()->get_shipping_company();
            // $return_array['shipping_country'] = WC()->cart->get_customer()->get_shipping_country();
            // $return_array['shipping_state'] = WC()->cart->get_customer()->get_shipping_state();
            // $return_array['shipping_postcode'] = WC()->cart->get_customer()->get_shipping_postcode();
            // $return_array['shipping_city'] = WC()->cart->get_customer()->get_shipping_city();
            // $return_array['shipping_address'] = WC()->cart->get_customer()->get_shipping_address();
            // $return_array['shipping_address_2'] = WC()->cart->get_customer()->get_shipping_address_2();

            // // Other stuff
            // $return_array['cross_sells'] = WC()->cart->get_cross_sells();
            // $return_array['cart_item_tax_classes_for_shipping'] = WC()->cart->get_cart_item_tax_classes_for_shipping();
            // $return_array['cart_hash'] = WC()->cart->get_cart_hash();
            // $return_array['customer'] = WC()->cart->get_customer();
            
            return $return_array;
        }
    ));

    //create order page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/create-order/(?P<user>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user'];
            $allow_guest = (isset($data['allow_guest']));
            $status = (isset($data['status'])) ? sanitize_text_field($data['status']) : 'wc-pending';
            $order_note = (isset($data['order_note'])) ? sanitize_text_field($data['order_note']) : 'Ordered from API';
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
            
            //CREATE ORDER
            $order = wc_create_order(array(
                'customer_id' => (sk_user_exists($user_id)) ? $user_id : null,
                // 'status' => null,
                // 'customer_note' => null,
                // 'parent' => null,
                // 'created_via' => null,
            ));
            //add products in cart by looping the items
            foreach($cart['items'] as $item) {
                $order->add_product(get_product($item['ID']), $item['quantity']);
            }
            //set address
            if (!is_null($billing_address))
                $order->set_address($billing_address, 'billing');

            if (!is_null($shipping_address))
                $order->set_address($shipping_address, 'shipping');

            // set payment gateways
            //  and update payment - later fix
            //NO NEED AGAIN
            //payment status confirmed and set with the developing app, android, ios, or web app.
            //
            $order->calculate_totals();
            //PAYMENT STATUS
            //wc-processing - for payed but order not completed
            //wc-completed - for payed and completed
            //wc-pending - for payment pending
            //wc-on-hold - for holding order without processing the payment
            //wc-cancelled - for order cancelled
            //wc-refunded - for order to refund
            //wc-failed - for failed order
            $order->update_status($status, "Ordered from API", true);
            $order->save();

            $return_array['order_created'] = true;
            $return_array['info'] = sk_order_info($order->get_id());

            return $return_array;
        }
    ));

    //list orders page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/orders/(?P<user>.*?)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $user_id = $data['user'];
            $status = (isset($data['status'])) ? $data['status'] : array('wc-processing', 'wc-on-hold', 'wc-completed');
            $date_completed = (isset($data['date_completed'])) ? $data['date_completed'] : null; //value eg: 2018-10-01...2018-10-10
            $paged = isset($data['paged']) ? $data['paged'] : 1;
            $post_per_page = isset($data['per_page']) ? $data['per_page'] : 20;

            $query = new WC_Order_Query(array(
                'limit' => $post_per_page,
                // 'orderby' => 'date',
                // 'order' => 'DESC',
                'customer_id' => $user_id,
                'user_id' => $user_id,
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
            ));
            $orders = $query->get_orders();
            $order_array = array();
            $order_array['orders'] = array();
            foreach($orders->orders as $order_id) { //i use $orders->orders bcos paginate is set true
                $order_array['orders'][] = sk_order_info($order_id);
            }
            
            $order_array['total'] = $orders->total;
            $order_array['max_num_pages'] = $orders->max_num_pages;

            return $order_array;
        }
    ));
    //single order page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/order/(?P<id>.*?)/(?P<user_id>.*?)', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $order_id = $data['id'];
            $user_id = $data['user_id'];
            $order = sk_order_info($order_id);
            if ($order['customer_id'] == $user_id || $order['user_id'] == $user_id) {
                return $order;
            } else {
                return array(
                    'code' => 'order-not-found',
                    'message' => 'Order by this user not found!',
                    'data' => null
                );
            }
        }
    ));
    //update order page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-order/(?P<order_id>.*?)/(?P<user_id>.*?)', array(
        'methods' => 'POST',
        'callback' => function($data) {
            $user_id = $data['user_id'];
            $order_id  = $data['order_id'];
            $status  = isset($data['status']) ? sanitize_text_field($data['status']) : null;
            $order_note  = isset($data['order_note']) ? sanitize_text_field($data['order_note']) : 'Updated from API';
            $return_array = array();
            //to add address to url: url?billing_address%Bfirst_name%5D=SEUN&billing_address%5Blast_name%5D=OYENIYI....
            //%5B is to select variable array with they key eg: address%5Bfirst_name
            //%5D is to add the value to the key eg:address%5Bfirst_name%5D=SEUN
            $billing_address = (isset($data['billing_address'])) ? $data['billing_address']: null;
            $shipping_address = (isset($data['shipping_address'])) ? $data['shipping_address']: null;
            // // address like an array address = array(
            //     'first_name' => '',
            //     'last_name' => '',
            //      'company' => '',
            //      'email' => '',
            //      'phone' => '',
            //      'address_1' => '', //street address
            //      'address_2' => '', //appartment
            //      'city' => '',
            //      'state' => '',
            //      'postcode' => '',
            //      'country' => '',
            //     ''
            // )

            // CUSTOM FIELD
            //%5B is to select variable array with they key eg: custom_field%5Bfirst_name
            //%5D is to add the value to the key eg:custom_field%5Bfirst_name%5D=SEUN
            $custom_field = (isset($data['custom_field'])) ? $data['custom_field']: null;

            //order exists
            $return_array['order_exists'] = sk_order_exists($order_id);
            if (!sk_order_exists($order_id))
                return $return_array;
            //user exists
            $return_array['user_exists'] = sk_user_exists($user_id);
            if (!sk_user_exists($user_id))
                return $return_array;
            
            //UPDATE ORDER
            // update address
            if (!is_null($billing_address)) {
                foreach ($billing_address as $key => $val) {
                    update_post_meta($order_id, '_billing_' . $key , $val);
                }
                $return_array['billing_address_updated'] = true;
            }
            if (!is_null($shipping_address)) {
                foreach ($shipping_address as $key => $val) {
                    update_post_meta($order_id, '_shipping_' . $key , $val);
                }
                $return_array['shipping_address_updated'] = true;
            }
            //update status
            if (!is_null($status)) {
                $order = new WC_Order($order_id);
                // $order->update_status('pending', 'order note'); sample
                $order->update_status($status, $order_note);
                $return_array['order_status_updated'] = true;
            }
            //custom field
            if (!is_null($custom_field)) {
                foreach ($custom_field as $key => $val) {
                    update_post_meta($order_id, $key, sanitize_text_field($val));
                }
                $return_array['custom_field_updated'] = true;
            }

            //order info
            $return_array['order_info'] = sk_order_info($order_id);

            return $return_array;
        }
    ));
    //categories page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/categories', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $with_sub = (isset($data['with_sub'])) ? true : false; //true for listing sub cateogries with parent output list and false for removing sub categories from parent output list
            $hide_empty = (isset($data['hide_empty'])) ? 1 : 0;
            $order_by = (isset($data['order_by'])) ? $data['order_by'] : null;

            $array_return = array();

            $categories = get_categories( array(
                'taxonomy' => 'product_cat',
                'orderby' => $order_by,
                'show_count' => 1,
                'pad_counts' => 1,
                'hierarchical' => 1,
                'title_li' => '',
                'hide_empty' => $hide_empty,
            ) );
            
            foreach($categories as $category) {
                if ($with_sub) { //will join sub categories to the parent list
                    $cat_arr = array(
                        'ID' => $category->term_id,
                        'name' => $category->name,
                        'link' => get_term_link($category->slug, 'product_cat'),
                        'count' => $category->count
                    );
                    $sub_cats = get_categories(array(
                        'taxonomy' => 'product_cat',
                        'child_of' => 0,
                        'parent' => $category->term_id,
                        'orderby' => $order_by,
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => $hide_empty,
                    ));
                    if ($sub_cats) {
                        $cat_arr['sub_cats'] = array();
                        foreach ($sub_cats as $cat) {
                            $cat_arr['sub_cats'][] = array(
                                'ID' => $cat->term_id,
                                'name' => $cat->name,
                                'link' => get_term_link($cat->slug, 'product_cat'),
                                'count' => $cat->count
                            );
                        }
                    }
                    $array_return[] = $cat_arr;
                } else { //will remove sub categories from the parent list
                    if ($category->category_parent == 0) {
                        $cat_arr = array(
                            'ID' => $category->term_id,
                            'name' => $category->name,
                            'link' => get_term_link($category->slug, 'product_cat'),
                            'count' => $category->count
                            
                        );
                        $sub_cats = get_categories(array(
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => $order_by,
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => $hide_empty,
                        ));
                        if ($sub_cats) {
                            $cat_arr['sub_cats'] = array();
                            foreach ($sub_cats as $cat) {
                                $cat_arr['sub_cats'][] = array(
                                    'ID' => $cat->term_id,
                                    'name' => $cat->name,
                                    'link' => get_term_link($cat->slug, 'product_cat'),
                                    'count' => $cat->count
                                );
                            }
                        }
                        $array_return[] = $cat_arr;
                    }
                }
            }

            return $array_return;

        }
    ));
    //tags page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/tags', array(
        'methods' => 'GET',
        'callback' => function($data) {
            $hide_empty = (isset($data['hide_empty'])) ? 1 : 0;
            $order_by = (isset($data['order_by'])) ? $data['order_by'] : null;

            $array_return = array();

            $tags = get_categories( array(
                'taxonomy' => 'product_tag',
                'orderby' => $order_by,
                'show_count' => 1,
                'pad_counts' => 1,
                'hierarchical' => 1,
                'title_li' => '',
                'hide_empty' => $hide_empty,
            ) );
            
            foreach($tags as $tag) {
                $array_return[] = array(
                        'ID' => $tag->term_id,
                        'name' => $tag->name,
                        'link' => get_term_link($tag->slug, 'product_tag'),
                        'count' => $tag->count
                );
            }

            return $array_return;

        }
    ));
    
});
