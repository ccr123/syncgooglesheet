<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'add_meta_boxes',
    'gssync_register_vehicle_meta_boxes'
);

function gssync_register_vehicle_meta_boxes()
{
    add_meta_box(
        'gssync-expense-generator',
        'Sheet Expense Generator',
        'gssync_expense_generator_callback',
        'gssync_vehicle',
        'side',
        'default'
    );
}

function gssync_expense_generator_callback($post)
{
    ?>
    <p>Generate expenses from sheet data.</p>

    <button
        type="button"
        class="button button-primary"
        id="gssync-generate-expenses"
        data-post-id="<?php echo esc_attr($post->ID); ?>"
    >
        Generate Expenses
    </button>

    <div id="gssync-expense-result"></div>
    <?php
}

add_action(
    'wp_ajax_gssync_generate_expenses',
    'gssync_generate_expenses_ajax'
);

function gssync_generate_expenses_ajax()
{
    check_ajax_referer(
        'gssync_preview_nonce',
        'nonce'
    );

    $spreadsheet_id = sanitize_text_field(
        $_POST['spreadsheet_id']
    );

    $sheet_name = sanitize_text_field(
        $_POST['sheet_name']
    );

    if (
        empty($spreadsheet_id) ||
        empty($sheet_name)
    ) {
        wp_send_json_error(
            'Spreadsheet ID or Sheet missing.'
        );
    }

    $rows = gssync_get_sheet_data(
        $spreadsheet_id,
        $sheet_name
    );

    if (empty($rows)) {
        wp_send_json_error(
            'No data found.'
        );
    }

    ob_start();

    include GSSYNC_PLUGIN_DIR . 'admin/templates/expenses-template.php';

    wp_send_json_success(
        ob_get_clean()
    );
}

add_action(
    'wp_ajax_gssync_toggle_favourite',
    'gssync_toggle_favourite'
);

function gssync_toggle_favourite()
{
    global $wpdb;

    $expense_id = absint(
        $_POST['expense_id'] ?? 0
    );

    $table = $wpdb->prefix . 'gssync_expenses';

    $current = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT is_favorite
            FROM {$table}
            WHERE id = %d",
            $expense_id
        )
    );

    if ($current === null) {

        wp_send_json_error();

    }

    $new_value = $current ? 0 : 1;

    $wpdb->update(
        $table,
        [
            'is_favorite' => $new_value
        ],
        [
            'id' => $expense_id
        ]
    );

    wp_send_json_success([
        'is_favorite' => $new_value
    ]);
}