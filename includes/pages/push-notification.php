
<br>
<br>
<?php


if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['notify'])) {
    $title = $_POST['notification_title'];
    $text = $_POST['notification_text'];
    $image = $_POST['notification_image'];
    $schedule_type = $_POST['schedule_type'];
    $schedule_date = $_POST['notification_schedule_date'];
    $schedule_time = $_POST['notification_schedule_time'];
    $option = get_option( "sk_devices", "[]"); //array();
    $devices = json_decode($option, true);
    
    $users = get_users(array(
        'meta_key'     => 'sk_device_id',
    ));

    foreach ($users as $user) {
        $user_device = get_user_meta( $user->ID, 'sk_device_id', true);
        if (!in_array($user_device, $devices)) {
            $devices[] = $user_device;
        }
    }

   
    $data = array(
        'title' => $title,
        'body' => $text
    );
    if (!empty($image) && strlen($image) > 10) {
        $data['image'] = $image;
    }

    if ($schedule_type != 'now') {
        $data["isScheduled"] = "true";
        $data["scheduledTime"] = $schedule_date . ' ' . $schedule_time;
    }
    
    $push = sk_push_notification($devices, $data);

   if ($push) { ?>
    <div id="message" class="updated inline"><p style="text-align: center;"><strong>Notification sent to <?php echo count($devices); ?> customers</strong></p></div>
  <?php } else { ?>
    <div id="message" class="updated inline"><p style="text-align: center;"><strong>Error... Unable to send notification, please contact the technical person in charge.</strong></p></div>
   <?php } ?>

   <?php
}

?>


<br>
<div class="push-notification-wrap">
<h1 style="text-align: center;">Push Notification</h1>

<p style="text-align: center;">Send targeted notifications to drive customer engagement.</p>

<form method="post" name="push-notifications">
    <p>
        <label for="notification_title"><b>Notification title</b>  ( 🥳 You can paste any emoji 🥳 )</label>
        <input name="notification_title" type="text" id="notification_title" value="" style="width: 100%;" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" placeholder="Enter optional title">
    </p>

    <p>
        <?php $placeholder = plugin_dir_url("") . "skye-app-api-for-woocommerce/" . "assets/woocommerce-placeholder-324x324.png"; ?>
        <label for="notification_text"><b>Image (Optional) </b> </label>
        <div id="noty_image" style="float: left; margin-right: 10px;">
                <img src="<?php echo $placeholder; ?>" width="120px" height="120px">
        </div>
        <input type="hidden" name="notification_image" id="notification_image">
        <button type="button" class="upload_image_button button">Upload/Add image</button>
        <button type="button" class="remove_image_button button" style="display: none;">Remove image</button>
    
        <script type="text/javascript">
               
                // Uploading files
                var sk_image_frame;

                jQuery(document).on('click', '.upload_image_button', function(event) {

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if (sk_image_frame) {
                        sk_image_frame.open();
                        return;
                    }

                    // Create the media frame.
                    sk_image_frame = wp.media.frames.downloadable_file = wp.media({
                        title: 'Choose an image',
                        button: {
                            text: 'Use image'
                        },
                        multiple: false
                    });

                    // When an image is selected, run a callback.
                    sk_image_frame.on('select', function() {
                        var attachment = sk_image_frame.state().get('selection').first().toJSON();
                        var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

                        jQuery('#notification_image').val(attachment_thumbnail.url);
                        jQuery('#noty_image').find('img').attr('src', attachment_thumbnail.url);
                        jQuery('.remove_image_button').show();
                    });

                    // Finally, open the modal.
                    sk_image_frame.open();
                });

                jQuery(document).on('click', '.remove_image_button', function() {
                    jQuery('#noty_image').find('img').attr('src', '<?php echo $placeholder; ?>');
                    jQuery('#notification_image').val('');
                    jQuery('.remove_image_button').hide();
                    return false;
                });
            </script>


    </p>
    <div class="clear"></div>



    <p>
        <label for="notification_text"><b>Notification text </b> </label>
        <textarea name="notification_text" id="notification_text" style="width: 100%;" placeholder="Enter notification text" required></textarea>
    </p>

    <p>
        <label for="notification_text"><b>Schedule </b> </label>
        <select name="schedule_type" onchange="if (this.value == 'date') jQuery('#date-select').show(); else jQuery('#date-select').hide(); ">
            <option value="now">Send Now</option>
            <option value="date">Select date & time</option>
        </select>
        <div id="date-select" style="display: none;">
            <input type="date" value="<?php echo date("Y-m-d"); ?>" name="notification_schedule_date" id="notification_schedule_date" style="width: 100%;" placeholder="Schedule Date">
            <br><br>
            <input type="time" name="notification_schedule_time" id="notification_schedule_time" style="width: 100%;" placeholder="Schedule Time">
        </div>
    </p>
	
	<p class="submit"><input type="submit" name="notify" id="" class="button button-primary" value="Send Notification" style="width: 100%; padding: 10px;"></p>
</form>
</div>