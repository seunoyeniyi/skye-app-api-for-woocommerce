<?php

// Add field - my account
function action_woocommerce_edit_account_form() {   
    woocommerce_form_field( 'birthday_field', array(
        'type'        => 'date',
        'label'       => __( 'Birth Date', 'woocommerce' ),
        'placeholder' => __( 'Date of Birth', 'woocommerce' ),
        'required'    => false,
    ), get_user_meta( get_current_user_id(), 'birthday_field', true ));

    woocommerce_form_field( 'gender_field', array(
        'type'        => 'text',
        'label'       => __( 'Gender', 'woocommerce' ),
        'placeholder' => __( 'Gender', 'woocommerce' ),
        'required'    => false,
    ), get_user_meta( get_current_user_id(), 'gender_field', true ));

    woocommerce_form_field( 'other_phone_field', array(
        'type'        => 'text',
        'label'       => __( 'Other Phone', 'woocommerce' ),
        'placeholder' => __( 'Other Phone', 'woocommerce' ),
        'required'    => false,
    ), get_user_meta( get_current_user_id(), 'other_phone_field', true ));

    
}
add_action( 'woocommerce_edit_account_form', 'action_woocommerce_edit_account_form' );



// Save - my account
function action_woocommerce_save_account_details( $user_id ) {  
    if( isset($_POST['birthday_field']) && ! empty($_POST['birthday_field']) ) {
        update_user_meta( $user_id, 'birthday_field', sanitize_text_field($_POST['birthday_field']) );
    }

    if( isset($_POST['gender_field']) && ! empty($_POST['gender_field']) ) {
        update_user_meta( $user_id, 'gender_field', sanitize_text_field($_POST['gender_field']) );
    }

    if( isset($_POST['other_phone_field']) && ! empty($_POST['other_phone_field']) ) {
        update_user_meta( $user_id, 'other_phone_field', sanitize_text_field($_POST['other_phone_field']) );
    }
}
add_action( 'woocommerce_save_account_details', 'action_woocommerce_save_account_details', 10, 1 );









// DISPLAY PROFILE IMAGE
function action_woocommerce_edit_account_form_display() {
    // Get current user id
    $user_id = get_current_user_id();

    // Get attachment id
    $attachment_id = get_user_meta( $user_id, 'image', true );

    // True
    if ( $attachment_id ) {
        $original_image_url = wp_get_attachment_url( $attachment_id );

        // Display Image instead of URL
        echo wp_get_attachment_image( $attachment_id, 'full');
    }
} 
add_action( 'woocommerce_edit_account_form_start', 'action_woocommerce_edit_account_form_display' );









//FOR PROFILE PICTURE
// Add field
function action_woocommerce_edit_account_form_start() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="image"><?php esc_html_e( 'Profile Image', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="file" class="woocommerce-Input" name="image" accept="image/x-png,image/gif,image/jpeg">
    </p>
    <?php
}
add_action( 'woocommerce_edit_account_form_start', 'action_woocommerce_edit_account_form_start' );


// Save
function action_woocommerce_save_account_details_pic( $user_id ) {  
    if ( isset( $_FILES['image'] ) ) {
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
}
add_action( 'woocommerce_save_account_details', 'action_woocommerce_save_account_details_pic', 10, 1 );

// Add enctype to form to allow image upload
function action_woocommerce_edit_account_form_tag() {
    echo 'enctype="multipart/form-data"';
} 
add_action( 'woocommerce_edit_account_form_tag', 'action_woocommerce_edit_account_form_tag' );




