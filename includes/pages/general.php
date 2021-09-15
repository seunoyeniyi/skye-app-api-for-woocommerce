<!-- <h1><?php esc_html_e('Site App - General Settings', 'sk_options'); ?></h1> -->

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

?>

<div id="welcome-panel" class="welcome-panel" style="padding-bottom: 20px;">
	<input type="hidden" id="welcomepanelnonce" name="welcomepanelnonce" value="633898c0b5">
	<div class="welcome-panel-content">
		<h2>Thanks for using <b>Skye App API</b> for WordPress WooCommerce!</h2>
		<p class="about-description">We’ve assembled some links to get you started:</p>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h3>Get Started</h3>
				<a class="button button-primary button-hero load-customize" href="<?php echo admin_url('admin.php?page=skye-app-banner-slides-list'); ?>">Update App Banners</a>
			</div>
			<div class="welcome-panel-column">
				<h3>Next Steps</h3>
				<ul>
					<li><span class="dashicons dashicons-cart"></span> <a href="<?php echo admin_url('admin.php?page=skye-app-delivery-drivers'); ?>">Delivery Drivers</a></li>
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