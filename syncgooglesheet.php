<?php
/**
 * Plugin Name: Google Sheet Sync
 * Plugin URI: https://example.com
 * Description: Sync Google Sheet data into WordPress.
 * Version: 1.0.0
 * Author: Shishir Nepal
 * Text Domain: google-sheet-sync
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Constants
|--------------------------------------------------------------------------
*/
define('GSSYNC_VERSION', '1.0.0');
define('GSSYNC_PLUGIN_FILE', __FILE__);
define('GSSYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GSSYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

/*
|--------------------------------------------------------------------------
| Activation
|--------------------------------------------------------------------------
*/
function gssync_activate()
{
    require_once GSSYNC_PLUGIN_DIR . 'admin/includes/post-types.php';
    require_once GSSYNC_PLUGIN_DIR . 'admin/includes/db/database.php';

    gssync_register_vehicle_post_type();
    gssync_create_tables();
    gssync_create_pages();
    // gssync_create_login_page();

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'gssync_activate');
/*
|--------------------------------------------------------------------------
| Deactivation
|--------------------------------------------------------------------------
*/
function gssync_deactivate()
{
    flush_rewrite_rules();
    wp_clear_scheduled_hook('gssync_nepali_date_sync');
}
register_deactivation_hook(__FILE__, 'gssync_deactivate');

/*
|--------------------------------------------------------------------------
| Includes
|--------------------------------------------------------------------------
*/
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/post-types.php';
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/page-settings.php';
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/page-get-sheets.php';
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/sync-locations.php';
require_once GSSYNC_PLUGIN_DIR . 'admin/includes/sync-expenses.php';


/*
|--------------------------------------------------------------------------
| Front end
|--------------------------------------------------------------------------
*/

require_once GSSYNC_PLUGIN_DIR . 'frontend/frontend.php';
require_once GSSYNC_PLUGIN_DIR . 'frontend/includes/class-nepali-date.php';