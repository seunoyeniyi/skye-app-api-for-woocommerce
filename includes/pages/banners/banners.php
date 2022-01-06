<?php



    global $wpdb;

    $banner_type = (isset($banner_type)) ? $banner_type : "slide";

    $table = new Skye_App_Banners_List_Table();
    $table->banner_type = $banner_type;
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'cltd_example'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php echo ucfirst($banner_type); ?> Banners <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=skye_edit_banner&banner_type=' . $banner_type . '&redirect=' . urlencode(admin_url( 'admin.php?page=' . $_GET['page'])) );?>"><?php _e('Add new', 'cltd_example')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
