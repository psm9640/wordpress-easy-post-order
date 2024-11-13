<?php

/*
Plugin Name: Post Order
Plugin URI: 
Description: Allows for drag and drop ordering of posts.
Author: Peter Marra
Version: 1.0.5
Author URI: http://marraman.com/
*/

// Define the path to the plugin directory relative to the WordPress plugins directory
define('PO_WP_PLUGIN_DIR', 'post-order-1m');

// Enqueue styles and scripts
function po_enqueue_styles_scripts() {
    $css_file = __DIR__ . '/post-order-1m.css';
    
    // Enqueue CSS if it exists
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'post-order-1m-style',
            plugins_url('post-order-1m.css', __FILE__),
            array(),
            filemtime($css_file)
        );
    } else {
        error_log('CSS file not found: ' . $css_file);
    }

    // Enqueue jQuery (already included in WordPress)
    wp_enqueue_script('jquery');

    // Enqueue jQuery UI (sortable component)
    wp_enqueue_script('jquery-ui-sortable');
}

// Hook to load styles and scripts on admin pages
add_action('admin_enqueue_scripts', 'po_enqueue_styles_scripts');

// Add settings page for post type selection
add_action('admin_menu', 'po_register_settings_page');
add_action('admin_init', 'po_register_settings');

function po_register_settings_page() {
    add_options_page('Post Order Settings', 'Post Order Settings', 'manage_options', 'post-order-settings', 'po_settings_page');
}

function po_register_settings() {
    register_setting('post_order_settings_group', 'post_order_post_types');
}

function po_settings_page() {
    $post_types = get_post_types(array('public' => true), 'objects');
    $selected_post_types = get_option('post_order_post_types', array());
    ?>
    <div class="wrap">
        <h1>Post Order Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('post_order_settings_group'); ?>
            <?php do_settings_sections('post_order_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Post Types</th>
                    <td>
                        <?php foreach ($post_types as $post_type) : ?>
                            <label>
                                <input type="checkbox" name="post_order_post_types[]" value="<?php echo esc_attr($post_type->name); ?>"
                                    <?php checked(in_array($post_type->name, $selected_post_types)); ?>>
                                <?php echo esc_html($post_type->label); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add a settings link on the plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'po_add_settings_link');

function po_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=post-order-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Admin submenu for each post type
add_action('admin_menu', 'post_order_admin');

function post_order_admin() {
    $selected_post_types = get_option('post_order_post_types', array());
    foreach ($selected_post_types as $post_type) {
        $pt_obj = get_post_type_object($post_type);
        $pt_name = $pt_obj->labels->name;
        add_submenu_page(
            'edit.php?post_type=' . $post_type, 
            $pt_name . ' Order', 
            $pt_name . ' Order', 
            'edit_posts', 
            'admin.php?page=onem-' . $post_type . '-order-mgr', 
            'onem_post_order_mgr'
        );
    }
}

// Manage post ordering in the admin
function onem_post_order_mgr() {
    $the_post_type = $_GET['post_type'];
    ?>
    <div class="wrap post-order-wrap"> <!-- Add top-level wrapper with class -->
        <h2>Update <?php echo esc_html(get_post_type_object($the_post_type)->labels->name); ?> Order</h2>
        <p><em>Drag and drop each item below and click "Update Order".</em></p>

        <?php
        if (isset($_GET['sort'])) {
            global $wpdb;
            $i = 1;
            foreach ($_GET['sort'] as $postID) {
                wp_update_post(array(
                    'ID' => $postID,
                    'menu_order' => $i++
                ));
            }
            echo '<div class="updated"><p>Order updated.</p></div>';
        }

        $posts = new WP_Query(array(
            'post_type' => $the_post_type,
            'posts_per_page' => -1,
            'post_status' => array('publish', 'future'),
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        if ($posts->have_posts()) : ?>
            <ul class="sortable onem-order" rel="<?php echo esc_attr($the_post_type); ?>">
                <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                    <li id="<?php the_ID(); ?>">
                        <table>
                            <tr>
                                <td width="160">
                                    <h4><?php the_title(); ?></h4>
                                </td>
                                <td>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </li>
                <?php endwhile; ?>
            </ul>
            <p class="submit">
                <input type="submit" value="Update Order" class="button-primary" id="update-order-submit" rel="<?php echo esc_attr($_GET['page']); ?>">
            </p>
        <?php endif; wp_reset_postdata(); ?>
    </div>

    <script>
        jQuery(document).ready(function(){
            jQuery(".sortable").sortable(); // Ensures jQuery UI sortable is applied
            jQuery("#update-order-submit").click(function(){
                var result = jQuery(".sortable").sortable("toArray");
                var query = "sort[]=" + result.join("&sort[]=");
                window.location = "?post_type=<?php echo esc_js($the_post_type); ?>&page=<?php echo esc_js($_GET['page']); ?>&order=sorted&" + query;
            });
        });
    </script>
    <?php
}

// Filter front-end and admin queries
add_action('pre_get_posts', 'po_order_posts_by_menu_order');

function po_order_posts_by_menu_order($query) {
    if (!is_admin() && $query->is_main_query() || (is_admin() && $query->get('post_type'))) {
        $selected_post_types = get_option('post_order_post_types', array());
        if (in_array($query->get('post_type'), $selected_post_types)) {
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC');
        }
    }
}

// Add hook to set menu_order on post publish
add_action('transition_post_status', 'po_set_menu_order_on_publish', 10, 3);

function po_set_menu_order_on_publish($new_status, $old_status, $post) {
    // Only proceed if the post is being published
    if ($new_status === 'publish' && $old_status !== 'publish') {
        // Get selected post types from the plugin settings
        $selected_post_types = get_option('post_order_post_types', array());

        // Only proceed if the post type is one of the selected ones
        if (in_array($post->post_type, $selected_post_types)) {
            global $wpdb;

            // Get the max menu_order value for this post type
            $max_menu_order = $wpdb->get_var($wpdb->prepare(
                "SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = %s",
                $post->post_type
            ));

            // Set menu_order to max menu_order + 1
            $new_menu_order = $max_menu_order + 1;

            // Update the menu_order of the post
            wp_update_post(array(
                'ID' => $post->ID,
                'menu_order' => $new_menu_order
            ));
        }
    }
}

?>
