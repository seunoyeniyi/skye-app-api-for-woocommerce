
<br>
<br>
<div class="sk-admin-middle-wrap middle-border">


    <h1>App Images Resources</h1>

    <?php

    $any_one_saved = false;

    // SAVING
    if (isset($_POST['splash_logo_id'])) {
        if (update_option( 'sk_app_splash_logo', $_POST['splash_logo_id'])) {
            $any_one_saved = true;
        }
    }

    if (isset($_POST['main_logo_id'])) {
        if (update_option( 'sk_app_main_logo', $_POST['main_logo_id'])) {
            $any_one_saved = true;
        }
    }

    if ($any_one_saved) { ?>
        <div id="setting-error-tgmpa" class="notice notice-warning settings-error is-dismissible"> 
            <p>Settings updated</p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php
    
    }

    ?>


    <form action="" method="POST">

    <!-- FOR SPLASH LOGO -->


    <?php

    $splash_logo = get_option("sk_app_splash_logo", null);

    $placeholder = plugin_dir_url("") . "skye-app-api-for-woocommerce/" . "assets/woocommerce-placeholder-324x324.png";

    $splash_logo_url = $placeholder;

    if ($splash_logo) {
        $logo = wp_get_attachment_image_src($splash_logo, null);
        if ($logo) {
            $splash_logo_url = $logo[0];
        }
    }

    ?>

    <br>

    <tr class="form-field">
        <th scope="row" valign="top">
            <h3 for=""><?php _e('Splash Screen Logo'); ?></h3>
        </th>
        <td>
            <div id="splash_logo" style="float: left; margin-right: 10px;">
                <img src="<?php echo $splash_logo_url; ?>" width="120px" height="120px">
            </div>
            <div style="line-height: 60px;">
                <input type="hidden" id="splash_logo_id" name="splash_logo_id" value="<?php echo ($splash_logo) ? $splash_logo : 0; ?>">
                <button type="button" class="upload_splash_logo_button button">Upload/Add image</button>
                <button type="button" class="remove_splash_logo_button button" style="<?php if (!$splash_logo) { echo "display: none;"; } ?>">Remove image</button>
            </div>
            <script type="text/javascript">
                // Only show the "remove icon" button when needed
                if ('0' === jQuery('#splash_logo_id').val()) {
                    jQuery('.remove_splash_logo_button').hide();
                }

                // Uploading files
                var sk_splash_frame;

                jQuery(document).on('click', '.upload_splash_logo_button', function(event) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if (sk_splash_frame) {
                        sk_splash_frame.open();
                        return;
                    }

                    // Create the media frame.
                    sk_splash_frame = wp.media.frames.downloadable_file = wp.media({
                        title: 'Choose an image',
                        button: {
                            text: 'Use image'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    sk_splash_frame.on('select', function() {
                        var attachment = sk_splash_frame.state().get('selection').first().toJSON();
                        var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                        jQuery('#splash_logo_id').val(attachment.id);
                        jQuery('#splash_logo').find('img').attr('src', attachment_thumbnail.url);
                        jQuery('.remove_splash_logo_button').show();
                    });

                    // Finally, open the modal.
                    sk_splash_frame.open();
                });

                jQuery(document).on('click', '.remove_splash_logo_button', function() {
                    jQuery('#splash_logo').find('img').attr('src', '<?php echo $placeholder; ?>');
                    jQuery('#splash_logo_id').val('0');
                    jQuery('.remove_splash_logo_button').hide();
                    return false;
                });
            </script>
            <div class="clear"></div>
        </td>
    </tr>



    <!-- END FOR SPLASH LOGO -->

    <!-- FOR MAIN LOGO -->

    <?php

    $main_logo = get_option("sk_app_main_logo", null);

    $placeholder = plugin_dir_url("") . "skye-app-api-for-woocommerce/" . "assets/woocommerce-placeholder-324x324.png";
    
    $main_logo_url = $placeholder;

    if ($main_logo) {
        $logo = wp_get_attachment_image_src($main_logo, null);
        if ($logo) {
            $main_logo_url = $logo[0];
        }
    }

    ?>

<br>
    
        <tr class="form-field">
            <th scope="row" valign="top">
                <h3 for=""><?php _e('Main Logo'); ?></h3>
            </th>
            <td>
                <div id="main_logo" style="float: left; margin-right: 10px;">
                    <img src="<?php echo $main_logo_url; ?>" width="120px" height="120px">
                </div>
                <div style="line-height: 60px;">
                    <input type="hidden" id="main_logo_id" name="main_logo_id" value="<?php echo ($main_logo) ? $main_logo : 0; ?>">
                    <button type="button" class="upload_main_logo_button button">Upload/Add image</button>
                    <button type="button" class="remove_main_logo_button button" style="<?php if (!$main_logo) { echo "display: none;"; } ?>">Remove image</button>
                </div>
                <script type="text/javascript">
                    // Only show the "remove icon" button when needed
                    if ('0' === jQuery('#main_logo_id').val()) {
                        jQuery('.remove_main_logo_button').hide();
                    }

                    // Uploading files
                    var sk_main_frame;

                    jQuery(document).on('click', '.upload_main_logo_button', function(event) {

                        event.preventDefault();

                        // If the media frame already exists, reopen it.
                        if (sk_main_frame) {
                            sk_main_frame.open();
                            return;
                        }

                        // Create the media frame.
                        sk_main_frame = wp.media.frames.downloadable_file = wp.media({
                            title: 'Choose an image',
                            button: {
                                text: 'Use image'
                            },
                            multiple: false
                        });

                        // When an image is selected, run a callback.
                        sk_main_frame.on('select', function() {
                            var attachment = sk_main_frame.state().get('selection').first().toJSON();
                            var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                            jQuery('#main_logo_id').val(attachment.id);
                            jQuery('#main_logo').find('img').attr('src', attachment_thumbnail.url);
                            jQuery('.remove_main_logo_button').show();
                        });

                        // Finally, open the modal.
                        sk_main_frame.open();
                    });

                    jQuery(document).on('click', '.remove_main_logo_button', function() {
                        jQuery('#main_logo').find('img').attr('src', '<?php echo $placeholder; ?>');
                        jQuery('#main_logo_id').val('0');
                        jQuery('.remove_main_logo_button').hide();
                        return false;
                    });
                </script>
                <div class="clear"></div>
            </td>
        </tr>

        <!-- END FOR MAIN LOGO -->


        <center>
            <br><br>
            <button class="button button-primary button-large" style="width: 80%; font-size: 18px;" type="submit">Save Settings</button>
        </center>

    </form>



</div>