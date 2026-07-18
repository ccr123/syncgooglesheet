<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/pages/create-page.php';
require_once __DIR__ . '/pages/access-control.php';
/*
|--------------------------------------------------------------------------
| Frontend Init
|--------------------------------------------------------------------------
*/

add_action(
    'init',
    'gssync_create_pages'
);


add_action(
    'template_redirect',
    'gssync_protect_dashboard'
);

add_filter(
    'template_include',
    'gssync_template_loader'
);

add_filter(
    'body_class',
    'gssync_add_page_slug_body_class'
);

add_action(
    'wp_enqueue_scripts',
    'gssync_frontend_assets'
);