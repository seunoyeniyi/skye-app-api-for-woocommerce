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