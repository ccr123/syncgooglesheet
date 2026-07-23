<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Create Dashboard Page
|--------------------------------------------------------------------------
*/



require_once __DIR__ . '/includes/hooks.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/cron/nepali-date.php';

    if (!wp_next_scheduled('gssync_nepali_date_sync')) {

        wp_schedule_event(
            time() + 60,
            'monthly',
            'gssync_nepali_date_sync'
        );

    }


add_action('gssync_nepali_date_sync', function () {
    GSSYNC_Nepali_Date_Cron::sync();
});
