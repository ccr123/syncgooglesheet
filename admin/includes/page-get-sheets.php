<?php

if (!defined('ABSPATH')) {
    exit;
}

function gssync_get_sheet_tabs($spreadsheet_id)
{
    $settings = get_option('gssync_settings', []);

    if (empty($settings['service_account_json'])) {
        return [];
    }

    $service_account = json_decode(
        $settings['service_account_json'],
        true
    );

    if (
        empty($service_account['client_email']) ||
        empty($service_account['private_key'])
    ) {
        return [];
    }

    $now = time();

    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $payload = [
        'iss'   => $service_account['client_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ];

    $base64url = function ($data) {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    };

    $jwt_header  = $base64url(wp_json_encode($header));
    $jwt_payload = $base64url(wp_json_encode($payload));

    $signature_input = $jwt_header . '.' . $jwt_payload;

    openssl_sign(
        $signature_input,
        $signature,
        $service_account['private_key'],
        OPENSSL_ALGO_SHA256
    );

    $jwt = $jwt_header . '.'
        . $jwt_payload . '.'
        . $base64url($signature);

    $token_request = wp_remote_post(
        'https://oauth2.googleapis.com/token',
        [
            'body' => [
                'grant_type' =>
                    'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]
    );

    if (is_wp_error($token_request)) {
        return [];
    }

    $token_body = json_decode(
        wp_remote_retrieve_body($token_request),
        true
    );

    if (empty($token_body['access_token'])) {
        return [];
    }

    $access_token = $token_body['access_token'];

    $response = wp_remote_get(
        'https://sheets.googleapis.com/v4/spreadsheets/' . $spreadsheet_id,
        [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
        ]
    );

    if (is_wp_error($response)) {
        return [];
    }

    $body = json_decode(
        wp_remote_retrieve_body($response),
        true
    );

    if (empty($body['sheets'])) {
        return [];
    }

    $tabs = [];

    foreach ($body['sheets'] as $sheet) {
        if (!empty($sheet['properties']['title'])) {
            $tabs[] = $sheet['properties']['title'];
        }
    }

    return $tabs;
}

add_action(
    'wp_ajax_gssync_preview_sheet',
    'gssync_preview_sheet_ajax'
);

function gssync_preview_sheet_ajax()
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

    /*
     * Fetch sheet here
     * Your existing Google API function
     */

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

    include GSSYNC_PLUGIN_DIR . 'admin/templates/sheet-preview-template.php';

    wp_send_json_success(
        ob_get_clean()
    );
}


function gssync_get_sheet_data($spreadsheet_id, $sheet_name)
{
    $settings = get_option('gssync_settings', []);

    if (empty($settings['service_account_json'])) {
        return [];
    }

    $service_account = json_decode(
        $settings['service_account_json'],
        true
    );

    if (
        empty($service_account['client_email']) ||
        empty($service_account['private_key'])
    ) {
        return [];
    }

    $now = time();

    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT',
    ];

    $payload = [
        'iss'   => $service_account['client_email'],
        'scope' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ];

    $base64url = function ($data) {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    };

    $jwt_header  = $base64url(wp_json_encode($header));
    $jwt_payload = $base64url(wp_json_encode($payload));

    $signature_input = $jwt_header . '.' . $jwt_payload;

    openssl_sign(
        $signature_input,
        $signature,
        $service_account['private_key'],
        OPENSSL_ALGO_SHA256
    );

    $jwt = $jwt_header . '.'
        . $jwt_payload . '.'
        . $base64url($signature);

    $token_request = wp_remote_post(
        'https://oauth2.googleapis.com/token',
        [
            'body' => [
                'grant_type' =>
                    'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]
    );

    if (is_wp_error($token_request)) {
        return [];
    }

    $token_body = json_decode(
        wp_remote_retrieve_body($token_request),
        true
    );

    if (empty($token_body['access_token'])) {
        return [];
    }

    $access_token = $token_body['access_token'];

    $range = rawurlencode($sheet_name . '!A:Z');

    $response = wp_remote_get(
        'https://sheets.googleapis.com/v4/spreadsheets/' .
        $spreadsheet_id .
        '/values/' .
        $range,
        [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
        ]
    );

    if (is_wp_error($response)) {
        return [];
    }

    $body = json_decode(
        wp_remote_retrieve_body($response),
        true
    );

    if (empty($body['values'])) {
        return [];
    }

    return $body['values'];
}

function gssync_admin_assets($hook)
{
    wp_enqueue_style(
        'gssync-admin-style',
        GSSYNC_PLUGIN_URL . 'admin/assets/css/style.css',
        array(),
        filemtime(GSSYNC_PLUGIN_DIR . 'admin/assets/css/style.css')
    );
}

add_action('admin_enqueue_scripts', 'gssync_admin_assets');