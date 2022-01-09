<?php


    global $wpdb;
    $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix

    $banner_type = (isset($_REQUEST['banner_type'])) ? $_REQUEST['banner_type'] : "slide";
    $redirect = (isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : get_admin_url(get_current_blog_id(), 'admin.php?page=skye-app-banner-slides');

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'image' => '',
        'title' => '',
        'description' => null,
        'category' => null,
        'on_click_to' => null,
        'url' => null,
        'banner_type' => null,
    );

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = skye_app_validate_banner($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Banner was successfully saved', 'cltd_example');
                } else {
                    $notice = __('There was an error while saving banner', 'cltd_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('ID' => $item['id']));
                if ($result) {
                    $message = __('Banner was successfully updated', 'cltd_example');
                } else {
                    $notice = __('There was an error while updating banner', 'cltd_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Banner not found', 'cltd_example');
            } else {
                $item['id'] = $item['ID']; // reverse back CAP to small
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('skye_app_banner_form_meta_box', 'Banner details', 'skye_app_banners_form_meta_box_handler', 'banner', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Banner', 'cltd_example')?> <a class="add-new-h2" href="<?php echo $redirect; ?>"><?php _e('Back to banners', 'cltd_example')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
        <input type="hidden" name="banner_type" value="<?php echo $banner_type; ?>"/>
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('banner', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'cltd_example')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php 