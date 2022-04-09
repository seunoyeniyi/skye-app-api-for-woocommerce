<?php
add_action( 'rest_api_init', function() {
    //SITE INFO page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/site-info', array(
            'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
            'callback' => function($data) {
                $arr = array();
                $arr['name'] = get_bloginfo('name'); // – Site title (set in Settings > General)
                $arr['description'] = get_bloginfo('description'); // – Site tagline (set in Settings > General)
                $arr['wpurl'] = get_bloginfo('wpurl'); // – The WordPress address (URL) (set in Settings > General)
                $arr['url'] = get_bloginfo('url'); // – The Site address (URL) (set in Settings > General)
                $arr['admin_email'] = get_bloginfo('admin_email'); // – Admin email (set in Settings > General)
                ///$arr['charset'] = get_bloginfo('charset'); // – The "Encoding for pages and feeds" (set in Settings > Reading)
                $arr['version'] = get_bloginfo('version'); // – The current WordPress version
                //$arr['html_type'] = get_bloginfo('html_type'); // – The content-type (default: "text/html"). Themes and plugins can override the default value using the ‘pre_option_html_type’ filter
                //$arr['text_direction'] = get_bloginfo('text_direction'); // – The text direction determined by the site’s language. is_rtl() should be used instead
                $arr['language'] = get_bloginfo('language'); // – Language code for the current site
                //$arr['stylesheet_url'] = get_bloginfo('stylesheet_url'); // – URL to the stylesheet for the active theme. An active child theme will take precedence over this value
                //$arr['stylesheet_directory'] = get_bloginfo('stylesheet_directory'); // – Directory path for the active theme. An active child theme will take precedence over this value
                //$arr['template_url'] = get_bloginfo('template_url'); // / ‘template_directory’ – URL of the active theme’s directory. An active child theme will NOT take precedence over this value
                //$arr['pingback_url'] = get_bloginfo('pingback_url'); // – The pingback XML-RPC file URL (xmlrpc.php)
                //$arr['atom_url'] = get_bloginfo('atom_url'); // – The Atom feed URL (/feed/atom)
                //$arr['rdf_url'] = get_bloginfo('rdf_url'); // – The RDF/RSS 1.0 feed URL (/feed/rdf)
                //$arr['rss_url'] = get_bloginfo('rss_url'); // – The RSS 0.92 feed URL (/feed/rss)
                //$arr['rss2_url'] = get_bloginfo('rss2_url'); // – The RSS 2.0 feed URL (/feed)
                //$arr['comments_atom_url'] = get_bloginfo('comments_atom_url'); // – The comments Atom feed URL (/comments/feed)
                //$arr['comments_rss2_url'] = get_bloginfo('comments_rss2_url'); // – The comments RSS 2.0 feed URL (/comments/feed)
                $arr["site_icon"] = get_site_icon_url();

                //BANNERS
                $arr["enable_slide_banners"] = get_option('sk_enable_slide_banners', 0);
                $arr["enable_big_banners"] = get_option('sk_enable_big_banners', 0);
                $arr["enable_carousel_banners"] = get_option('sk_enable_carousel_banners', 0);
                $arr["enable_thin_banners"] = get_option('sk_enable_thin_banners', 0);
                // $arr["enable_sale_banners"] = get_option('sk_enable_sale_banners', 0);
                $arr["enable_grid_banners"] = get_option('sk_enable_grid_banners', 0);
                $arr["enable_video_banners"] = get_option('sk_enable_video_banners', 0);

                return $arr;
            }
    ));
    //register user
    register_rest_route( SKYE_API_NAMESPACE_V1, '/register', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
            $password = (isset($data['password'])) ? $data['password'] : null;
            $register_type = (isset($data['register_type'])) ? $data['register_type'] : null;
            $replace_cart_user = (isset($data['replace_cart_user'])) ? $data['replace_cart_user'] : null;
            $tron_wallet = (isset($data['tron_wallet'])) ? $data['tron_wallet'] : null;
            $phone = $data['phone'] ?? null;

            //return ID of the newly registerd user
            if (!is_null($register_type)) {
                $register_id = (isset($data['register_id'])) ? $data['register_id'] : "";
                $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
                $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
                if (!sk_user_exist_by_login_type($register_type, $register_id)) {
                    //register the user;
                    $user_id = wp_create_user($username, wp_generate_password(8), $email);
                    if (is_numeric($user_id)) {
                        update_user_meta( $user_id, 'sk_' . $register_type, $register_id); //update the user meta to have the login type
                        return sk_get_user_info($user_id);
                    } else {
                        return $user_id;
                    }
                } else {
                    return array(
                        'code' => 'id_already_registered',
                        'message' => 'This account has been registered already!',
                        'data' => null
                    );
                }

            } else {
                $user_id = wp_create_user($username, $password, $email);
                if (!is_null($tron_wallet)) { //add tron wallet to user profile
                    update_user_meta( $user_id, 'tron_wallet', $tron_wallet );
                }
                if (!is_null($phone)) {
                    update_user_meta( $user_id, 'sk_phone_login', $phone);
                }



            if (is_numeric($user_id)) {
                if (!is_null($replace_cart_user)) {
                            if (sk_user_cart_exists($replace_cart_user))
                                sk_change_cart_user_id($replace_cart_user, $user_id);
                }
                return sk_get_user_info($user_id);
            } else {
                return $user_id;
            }
            }
            
        }
    ));
    //register user
    register_rest_route( SKYE_API_NAMESPACE_V1, '/register-username', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
            $password = (isset($data['password'])) ? $data['password'] : null;
            $register_type = (isset($data['register_type'])) ? $data['register_type'] : null;
            $replace_cart_user = (isset($data['replace_cart_user'])) ? $data['replace_cart_user'] : null;
            $country_code = $data['country_code'] ?? 'IN';

            //return ID of the newly registerd user
            if (!is_null($register_type)) {
                $register_id = (isset($data['register_id'])) ? $data['register_id'] : "";
                $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
                $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;

                if (!sk_user_exist_by_login_type($register_type, $register_id)) {
                    //register the user;
                    $user_id = wp_create_user($username, wp_generate_password(8), $email);
                    if (is_numeric($user_id)) {
                        update_user_meta( $user_id, 'sk_' . $register_type, $register_id); //update the user meta to have the login type
                        return sk_get_user_info($user_id);
                    } else {
                        return $user_id;
                    }
                } else {
                    return array(
                        'code' => 'id_already_registered',
                        'message' => 'This account has been registered already!',
                        'data' => null
                    );
                }

            } else {
                $user_id = wp_create_user($username, $username, $email); //let's use username(phone number) as the password also

                if (is_numeric($user_id)) {
                    //update user information
                    add_user_meta( $user_id, "sk_phone_login", $username, true );

                    if (!is_null($replace_cart_user)) {
                                if (sk_user_cart_exists($replace_cart_user))
                                    sk_change_cart_user_id($replace_cart_user, $user_id);
                    }
                    return sk_get_user_info($user_id);
                } else {
                    return $user_id;
                }
            }
            
        }
    ));
    //confirm user login
    register_rest_route( SKYE_API_NAMESPACE_V1, '/authenticate', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $email = (isset($data['email'])) ? sanitize_email($data['email']) : null;
            $password = (isset($data['password'])) ? $data['password'] : null;
            $replace_cart_user = (isset($data['replace_cart_user'])) ? $data['replace_cart_user'] : null;
            $login_type = (isset($data['login_type'])) ? $data['login_type'] : null;

            if (!is_null($login_type)) {
                $login_id = (isset($data['login_id'])) ? $data['login_id'] : null;
                if (sk_user_exist_by_login_type($login_type, $login_id)) {
                    $user_info = sk_get_user_info_by_login_type($login_type, $login_id);
                    if (!is_null($replace_cart_user)) {
                            if (sk_user_cart_exists($replace_cart_user))
                                sk_change_cart_user_id($replace_cart_user, $user_info['ID']);
                    }
                    //push notification
                    sk_push_notification(get_user_meta( $user_info["ID"], 'sk_device_id'), array('title'=>' Welcome', 'body'=>'New login detected!'));
                    return $user_info;
                } else {
                    return array(
                        'code' => 'unregistered_id',
                        'message' => 'This account has not been registered!',
                        'data' => null
                    );
                }
            } else {
                $auth = wp_authenticate( (!is_null($username)) ? $username : $email, $password);
                //replace cart user if set
                if (!is_null($replace_cart_user)) {
                    //if user exists
                    if (property_exists($auth, 'data')) {
                        if (sk_user_cart_exists($replace_cart_user))
                            sk_change_cart_user_id($replace_cart_user, $auth->data->ID);
                    }
                }

                return $auth;
            }
        }
    ));
    //confirm user login by username (phone number)
    register_rest_route( SKYE_API_NAMESPACE_V1, '/authenticate-username', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $username = (isset($data['username'])) ? sanitize_text_field($data['username']) : null;
            $replace_cart_user = (isset($data['replace_cart_user'])) ? $data['replace_cart_user'] : null;
            $login_type = (isset($data['login_type'])) ? $data['login_type'] : null;

            if (is_null($username) || empty($username)) {
                return array(
                    'code' => 'username-not-set',
                    'message' => 'Please provide username.',
                    'data' => null
                );
            }

            if (!is_null($login_type)) {
                $login_id = (isset($data['login_id'])) ? $data['login_id'] : null;
                if (sk_user_exist_by_login_type($login_type, $login_id)) {
                    $user_info = sk_get_user_info_by_login_type($login_type, $login_id);
                    if (!is_null($replace_cart_user)) {
                            if (sk_user_cart_exists($replace_cart_user))
                                sk_change_cart_user_id($replace_cart_user, $user_info['ID']);
                    }
                    //push notification
                    sk_push_notification(get_user_meta( $user_info["ID"], 'sk_device_id'), array('title'=>' Welcome', 'body'=>'New login detected!'));
                    return $user_info;
                } else {
                    return array(
                        'code' => 'unregistered_id',
                        'message' => 'This account has not been registered!',
                        'data' => null
                    );
                }
            } else {
                
                $ret = array();

                $user_id = username_exists( $username); //first check as username

                if (!$user_id)
                    $user_id = sk_get_user_info_by_meta_data("sk_phone_login", $username); //use sk_phone_login
          
                if ($user_id) {
                    $ret = sk_get_user_info($user_id);
                } else {
                    $ret = array(
                        'code' => 'username-not-exist',
                        'message' => 'Username does not exist!',
                        'data' => null
                    );
                }
                
                //replace cart user if set
                if (!is_null($replace_cart_user)) {
                    //if user exists
                    if ($user_id) {
                        if (sk_user_cart_exists($replace_cart_user))
                            sk_change_cart_user_id($replace_cart_user, $user_id);
                    }
                }

                return $ret;
            }
        }
    ));
    //update user billing address
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-billing-address/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
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
            'permission_callback' => 'sk_api_security_check',
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

            if (isset($data['username']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_login' => $data['username']],
                    ['ID' => $user_id]
                );
            }

            if (isset($data['email']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_email' => $data['email']],
                    ['ID' => $user_id]
                );
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
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id = $data['user'];
            $arr = sk_get_user_info($user_id);
            if (isset($data['with_regions'])) $arr['regions'] = sk_get_regions();

            return $arr;
        }
    ));
    //update user info
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-user-info/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
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
            if (isset($data['username']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_login' => $data['username']],
                    ['ID' => $user_id]
                );
            }
            if (isset($data['email']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_email' => $data['email']],
                    ['ID' => $user_id]
                );
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


            
            return sk_get_user_info(wp_update_user($array_to_change));
        }
    ));
    //update user shipping address
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-user-shipping-address/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
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
                sk_update_cart_shipping($user_id, $data['selected_country'], 
                    $data['selected_state'], 
                    (isset($data['postcode'])) ? $data['postcode'] : "", 
                    (isset($data['shipping_provider'])) ? $data['shipping_provider'] : "woocommerce",
                    (isset($data['shipping_provider_cost'])) ? $data['shipping_provider_cost'] : 0
                );
            } else { //use name of the shipping country and state
                sk_update_cart_shipping_by_name(
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
            if (isset($data['username']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_login' => $data['username']],
                    ['ID' => $user_id]
                );
            }
            if (isset($data['email']) && ($data['access'] ?? "") == "2546") {
                global $wpdb;
                $wpdb->update(
                    $wpdb->users,
                    ['user_email' => $data['email']],
                    ['ID' => $user_id]
                );
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
    //update user billing address
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-user-billing-address/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id = $data['user'];
            $customer = new WC_Customer($user_id);
            if (isset($data['first_name'])) $customer->set_billing_first_name($data['first_name']);
            if (isset($data['last_name'])) $customer->set_billing_last_name($data['last_name']);
            if (isset($data['company'])) $customer->set_billing_company($data['company']);
            if (isset($data['country'])) $customer->set_billing_country($data['country']);
            if (isset($data['state'])) $customer->set_billing_state($data['state']);
            if (isset($data['postcode'])) $customer->set_billing_postcode($data['postcode']);
            if (isset($data['city'])) $customer->set_billing_city($data['city']);
            if (isset($data['address_1'])) $customer->set_billing_address($data['address_1']);
            if (isset($data['address_2'])) $customer->set_billing_address_2($data['address_2']);
            if (isset($data['email'])) $customer->set_billing_email($data['email']);
            if (isset($data['phone'])) $customer->set_billing_phone($data['phone']);
            
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
     //change user password
     register_rest_route( SKYE_API_NAMESPACE_V1, '/change-password/(?P<user_id>.*?)/(?P<old_password>.*?)/(?P<new_password>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
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
            'permission_callback' => 'sk_api_security_check',
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
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $paged = isset($data['paged']) ? $data['paged'] : 1;
            $post_per_page = isset($data['per_page']) ? $data['per_page'] : 20;
            $product_cat = isset($data['cat']) ? $data['cat'] : null;
            $post_in = isset($data['ids']) ? explode(',', rtrim($data['ids'], ',')) : null;
            $search = isset($data['search']) ? $data['search'] : null;
			$tag = isset($data['tag']) ? $data['tag'] : null;

            $query_args = array(
                'post_type' => 'product',
                'posts_per_page' => $post_per_page,
                'paged' => $paged,
                'product_cat' => $product_cat,
                'post__in' => $post_in,
                's' => $search,
            );
			if (!is_null($tag)) {
				$query_args["product_tag"] = $tag;
			}
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
            return null;

            $product_array = array();

            while($query->have_posts()) {
                $query->the_post();
                $product_array["results"][] = sk_get_product_array(get_the_ID(), isset($data['user_id']) ? $data['user_id'] : null);
                }
    
            // add pagination
            $product_array['paged'] = $paged;
            $product_array['pagination'] = sk_numeric_pagination($query, $data);
            wp_reset_query();
            return $product_array;
        }
    ));
    //simple products page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/simple-products', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $paged = isset($data['paged']) ? $data['paged'] : 1;
            $post_per_page = isset($data['per_page']) ? $data['per_page'] : 20;
            $product_cat = isset($data['cat']) ? $data['cat'] : null;
            $post_in = isset($data['ids']) ? explode(',', rtrim($data['ids'], ',')) : null;
            $search = isset($data['search']) ? $data['search'] : null;
			$tag = isset($data['tag']) ? $data['tag'] : null;
			$hide_description = isset($data['hide_description']);
            $show_variation = isset($data['show_variation']);
            $show_outofstock = isset($data['show_outofstock']);

            $query_args = array(
                'post_type' => 'product',
                'posts_per_page' => $post_per_page,
                'paged' => $paged,
                'product_cat' => $product_cat,
                'post__in' => $post_in,
                's' => $search,
            );
			if (!is_null($tag)) {
				$query_args["product_tag"] = $tag;
			}
            if (isset($data['meta_key'])) $query_args['meta_key'] = $data['meta_key'];
            if (isset($data['orderby'])) {
                $query_args['orderby'] = $data['orderby'];
                if ($data['orderby'] == "popularity") {
                    $query_args['orderby'] = 'meta_value_num';
                    $query_args['meta_key'] = 'total_sales';
                }
            }
            if (isset($data['order'])) $query_args['order'] = $data['order'];
            
            if (isset($data['price_range'])) {
				$price_btw = explode('|', $data['price_range']);
				if (strpos($data['price_range'], ',') !== false) {
					$price_btw = explode(',', $data['price_range']);
				}
                $query_args['meta_query'][] = array(
                    array(
                        'key' => '_price',
                        'value' => array($price_btw[0], $price_btw[1]),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC'
                    )
                );
            }
            if (isset($data['brand'])) {
                if (taxonomy_exists('product_brand')) {
                    $query_args['tax_query'][] = array(
                        array(
                            'taxonomy'  => 'product_brand', // Woocommerce product category taxonomy
                            'field'     => 'slug', // can be: 'name', 'slug' or 'term_id'
                            'terms'     => explode(',', $data['brand']),
                        )
                    );
                }
            }
            // 			for featured products
			if (isset($data['featured'])) { 
                $query_args['tax_query'][] = array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    )
                );
            }

            //Colour
            if (isset($data['color'])) {
                $color = explode(',', $data['color']);
              
                $query_args['tax_query'][] = array( array(
                        'taxonomy'        => 'pa_color',
                        'field'           => 'slug',
                        'terms'           =>  $color,
                        'operator'        => 'IN',
                    ) );
            }

            //Size
            if (isset($data['size'])) {
                $size = explode(',', $data['size']);
				
                $query_args['tax_query'][] = array( array(
                        'taxonomy'        => 'pa_size',
                        'field'           => 'slug',
                        'terms'           =>  $size,
                        'operator'        => 'IN',
                    ) );
            }
			
			//Size kids
			if (isset($data['pa_size-kids'])) {
                $size = explode(',', $data['pa_size-kids']);
				
                $query_args['tax_query'][] = array( array(
                        'taxonomy'        => 'pa_size-kids',
                        'field'           => 'slug',
                        'terms'           =>  $size,
                        'operator'        => 'IN',
                    ) );
            }
            

            if (!$show_outofstock) {
                $query_args['tax_query'][] = array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => array('outofstock'),
                        'operator' => 'NOT IN'
                    )
                );
            }
            

            $query_args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_price',
                    'value'   => '',
                    'type'    => 'numeric',
                    'compare' => '!='
                ),
                array(
                    'key'     => '_price',
                    'value'   => 0,
                    'type'    => 'numeric',
                    'compare' => '!='
                )
            );

   

            
            $query = new WP_Query($query_args);

            if (!$query->have_posts())
            return null;

            $product_array = array();
            
            while($query->have_posts()) {
                $query->the_post();
                $product_array["results"][] = sk_get_simple_product_array(get_the_ID(), isset($data['user_id']) ? $data['user_id'] : null, $hide_description, $show_variation);
            }
    
            //add brand key (if site has brand taxonomy)
            if (taxonomy_exists('product_brand')) {
                $brands = get_terms( 'product_brand', array(
                    'orderby'  => 'name'
                ) );
                $brands = array_values($brands); //reset $brands back to array
                $product_array['brands'] = $brands;
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
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $product_id = $data['id'];
            $related_product_per_page = isset($data['related_product_per_page']) ? $data['related_product_per_page'] : 10;
            $user_id = isset($data['user']) ? $data['user'] : null;
            if (isset($data['user_id'])) $user_id = $data['user_id'];

            $info_array = sk_get_product_array($product_id, $user_id);

            if (isset($data['show_related'])) {
                $info_array['related_products'] = array();
                $related_ids  = wc_get_related_products($product_id, $related_product_per_page);
                foreach($related_ids as $id) {
                    $info_array['related_products'][] = sk_get_simple_product_array($id, $user_id);
                }
            }
                

            return $info_array;
        }
    ));

    //add to cart page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/add-to-cart/(?P<product_id>\d+)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
 
            $user_id = null;

            //confirm if data['user'] isset
            if (isset($data['user'])) {
                $user_id = $data['user'];
                //if is a registered user
                if (!sk_user_exists($user_id)) {
                    //if user id exists in cart if not registered user
                    if (!sk_user_cart_exists($user_id)) 
                        return array('user_cart_not_exists' => true);
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
            $return_array["to_be_added"] = $data["product_id"];

            //merget the cart value
            return array_merge($return_array, json_decode(sk_get_cart_value($user_id), true));
        }
    ));

    //add cart coupon
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-cart-coupon/(?P<user>.*?)/(?P<coupon>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
 
            $user_id = $data['user'];
            sk_update_cart_coupon($user_id, $data['coupon']);

            return json_decode(sk_get_cart_value($user_id), true);
        }
    ));
    //apply reward discount
    register_rest_route( SKYE_API_NAMESPACE_V1, '/apply-cart-reward/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
 
            $user_id = $data['user'];
            sk_apply_reward($user_id);

            return json_decode(sk_get_cart_value($user_id), true);
        }
    ));
    //remove reward discount
    register_rest_route( SKYE_API_NAMESPACE_V1, '/remove-cart-reward/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
 
            $user_id = $data['user'];
            sk_remove_reward($user_id);

            return json_decode(sk_get_cart_value($user_id), true);
        }
    ));

    //cart info page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/cart/(?P<user>.*?)', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            global $wpdb;
            

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
    //create order page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/create-order/(?P<user>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id = $data['user'];
            $allow_guest = (isset($data['allow_guest']));
            $status = (isset($data['status'])) ? sanitize_text_field($data['status']) : 'wc-pending';
            $order_note = (isset($data['order_note'])) ? sanitize_text_field($data['order_note']) : 'Ordered from API';
            $payment_method = (isset($data['payment_method'])) ? $data['payment_method'] : null;
        
            $return_array = array();
            //to add address to url: url?billing_address%5Bfirst_name%5D=SEUN&billing_address%5Blast_name%5D=OYENIYI....
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
            //add coupon if any
            if ($cart['has_coupon']) {
                $order->apply_coupon($cart['coupon']);
            }
            
            //set address
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

            if (!is_null($billing_address)) {
                $order->set_address($billing_address, 'billing');
            } else { //use profiel shipping address
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
            $item->set_method_title($method_title);
            // $item->set_method_id( "amazon_flat_rate:17" );
            $item->set_total( $cart['shipping_cost'] );
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

    //list orders page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/orders/(?P<user>.*?)', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id = $data['user'];
            $status = (isset($data['status'])) ? $data['status'] : array('wc-processing', 'wc-on-hold', 'wc-completed', 'wc-pending');
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
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $order_id = $data['id'];
            $user_id = $data['user_id'];
            $order = sk_order_info($order_id);
            if ((isset($order['customer_id'])) ? ($order['customer_id'] == $user_id || $order['user_id'] == $user_id) : false) {
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
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id = $data['user_id'];
            $order_id  = $data['order_id'];
            $status  = isset($data['status']) ? sanitize_text_field($data['status']) : null;
            $order_note  = isset($data['order_note']) ? sanitize_text_field($data['order_note']) : 'Updated from API';
            $paypal_payment_id = (isset($data['paypal_payment_id'])) ? $data['paypal_payment_id'] : null;
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
            //$return_array['user_exists'] = sk_user_exists($user_id);
            //if (!sk_user_exists($user_id))
            //    return $return_array;
            
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
            //for paypal
            if (!is_null($paypal_payment_id)) {
                update_post_meta($order_id, 'sk_paypal_payment_id', $paypal_payment_id);
                update_post_meta($order_id, '_transaction_id', $paypal_payment_id);
            }

            //order info
            $return_array['order_info'] = sk_order_info($order_id);

            //clear cart if set
            if (isset($data['clear_cart'])) sk_delete_user_cart($user_id);

            return $return_array;
        }
    ));
    //categories page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/categories', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $with_sub = (isset($data['with_sub'])) ? true : false; //true for listing sub cateogries with parent output list and false for removing sub categories from parent output list
            $hide_empty = (isset($data['hide_empty'])) ? 1 : 0;
            $order_by = (isset($data['order_by'])) ? $data['order_by'] : null;
            $with_uncategory = (isset($data['with_uncategory'])) ? true : false;
            // $per_page = (isset($data['per_page'])) ? $data['per_page'] : '';


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
            
            foreach($categories as  $category) {
            if (!$with_uncategory) {
                if ($category->slug == 'uncategorized')
                    continue;
            }
                if ($with_sub) { //will join sub categories to the parent list
                    $cat_arr = array(
                        'ID' => $category->term_id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image' => wp_get_attachment_url( get_term_meta( $category->term_id, 'thumbnail_id', true ) ),
                        'icon' => wp_get_attachment_url( get_term_meta( $category->term_id, 'product_cat_icon', true ) ),
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
                                'slug' => $cat->slug,
                                'image' => wp_get_attachment_url( get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true ) ),
                                'icon' => wp_get_attachment_url( get_term_meta( $cat->term_id, 'product_cat_icon', true ) ),
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
                            'slug' => $category->slug,
                            'image' => wp_get_attachment_url( get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true ) ),
                            'icon' => wp_get_attachment_url( get_term_meta( $category->term_id, 'product_cat_icon', true ) ),
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
                                    'slug' => $cat->slug,
                                    'image' => wp_get_attachment_url( get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true ) ),
                                    'icon' => wp_get_attachment_url( get_term_meta( $cat->term_id, 'product_cat_icon', true ) ),
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
            'permission_callback' => 'sk_api_security_check',
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
						'slug' => $tag->slug,
                        'link' => get_term_link($tag->slug, 'product_tag'),
                        'count' => $tag->count
                );
            }

            return $array_return;

        }
    ));
    //get variation from attributes;
    register_rest_route( SKYE_API_NAMESPACE_V1, '/product-variation/(?P<product_id>\d+)', array(
            'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
            'callback' => function($data) {
                function treat($s) {
                                    $s = strtolower($s);
                					$s = sanitize_title($s);
                					return $s;
                                }
                $product_id = $data['product_id'];
                $attribute_params = $data->get_query_params();
                $attribute_params = array_change_key_case($attribute_params, CASE_LOWER); //to be able to match
                $attribute_params = array_map('treat', $attribute_params); //to be able to match or compare to variation attribute
                // return $attribute_params;
                function variation_attribues_to_simple_array($attributes) {
                    $array_to_return = array();
                    foreach($attributes as $key => $attr) {
                        foreach($attr as $attr_key => $attr_value) {
                            $array_to_return[$attr_key] = $attr_value;
                        }
                    }
                    return $array_to_return;
                }
                $variations = sk_get_product_variations($product_id);
                // return $variations;
                // return $attribute_params;
                foreach($variations as $variation) {
                    $variation_attributes = $variation['attributes'];
                    $variation_attributes = variation_attribues_to_simple_array($variation_attributes);
                    $variation_attributes = array_change_key_case($variation_attributes, CASE_LOWER);
                    $variation_attributes = array_map('treat', $variation_attributes); 
                    if ($variation_attributes == $attribute_params) return $variation;

                }

                return null;
            }
    ));

    //COPY CODE AFTER THIS FOR GIVEPHUCK UPDATE
    register_rest_route( SKYE_API_NAMESPACE_V1, '/regions', array(
            'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
            'callback' => function($data) {  
                return sk_get_regions();
            }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/test', array(
            'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
            'callback' => function($data) {
                

                
                $array = array();
                $delivery_zones = WC_Shipping_Zones::get_zones();
                foreach($delivery_zones as $zone) {
                    $newarr = array();
                    $newarr['ID'] = $zone['id'];
                    $newarr['zone_name'] = $zone['zone_name'];
                    $newarr['zone_locations'] = array();
                    foreach($zone['zone_locations'] as $location) {
                        $newarr['zone_locations'][] = array(
                            'code' => $location->code,
                            'type' => $location->type
                        );
                    }
                    $newarr['formatted_zone_location'] = $zone['formatted_zone_location'];
                    $newarr['shipping_methods'] = array();
                    foreach($zone['shipping_methods'] as $method) {
                        $newarr['shipping_methods'][] = array(
                            'id' => $method->id,
                            'title' => $method->method_title,
                            'cost' => $method->cost,
                        );
                    }
                    $array[] = $newarr;
                }
                    
                return $array;
            }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/change-cart-shipping-method/(?P<user_id>.*?)/(?P<shipping_method>.*?)', array(
            'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
            'callback' => function($data) {
                $array = array();
                $user_id = $data['user_id'];
                $method = $data['shipping_method'];
                sk_change_cart_shipping_method($user_id, $method);
                
                return json_decode(sk_get_cart_value($user_id), true);
            }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/banners', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix
            
            $banner_type = (isset($data['type'])) ? $data['type'] : "slide";
            
            $count = 0;
            $arrays = array(
                // 'enabled' => (get_option('sk_app_enable_banner_slides', 0)) ? true : false,
                'empty' => true,
                'count' => $count,
                'results' => null
            );

            //fetch banners
            $result = $wpdb->get_results("SELECT * FROM $table_name WHERE banner_type='$banner_type' ORDER BY ID ASC");
            $count = count($result);
            if ($result && count($result) > 0) {
                foreach ($result as $item) {
                    $arrays['results'][] = array(
                        'image' => ($item->banner_type == "video") ? wp_get_attachment_url($item->image) : wp_get_attachment_image_src( $item->image, null)[0],
                        'title' => $item->title,
                        'description' => $item->description,
                        'on_click_to' => $item->on_click_to,
                        'category' => $item->category,
                        'url' => $item->url,
                    );
                }
                
            }


            //update count and empty
            $arrays['count'] = $count;
            $arrays['empty'] = ($count > 0);
            return $arrays;
        }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/wishlists/(?P<user_id>.*?)', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id =  $data['user_id'];
            $hide_description = isset($data['hide_description']);
            $show_variation = isset($data['show_variation']);

            return sk_wishlist_products($user_id, $data, $hide_description, $show_variation);
        }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/add-to-wishlist/(?P<user_id>.*?)/(?P<product_id>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id =  $data['user_id'];
            $product_id = $data['product_id'];
            
            sk_update_wishlist($user_id, $product_id);
            
            //GET WISH LIST PRODUCTS
            return sk_wishlist_products($user_id, $data);
        }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/remove-from-wishlist/(?P<user_id>.*?)/(?P<product_id>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $user_id =  $data['user_id'];
            $product_id = $data['product_id'];
            
            sk_remove_from_wishlist($user_id, $product_id);
            
            //GET WISH LIST PRODUCTS
            return sk_wishlist_products($user_id, $data);
        }
    ));

    //FOR GIVEPHUCK ONLY
    register_rest_route( SKYE_API_NAMESPACE_V1, '/update-wallet-address/(?P<user_id>.*?)/(?P<address>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $address = $data['address'];
            $user_id = $data['user_id'];
            $update = update_user_meta( $user_id, 'tron_wallet', $address );
            if ($update) {
                //get address again
                return array(
                    'address' => get_the_author_meta( 'tron_wallet', $user_id )
                );
            } else {
                return array(
                    'code' => "error_occured",
                    'message' => "Unable to save address",
                    'data' => null
                );
            }
        }
    ));
    register_rest_route( SKYE_API_NAMESPACE_V1, '/wallet-address/(?P<user_id>.*?)', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {  
            $user_id = $data['user_id'];
            return array(
                'address' => get_the_author_meta( 'tron_wallet', $user_id )
            );
        }
    ));

    register_rest_route( SKYE_API_NAMESPACE_V1, "/test-zone", array(
        'methods' => "GET",
        'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {

            //get zone
            $zone = WC_Shipping_Zones::get_zone_matching_package(array(
                'destination' => array(
                    'country' => $data['country'],
                    'state' => $data['state'],
                    'postcode' => $data['postcode']
                )
            ));
            $zone_id = $zone->get_id();
            //get the shipping methods

            $shipping_methods = $zone->get_shipping_methods(true, 'values');
            $arr = array();
            $arr['zone_id'] = $zone_id;
            $arr['zone_name'] = $zone->get_zone_name();
            $arr['methods'] = array();
            foreach ($shipping_methods as $method) {
                $arr['methods'][] = array(
                    'id' => $method->id,
                    'title' => $method->method_title,
                    'cost' => $method->cost,
                );
            }

            return $arr;
        }
    ));

    //save user device
    register_rest_route( SKYE_API_NAMESPACE_V1, '/save-user-device/(?P<user_id>.*?)', array(
        'methods' => 'POST',
        'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $device_id = $data["device"];
            $user_id = $data['user_id'];
            $array = array(
                "status" => "failed",
                "device_id" => ""
            );
           
            if (update_user_meta( $user_id, "sk_device_id", $device_id)) {
                $array["status"] = "success";
            }
            $array["device_id"] = get_user_meta( $user_id, "sk_device_id", true);


            return $array;
        }
    ));
    //add device
    register_rest_route( SKYE_API_NAMESPACE_V1, '/add-device/(?P<user_id>.*?)', array(
        'methods' => 'POST',
        'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $device_id = $data["device"];
            $user_id = $data['user_id'];

            
            
            $array = array(
                "status" => "failed",
                "user_device_saved" => false,
                "device_saved" => false,
                "device_id" => ""
            );
           
            if (sk_user_exists($user_id)) {
                if (update_user_meta( $user_id, "sk_device_id", $device_id)) {
                    $array["status"] = "success";
                    $array["user_device_saved"] = true;
                }
            }

            //user exists OR NOT all devices must be saved
            $devices = get_option( "sk_devices", "[]");
            $devices = json_decode($devices, true);

            if (!in_array($device_id, $devices)) {
                $devices[] = $device_id;
            }

            //save back the updated devices
            if (update_option( "sk_devices", json_encode($devices))) {
                $array["status"] = "success";
                $array["device_saved"] = true;
            }
            

            $array["device_id"] = $device_id;

            return $array;
        }
    ));
    //devices
    register_rest_route( SKYE_API_NAMESPACE_V1, '/devices', array(
        'methods' => 'GET',
        'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
           
            $return_arr = array(
                'users_with_device' => array(),
                'devices' => array()
            );

            
            $devices = get_option( "sk_devices", "[]");
            $devices = json_decode($devices, true);
            

            //users with device
            $users = get_users(array(
                'meta_key'     => 'sk_device_id',
            ));
        
            foreach ($users as $user) {
                $user_device = get_user_meta( $user->ID, 'sk_device_id', true);
                $return_arr["users_with_device"][] = array(
                    "ID" => $user->ID,
                    "device" =>  $user_device
                );
                
                if (!in_array($user_device, $devices)) {
                    $devices[] = $user_device;
                }
            }

            $return_arr["devices"] = $devices;

            return $return_arr;
        }
    ));

    //specific attributes name and values page
    register_rest_route( SKYE_API_NAMESPACE_V1, '/attributes', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
           $name = (isset($data['name'])) ? $data['name'] : "all";
           $hide_empty = isset($data['hide_empty']);

            $array = array();
            $terms = wc_get_attribute_taxonomies();

           if ($name == "all") {
            foreach ($terms as $term) {
                $term->terms = get_terms(
                    array(
                        'taxonomy' => 'pa_' . $term->attribute_name,
                        'hide_empty' => $hide_empty
                    )
                );
                $array[] = $term;
            }
           } else {

            foreach($terms as $term) {
                if ($term->attribute_name == $name) {
                    $term->terms = get_terms(
                        array(
                            'taxonomy' => 'pa_' . $term->attribute_name,
                            'hide_empty' => $hide_empty
                        )
                    );
                    return $term;
                }
            }


           }
           return $array;

        }
    ));


    //search -- suggestive search
    register_rest_route( SKYE_API_NAMESPACE_V1, '/search', array(
        'methods' => 'GET',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $s = $data['s'];
        

            $query_args = array(
                'post_type' => 'product',
                'posts_per_page' => 10,
                's' => $s,
            );
			
           
            $query = new WP_Query($query_args);

            if (!$query->have_posts())
                return array();

            $product_array = array();
            
            while($query->have_posts()) {
                $query->the_post();
                $product_array[] = array(
                    'ID' => get_the_ID(),
                    'name' => get_the_title(),
                );
            }
    
            return $product_array;
        }
    ));

    //submit reviews
    register_rest_route( SKYE_API_NAMESPACE_V1, '/add-review/(?P<product_id>.*?)/(?P<user_id>.*?)', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {
            $product_id = $data['product_id'];
            $user_id = $data['user_id'];
            $rating = isset($data['rating']) ? $data['rating'] : null;
            $comment = isset($data['comment']) ? $data['comment'] : null;

            $user = get_userdata($user_id);

    

            $arr = array(
                'status' => 'failed',
                'rating' => $rating,
                'comment_id' => null,
            );

            if (is_null($rating) || is_null($comment)) {
                return $arr;
            }

            $comment_id = wp_insert_comment( array(
                'comment_post_ID'      => $product_id,
                'comment_author'       => $user->user_login,
                'comment_author_email' => $user->user_email, // <== Important
                'comment_author_url'   => '',
                'comment_content'      => $comment,
                'comment_type'         => '',
                'comment_parent'       => 0,
                'user_id'              => $user->ID, // <== Important
                'comment_author_IP'    => '',
                'comment_agent'        => '',
                'comment_date'         => date('Y-m-d H:i:s'),
                'comment_approved'     => 1,
            ) );

            // HERE inserting the rating (an integer from 1 to 5)
            $rate = update_comment_meta( $comment_id, 'rating', $rating );

            

            if ($rate)  {
                $arr['status'] = 'success';
                $arr['comment_id'] = $comment_id;
            }
    
            return $arr;
        }
    ));

    //SMS VERIFICATION
    //submit reviews
    register_rest_route( SKYE_API_NAMESPACE_V1, '/twilio-sms', array(
        'methods' => 'POST',
            'permission_callback' => 'sk_api_security_check',
        'callback' => function($data) {

            $to = $data["to"] ?? null;
            $from = $data["from"] ?? null;
            $message = $data["message"] ?? null;
            $ssid =  $data["ssid"] ?? null;

            return null;
        }
    ));
    


    //FOR DELIVERY API
    include(plugin_dir_path( __FILE__ ) . 'delivery-api.php');

    //VERISON 2 UPDATE
    include(plugin_dir_path( __FILE__ ) . 'skye-api-v2.php');

});
