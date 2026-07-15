<?php

if (!defined('ABSPATH')) {
    exit;
}

function gssync_create_tables()
{
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    /*
    |--------------------------------------------------------------------------
    | Locations
    |--------------------------------------------------------------------------
    */

    $locations_table = $wpdb->prefix . 'gssync_locations';

    $sql_locations = "CREATE TABLE {$locations_table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        location_name VARCHAR(255) NOT NULL,

        usage_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
        is_favorite TINYINT(1) NOT NULL DEFAULT 0,
        last_used_at DATETIME NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        UNIQUE KEY location_name (location_name),
        KEY usage_count (usage_count),
        KEY is_favorite (is_favorite)
    ) {$charset_collate};";

    /*
    |--------------------------------------------------------------------------
    | Expense Types
    |--------------------------------------------------------------------------
    */

    $expense_table = $wpdb->prefix . 'gssync_expenses';

    $sql_expenses = "CREATE TABLE {$expense_table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        expense_name VARCHAR(255) NOT NULL,

        usage_count BIGINT UNSIGNED NOT NULL DEFAULT 0,
        is_favorite TINYINT(1) NOT NULL DEFAULT 0,
        last_used_at DATETIME NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
        UNIQUE KEY expense_name (expense_name),
        KEY usage_count (usage_count),
        KEY is_favorite (is_favorite)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql_locations);
    dbDelta($sql_expenses);
}