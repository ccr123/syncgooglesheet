<?php

$service_account_json = isset($settings['service_account_json'])
    ? $settings['service_account_json']
    : '';

if (
    isset($_POST['gssync_save_sheet_settings']) &&
    check_admin_referer(
        'gssync_settings_nonce_action',
        'gssync_settings_nonce'
    )
) {

    $settings['service_account_json'] = wp_unslash(
        $_POST['service_account_json']
    );

    update_option(
        'gssync_settings',
        $settings
    );

    echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
}
?>

<form method="post">

    <?php wp_nonce_field(
        'gssync_settings_nonce_action',
        'gssync_settings_nonce'
    ); ?>

    <table class="form-table">

        <tr>

            <th>
                Service Account JSON
            </th>

            <td>

                <textarea
                    name="service_account_json"
                    rows="20"
                    class="large-text code"
                ><?php echo esc_textarea($service_account_json); ?></textarea>

            </td>

        </tr>

    </table>

    <input
        type="hidden"
        name="gssync_save_sheet_settings"
        value="1"
    >

    <?php submit_button('Save Sheet Settings'); ?>

</form>