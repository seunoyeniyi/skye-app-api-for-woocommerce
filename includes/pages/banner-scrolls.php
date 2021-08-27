<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //saving the banners form
    $enable_banner = (isset($_POST['enable-banner'])) ? 1 : 0;

    //update banner one metas
    update_option( 'sk_app_enable_banner_scrolls', $enable_banner);
}
?>






<h1><?php esc_html_e( 'Site App - Banners', 'sk_options' ); ?></h1>
<div class="notice notice-info">
    <p>
        <b>Note:</b> Please make sure your banners are of the same size... Using the developed app banner scrolls size.
    </p>
</div>
<br>
<form method="post" action="">
<div class="card">
    <p>
    <label><input type="checkbox" name="enable-banner" value="" <?php echo (get_option('sk_app_enable_banner_scrolls', 0)) ? "checked" : ""; ?>> <b>Enable app banner scrolls</b></label>
    </p>
</div>




<?php  submit_button(); ?>
</form>