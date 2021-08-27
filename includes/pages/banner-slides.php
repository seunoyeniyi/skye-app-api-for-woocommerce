<?php 
if ($_SERVER['REQUEST_METHOD'] == "POST") {
//saving the banners form
$enable_banner = (isset($_POST['enable-banner'])) ? 1 : 0;

$banner_1_image = $_POST['banner-image-1'];
$banner_2_image = $_POST['banner-image-2'];
$banner_3_image = $_POST['banner-image-3'];
$banner_4_image = $_POST['banner-image-4'];
$banner_5_image = $_POST['banner-image-5'];

$banner_1_click_to = $_POST['banner-1-click-to'];
$banner_2_click_to = $_POST['banner-2-click-to'];
$banner_3_click_to = $_POST['banner-3-click-to'];
$banner_4_click_to = $_POST['banner-4-click-to'];
$banner_5_click_to = $_POST['banner-5-click-to'];

$banner_1_category = $_POST['banner-1-category'];
$banner_2_category = $_POST['banner-2-category'];
$banner_3_category = $_POST['banner-3-category'];
$banner_4_category = $_POST['banner-4-category'];
$banner_5_category = $_POST['banner-5-category'];

$banner_1_url = $_POST['banner-1-url'];
$banner_2_url = $_POST['banner-2-url'];
$banner_3_url = $_POST['banner-3-url'];
$banner_4_url = $_POST['banner-4-url'];
$banner_5_url = $_POST['banner-5-url'];

//update banners metas
update_option( 'sk_app_enable_banner_slides', $enable_banner);

update_option( 'sk_app_banner_1_image', $banner_1_image);
update_option( 'sk_app_banner_2_image', $banner_2_image);
update_option( 'sk_app_banner_3_image', $banner_3_image);
update_option( 'sk_app_banner_4_image', $banner_4_image);
update_option( 'sk_app_banner_5_image', $banner_5_image);

update_option( 'sk_app_banner_1_click_to', $banner_1_click_to);
update_option( 'sk_app_banner_2_click_to', $banner_2_click_to);
update_option( 'sk_app_banner_3_click_to', $banner_3_click_to);
update_option( 'sk_app_banner_4_click_to', $banner_4_click_to);
update_option( 'sk_app_banner_5_click_to', $banner_5_click_to);

update_option( 'sk_app_banner_1_category', $banner_1_category);
update_option( 'sk_app_banner_2_category', $banner_2_category);
update_option( 'sk_app_banner_3_category', $banner_3_category);
update_option( 'sk_app_banner_4_category', $banner_4_category);
update_option( 'sk_app_banner_5_category', $banner_5_category);

update_option( 'sk_app_banner_1_url', $banner_1_url);
update_option( 'sk_app_banner_2_url', $banner_2_url);
update_option( 'sk_app_banner_3_url', $banner_3_url);
update_option( 'sk_app_banner_4_url', $banner_4_url);
update_option( 'sk_app_banner_5_url', $banner_5_url);


}
?>







<h1><?php esc_html_e( 'Site App - Banners', 'sk_options' ); ?></h1>
<div class="notice notice-info">
    <p>
        <b>Note:</b> Please make sure your banners are of the same size... Using the developed app banner slides size.
    </p>
</div>
<br>
<form method="post" action="">
<div class="card">
    <p>
    <label><input type="checkbox" name="enable-banner" value="" <?php echo (get_option('sk_app_enable_banner_slides', 0)) ? "checked" : ""; ?>> <b>Enable app banner slides</b></label>
    </p>
</div>

