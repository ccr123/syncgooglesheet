<?php

if (!defined('ABSPATH')) {
    exit;
}

function gssync_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $active_tab = isset($_GET['tab'])
        ? sanitize_key($_GET['tab'])
        : 'sheet';

    $settings = get_option('gssync_settings', []);
?>

<div class="wrap">

    <h1>Google Sheet Sync Settings</h1>

    <h2 class="nav-tab-wrapper">

        <a
            href="?post_type=gssync_vehicle&page=gssync-settings&tab=sheet"
            class="nav-tab <?php echo $active_tab === 'sheet' ? 'nav-tab-active' : ''; ?>"
        >
            Sheet Settings
        </a>

        <a
            href="?post_type=gssync_vehicle&page=gssync-settings&tab=driver"
            class="nav-tab <?php echo $active_tab === 'driver' ? 'nav-tab-active' : ''; ?>"
        >
            Driver Settings
        </a>

        <a
            href="?post_type=gssync_vehicle&page=gssync-settings&tab=employee"
            class="nav-tab <?php echo $active_tab === 'employee' ? 'nav-tab-active' : ''; ?>"
        >
            Employee Settings
        </a>

        <a
            href="?post_type=gssync_vehicle&page=gssync-settings&tab=other"
            class="nav-tab <?php echo $active_tab === 'other' ? 'nav-tab-active' : ''; ?>"
        >
            Other Settings
        </a>

    </h2>

    <div class="gssync-settings-content">

        <?php

        switch ($active_tab) {

            case 'driver':
                include GSSYNC_PLUGIN_DIR . 'admin/templates/settings-driver.php';
                break;

            case 'employee':
                include GSSYNC_PLUGIN_DIR . 'admin/templates/settings-employee.php';
                break;

            case 'other':
                include GSSYNC_PLUGIN_DIR . 'admin/templates/settings-other.php';
                break;

            case 'sheet':
            default:
                include GSSYNC_PLUGIN_DIR . 'admin/templates/settings-sheet.php';
                break;
        }

        ?>

    </div>

</div>

<?php
}