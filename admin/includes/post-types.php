<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once GSSYNC_PLUGIN_DIR . 'admin/includes/meta-box/single-vehicle-meta.php';
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/meta-box/vehicle-data-meta.php';

function gssync_register_vehicle_post_type()
{
    $labels = array(
        'name'               => __('Vehicles', 'google-sheet-sync'),
        'singular_name'      => __('Vehicle', 'google-sheet-sync'),
        'menu_name'          => __('Vehicles', 'google-sheet-sync'),
        'add_new'            => __('Add Vehicle', 'google-sheet-sync'),
        'add_new_item'       => __('Add New Vehicle', 'google-sheet-sync'),
        'edit_item'          => __('Edit Vehicle', 'google-sheet-sync'),
        'new_item'           => __('New Vehicle', 'google-sheet-sync'),
        'view_item'          => __('View Vehicle', 'google-sheet-sync'),
        'all_items'          => __('All Vehicles', 'google-sheet-sync'),
        'search_items'       => __('Search Vehicles', 'google-sheet-sync'),
        'not_found'          => __('No vehicles found', 'google-sheet-sync'),
        'not_found_in_trash' => __('No vehicles found in trash', 'google-sheet-sync'),
    );

    $args = array(
        'labels' => $labels,

        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,

        'menu_icon' => 'dashicons-car',

        'supports' => array(
            'title',
        ),

        'has_archive' => false,
        'rewrite' => false,
        'show_in_rest' => true,
    );

    register_post_type('gssync_vehicle', $args);

}

add_action('init', 'gssync_register_vehicle_post_type');

/**
 * Vehicle Spreadsheet ID Meta Box
 */
function gssync_add_vehicle_meta_boxes()
{
    add_meta_box(
        'gssync_vehicle_sheet',
        __('Google Sheet Sync', 'google-sheet-sync'),
        'gssync_vehicle_sheet_callback',
        'gssync_vehicle',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'gssync_add_vehicle_meta_boxes');

add_action('admin_menu', 'add_settings_google_sheet_json');

function add_settings_google_sheet_json() {

    add_submenu_page(
        'edit.php?post_type=gssync_vehicle', // Vehicles CPT
        __('Settings', 'google-sheet-sync'),
        __('Settings', 'google-sheet-sync'),
        'manage_options',
        'google-sheet-sync',
        'gssync_render_settings_page',
        999 // show at bottom
    );
    add_submenu_page(
        'edit.php?post_type=gssync_vehicle',
        __('Locations', 'google-sheet-sync'),
        __('Locations', 'google-sheet-sync'),
        'manage_options',
        'gssync-locations',
        'gssync_locations_page'
    );
    add_submenu_page(
        'edit.php?post_type=gssync_vehicle',
        __('Expenses', 'google-sheet-sync'),
        __('Expenses', 'google-sheet-sync'),
        'manage_options',
        'gssync-expenses',
        'gssync_expenses_page'
    );
}

function gssync_vehicle_sheet_callback($post)
{
    wp_nonce_field('gssync_vehicle_meta_nonce', 'gssync_vehicle_meta_nonce');

    $spreadsheet_id = get_post_meta(
        $post->ID,
        '_gssync_spreadsheet_id',
        true
    );

    $tabs = [];

    if (!empty($spreadsheet_id)) {
        $tabs = gssync_get_sheet_tabs($spreadsheet_id);
    }
?>
    <table class="form-table">

        <tr>
            <th>
                <label for="gssync_spreadsheet_id">
                    Spreadsheet ID
                </label>
            </th>
            <td>
                <input
                    type="text"
                    id="gssync_spreadsheet_id"
                    name="gssync_spreadsheet_id"
                    value="<?php echo esc_attr($spreadsheet_id); ?>"
                    class="regular-text"
                    style="width:100%;"
                />
            </td>
        </tr>

        <?php if (!empty($tabs)) : ?>

        <tr>
            <th>Worksheet</th>
            <td>
                <select name="gssync_sheet_name">
                    <?php foreach ($tabs as $tab) : ?>
                        <option value="<?php echo esc_attr($tab); ?>">
                            <?php echo esc_html($tab); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>Preview</th>
            <td>

                <button
                    type="button"
                    class="button button-primary"
                    id="gssync-preview-sheet"
                >
                    Preview Sheet Data
                </button>

                <span class="spinner"></span>

                <div id="gssync-sheet-preview" style="margin-top:20px;"></div>

            </td>
        </tr>

        <tr>
            <th>

                <button
                    type="button"
                    class="button button-primary"
                    id="gssync-sync-locations"
                    data-post-id="<?php echo $post->ID; ?>"
                >
                    Sync Sheet Locations
                </button>

                <span class="spinner"></span>

                <div id="gssync-sheet-location" style="margin-top:20px;"></div>

            </th><td>

                <button
                    type="button"
                    class="button button-primary"
                    id="gssync-sync-expense-types"
                    data-post-id="<?php echo $post->ID; ?>"
                >
                    Sync Expense Types
                </button>

                <span class="spinner"></span>

                <div id="gssync-expense-types-result" style="margin-top:20px;"></div>

            </td>
        </tr>

        <?php endif; ?>

    </table>
<?php
}

/**
 * Save Vehicle Meta
 */
function gssync_save_vehicle_meta($post_id)
{
    if (!isset($_POST['gssync_vehicle_meta_nonce'])) {
        return;
    }

    if (
        !wp_verify_nonce(
            $_POST['gssync_vehicle_meta_nonce'],
            'gssync_vehicle_meta_nonce'
        )
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (get_post_type($post_id) !== 'gssync_vehicle') {
        return;
    }

    if (isset($_POST['gssync_spreadsheet_id'])) {
        update_post_meta(
            $post_id,
            '_gssync_spreadsheet_id',
            sanitize_text_field(
                wp_unslash($_POST['gssync_spreadsheet_id'])
            )
        );
    }
}
add_action('save_post', 'gssync_save_vehicle_meta');

add_action('admin_enqueue_scripts', 'gssync_admin_scripts');

function gssync_admin_scripts($hook)
{
    global $post;

    if (
        isset($post->post_type) &&
        $post->post_type === 'gssync_vehicle'||

        isset($_GET['page']) &&
    $_GET['page'] === 'gssync-expenses'
    ) {
        wp_enqueue_script(
            'gssync-admin',
            GSSYNC_PLUGIN_URL . '/admin/assets/js/admin.js',
            array('jquery'),
            time(),
            true
        );

        wp_localize_script(
            'gssync-admin',
            'gssync_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('gssync_preview_nonce'),
            )
        );
    }
}

function gssync_locations_page()
{
    require_once GSSYNC_PLUGIN_DIR . 'admin/includes/page-locations.php';
}

function gssync_expenses_page()
{
    require_once GSSYNC_PLUGIN_DIR . 'admin/includes/page-expenses.php';
}

