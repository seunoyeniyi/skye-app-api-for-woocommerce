<?php
if (isset($_GET['clear_customers_cart'])) {
	global $wpdb;
	$cart_table = $wpdb->prefix . "skye_carts";
	if ($delete = $wpdb->query("TRUNCATE TABLE " . $cart_table)) { ?>
		<div id="message" class="updated woocommerce-message wc-connect woocommerce-message--success">
			<p><b>Cleared!</b> All customers cart has been cleared.</p>
		</div>
	<?php } else { ?>
		<div id="message" class="updated woocommerce-message wc-connect woocommerce-message--success">
			<p><b>ERROR!</b> Unable to clear customers cart.</p>
		</div>
<?php }
}



//SETTINGS 
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['save-app'])) {
	update_option('sk_enable_slide_banners', isset($_POST['enable_slide_banners']) ? 1 : 0);
	update_option('sk_enable_big_banners', isset($_POST['enable_big_banners']) ? 1 : 0);
	update_option('sk_enable_carousel_banners', isset($_POST['enable_carousel_banners']) ? 1 : 0);
	update_option('sk_enable_thin_banners', isset($_POST['enable_thin_banners']) ? 1 : 0);
	// update_option('sk_enable_sale_banners', isset($_POST['enable_sale_banners']) ? 1 : 0);
	update_option('sk_enable_grid_banners', isset($_POST['enable_grid_banners']) ? 1 : 0);
	update_option('sk_enable_video_banners', isset($_POST['enable_video_banners']) ? 1 : 0);
	update_option('sk_push_api_key', $_POST['push_api_key']);

	?>
	<div id="message" class="updated inline"><p><strong>Your settings have been saved.</strong></p></div>
	<?php
}


?>






<div id="welcome-panel" class="welcome-panel" style="padding-bottom: 20px;">
	<input type="hidden" id="welcomepanelnonce" name="welcomepanelnonce" value="633898c0b5">
	<div class="welcome-panel-content">
		<h2>Thanks for using <b>Skye App API</b> for WordPress WooCommerce!</h2>
		<p class="about-description">We’ve assembled some links to get you started:</p>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h3>Get Started</h3>
				<a class="button button-primary button-hero load-customize" href="<?php echo admin_url('admin.php?page=skye-app-banner-slides'); ?>">Update App Banners</a>
			</div>
			<div class="welcome-panel-column">
				<h3>Next Steps</h3>
				<ul>
					<li><span class="dashicons dashicons-bell"></span> <a href="<?php echo admin_url('admin.php?page=skye-app-push-notification'); ?>">Send Push Notification</a></li>
					<!-- <li><a href="http://localhost/wp-admin/post-new.php?post_type=page" class="welcome-icon welcome-add-page">Add additional pages</a></li> -->
					<!-- <li><a href="http://localhost/" class="welcome-icon welcome-view-site">View your site</a></li> -->
				</ul>
			</div>
			<div class="welcome-panel-column welcome-panel-last">
				<h3>More Actions</h3>
				<ul>
					<li><span class="dashicons dashicons-image-rotate"></span> <a href="<?php echo admin_url('admin.php?page=skye-app&clear_customers_cart=1'); ?>">Clear customers cart</a></li>
					<!-- <li><a href="http://localhost/wp-admin/nav-menus.php" class="welcome-icon welcome-menus">Manage menus</a></li> -->
					<!-- <li><a href="http://localhost/wp-admin/options-discussion.php" class="welcome-icon welcome-comments">Turn comments on or off</a></li> -->
					<!-- <li><a href="https://wordpress.org/support/article/first-steps-with-wordpress-b/" class="welcome-icon welcome-learn-more">Learn more about getting started</a></li> -->
				</ul>
			</div>
		</div>
	</div>
</div>












<div class="woocommerce" style="padding-left: 20px; padding-right: 20px;">
	<form method="post" id="mainform" action="" enctype="multipart/form-data">

		<h1>Quick Settings</h1>
		<h2>Store Banners</h2>
		<div id="store_address-description">
			<p>This is how you control which banner to display in your app. <br> <b>NOTE:</b> Enabling banners is not active until it is accessed from the APP.</p>
		</div>
		<table class="form-table">

			<tbody>
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Slide Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_slide_banners">
								<input name="enable_slide_banners" id="enable_slide_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_slide_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Big Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_big_banners">
								<input name="enable_big_banners" id="enable_big_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_big_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Carousel Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_carousel_banners">
								<input name="enable_carousel_banners" id="enable_carousel_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_carousel_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Thin Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_thin_banners">
								<input name="enable_thin_banners" id="enable_thin_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_thin_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			<!-- <tr valign="top" class="">
					<th scope="row" class="titledesc">Sale Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_sale_banners">
								<input name="enable_sale_banners" id="enable_sale_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_sale_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr> -->
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Grid Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_grid_banners">
								<input name="enable_grid_banners" id="enable_grid_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_grid_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			<tr valign="top" class="">
					<th scope="row" class="titledesc">Video Banners</th>
					<td class="">
							<legend class="screen-reader-text"><span>Enable</span></legend>
							<label for="enable_video_banners">
								<input name="enable_video_banners" id="enable_video_banners" type="checkbox" class="" <?php echo (get_option( "sk_enable_video_banners", 0)) ? "checked" : ""; ?>> Enable</label>
					</td>
			</tr>
			</tbody>
		</table>
		<h2>Push Notification</h2>
		<div id="store_address-description">
			<p>Provide the API keys for your push notification, support only API key from Firebase(FCM)</p>
		</div>
		<table class="form-table">

			<tbody>
			<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="push_api_key">API Key <span class="woocommerce-help-tip"></span></label>
					</th>
					<td class="forminp forminp-text">
						<input name="push_api_key" id="push_api_key" type="text" style="width: 80%;" value="<?php echo get_option( "sk_push_api_key", ""); ?>" class="" placeholder="Key">
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<button name="save-app" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>
		</p>
	</form>
</div>