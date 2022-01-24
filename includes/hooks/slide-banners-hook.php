<?php

function load_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_media_files' );


function skye_app_validate_banner($item)
{
    $messages = array();

    if (empty($item['image'])) $messages[] = __('Image is required', 'cltd_example');
    // if (empty($item['title'])) $messages[] = __('Title is required', 'cltd_example');
    // if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'cltd_example');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}
function skye_app_banners_form_meta_box_handler($item)
{
    $banner_type = $_GET['banner_type'];
    $label = ($banner_type == "video") ? "Video" : "Image";
    $s_label = ($banner_type == "video") ? "video" : "image";
?>
    <input type="hidden" id="media-type" value="<?php echo $s_label; ?>">
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table" id="skye-app-banner-form">
        <tbody>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="image"><?php _e($label, 'cltd_example') ?></label>
                </th>
                <td>

                    <input name="image" id="image" type="hidden" value="<?php echo esc_attr($item['image']) ?>" required>
                    <?php if ($item['id'] == 0 || empty($item['image'])) { ?>
                        <a href="javascript:void(0);" id="skye-app-change-image" style="cursor: pointer; display: inline-block;"></a>
                        <button type="button" id="skye-app-select-image" class="button">Select <?php echo $s_label; ?></button>
                        <button type="button" id="skye-app-remove-image" class="button button-primary" style="display: none;">Remove <?php echo $s_label; ?></button>
                    <?php } else { ?>
                        <?php
                        $banner_image = wp_get_attachment_image_src($item['image'], null);
                        if (!$banner_image)
                            $banner_image = plugin_dir_url(__DIR__) . "assets/icons8_image_500px.png";
                        ?>

                        <a href="#" id="skye-app-change-image" style="cursor: pointer; display: inline-block;">
                            <?php if ($banner_type == "video") { ?>

                                <video style="width: auto; height: auto; max-height: 200px; border: 1px solid #dfdfdf;" controls>
                                    <source src="<?php echo  wp_get_attachment_url($item['image']); ?>" type="video/mp4">
                                </video>
                            <?php } else { ?>
                                <img style="width: auto; height: auto; max-height: 200px; border: 1px solid #dfdfdf;" src="<?php echo  $banner_image[0]; ?>" />
                            <?php } ?>

                        </a>
                        <button type="button" id="skye-app-remove-image" class="button button-primary">Remove <?php echo $s_label; ?></button>
                        <button type="button" id="skye-app-select-image" class="button" style="display: none;">Select <?php echo $s_label; ?></button>
                    <?php } ?>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="title"><?php _e('Title', 'cltd_example') ?></label>
                </th>
                <td>
                    <input id="title" name="title" type="text" style="width: 95%" value="<?php echo str_replace('\\', '', $item['title']); ?>" size="50" class="code" placeholder="<?php _e('Title', 'cltd_example') ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="description"><?php _e('Description', 'cltd_example') ?></label>
                </th>
                <td>
                    <textarea id="d" name="description" style="width: 95%" size="50" class="code" placeholder="<?php _e('Description', 'cltd_example') ?>"><?php echo $item['description']; ?></textarea>
                </td>
            </tr>
            <tr class="form-field">
                <th valign="top" scope="row">
                    <label for="on-click-to"><?php _e('On Click to', 'cltd_example') ?></label>
                </th>
                <td>
                    <select id="on-click-to" name="on_click_to" style="width: 95%" class="code">
                        <option value="none" <?php echo ($item['on_click_to'] == "none") ? "selected" : ""; ?>>None</option>
                        <option value="shop" <?php echo ($item['on_click_to'] == "shop") ? "selected" : ""; ?>>Shop</option>
                        <option value="category" <?php echo ($item['on_click_to'] == "category") ? "selected" : ""; ?>>Category</option>
                        <option value="url" <?php echo ($item['on_click_to'] == "url") ? "selected" : ""; ?>>URL</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field" id="categories-row" style="display: <?php echo ($item['on_click_to'] == "category") ? "dull" : "none"; ?>;">
                <th valign="top" scope="row">
                    <label for="categories"><?php _e('Category', 'cltd_example') ?></label>
                </th>
                <td>
                    <select id="categories" name="category" style="width: 95%" class="code" required>
                        <?php

                        $categories = get_categories(array(
                            'taxonomy' => 'product_cat',
                            'orderby' => 'menu_order',
                            'show_count' => 1,
                            'pad_counts' => 1,
                            'hierarchical' => 1,
                            'title_li' => '',
                            'hide_empty' => 0,
                        ));

                        foreach ($categories as $category) { ?>
                            <option value="<?php echo $category->slug; ?>" <?php echo ($item['category'] == $category->slug) ? "selected" : ""; ?>><?php echo $category->name; ?></option>
                        <?php }

                        ?>
                    </select>
                </td>
            </tr>
            <tr class="form-field" id="url-row" style="display: <?php echo ($item['on_click_to'] == "url") ? "dull" : "none"; ?>;">
                <th valign="top" scope="row">
                    <label for="url"><?php _e('URL', 'cltd_example') ?></label>
                </th>
                <td>
                    <input type="url" id="url" name="url" style="width: 95%" class="code" placeholder="URL" value="<?php echo $item['url']; ?>">
                </td>
            </tr>
        </tbody>
    </table>

    <script>
        jQuery(document).ready(function($) {
            $("#on-click-to").change(function(e) {
                var selected = e.target.value;
                if (selected == "category") {
                    $("#categories-row").show();
                    $("#url-row").hide();
                } else if (selected == "url") {
                    $("#categories-row").hide();
                    $("#url-row").show();
                } else {
                    $("#categories-row").hide();
                    $("#url-row").hide();
                }
            });
        });
    </script>


<?php
}

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class Skye_App_Banners_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    public $banner_type = "slide";

    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'banner',
            'plural' => 'banners',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_id($item)
    {
        return '<em>' . $item['ID'] . '</em>';
    }
    function column_title($item)
    {
        return '<strong>' . str_replace('\\', '', $item['title']) . '</strong>';
    }
    // function column_description($item)
    // {
    //     return '<em>' . $item['description'] . '</em>';
    // }
    function column_on_click_to($item)
    {
        return '<em>' . $item['on_click_to'] . '</em>';
    }
    function column_url($item)
    {
        return '<em>' . $item['url'] . '</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_image($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=skye_edit_banner&id=%s&banner_type=%s&redirect=%s">%s</a>', $item['ID'], $this->banner_type, urlencode(admin_url('admin.php?page=' . $_REQUEST['page'])), __('Edit', 'cltd_example')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['ID'], __('Delete', 'cltd_example')),
        );

      
        if ($this->banner_type == "video") {
            return sprintf(
                '%s %s',
                "<video style='width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;' controls><source src='" . wp_get_attachment_url($item['image']) . "' type='video/mp4'></video>",
                $this->row_actions($actions)
            );
        }

        //else image
        $banner_image = wp_get_attachment_image_src($item['image'], null);
        if (!$banner_image)
            $banner_image = plugin_dir_url(__DIR__) . "assets/icons8_image_500px.png";

        return sprintf(
            '%s %s',
            "<img src='" . $banner_image[0] . "' style='width: auto; heigth: auto; max-height: 200px; border: 1px solid #dfdfdf;'/>",
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'image' => __('Image', 'cltd_example'),
            'title' => __('Title', 'cltd_example'),
            // 'description' => __('Description', 'cltd_example'),
            // 'on_click_to' => __('On Click to', 'cltd_example'),
            // 'url' => __('URL', 'cltd_example'),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'title' => array('title', true),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . 'skye_app_banners'; // do not forget about tables prefix

        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ID';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE banner_type='" . $this->banner_type . "' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