<!-- BANNER ONE -->
<h2><?php esc_html_e( '1st Banner', 'sk_options' ); ?></h2>
<div class="card" style="display: block; width: 100%; max-width: 100%;">
<?php
$banner1 = get_option( "sk_app_banner_1_image", 0);
if( $banner_image1 = wp_get_attachment_image_src( $banner1, null) ) { ?>

    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="banner-upload-btn" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image1[0]; ?>" /></a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn">Remove image</a>
            <input type="hidden" name="banner-image-1" id="banner-image-value" value="<?php echo $banner1; ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-1-click-to" class="banner-click-to">
                <option value="none" <?php echo (get_option('sk_app_banner_1_click_to') == "none") ? "selected" : ""; ?>>None</option>
                <option value="shop" <?php echo (get_option('sk_app_banner_1_click_to') == "shop") ? "selected" : ""; ?>>Shop</option>
                <option value="category" <?php echo (get_option('sk_app_banner_1_click_to') == "category") ? "selected" : ""; ?>>Category</option>
                <option value="profile" <?php echo (get_option('sk_app_banner_1_click_to') == "profile") ? "selected" : ""; ?>>Profile</option>
                <option value="url" <?php echo (get_option('sk_app_banner_1_click_to') == "url") ? "selected" : ""; ?>>URL</option>
            </select>
            <select name="banner-1-category" class="banner-category" style="display: <?php echo (get_option('sk_app_banner_1_click_to') == "category") ? "inline-block" : "none"; ?>;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>" <?php echo (get_option('sk_app_banner_1_category') == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>" <?php echo (get_option('sk_app_banner_1_category') == $cat->slug) ? "selected" : ""; ?>><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: <?php echo (get_option('sk_app_banner_1_click_to') == "url") ? "block" : "none"; ?>;">
                <input type="url" name="banner-1-url" id="banner-url" placeholder="URL" value="<?php echo get_option('sk_app_banner_1_url'); ?>">
            </div>
        </td>
    </tr>
    </tbody>
    </table>

<?php } else { ?>
    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="button button-primary banner-upload-btn" style="cursor: pointer; display: inline-block;">Upload image</a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn" style="display:none;">Remove image</a>
            <input type="hidden" name="banner-image-1" id="banner-image-value" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-1-click-to" class="banner-click-to">
                <option value="none">None</option>
                <option value="shop">Shop</option>
                <option value="category">Category</option>
                <option value="profile">Profile</option>
                <option value="url">URL</option>
            </select>
            <select name="banner-1-category" class="banner-category" style="display: none;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: none;">
                <input type="url" name="banner-1-url" id="banner-url" placeholder="URL">
            </div>
        </td>
    </tr>
    </tbody>
    </table>
<?php } ?>
</div>
 <!-- BANNER TWO -->
<h2><?php esc_html_e( '2nd Banner', 'sk_options' ); ?></h2>
<div class="card" style="display: block; width: 100%; max-width: 100%;">
<?php
$banner2 = get_option( "sk_app_banner_2_image", 0);
if( $banner_image2 = wp_get_attachment_image_src( $banner2, null) ) { ?>

    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="banner-upload-btn" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image2[0]; ?>" /></a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn">Remove image</a>
            <input type="hidden" name="banner-image-2" id="banner-image-value" value="<?php echo $banner2; ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-2-click-to" class="banner-click-to">
                <option value="none" <?php echo (get_option('sk_app_banner_2_click_to') == "none") ? "selected" : ""; ?>>None</option>
                <option value="shop" <?php echo (get_option('sk_app_banner_2_click_to') == "shop") ? "selected" : ""; ?>>Shop</option>
                <option value="category" <?php echo (get_option('sk_app_banner_2_click_to') == "category") ? "selected" : ""; ?>>Category</option>
                <option value="profile" <?php echo (get_option('sk_app_banner_2_click_to') == "profile") ? "selected" : ""; ?>>Profile</option>
                <option value="url" <?php echo (get_option('sk_app_banner_2_click_to') == "url") ? "selected" : ""; ?>>URL</option>
            </select>
            <select name="banner-2-category" class="banner-category" style="display: <?php echo (get_option('sk_app_banner_2_click_to') == "category") ? "inline-block" : "none"; ?>;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>" <?php echo (get_option('sk_app_banner_2_category') == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>" <?php echo (get_option('sk_app_banner_2_category') == $cat->slug) ? "selected" : ""; ?>><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: <?php echo (get_option('sk_app_banner_2_click_to') == "url") ? "block" : "none"; ?>;">
                <input type="url" name="banner-2-url" id="banner-url" placeholder="URL" value="<?php echo get_option('sk_app_banner_2_url'); ?>">
            </div>
        </td>
    </tr>
    </tbody>
    </table>

<?php } else { ?>
    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="button button-primary banner-upload-btn" style="cursor: pointer; display: inline-block;">Upload image</a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn" style="display:none;">Remove image</a>
            <input type="hidden" name="banner-image-2" id="banner-image-value" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-2-click-to" class="banner-click-to">
                <option value="none">None</option>
                <option value="shop">Shop</option>
                <option value="category">Category</option>
                <option value="profile">Profile</option>
                <option value="url">URL</option>
            </select>
            <select name="banner-2-category" class="banner-category" style="display: none;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: none;">
                <input type="url" name="banner-2-url" id="banner-url" placeholder="URL">
            </div>
        </td>
    </tr>
    </tbody>
    </table>
<?php } ?>
</div>
 <!-- BANNER THREE -->
<h2><?php esc_html_e( '3rd Banner', 'sk_options' ); ?></h2>
<div class="card" style="display: block; width: 100%; max-width: 100%;">
<?php
$banner3 = get_option( "sk_app_banner_3_image", 0);
if( $banner_image3 = wp_get_attachment_image_src( $banner3, null) ) { ?>

    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="banner-upload-btn" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image3[0]; ?>" /></a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn">Remove image</a>
            <input type="hidden" name="banner-image-3" id="banner-image-value" value="<?php echo $banner3; ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-3-click-to" class="banner-click-to">
                <option value="none" <?php echo (get_option('sk_app_banner_3_click_to') == "none") ? "selected" : ""; ?>>None</option>
                <option value="shop" <?php echo (get_option('sk_app_banner_3_click_to') == "shop") ? "selected" : ""; ?>>Shop</option>
                <option value="category" <?php echo (get_option('sk_app_banner_3_click_to') == "category") ? "selected" : ""; ?>>Category</option>
                <option value="profile" <?php echo (get_option('sk_app_banner_3_click_to') == "profile") ? "selected" : ""; ?>>Profile</option>
                <option value="url" <?php echo (get_option('sk_app_banner_3_click_to') == "url") ? "selected" : ""; ?>>URL</option>
            </select>
            <select name="banner-3-category" class="banner-category" style="display: <?php echo (get_option('sk_app_banner_3_click_to') == "category") ? "inline-block" : "none"; ?>;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>" <?php echo (get_option('sk_app_banner_3_category') == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>" <?php echo (get_option('sk_app_banner_3_category') == $cat->slug) ? "selected" : ""; ?>><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: <?php echo (get_option('sk_app_banner_3_click_to') == "url") ? "block" : "none"; ?>;">
                <input type="url" name="banner-3-url" id="banner-url" placeholder="URL" value="<?php echo get_option('sk_app_banner_3_url'); ?>">
            </div>
        </td>
    </tr>
    </tbody>
    </table>

<?php } else { ?>
    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="button button-primary banner-upload-btn" style="cursor: pointer; display: inline-block;">Upload image</a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn" style="display:none;">Remove image</a>
            <input type="hidden" name="banner-image-3" id="banner-image-value" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-3-click-to" class="banner-click-to">
                <option value="none">None</option>
                <option value="shop">Shop</option>
                <option value="category">Category</option>
                <option value="profile">Profile</option>
                <option value="url">URL</option>
            </select>
            <select name="banner-3-category" class="banner-category" style="display: none;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: none;">
                <input type="url" name="banner-3-url" id="banner-url" placeholder="URL">
            </div>
        </td>
    </tr>
    </tbody>
    </table>
<?php } ?>
</div>
 <!-- BANNER FOUR -->
<h2><?php esc_html_e( '4th Banner', 'sk_options' ); ?></h2>
<div class="card" style="display: block; width: 100%; max-width: 100%;">
<?php
$banner4 = get_option( "sk_app_banner_4_image", 0);
if( $banner_image4 = wp_get_attachment_image_src( $banner4, null) ) { ?>

    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="banner-upload-btn" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image4[0]; ?>" /></a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn">Remove image</a>
            <input type="hidden" name="banner-image-4" id="banner-image-value" value="<?php echo $banner4; ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-4-click-to" class="banner-click-to">
                <option value="none" <?php echo (get_option('sk_app_banner_4_click_to') == "none") ? "selected" : ""; ?>>None</option>
                <option value="shop" <?php echo (get_option('sk_app_banner_4_click_to') == "shop") ? "selected" : ""; ?>>Shop</option>
                <option value="category" <?php echo (get_option('sk_app_banner_4_click_to') == "category") ? "selected" : ""; ?>>Category</option>
                <option value="profile" <?php echo (get_option('sk_app_banner_4_click_to') == "profile") ? "selected" : ""; ?>>Profile</option>
                <option value="url" <?php echo (get_option('sk_app_banner_4_click_to') == "url") ? "selected" : ""; ?>>URL</option>
            </select>
            <select name="banner-4-category" class="banner-category" style="display: <?php echo (get_option('sk_app_banner_4_click_to') == "category") ? "inline-block" : "none"; ?>;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>" <?php echo (get_option('sk_app_banner_4_category') == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>" <?php echo (get_option('sk_app_banner_4_category') == $cat->slug) ? "selected" : ""; ?>><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: <?php echo (get_option('sk_app_banner_4_click_to') == "url") ? "block" : "none"; ?>;">
                <input type="url" name="banner-4-url" id="banner-url" placeholder="URL" value="<?php echo get_option('sk_app_banner_4_url'); ?>">
            </div>
        </td>
    </tr>
    </tbody>
    </table>

<?php } else { ?>
    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="button button-primary banner-upload-btn" style="cursor: pointer; display: inline-block;">Upload image</a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn" style="display:none;">Remove image</a>
            <input type="hidden" name="banner-image-4" id="banner-image-value" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-4-click-to" class="banner-click-to">
                <option value="none">None</option>
                <option value="shop">Shop</option>
                <option value="category">Category</option>
                <option value="profile">Profile</option>
                <option value="url">URL</option>
            </select>
            <select name="banner-4-category" class="banner-category" style="display: none;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: none;">
                <input type="url" name="banner-4-url" id="banner-url" placeholder="URL">
            </div>
        </td>
    </tr>
    </tbody>
    </table>
<?php } ?>
</div>
 <!-- BANNER FIVE -->
<h2><?php esc_html_e( '5th Banner', 'sk_options' ); ?></h2>
<div class="card" style="display: block; width: 100%; max-width: 100%;">
<?php
$banner5 = get_option( "sk_app_banner_5_image", 0);
if( $banner_image5 = wp_get_attachment_image_src( $banner5, null) ) { ?>

    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="banner-upload-btn" style="cursor: pointer; display: inline-block;"><img style="width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image5[0]; ?>" /></a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn">Remove image</a>
            <input type="hidden" name="banner-image-5" id="banner-image-value" value="<?php echo $banner5; ?>">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-5-click-to" class="banner-click-to">
                <option value="none" <?php echo (get_option('sk_app_banner_5_click_to') == "none") ? "selected" : ""; ?>>None</option>
                <option value="shop" <?php echo (get_option('sk_app_banner_5_click_to') == "shop") ? "selected" : ""; ?>>Shop</option>
                <option value="category" <?php echo (get_option('sk_app_banner_5_click_to') == "category") ? "selected" : ""; ?>>Category</option>
                <option value="profile" <?php echo (get_option('sk_app_banner_5_click_to') == "profile") ? "selected" : ""; ?>>Profile</option>
                <option value="url" <?php echo (get_option('sk_app_banner_5_click_to') == "url") ? "selected" : ""; ?>>URL</option>
            </select>
            <select name="banner-5-category" class="banner-category" style="display: <?php echo (get_option('sk_app_banner_5_click_to') == "category") ? "inline-block" : "none"; ?>;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>" <?php echo (get_option('sk_app_banner_5_category') == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>" <?php echo (get_option('sk_app_banner_5_category') == $cat->slug) ? "selected" : ""; ?>><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: <?php echo (get_option('sk_app_banner_5_click_to') == "url") ? "block" : "none"; ?>;">
                <input type="url" name="banner-5-url" id="banner-url" placeholder="URL" value="<?php echo get_option('sk_app_banner_5_url'); ?>">
            </div>
        </td>
    </tr>
    </tbody>
    </table>

<?php } else { ?>
    <table class="form-table">
    <tbody>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>Image</label></th>
        <td>
            <a href="#" class="button button-primary banner-upload-btn" style="cursor: pointer; display: inline-block;">Upload image</a>
            <br>
            <a href="#" class="button button-secondary banner-remove-btn" style="display:none;">Remove image</a>
            <input type="hidden" name="banner-image-5" id="banner-image-value" value="">
        </td>
    </tr>
    <tr valign="top">
        <th scope="row" class="titledesc"><label>On click to</label></th>
        <td>
            <select name="banner-5-click-to" class="banner-click-to">
                <option value="none">None</option>
                <option value="shop">Shop</option>
                <option value="category">Category</option>
                <option value="profile">Profile</option>
                <option value="url">URL</option>
            </select>
            <select name="banner-5-category" class="banner-category" style="display: none;">
                <option value="none">Select a category</option>
                <?php
                    $categories = get_categories( array(
                        'taxonomy' => 'product_cat',
                        'orderby' => 'menu_order',
                        'show_count' => 1,
                        'pad_counts' => 1,
                        'hierarchical' => 1,
                        'title_li' => '',
                        'hide_empty' => 0,
                    ) );
                    foreach($categories as $category) { ?>
                    <option value="<?php echo $category->slug; ?>"><?php echo $category->name; ?></option>
                        <?php 
                        $sub_cats = get_categories(array(//with sub added
                            'taxonomy' => 'product_cat',
                            'child_of' => 0,
                            'parent' => $category->term_id,
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));
                        foreach ($sub_cats as $cat) { ?>
                        <option value="<?php echo $cat->slug; ?>"><?php echo $cat->name; ?></option>
                        <?php
                        }
                    }
                ?>
            </select>

            <div id="banner-url-container" style="margin-top: 10px; display: none;">
                <input type="url" name="banner-5-url" id="banner-url" placeholder="URL">
            </div>
        </td>
    </tr>
    </tbody>
    </table>
<?php } ?>
</div>





<?php  submit_button(); ?>
</form>