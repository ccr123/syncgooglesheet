<?php

add_action(
    'wp_ajax_gssync_sync_expenses',
    'gssync_sync_expenses_ajax'
);

function gssync_sync_expenses_ajax()
{
    global $wpdb;

    $spreadsheet_id = sanitize_text_field(
        $_POST['spreadsheet_id'] ?? ''
    );

    $sheet_name = sanitize_text_field(
        $_POST['sheet_name'] ?? ''
    );

    if (
        empty($spreadsheet_id) ||
        empty($sheet_name)
    ) {
        wp_send_json_error(
            'Spreadsheet ID or Sheet Name missing.'
        );
    }

    $rows = gssync_get_sheet_data(
        $spreadsheet_id,
        $sheet_name
    );

    if (empty($rows)) {
        wp_send_json_error(
            'No sheet data found.'
        );
    }

    $table = $wpdb->prefix . 'gssync_expenses';

    $header_index = false;
    $amount_index = false;
    $revenue_index = false;

    /*
    |--------------------------------------------------------------------------
    | Find Header Row
    |--------------------------------------------------------------------------
    */
    foreach ($rows as $row_index => $row) {

        if (
            in_array('AMOUNT', $row, true) &&
            in_array('REVENUE', $row, true)
        ) {

            $header_index = $row_index;

            $amount_index = array_search(
                'AMOUNT',
                $row,
                true
            );

            $revenue_index = array_search(
                'REVENUE',
                $row,
                true
            );

            break;
        }
    }

    if ($header_index === false) {

        wp_send_json_error(
            'AMOUNT / REVENUE columns not found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Collect Expense Types
    |--------------------------------------------------------------------------
    */
    $expense_types = [];

    for (
        $i = $amount_index + 1;
        $i < $revenue_index;
        $i++
    ) {

        if (
            !empty($rows[$header_index][$i])
        ) {

            $expense_types[] = strtoupper(
                trim(
                    $rows[$header_index][$i]
                )
            );
        }
    }

    $expense_types = array_unique(
        $expense_types
    );
    // print_r($expense_types);exit;
    if (empty($expense_types)) {

        wp_send_json_error(
            'No expense types found.'
        );
    }

    /*
|--------------------------------------------------------------------------
| Save Expense Types
|--------------------------------------------------------------------------
*/
    $inserted = 0;
    $existing = 0;

    foreach ($expense_types as $expense_name) {

        $expense_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id
                FROM {$table}
                WHERE expense_name = %s",
                $expense_name
            )
        );

        if ($expense_id) {

            $wpdb->update(
                $table,
                [
                    'last_used_at' => current_time('mysql'),
                ],
                [
                    'id' => $expense_id,
                ]
            );

            $existing++;

        } else {

            $result = $wpdb->insert(
                $table,
                [
                    'expense_name' => $expense_name,
                    'last_used_at' => current_time('mysql'),
                ]
            );

            if ($result !== false) {
                $inserted++;
            }
        }
    }

    wp_send_json_success(
        sprintf(
            '%d new expense types added. %d existing expense types found. %d total expense types in sheet.',
            $inserted,
            $existing,
            count($expense_types)
        )
    );
}