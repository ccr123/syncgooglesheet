<?php

add_action(
    'wp_ajax_gssync_sync_locations',
    'gssync_sync_locations_ajax'
);

function gssync_sync_locations_ajax()
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

    $table = $wpdb->prefix . 'gssync_locations';

    $header_index = false;
    $source_index = false;
    $destination_index = false;

    /*
    |--------------------------------------------------------------------------
    | Find Header Row
    |--------------------------------------------------------------------------
    */
    foreach ($rows as $row_index => $row) {

        if (
            in_array('SOURCE', $row, true) &&
            in_array('DESTINATION', $row, true)
        ) {

            $header_index = $row_index;

            $source_index = array_search(
                'SOURCE',
                $row,
                true
            );

            $destination_index = array_search(
                'DESTINATION',
                $row,
                true
            );

            break;
        }
    }

    if ($header_index === false) {
        wp_send_json_error(
            'SOURCE / DESTINATION columns not found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Collect Locations
    |--------------------------------------------------------------------------
    */
    $locations = [];

    foreach ($rows as $row_index => $row) {

        if ($row_index <= $header_index) {
            continue;
        }

        if (
            $source_index !== false &&
            isset($row[$source_index]) &&
            !empty(trim($row[$source_index]))
        ) {
            $locations[] = strtoupper(
                trim($row[$source_index])
            );
        }

        if (
            $destination_index !== false &&
            isset($row[$destination_index]) &&
            !empty(trim($row[$destination_index]))
        ) {
            $locations[] = strtoupper(
                trim($row[$destination_index])
            );
        }
    }

    $locations = array_unique($locations);

    if (empty($locations)) {
        wp_send_json_error(
            'No locations found.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save New Locations
    |--------------------------------------------------------------------------
    */
    $inserted = 0;

    foreach ($locations as $location) {

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id
                FROM {$table}
                WHERE location_name = %s",
                $location
            )
        );

        if (!$exists) {

            $wpdb->insert(
                $table,
                [
                    'location_name' => $location,
                ]
            );

            $inserted++;
        }
    }

    wp_send_json_success(
        sprintf(
            '%d new locations synced. %d total locations found.',
            $inserted,
            count($locations)
        )
    );
}