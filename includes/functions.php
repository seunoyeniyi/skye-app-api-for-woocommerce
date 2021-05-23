<?php
if (!function_exists('sk_search_item_in_array')) {
    function sk_search_item_in_array($id, $array) {
        foreach($array as $key => $val) {
            if ($val['ID'] === $id) return $key;
        }
        return null;
    }
}
if (!function_exists('sk_cart_subtotal')) {
    function sk_cart_subtotal($items) {
    $total = 0;
    foreach($items as $item) {
        $total += $item['subtotal'];
    }
    return $total;
    }
}
if (!function_exists('sk_get_product_array')) {
    function sk_get_product_array($product_id) {
    $product = wc_get_product($product_id);

    //categories
    $categories_ids = $product->get_category_ids();
    $categories = array();
    foreach($categories_ids as $cat_id) {
        $categories[] = get_term_by('id', $cat_id, 'product_cat');
    }
    //tags
    $tags_ids = $product->get_tag_ids();
    $tags = array();
    foreach($tags_ids as $tag_id) {
        $tags[] = get_term_by('id', $tag_id, 'product_tag');
    }

    return array(
        // General Info
        'ID' => $product->get_id(),
        'title' => $product->get_title(),
        'type' => $product->get_type(),
        'name' => $product->get_name(),
        'slug' => $product->get_slug(),
        'date_created' => $product->get_date_created(),
        'date_modified' => $product->get_date_modified(),
        'status' => $product->get_status(),
        'featured' => $product->get_featured(),
        'catalog_visibility' => $product->get_catalog_visibility(),
        'description' => $product->get_description(),
        'sku' => $product->get_sku(),
        'menu_order' => $product->get_menu_order(),
        'virtual' => $product->get_virtual(),
        'permalink' => get_permalink($product->get_id()),

        // Product prices
        'price' => $product->get_price(),
        'regular_price' => $product->get_regular_price(),
        'sale_price' => $product->get_sale_price(),
        'date_on_sale_from' => $product->get_date_on_sale_from(),
        'date_on_sale_to' => $product->get_date_on_sale_to(),
        'total_sales' => $product->get_total_sales(),

        // Product Dimensions
        'weight' => $product->get_weight(),
        'length' => $product->get_length(),
        'width' => $product->get_width(),
        'height' => $product->get_height(),
        'dimensions' => $product->get_dimensions(),

        // Linked Products
        'upsell_ids' => $product->get_upsell_ids(),
        'cross_sell_ids' => $product->get_cross_sell_ids(),
        'parent_id' => $product->get_parent_id(),

        // Product Variations and Attributes
        'children' => $product->get_children(),
        'attributes' => $product->get_attributes(),
        'default_attributes' => $product->get_default_attributes(),
        //'attribute' => $product->get_attribute('attributeid'), //get specific attribute value

        // Product Taxonomies
        // 'categories' => $product->get_categories(), //return categires html links
        'categories' => $categories,
        'category_ids' => $product->get_category_ids(),
        'tags' => $tags,
        'tag_ids' => $product->get_tag_ids(),

        // Product Downloads
        'downloads' => $product->get_downloads(),
        'download_expiry' => $product->get_download_expiry(),
        'downloadable' => $product->get_downloadable(),
        'download_limit' => $product->get_download_limit(),

        // Product Images
        'image_id' => $product->get_image_id(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        // 'image2' => $product->get_image(), //return html image
        'gallery_image_ids' => $product->get_gallery_image_ids(),

        //Product Reviews
        'reviews_allowed' => $product->get_reviews_allowed(),
        'rating_counts' => $product->get_rating_counts(),
        'average_rating' => $product->get_average_rating(),
        'review_count' => $product->get_review_count()

    );
    }
}
if (!function_exists('sk_user_cart_exists')) {
    function sk_user_cart_exists($user_id) {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $cart_table = $wpdb->prefix . "skye_carts";
    
    $result = $wpdb->get_results("SELECT * FROM $cart_table WHERE user='$user_id' LIMIT 1");
    if (count($result) > 0)
        return true;
    else
        return false;
    }
}
if (!function_exists('sk_get_cart_value')) {
    function sk_get_cart_value($user_id) {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $cart_table = $wpdb->prefix . "skye_carts";
    
    $result = $wpdb->get_results("SELECT * FROM $cart_table WHERE user='$user_id' LIMIT 1");
    if (count($result) > 0)
        return $result[0]->cart_value;
    else
        return null;
    }
}
if (!function_exists('sk_update_cart_value')) {
    function sk_update_cart_value($user_id, $data) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";
    //update
    $current_date = date("Y-m-d H:i:s");
    $expiry_datetime = date("Y-m-d H:i:s", strtotime($current_date . ' + 14 days'));
    return $wpdb->update($cart_table, array(
        'cart_value' => json_encode(sk_cart_json_handler($user_id, $data, sk_get_cart_value($user_id))),
        'session_expiry' => $expiry_datetime, //renew the expiry as cart updated
    ), array(
        'user' => $user_id
    ));
    }
}
if (!function_exists('sk_insert_cart')) {
    function sk_insert_cart($user_id, $data) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";

    if (!is_null($user_id)) {
        $current_date = date("Y-m-d H:i:s");
        $expiry_datetime = date("Y-m-d H:i:s", strtotime($current_date . ' + 14 days'));
        return $wpdb->insert($cart_table, array(
            'user' => $user_id,
            'cart_value' => json_encode(sk_cart_json_handler($user_id, $data)),
            'session_expiry' => $expiry_datetime
        ));
    } else {
        return false;
    }
    }
}
if (!function_exists('sk_generate_new_user_hash_id')) {
    function sk_generate_new_user_hash_id() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $cart_table = $wpdb->prefix . "skye_carts";
    
    $result = $wpdb->get_results("SELECT ID FROM $cart_table ORDER BY ID DESC LIMIT 1");
    
    if (!empty($result))
        return md5(((int) $result[0]->ID) + 1);
    else
        return md5(1);
    }
}
if (!function_exists('sk_cart_json_handler')) {
    function sk_cart_json_handler($user_id, $data, $old_cart_json = null) { //return array
    $product_id = $data['product_id'];
    $quantity = (isset($data['quantity'])) ? $data['quantity'] : 1;
    $product = wc_get_product($product_id);
    $replace_qty = (isset($data['replace_quantity'])) ? true : false;
    
    $return_array = array();
    if (is_null($old_cart_json)) { //create new array
            //DEFAULT VALUE FOR NEW CART
            $return_array['user'] = $user_id;
            // // cat conditional if
            $return_array['is_empty'] = false;
            // $return_array['display_prices_including_tax'] = WC()->cart->display_prices_including_tax();

            // // Get cart totals
            $return_array['contents_count'] = 1;
            // $return_array['cart_subtotal'] = WC()->cart->get_cart_subtotal();
            // $return_array['subtotal_ex_tax'] = WC()->cart->subtotal_ex_tax;
            $return_array['subtotal'] = $product->get_price() * $quantity;
            // $return_array['displayed_subtotal'] = WC()->cart->get_displayed_subtotal();
            // $return_array['coupons'] = WC()->cart->get_coupons();
            // // $return_array['coupon_discount_amount'] = WC()->cart->get_coupon_discount_amount( 'coupon_code' );
            // $return_array['fees'] = WC()->cart->get_fees();
            // $return_array['discount_total'] = WC()->cart->get_discount_total();
            // $return_array['total'] = WC()->cart->get_total();
            // $return_array['total'] = WC()->cart->total;

        //ITEMS in the cart
        $return_array['items'] = array(); 
        $return_array['items'][] = array( //products added
                    'ID' => $product_id,
                    'quantity' => $quantity,
                    'price' => $product->get_price(),
                    'subtotal' => $product->get_price() * $quantity,
                    'attributes' =>  $product->get_attributes(),
                    // 'whatever_attribute' => $product->get_attribute( 'whatever' ),
                    // 'whatever_attribute_tax' => $product->get_attribute( 'pa_whatever' ),
                    // 'any_attribute' => $cart_item['variation']['attribute_whatever'],
                    // 'meta' => wc_get_formatted_cart_item_data( $cart_item ),
                    // more single product info
                    // 'product_info' => sk_get_product_array($cart_item['product_id']),
        );

    } else { //update the old json
        $return_array = json_decode($old_cart_json, true); //second parameter is true, to convert to array instead of object
        //SAMPLE FROM DATABASE
            // `{
            // "user":"c4ca4238a0b923820dcc509a6f75849b",
            // "is_empty":false,
            // "contents_count":1,
            // "subtotal":199,
            // "items":[
            //     {
            //         "ID":"25",
            //         "quantity":1,
            //         "price":"199",
            //         "subtotal":199,
            //         "attributes":[]
            //     }
            //     ]
            // }`

        //updating items in cart
        //search product in the items
        $search = sk_search_item_in_array($product_id, $return_array['items']);
        if (!is_null($search)) { //update items
            if ($quantity > 0) { //update product in items
                $return_array['items'][$search]['quantity'] = ($replace_qty) ? $quantity : ($return_array['items'][$search]['quantity'] + $quantity);
                $return_array['items'][$search]['price'] = $product->get_price();
                $return_array['items'][$search]['subtotal'] = $product->get_price() * $return_array['items'][$search]['quantity']; //since the quantity has been update, it can now be re-use
                $return_array['items'][$search]['attributes'] =  $product->get_attributes();
            } else { //delete item since quantity is zero(0)
                unset($return_array['items'][$search]);
            }
        } else { //add to items
            $return_array['items'][] = array(
                'ID' => $product_id,
                'quantity' => $quantity,
                'price' => $product->get_price(),
                'subtotal' => $product->get_price() * $quantity,
                'attributes' =>  $product->get_attributes(),
            );
        }


        //update general cart value
        $return_array['is_empty'] = (count($return_array['items']) > 0) ? false : true;
        $return_array['contents_count'] = count($return_array['items']);
        $return_array['subtotal'] = sk_cart_subtotal($return_array['items']);
    }

    return $return_array;
    }
}
if (!function_exists('sk_user_exists')) {
    function sk_user_exists($user_id) {
    $user = get_userdata($user_id);
    return ($user != false);
    }
}
if (!function_exists('sk_order_info')) {
    function sk_order_info($order_id) {
    $return_array = array();
    $order = wc_get_order( $order_id );
    if ( $order ) {
        $return_array['ID'] = $order->get_id();
 
        // Get Order Totals $0.00
        // $return_array['formatted_order_total'] = $order->get_formatted_order_total(); //html formatted
        $return_array['cart_tax'] = $order->get_cart_tax();
        $return_array['currency'] = $order->get_currency();
        $return_array['discount_tax'] = $order->get_discount_tax();
        // $return_array['discount_to_display'] = $order->get_discount_to_display(); //html formatted
        $return_array['discount_total'] = $order->get_discount_total();
        $return_array['fees'] = $order->get_fees();
        // $return_array['formatted_line_subtotal'] = $order->get_formatted_line_subtotal(); //parameter error fix later
        $return_array['shipping_tax'] = $order->get_shipping_tax();
        $return_array['shipping_total'] = $order->get_shipping_total();
        $return_array['subtotal'] = $order->get_subtotal();
        // $return_array['subtotal_to_display'] = $order->get_subtotal_to_display(); //html formated
        // $return_array['tax_location'] = $order->get_tax_location(); //error protected function fix later
        $return_array['tax_totals'] = $order->get_tax_totals();
        $return_array['taxes'] = $order->get_taxes();
        $return_array['total'] = $order->get_total();
        $return_array['total_discount'] = $order->get_total_discount();
        $return_array['total_tax'] = $order->get_total_tax();
        $return_array['total_refunded'] = $order->get_total_refunded();
        $return_array['total_tax_refunded'] = $order->get_total_tax_refunded();
        $return_array['total_shipping_refunded'] = $order->get_total_shipping_refunded();
        $return_array['item_count_refunded'] = $order->get_item_count_refunded();
        $return_array['total_qty_refunded'] = $order->get_total_qty_refunded();
        // $return_array['qty_refunded_for_item'] = $order->get_qty_refunded_for_item(); //parameter error fix later
        // $return_array['total_refunded_for_item'] = $order->get_total_refunded_for_item(); //parameter error fix later
        // $return_array['tax_refunded_for_item'] = $order->get_tax_refunded_for_item(); //parameter error fix later
        // $return_array['total_tax_refunded_by_rate_id'] = $order->get_total_tax_refunded_by_rate_id(); //parameter error fix later
        $return_array['remaining_refund_amount'] = $order->get_remaining_refund_amount();
        // Get and Loop Over Order Items
        $return_array['products'] = array();
        foreach ( $order->get_items() as $item_id => $item ) {
            $rr = array(
                'ID' => $item->get_product_id(),
                'variation_id' => $item->get_variation_id(),
                'quantity' => $item->get_quantity(),
                'subtotal' => $item->get_subtotal(),
                'total' => $item->get_total(),
                'tax' => $item->get_subtotal_tax(),
                'taxclass' => $item->get_tax_class(),
                'taxstat' => $item->get_tax_status(),
                'allmeta' => $item->get_meta_data(),
                // 'somemeta' => $item->get_meta( '_whatever', true ),
                'type' => $item->get_type(),
            );
            $return_array['products'][] = array_merge($rr, sk_get_product_array($item->get_product_id()));
         }
     
         // Other Secondary Items Stuff
        // $return_array['items_key'] = $order->get_items_key(); //protected error
        $return_array['items_tax_classes'] = $order->get_items_tax_classes();
        $return_array['item_count'] = $order->get_item_count();
        // $return_array['item_total'] = $order->get_item_total(); //few argument
        $return_array['downloadable_items'] = $order->get_downloadable_items();

        // Get Order Lines
        // $return_array['line_subtotal'] = $order->get_line_subtotal(); //few argument
        // $return_array['line_tax'] = $order->get_line_tax(); //protected error
        // $return_array['line_total'] = $order->get_line_total(); //few argument

        // Get Order Shipping
        $return_array['shipping_method'] = $order->get_shipping_method();
        $return_array['shipping_methods'] = $order->get_shipping_methods();
        $return_array['shipping_to_display'] = $order->get_shipping_to_display();

        // Get Order Dates
        $return_array['date_created'] = $order->get_date_created();
        $return_array['date_modified'] = $order->get_date_modified();
        $return_array['date_completed'] = $order->get_date_completed();
        $return_array['date_paid'] = $order->get_date_paid();

        // Get Order User, Billing & Shipping Addresses
        $return_array['customer_id'] = $order->get_customer_id();
        $return_array['user_id'] = $order->get_user_id();
        $return_array['customer_ip_address'] = $order->get_customer_ip_address();
        $return_array['customer_user_agent'] = $order->get_customer_user_agent();
        $return_array['created_via'] = $order->get_created_via();
        $return_array['customer_note'] = $order->get_customer_note();
        // $return_array['address_prop'] = $order->get_address_prop(); //protected error
        $return_array['billing_first_name'] = $order->get_billing_first_name();
        $return_array['billing_last_name'] = $order->get_billing_last_name();
        $return_array['billing_company'] = $order->get_billing_company();
        $return_array['billing_address_1'] = $order->get_billing_address_1();
        $return_array['billing_address_2'] = $order->get_billing_address_2();
        $return_array['billing_city'] = $order->get_billing_city();
        $return_array['billing_state'] = $order->get_billing_state();
        $return_array['billing_postcode'] = $order->get_billing_postcode();
        $return_array['billing_country'] = $order->get_billing_country();
        $return_array['billing_email'] = $order->get_billing_email();
        $return_array['billing_phone'] = $order->get_billing_phone();
        $return_array['shipping_first_name'] = $order->get_shipping_first_name();
        $return_array['shipping_last_name'] = $order->get_shipping_last_name();
        $return_array['shipping_company'] = $order->get_shipping_company();
        $return_array['shipping_address_1'] = $order->get_shipping_address_1();
        $return_array['shipping_address_2'] = $order->get_shipping_address_2();
        $return_array['shipping_city'] = $order->get_shipping_city();
        $return_array['shipping_state'] = $order->get_shipping_state();
        $return_array['shipping_postcode'] = $order->get_shipping_postcode();
        $return_array['shipping_country'] = $order->get_shipping_country();
        $return_array['address'] = $order->get_address();
        $return_array['shipping_address_map_url'] = $order->get_shipping_address_map_url();
        $return_array['formatted_billing_full_name'] = $order->get_formatted_billing_full_name();
        $return_array['formatted_shipping_full_name'] = $order->get_formatted_shipping_full_name();
        $return_array['formatted_billing_address'] = $order->get_formatted_billing_address();
        $return_array['formatted_shipping_address'] = $order->get_formatted_shipping_address();

        // Get Order Payment Details
        $return_array['payment_method'] = $order->get_payment_method();
        $return_array['payment_method_title'] = $order->get_payment_method_title();
        $return_array['transaction_id'] = $order->get_transaction_id();

        // Get Order URLs
        // $return_array['checkout_payment_url'] = $order->get_checkout_payment_url();
        // $return_array['checkout_order_received_url'] = $order->get_checkout_order_received_url();
        // $return_array['cancel_order_url'] = $order->get_cancel_order_url();
        // $return_array['cancel_endpoint'] = $order->get_cancel_order_url_raw();
        // $return_array['cancel_endpoint'] = $order->get_cancel_endpoint();
        // $return_array['view_order_url'] = $order->get_view_order_url();
        // $return_array['edit_order_url'] = $order->get_edit_order_url();

        // Get Order Status
        $return_array['status'] = $order->get_status();




     }
     return $return_array;
    }
}
if (!function_exists('sk_order_exists')) {
    function sk_order_exists($order_id) {
    $order = wc_get_order( $order_id );
    if ($order)
        return true;
    else
        return false;
    }
}
//code from WPBeginner
if (!function_exists('sk_numeric_pagination')) {
    function sk_numeric_pagination($query, $data) {
 
 if( is_singular() )
     return null;


 /** Stop execution if there's only 1 page */
 if( $query->max_num_pages <= 1 )
     return null;

 $paged = $data['paged'] ? absint( $data['paged'] ) : 1;
 $max   = intval( $query->max_num_pages );

 /** Add current page to the array */
 if ( $paged >= 1 )
     $links[] = $paged;

 /** Add the pages around the current page to the array */
 if ( $paged >= 3 ) {
     $links[] = $paged - 1;
     $links[] = $paged - 2;
 }

 if ( ( $paged + 2 ) <= $max ) {
     $links[] = $paged + 2;
     $links[] = $paged + 1;
 }

    //  link to first page if not in links
 if ( ! in_array( 1, $links ) ) {
    $links[] = 1;
    }

 /**	Link to last page if not in links*/
    if ( ! in_array( $max, $links ) ) {
    $links[] = $max;
    }



    $array_to_return = array();
    /** Link to current page, plus 2 pages in either direction if necessary */
    sort( $links );
    foreach ( (array) $links as $link ) {
        array_push($array_to_return, $link);
    }


    return $array_to_return;
    }
}