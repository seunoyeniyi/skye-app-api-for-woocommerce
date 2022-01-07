
<br>
<br>
<?php


if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['notify'])) {
    $title = $_POST['notification_title'];
    $text = $_POST['notification_text'];
    $devices = array();
    
    $users = get_users(array(
        'meta_key'     => 'sk_device_id',
    ));

    foreach ($users as $user) {
        $devices[] = get_user_meta( $user->ID, 'sk_device_id', true);
    }

    $push = sk_push_notification($devices, array(
        'title' => $title,
        'body' => $text
    ));

   if ($push) { ?>
    <div id="message" class="updated inline"><p style="text-align: center;"><strong>Notification sent to <?php echo count($users); ?> customers</strong></p></div>
  <?php } else { ?>
    <div id="message" class="updated inline"><p style="text-align: center;"><strong>Error... Unable to send notification, please contact the technical person in charge.</strong></p></div>
   <?php } ?>

   <?php
}

?>


<br>
<div class="wrap push-notification-wrap">
<h1 style="text-align: center;">Push Notification</h1>

<p style="text-align: center;">Send targeted notifications to drive customer engagement.</p>

<form method="post" name="push-notifications">
    <p>
        <label for="notification_title"><b>Notification title</b> (You can paste any emoji)</label>
        <input name="notification_title" type="text" id="notification_title" value="" style="width: 100%;" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" placeholder="Enter optional title">
    </p>

    <p>
        <label for="notification_text"><b>Notification text</b></label>
        <textarea name="notification_text" id="notification_text" style="width: 100%;" placeholder="Enter notification text" required></textarea>
    </p>
	
	<p class="submit"><input type="submit" name="notify" id="" class="button button-primary" value="Send Notification" style="width: 100%; padding: 10px;"></p>
</form>
</div>