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
    function sk_get_product_array($product_id, $user_id = null) {
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
    
    //setup attributes
    $attributes = $product->get_attributes();
    // die(print_r($data['kdl'] = $product->get_attribute("pa_size")));
    $return_attributes = array();
    if ($product->is_type('variation')) {
        $return_attributes['terms'] = $attributes;
        $return_attributes['selected'] = array();
        foreach($attributes as $key => $value) {
            $return_attributes['selected'][] = array(
                'label' => wc_attribute_label($key),
                'selected' => $product->get_attribute($key),
            );
        }

    } else {
    foreach ($attributes as $attr => $attribute) {
        $data = $attribute->get_data();
        $data['label'] = wc_attribute_label($data['name']);
        $options = $attribute->get_options();
        // $data['print_echo'] = $attribute->get_terms();
        $data['options'] = array(); //clear default array;
        if ($attribute->is_taxonomy()) {
            foreach((array)$attribute->get_terms() as $term) {
                $data['options'][] = array(
                    'name' => $term->slug,
                    'value' => $term->name,
                );
            }
        } else {
            foreach($options as $opt) {
                $data['options'][] = array(
                    'name' => $opt,
                    'value' => $opt,
                );
            }
        }
        $return_attributes[] = $data;
    }
    }
    //setup default attributes
    $return_default_attributes = array();
    $default_attributes = $product->get_default_attributes();
    foreach ($default_attributes as $attr => $attribute) {
		if (is_string($attribute))
			continue;
        $data = $attribute->get_data();
        $data['label'] = wc_attribute_label($data['name']);
        $return_default_attributes[] = $data;
    }


    return array(
        // General Info
        'ID' => $product->get_id(),
        'title' => $product->get_title(),
        'type' => $product->get_type(),
        'name' => $product->get_name(),
        'slug' => $product->get_slug(),
        'in_wishlist' => sk_in_wishlist($user_id, $product->get_id()),
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
		
		'in_stock' => $product->is_in_stock(),
		'stock_status' => $product->get_stock_status(),

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
        'product_type' => $product->get_type(),
        'attributes' => $return_attributes,
        'default_attributes' => $return_default_attributes,
        //'attribute' => $product->get_attribute('attributeid'), //get specific attribute value

        //Product available variations
        'variations' => sk_get_product_variations($product->get_id()),

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
if (!function_exists('sk_get_simple_product_array')) {
    function sk_get_simple_product_array($product_id, $user_id = null) {
    $product = wc_get_product($product_id);

    //categories
    $categories_ids = $product->get_category_ids();
    $categories = array();
    foreach($categories_ids as $cat_id) {
        $cat_term = get_term_by('id', $cat_id, 'product_cat');
        $categories[] = array('name' => $cat_term->name, 'slug' => $cat_term->slug);
    }
    // //tags
    // $tags_ids = $product->get_tag_ids();
    // $tags = array();
    // foreach($tags_ids as $tag_id) {
    //     $tags[] = get_term_by('id', $tag_id, 'product_tag');
    // }

    // //setup attributes
    // $attributes = $product->get_attributes();
    // // die(print_r($data['kdl'] = $product->get_attribute("pa_size")));
    // $return_attributes = array();
    // if ($product->is_type('variation')) {
    //     $return_attributes['terms'] = $attributes;
    //     $return_attributes['selected'] = array();
    //     foreach($attributes as $key => $value) {
    //         $return_attributes['selected'][] = array(
    //             'label' => wc_attribute_label($key),
    //             'selected' => $product->get_attribute($key),
    //         );
    //     }

    // } else {
    // foreach ($attributes as $attr => $attribute) {
    //     $data = $attribute->get_data();
    //     $data['label'] = wc_attribute_label($data['name']);
    //     $options = $attribute->get_options();
    //     // $data['print_echo'] = $attribute->get_terms();
    //     $data['options'] = array(); //clear default array;
    //     if ($attribute->is_taxonomy()) {
    //         foreach((array)$attribute->get_terms() as $term) {
    //             $data['options'][] = array(
    //                 'name' => $term->slug,
    //                 'value' => $term->name,
    //             );
    //         }
    //     } else {
    //         foreach($options as $opt) {
    //             $data['options'][] = array(
    //                 'name' => $opt,
    //                 'value' => $opt,
    //             );
    //         }
    //     }
    //     $return_attributes[] = $data;
    // }
    // }
    // //setup default attributes
    // $return_default_attributes = array();
    // $default_attributes = $product->get_default_attributes();
    // foreach ($default_attributes as $attr => $attribute) {
    //     $data = $attribute->get_data();
    //     $data['label'] = wc_attribute_label($data['name']);
    //     $return_default_attributes[] = $data;
    // }


    return array(
        // General Info
        'ID' => $product->get_id(),
        // 'title' => $product->get_title(),
        'type' => $product->get_type(),
        'name' => $product->get_name(),
        // 'slug' => $product->get_slug(),
        // 'date_created' => $product->get_date_created(),
        // 'date_modified' => $product->get_date_modified(),
        'status' => $product->get_status(),
        // 'featured' => $product->get_featured(),
        // 'catalog_visibility' => $product->get_catalog_visibility(),
        'description' => $product->get_description(),
        // 'sku' => $product->get_sku(),
        // 'menu_order' => $product->get_menu_order(),
        // 'virtual' => $product->get_virtual(),
        // 'permalink' => get_permalink($product->get_id()),
        // 
        
		'stock_status' => $product->get_stock_status(),

        // Product prices
        'price' => $product->get_price(),
        'regular_price' => $product->get_regular_price(),
        // 'sale_price' => $product->get_sale_price(),
        // 'date_on_sale_from' => $product->get_date_on_sale_from(),
        // 'date_on_sale_to' => $product->get_date_on_sale_to(),
        // 'total_sales' => $product->get_total_sales(),

        // Product Dimensions
        // 'weight' => $product->get_weight(),
        // 'length' => $product->get_length(),
        // 'width' => $product->get_width(),
        // 'height' => $product->get_height(),
        // 'dimensions' => $product->get_dimensions(),

        // Linked Products
        // 'upsell_ids' => $product->get_upsell_ids(),
        // 'cross_sell_ids' => $product->get_cross_sell_ids(),
        // 'parent_id' => $product->get_parent_id(),

        // Product Variations and Attributes
        // 'children' => $product->get_children(),
        'product_type' => $product->get_type(),
        'in_wishlist' => sk_in_wishlist($user_id, $product->get_id()),
        // 'attributes' => $return_attributes,
        // 'default_attributes' => $return_default_attributes,
        //'attribute' => $product->get_attribute('attributeid'), //get specific attribute value

        //Product available variations
        // 'variations' => sk_get_product_variations($product->get_id()),

        // Product Taxonomies
        // 'categories' => $product->get_categories(), //return categires html links
        'categories' => $categories,
        // 'category_ids' => $product->get_category_ids(),
        // 'tags' => $tags,
        // 'tag_ids' => $product->get_tag_ids(),

        // Product Downloads
        // 'downloads' => $product->get_downloads(),
        // 'download_expiry' => $product->get_download_expiry(),
        // 'downloadable' => $product->get_downloadable(),
        // 'download_limit' => $product->get_download_limit(),

        // Product Images
        // 'image_id' => $product->get_image_id(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        // 'image2' => $product->get_image(), //return html image
        // 'gallery_image_ids' => $product->get_gallery_image_ids(),

        //Product Reviews
        // 'reviews_allowed' => $product->get_reviews_allowed(),
        // 'rating_counts' => $product->get_rating_counts(),
        // 'average_rating' => $product->get_average_rating(),
        // 'review_count' => $product->get_review_count()

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
if (!function_exists('sk_change_cart_user_id')) {
    function sk_change_cart_user_id($cart_user_id, $new_user_id) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";
    //update
    $current_date = date("Y-m-d H:i:s");
    $expiry_datetime = date("Y-m-d H:i:s", strtotime($current_date . ' + 14 days'));
    //delete new user cart if already exists
    $wpdb->delete($cart_table, array('user' => $new_user_id));
    //get the cart user json to update the id in the json
    $user_cart_json = json_decode(sk_get_cart_value($cart_user_id), true);
    $user_cart_json['user'] = $new_user_id;

    return $wpdb->update($cart_table, array(
        'user' => $new_user_id,
        'cart_value' => json_encode($user_cart_json),
        'session_expiry' => $expiry_datetime, //renew the expiry as cart updated
    ), array(
        'user' => $cart_user_id
    ));
    }
}
if (!function_exists('sk_update_cart_coupon')) {
    function sk_update_cart_coupon($user_id, $coupon) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";
    //update

    $cart_json = json_decode(sk_get_cart_value($user_id), true);
    if (!isset($cart_json['shipping_cost'])) $cart_json['shipping_cost'] = 0; //to be able to calculate total in app
    $shipping_cost = $cart_json['shipping_cost'];
    $coup = new WC_Coupon($coupon);
    if ($coup->id) {
        $cart_json['coupon'] = $coupon;
        $cart_json['coupon_type'] = $coup->get_discount_type();
        $cart_json['coupon_amount'] = $coup->get_amount();
        $cart_json['has_coupon'] = true;
        //calculate coupon price for the cart
        $subtotal = $cart_json['subtotal'];
        $items_count = $cart_json['contents_count'];
        if ($coup->get_discount_type() == "percent") { //calculate minus percentage of subtotal
            $cart_json['coupon_discount'] = (($coup->get_amount() / 100) * $subtotal);
        } elseif ($coup->get_discount_type() == "fixed_cart") { // minus amount from subtotal
            $cart_json['coupon_discount'] = $coup->get_amount();
        } elseif ($coup->get_discount_type() == "fixed_product") { // multiply amount by number of items in cart (and minus result from to subtotal)
            $cart_json['coupon_discount'] = ($coup->get_amount() * $items_count);
        }
        //calculate total
        $cart_json['total'] = ($subtotal + $shipping_cost) - $cart_json['coupon_discount'];
        //deduct reward discount if applied
        if ($cart_json['apply_reward']) {
            $cart_json['total'] -= $cart_json['reward_discount'];
        }
    } else {
        $cart_json['has_coupon'] = false;
        $cart_json['coupon_discount'] = 0;
        $cart_json['total'] = $cart_json['subtotal'] + $cart_json['shipping_cost'];
        //deduct reward discount if applied
        if ($cart_json['apply_reward']) {
            $cart_json['total'] -= $cart_json['reward_discount'];
        }
    }

    $cart_json['has_shipping'] = (isset($cart_json['has_shipping'])) ? $cart_json['has_shipping'] : false;

    return $wpdb->update($cart_table, array(
        'cart_value' => json_encode($cart_json),
    ), array(
        'user' => $user_id
    ));
    }
}
if (!function_exists('sk_apply_reward')) {
    function sk_apply_reward($user_id) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";
    //update

    $cart_json = json_decode(sk_get_cart_value($user_id), true);
    
    $cart_json['apply_reward'] = true;

    $shipping_cost = $cart_json['shipping_cost'];
    $subtotal = $cart_json['subtotal'];
    $coupon_discount = $cart_json['coupon_discount'];

    $reward_discount = 0;

    //RESET REWARDS CALCULATIONS
        if (class_exists("WC_Points_Rewards_Manager")) {
            $current_user_points = WC_Points_Rewards_Manager::get_users_points($user_id); //points
            $current_user_points_value = WC_Points_Rewards_Manager::get_users_points_value($user_id); //price
            $cart_json['user_points'] = $current_user_points;
            $cart_json['user_points_value'] = $current_user_points_value; //in price
            //calculate reward discount
            $cart_subtotal = $subtotal; //total items price - $subtotal has been declared at the top
            $cart_subtotal_points = WC_Points_Rewards_Manager::calculate_points_for_discount($cart_subtotal); //there is a difference btw calculate_poitns() and calculate_points_for_discount
            if ($current_user_points >= $cart_subtotal_points) {
                $cart_json['reward_discount_points'] = $cart_subtotal_points;
                $cart_json['reward_discount'] = WC_Points_Rewards_Manager::calculate_points_value($cart_subtotal_points);
            } else {
                $cart_json['reward_discount_points'] = $current_user_points;
                $cart_json['reward_discount'] = $current_user_points_value;
            }
            $reward_discount = $cart_json['reward_discount'];
        }
    

    //calculate total
    $cart_json['total'] = ($subtotal + $shipping_cost) - $coupon_discount - $reward_discount;


    return $wpdb->update($cart_table, array(
        'cart_value' => json_encode($cart_json),
    ), array(
        'user' => $user_id
    ));
    }
}
if (!function_exists('sk_remove_reward')) {
    function sk_remove_reward($user_id) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";
    //update

    $cart_json = json_decode(sk_get_cart_value($user_id), true);
    
    $cart_json['apply_reward'] = false;

    $shipping_cost = $cart_json['shipping_cost'];
    $subtotal = $cart_json['subtotal'];
    $coupon_discount = $cart_json['coupon_discount'];

    // $reward_discount = $cart_json['reward_discount'];

    //RESET REWARDS CALCULATIONS
    if (class_exists("WC_Points_Rewards_Manager")) {
        $current_user_points = WC_Points_Rewards_Manager::get_users_points($user_id); //points
        $current_user_points_value = WC_Points_Rewards_Manager::get_users_points_value($user_id); //price
        $cart_json['user_points'] = $current_user_points;
        $cart_json['user_points_value'] = $current_user_points_value; //in price
        //calculate reward discount
        $cart_subtotal = $subtotal; //total items price - $subtotal has been declared at the top
        $cart_subtotal_points = WC_Points_Rewards_Manager::calculate_points_for_discount($cart_subtotal); //there is a difference btw calculate_poitns() and calculate_points_for_discount
        if ($current_user_points >= $cart_subtotal_points) {
            $cart_json['reward_discount_points'] = $cart_subtotal_points;
            $cart_json['reward_discount'] = WC_Points_Rewards_Manager::calculate_points_value($cart_subtotal_points);
        } else {
            $cart_json['reward_discount_points'] = $current_user_points;
            $cart_json['reward_discount'] = $current_user_points_value;
        }
    }

    //calculate total
    $cart_json['total'] = ($subtotal + $shipping_cost) - $coupon_discount; //dont deduct reward discount again


    return $wpdb->update($cart_table, array(
        'cart_value' => json_encode($cart_json),
    ), array(
        'user' => $user_id
    ));
    }
}

if (!function_exists('sk_delete_user_cart')) {
    function sk_delete_user_cart($user_id) {
    global $wpdb;
    $cart_table = $wpdb->prefix . "skye_carts";

    return $wpdb->delete($cart_table, array('user' => $user_id));
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
if (!function_exists("sk_get_product_variations")) {
    function sk_get_product_variations($product_id) {
        $product = wc_get_product($product_id);
        $array_return = array();
    if($product->is_type('variable')){
        foreach($product->get_available_variations() as $variation ){
            $attr_collections = array();
            // Variation ID
            $variation_id = $variation['variation_id'];
            $attr_collections["ID"] = $variation_id;

            // Attributes
            $attr_collections['attributes'] = array();
            foreach( $variation['attributes'] as $key => $value ){
                $taxonomy = str_replace('attribute_', '', $key );
                $attr_collections['attributes'][] = array(
                    "$taxonomy" => $value
                );
            }
            // Price
            $attr_collections['price'] = $variation['display_price'];
            $array_return[] = $attr_collections;
        }
        return $array_return;
    } else {
        return null;
    }
    }
}
if (!function_exists("sk_get_variation_attributes")) {
    function sk_get_variation_attributes($variation_id) {
        $product = wc_get_product($variation_id);
        if ($product->is_type('variation')) {
            return $product->get_attributes();
        } else {
            return null;
        }
    }
}
if (!function_exists('sk_cart_json_handler')) {
    function sk_cart_json_handler($user_id, $data, $old_cart_json = null) { //return array
    $product_id = $data['product_id'];
    $quantity = (isset($data['quantity'])) ? $data['quantity'] : 1;
    $product = wc_get_product($product_id);
    $replace_qty = (isset($data['replace_quantity'])) ? true : false;


    
    //setup attributes
    $attributes = $product->get_attributes();
    $return_attributes = array();
    if ($product->is_type('variation')) {
        if ($product->is_type('variation')) {
            $return_attributes['terms'] = $attributes;
            $return_attributes['selected'] = array();
            foreach($attributes as $key => $value) {
                $return_attributes['selected'][] = array(
                    'label' => wc_attribute_label($key),
                    'selected' => $product->get_attribute($key),
                );
            }

        }
    } else {
        foreach ($attributes as $attr => $attribute) {
            $return_attributes[$attr] = $attribute->get_data();
        }
    }

    $return_array = array();
    if (is_null($old_cart_json)) { //create new array
            //DEFAULT VALUE FOR NEW CART
            $return_array['user'] = $user_id;
            // // cat conditional if
            $return_array['is_empty'] = false;
            // $return_array['display_prices_including_tax'] = WC()->cart->display_prices_including_tax();

            // // Get cart totals
            $return_array['contents_count'] = 1;
            $return_array['has_coupon'] = false;
            $return_array['has_shipping'] = false;
            // $return_array['cart_subtotal'] = WC()->cart->get_cart_subtotal();
            // $return_array['subtotal_ex_tax'] = WC()->cart->subtotal_ex_tax;
            $return_array['subtotal'] = $product->get_price() * $quantity;
            $return_array['total'] = $product->get_price() * $quantity;
            $return_array['coupon_discount'] = 0;
            $return_array['shipping_cost'] = 0;
            // $return_array['displayed_subtotal'] = WC()->cart->get_displayed_subtotal();
            // $return_array['coupons'] = WC()->cart->get_coupons();
            // // $return_array['coupon_discount_amount'] = WC()->cart->get_coupon_discount_amount( 'coupon_code' );
            // $return_array['fees'] = WC()->cart->get_fees();
            // $return_array['discount_total'] = WC()->cart->get_discount_total();
            // $return_array['total'] = WC()->cart->get_total();
            // $return_array['total'] = WC()->cart->total;
            //add points
            $return_array['points'] = 0;
            if (class_exists("WC_Points_Rewards_Manager")) {
                $return_array['points'] = WC_Points_Rewards_Manager::calculate_points($product->get_price()) * $quantity;
            }
            if (!$return_array['points']) $return_array['points'] = 0;

            //setup Points and Rewards discount - default
            $return_array['apply_reward'] = false;
            $return_array['user_points'] = 0;
            $return_array['user_points_value'] = 0;
            $return_array['reward_discount_points'] = 0; //reward points needed for the discount
            $return_array['reward_discount'] = 0; //reward price needed for the discount

            if (class_exists("WC_Points_Rewards_Manager")) {
                $current_user_points = WC_Points_Rewards_Manager::get_users_points($user_id); //points
                $current_user_points_value = WC_Points_Rewards_Manager::get_users_points_value($user_id); //price
                $return_array['user_points'] = $current_user_points;
                $return_array['user_points_value'] = $current_user_points_value; //in price

                //calculate reward discount
                $cart_subtotal = $return_array['subtotal']; //total items price
                $cart_subtotal_points = WC_Points_Rewards_Manager::calculate_points_for_discount($cart_subtotal); //there is a difference btw calculate_poitns() and calculate_points_for_discount
                if ($current_user_points >= $cart_subtotal_points) {
                    $return_array['reward_discount_points'] = $cart_subtotal_points;
                    $return_array['reward_discount'] = WC_Points_Rewards_Manager::calculate_points_value($cart_subtotal_points);
                } else {
                    $return_array['reward_discount_points'] = $current_user_points;
                    $return_array['reward_discount'] = $current_user_points_value;
                }
            }
        




        //ITEMS in the cart
        $return_array['items'] = array();
        $return_array['items'][] = array( //products added
                    'ID' => $product_id,
                    'quantity' => $quantity,
                    'price' => $product->get_price(),
                    'subtotal' => $product->get_price() * $quantity,
                    'product_type' => $product->get_type(),
                    'product_type' => $product->get_type(),
                    'product_title' => $product->get_title(),
                    'product_image' => wp_get_attachment_url($product->get_image_id()),
                    'attributes' =>  $return_attributes,
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
                $return_array['items'] = array_values($return_array['items']); //reset the indexes
            }
        } else { //add to items
            if ($quantity > 0) {
            $return_array['items'][] = array(
                'ID' => $product_id,
                'quantity' => $quantity,
                'price' => $product->get_price(),
                'subtotal' => $product->get_price() * $quantity,
                'product_type' => $product->get_type(),
                'product_title' => $product->get_title(),
                'product_image' => wp_get_attachment_url($product->get_image_id()),
                'attributes' =>  $return_attributes,
            );
        } else {
            unset($return_array['items'][$search]);
            $return_array['items'] = array_values($return_array['items']); //reset the indexes
        }
        }


        //update general cart value
        $return_array['is_empty'] = (count($return_array['items']) > 0) ? false : true;
        $return_array['contents_count'] = count($return_array['items']);
        $return_array['subtotal'] = sk_cart_subtotal($return_array['items']);
        $return_array['shipping_cost'] = isset($return_array['shipping_cost']) ? $return_array['shipping_cost'] : 0;
        $subtotal = $return_array['subtotal'];
        $shipping_cost = $return_array['shipping_cost'];
        if (isset($return_array['has_coupon'])) {
            if ($return_array['has_coupon']) {
                //calculate coupon price for the cart
                $coup = new WC_Coupon($return_array['coupon']);
                $items_count = $return_array['contents_count'];
                if ($coup->get_discount_type() == "percent") { //calculate minus percentage of subtotal
                    $return_array['coupon_discount'] = (($coup->get_amount() / 100) * $subtotal);
                } elseif ($coup->get_discount_type() == "fixed_cart") { // minus amount from subtotal
                    $return_array['coupon_discount'] = $coup->get_amount();
                } elseif ($coup->get_discount_type() == "fixed_product") { // multiply amount by number of items in cart (and minus result from to subtotal)
                    $return_array['coupon_discount'] = ($coup->get_amount() * $items_count);
                }

            }
        }
        $return_array['coupon_discount'] = (isset($return_array['coupon_discount'])) ? $return_array['coupon_discount'] :0;
        //calculate total
        $coupon_discount = $return_array['coupon_discount'];
        $return_array['total'] = ($subtotal + $shipping_cost) - $coupon_discount;

        $return_array['has_shipping'] = (isset($return_array['has_shipping'])) ? $return_array['has_shipping'] : false;
        //calculate points
        $points = 0;
        if (class_exists("WC_Points_Rewards_Manager")) {
            foreach($return_array['items'] as $item) {
                $product = wc_get_product($item['ID']);
                $points += WC_Points_Rewards_Manager::calculate_points($product->get_price() * $item['quantity']);
            }
        }
        
        $return_array['points'] = $points;

        //setup Points and Rewards discount - update
        //$return_array is the cart json array
        $return_array['apply_reward'] = isset($return_array['apply_reward']) ? $return_array['apply_reward'] : false;

        if (class_exists("WC_Points_Rewards_Manager")) {
            $current_user_points = WC_Points_Rewards_Manager::get_users_points($user_id); //points
            $current_user_points_value = WC_Points_Rewards_Manager::get_users_points_value($user_id); //price
            $return_array['user_points'] = $current_user_points;
            $return_array['user_points_value'] = $current_user_points_value; //in price
            //calculate reward discount
            $cart_subtotal = $subtotal; //total items price - $subtotal has been declared at the top
            $cart_subtotal_points = WC_Points_Rewards_Manager::calculate_points_for_discount($cart_subtotal); //there is a difference btw calculate_poitns() and calculate_points_for_discount
            if ($current_user_points >= $cart_subtotal_points) {
                $return_array['reward_discount_points'] = $cart_subtotal_points;
                $return_array['reward_discount'] = WC_Points_Rewards_Manager::calculate_points_value($cart_subtotal_points);
            } else {
                $return_array['reward_discount_points'] = $current_user_points;
                $return_array['reward_discount'] = $current_user_points_value;
            }
        }


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
if (!function_exists('sk_is_user_driver')) {
    function sk_is_user_driver($user_id)
    {
        $user = get_user_by('ID', $user_id);
        if ($user) {
            $roles = $user->roles;
            return (in_array('skye_delivery_driver', $roles));
        } else {
            return false;
        }
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

        $return_array['skye_delivery_status'] = $order->get_meta('skye_order_delivery_status');
        $return_array['skye_driver_location'] = $order->get_meta('skye_order_driver_location');
        $return_array['sk_paypal_payment_id'] = $order->get_meta('sk_paypal_payment_id');

        // Get Order Shipping
        $return_array['shipping_method'] = $order->get_shipping_method();
        $return_array['shipping_methods'] = $order->get_shipping_methods();
        $return_array['shipping_to_display'] = $order->get_shipping_to_display();

        // Get Order Dates
        $return_array['date_created'] = $order->get_date_created();
        $return_array['date_modified'] = $order->get_date_modified();
        $date_modified = $order->get_date_modified();
        $return_array['date_modified_date'] = $date_modified->date("d-m-Y");
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
        $return_array['checkout_payment_url'] = $order->get_checkout_payment_url();
        $return_array['checkout_order_received_url'] = $order->get_checkout_order_received_url();
        $return_array['cancel_order_url'] = $order->get_cancel_order_url();
        $return_array['cancel_endpoint'] = $order->get_cancel_order_url_raw();
        $return_array['cancel_endpoint'] = $order->get_cancel_endpoint();
        $return_array['view_order_url'] = $order->get_view_order_url();
        $return_array['edit_order_url'] = $order->get_edit_order_url();

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
if (!function_exists("sk_find_variation_id")) {
    function sk_find_variation_id($product_id, $attributes) {
        return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new \WC_Product($product_id),
            $attributes
        );
    }
}
if (!function_exists("sk_get_user_shipping_address")) {
    function sk_get_user_shipping_address($user_id) {
        $customer = new WC_Customer( $user_id );
        $array = array();

        $array['username']     = $customer->get_username(); // Get username
        $array['user_email']   = $customer->get_email(); // Get account email
        $array['first_name']   = $customer->get_first_name();
        $array['last_name']    = $customer->get_last_name();
        $array['display_name'] = $customer->get_display_name();
        // Customer shipping information details (from account)
        $array['shipping_first_name'] = $customer->get_shipping_first_name();
        $array['shipping_last_name']  = $customer->get_shipping_last_name();
        $array['shipping_company']    = $customer->get_shipping_company();
        $array['shipping_address_1']  = $customer->get_shipping_address_1();
        $array['shipping_address_2']  = $customer->get_shipping_address_2();
        $array['shipping_city']       = $customer->get_shipping_city();
        $array['shipping_state']      = $customer->get_shipping_state();
        $array['shipping_postcode']   = $customer->get_shipping_postcode();
        $array['shipping_country']    = $customer->get_shipping_country();
        $array['shipping_phone']    = $customer->get_billing_phone(); // since there is not method to get phone for shipping
        $array['shipping_email']    = $customer->get_billing_email(); // since there is not method to get email for shipping


        return $array;
    }
}
if (!function_exists("sk_get_user_billing_address")) {
    function sk_get_user_billing_address($user_id) {
        $customer = new WC_Customer( $user_id );
        $array = array();

        $array['username']     = $customer->get_username(); // Get username
        $array['user_email']   = $customer->get_email(); // Get account email
        $array['first_name']   = $customer->get_first_name();
        $array['last_name']    = $customer->get_last_name();
        $array['display_name'] = $customer->get_display_name();
        // Customer billing information details (from account)
        $array['billing_first_name'] = $customer->get_billing_first_name();
        $array['billing_last_name']  = $customer->get_billing_last_name();
        $array['billing_company']    = $customer->get_billing_company();
        $array['billing_address_1']  = $customer->get_billing_address_1();
        $array['billing_address_2']  = $customer->get_billing_address_2();
        $array['billing_city']       = $customer->get_billing_city();
        $array['billing_state']      = $customer->get_billing_state();
        $array['billing_postcode']   = $customer->get_billing_postcode();
        $array['billing_country']    = $customer->get_billing_country();
        $array['billing_phone']    = $customer->get_billing_phone();
        $array['billing_email']    = $customer->get_billing_email();

        return $array;
    }
}
if (!function_exists("sk_get_user_info")) {
    function sk_get_user_info($user_id) {
        $user = (array) get_userdata($user_id);
        $user['shipping_address'] = sk_get_user_shipping_address($user_id);
        $user['billing_address'] = sk_get_user_billing_address($user_id);
        return $user;
    }
}
if (!function_exists("sk_user_exist_by_login_type")) {
    function sk_user_exist_by_login_type($login_type, $login_id) {
        $users = get_users(array(
            'meta_key' => 'sk_' . $login_type,
            'meta_value' => $login_id,
            'meta_compare' => '='
        ));
        if (count($users) > 0) {
            return true;
        } else {
            return false;
        }

    }
}
if (!function_exists("sk_get_user_info_by_login_type")) {
    function sk_get_user_info_by_login_type($login_type, $login_id) {
        $users = get_users(array(
            'meta_key' => 'sk_' . $login_type,
            'meta_value' => $login_id,
            'meta_compare' => '='
        ));
        if (count($users) > 0) {
            //get first user - as the owner
            $user_id = $users[0]->ID;
            return sk_get_user_info($user_id);
        } else {
            return null;
        }

    }
}

//also coppy code below for givephuck
if (!function_exists("sk_get_regions")) {
    function sk_get_regions() {
        $array = array();

        $wc_countries = new WC_Countries();
        $array['countries'] = $wc_countries->get_countries();
        $array['states'] = $wc_countries->get_states();

        return $array;
    }
}
if (!function_exists("sk_get_country_code")) {
    function sk_get_country_code($country_name) {
        $wc_countries = new WC_Countries();
        $countries = $wc_countries->get_countries();
        foreach($countries as $code => $name) {
            if ($name == $country_name) return $code;
        }
        return "";
    }
}
if (!function_exists("sk_get_state_code")) {
    function sk_get_state_code($country_code, $state_name) {
        $wc_countries = new WC_Countries();
        $states = $wc_countries->get_states();
        if (isset($states[$country_code])) {
            $states_in_the_country = $states[$country_code];
            foreach($states_in_the_country as $code => $name) {
                if ($name == $state_name) return $code;
            }
        } else {
            return "";
        }
    }
}

if (!function_exists("sk_update_cart_shipping")) {
    function sk_update_cart_shipping($user_id, $country_code, $state_code, $postcode, $provider = "woocommerce", $shipping_provider_cost = 0)
    {
		
        global $wpdb;
        $cart_table = $wpdb->prefix . "skye_carts";
        //update

        $cart_json = json_decode(sk_get_cart_value($user_id), true);

        if ($provider == "woocommerce") {
            //get zone
            $zone = WC_Shipping_Zones::get_zone_matching_package(array(
                'destination' => array(
                    'country' => $country_code,
                    'state' => $state_code,
                    'postcode' => $postcode
                )
            ));
            $zone_id = $zone->get_id();
            //get the shipping methods
            $shipping_methods = $zone->get_shipping_methods(true, 'values');
			//die(print_r($shipping_methods));
            $cart_json['shipping_methods'] = array();
            foreach ($shipping_methods as $method) {
                //calculate each product shipping class cost
                $classes_cost = 0;
                $items = $cart_json['items'];
                foreach ($items as $item) {
                    $item_product = wc_get_product($item['ID']);
                    $shipping_class_id = $item_product->get_shipping_class_id();
                    $instance = $method->instance_settings;

                    //skip if (type is not set or type != class)
                    if (!isset($method->type)) {
                        continue;
                    } else {
                        if ($method->type != "class")
                        continue;
                    }
                    //end of skip

                    if ($shipping_class_id) {
                        $unit_cost = $instance['class_cost_' . $shipping_class_id];
                        $classes_cost += (is_numeric($unit_cost)) ? $unit_cost : 0;
                    } else {
                        //no class cost
                        $unit_cost = isset($instance['no_class_cost']) ? $instance['no_class_cost'] : 0;
                        $classes_cost += (is_numeric($unit_cost)) ? $unit_cost : 0;
                    }
                }
                //add the shipping classes cost to the shipping cost
                
				$method_cost = is_numeric($method->cost) ? $method->cost : 0;
				if ($method->id == "free_shipping") {
					if ($method->requires == "min_amount") {
						if ($cart_json["subtotal"] >= $method->min_amount) {
							$cart_json['shipping_methods'][$method->id] = array(
                    			'title' => $method->method_title,
                    			'cost' => isset($method->cost) ? ($method_cost + $classes_cost) : $classes_cost,
								'id' => $method->id,
                			);
						}
					}
				} else {
					$cart_json['shipping_methods'][$method->id] = array(
                    	'title' => $method->method_title,
                    	'cost' => isset($method->cost) ? ($method_cost + $classes_cost) : $classes_cost,
						'id' => $method->id,
                	);
				}
                
            }

            $shipping_method = (isset($cart_json['shipping_methods']['flat_rate'])) ? 'flat_rate' : null; //default method else null
            $cart_json['shipping_method'] = $shipping_method;
            $cart_json['shipping_cost'] = (!is_null($shipping_method)) ? $cart_json['shipping_methods'][$shipping_method]['cost'] : 0;
        } else {
            $cart_json['shipping_method'] = "by_" . $provider;
            $cart_json['shipping_methods'] = null;
            $cart_json['shipping_cost'] = $shipping_provider_cost;
        }


        //calculate total
        $subtotal = $cart_json['subtotal'];
        $shipping_cost = $cart_json['shipping_cost'];
        $coupon_discount = (isset($cart_json['coupon_discount'])) ? $cart_json['coupon_discount'] : 0;
        $cart_json['total'] = ($subtotal + $shipping_cost) - $coupon_discount;

        //deduct reward discount if applied
        if ($cart_json['apply_reward']) {
            $cart_json['total'] -= $cart_json['reward_discount'];
        }

        if ($shipping_cost > 0) {
            $cart_json['has_shipping'] = true;
        } else {
            $cart_json['has_shipping'] = false;
        }


        return $wpdb->update($cart_table, array(
            'cart_value' => json_encode($cart_json),
        ), array(
            'user' => $user_id
        ));
    }
}
if (!function_exists("sk_update_cart_shipping_by_name")) {
    function sk_update_cart_shipping_by_name($user_id, $country_name, $state_name, $postcode, $provider = "woocommerce", $shipping_provider_cost = 0) {
        $country_code = sk_get_country_code($country_name);
        $state_code = sk_get_state_code($country_code, $state_name);
        return sk_update_cart_shipping($user_id, $country_code, $state_code, $postcode, $provider, $shipping_provider_cost);
    }
}
if (!function_exists("sk_change_cart_shipping_method")) {
    function sk_change_cart_shipping_method($user_id, $shipping_method) {
        global $wpdb;
        $cart_table = $wpdb->prefix . "skye_carts";
        //update

        $cart_json = json_decode(sk_get_cart_value($user_id), true);

        $subtotal = $cart_json['subtotal'];
        $coupon_discount = (isset($cart_json['coupon_discount'])) ? $cart_json['coupon_discount'] : 0;

        //update shipping_method and cost
        if (isset($cart_json['shipping_methods'])) {
            $shipping_methods = $cart_json['shipping_methods'];
            if (isset($shipping_methods[$shipping_method])) {
                $method = $shipping_methods[$shipping_method];
                $cart_json['shipping_method'] = $shipping_method;
                $cart_json['shipping_cost'] = isset($method['cost']) ? $method['cost'] : 0;
            }
        } 

        //calculate total
        $shipping_cost = $cart_json['shipping_cost'];
        $cart_json['total'] = ($subtotal + $shipping_cost) - $coupon_discount;

        //deduct reward discount if applied
        if ($cart_json['apply_reward']) {
            $cart_json['total'] -= $cart_json['reward_discount'];
        }

        if ($shipping_cost > 0 || $cart_json['shipping_method'] == "free_shipping") {
            $cart_json['has_shipping'] = true;
        } else {
            $cart_json['has_shipping'] = false;
        }

        //save data
        return $wpdb->update($cart_table, array(
            'cart_value' => json_encode($cart_json),
        ), array(
            'user' => $user_id
        ));
    }
}
if (!function_exists("sk_update_wishlist")) {
    function sk_update_wishlist($user_id, $product_id) {

            $user_wishlist = get_user_meta( $user_id, 'sk_wishlist',true);
            $wishlist = json_decode($user_wishlist, true);
            
            if ($wishlist && !empty($wishlist) && count($wishlist) > 0) {
                //update
                if (!sk_in_wishlist($user_id, $product_id))
                    $wishlist[] = $product_id;
            } else {
                //create new
                $wishlist = array($product_id);
            }
            return update_user_meta( $user_id, 'sk_wishlist', json_encode($wishlist));
    }
}
if (!function_exists("sk_remove_from_wishlist")) {
    function sk_remove_from_wishlist($user_id, $product_id) {

            $user_wishlist = get_user_meta( $user_id, 'sk_wishlist',true);
            $wishlist = json_decode($user_wishlist, true);
            
            if ($wishlist && !empty($wishlist) && count($wishlist) > 0) {
                //remove from wishlist
                foreach($wishlist as $pos => $wish_id) {
                    if ($wish_id == $product_id) {
                        unset($wishlist[$pos]);
                        $wishlist = array_values($wishlist);
                    }
                }
            }
            return update_user_meta( $user_id, 'sk_wishlist', json_encode($wishlist));
    }
}
if (!function_exists("sk_clear_wishlist")) {
    function sk_clear_wishlist($user_id, $product_id) {

            $user_wishlist = get_user_meta( $user_id, 'sk_wishlist',true);
            $wishlist = json_decode($user_wishlist, true);
            
            if ($wishlist && !empty($wishlist) && count($wishlist) > 0) {
                //update
                if (!sk_in_wishlist($user_id, $product_id))
                    $wishlist[] = $product_id;
            } else {
                //create new
                $wishlist = array($product_id);
            }
            return update_user_meta( $user_id, 'sk_wishlist', json_encode($wishlist));
    }
}
if (!function_exists("sk_get_wishlist")) {
    function sk_get_wishlist($user_id) {
            $user_wishlist = get_user_meta( $user_id, 'sk_wishlist',true);
            return json_decode($user_wishlist, true); 
    }
}
if (!function_exists("sk_wishlist_products")) {
    function sk_wishlist_products($user_id, $data) {

        $paged = isset($data['paged']) ? $data['paged'] : 1;
        $post_per_page = isset($data['per_page']) ? $data['per_page'] : 20;
        $product_cat = isset($data['cat']) ? $data['cat'] : null;
        $post_in = (count(sk_get_wishlist($user_id)) > 0 ? sk_get_wishlist($user_id) : [0]);
        $search = isset($data['search']) ? $data['search'] : null;

        $query_args = array(
            'post_type' => 'product',
            'posts_per_page' => $post_per_page,
            'paged' => $paged,
            'product_cat' => $product_cat,
            'post__in' => $post_in,
            's' => $search,
        );
        if (isset($data['meta_key'])) $query_args['meta_key'] = $data['meta_key'];
        if (isset($data['orderby'])) $query_args['orderby'] = $data['orderby'];
        if (isset($data['order'])) $query_args['order'] = $data['order'];
        if (isset($data['price_range'])) { 
            $price_btw = explode('|', $data['price_range']);
            $query_args['meta_query'] = array(
                array(
                    'key' => '_price',
                    'value' => array($price_btw[0], $price_btw[1]),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC'
                )
            );
        }
        // 			for featured products
        if (isset($data['featured'])) { 
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_visibility',
                    'field'    => 'name',
                    'terms'    => 'featured',
                )
            );
        }
        
        $query = new WP_Query($query_args);
        

        if (!$query->have_posts())
        return array(
            'results' => []
        );

        $product_array = array();
        
        while($query->have_posts()) {
            $query->the_post();
            $product_array["results"][] = sk_get_simple_product_array(get_the_ID(), $user_id);
            }

        // add pagination
        $product_array['paged'] = $paged;
        $product_array['pagination'] = sk_numeric_pagination($query, $data);
        wp_reset_query();
        
        
        return $product_array;
    }
}
if (!function_exists("sk_in_wishlist")) {
    function sk_in_wishlist($user_id, $product_id) {

            $user_wishlist = get_user_meta( $user_id, 'sk_wishlist',true);
            $wishlist = json_decode($user_wishlist, true);
            
            $result = false;
            if ($wishlist && !empty($wishlist) && count($wishlist) > 0) {
                foreach($wishlist as $product) {
                    if ($product == $product_id) $result = true;
                }
            }
            return $result;
    }
}
if (!function_exists('sk_order_assigned_to_this_driver')) {
    function sk_order_assigned_to_this_driver($order_id, $user_id) {
        $order = new WC_Order($order_id);
        return ($order->get_meta("skye_order_driver") == $user_id);
    }
}

if (!function_exists("sk_wc_order_add_discount")) {
    function sk_wc_order_add_discount($order_object, $title, $amount, $tax_class = '')
    {
        $order    = $order_object;
        $subtotal = $order->get_subtotal();
        $item     = new WC_Order_Item_Fee();

        if (strpos($amount, '%') !== false) {
            $percentage = (float) str_replace(array('%', ' '), array('', ''), $amount);
            $percentage = $percentage > 100 ? -100 : -$percentage;
            $discount   = $percentage * $subtotal / 100;
        } else {
            $discount = (float) str_replace(' ', '', $amount);
            $discount = $discount > $subtotal ? -$subtotal : -$discount;
        }

        $item->set_tax_class($tax_class);
        $item->set_name($title);
        $item->set_amount($discount);
        $item->set_total($discount);

        if ('0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled()) {
            $tax_for   = array(
                'country'   => $order->get_shipping_country(),
                'state'     => $order->get_shipping_state(),
                'postcode'  => $order->get_shipping_postcode(),
                'city'      => $order->get_shipping_city(),
                'tax_class' => $item->get_tax_class(),
            );
            $tax_rates = WC_Tax::find_rates($tax_for);
            $taxes     = WC_Tax::calc_tax($item->get_total(), $tax_rates, false);
            // print_pr($taxes);

            if (method_exists($item, 'get_subtotal')) {
                $subtotal_taxes = WC_Tax::calc_tax($item->get_subtotal(), $tax_rates, false);
                $item->set_taxes(array('total' => $taxes, 'subtotal' => $subtotal_taxes));
                $item->set_total_tax(array_sum($taxes));
            } else {
                $item->set_taxes(array('total' => $taxes));
                $item->set_total_tax(array_sum($taxes));
            }
            $has_taxes = true;
        } else {
            $item->set_taxes(false);
            $has_taxes = false;
        }
        $item->save();

        $order->add_item($item);
        // $order->calculate_totals($has_taxes);
        // $order->save();
    }
}

