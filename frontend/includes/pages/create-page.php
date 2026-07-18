<?php

if (!defined('ABSPATH')) {
    exit;
}

function gssync_create_pages()
{
    /*
    |--------------------------------------------------------------------------
    | Login Page
    |--------------------------------------------------------------------------
    */
    if (!get_page_by_path('edrive-login')) {

        wp_insert_post([
            'post_title'   => 'Edrive Login',
            'post_name'    => 'edrive-login',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Page
    |--------------------------------------------------------------------------
    */
    if (!get_page_by_path('edrive-dashboard')) {

        wp_insert_post([
            'post_title'   => 'Edrive Dashboard',
            'post_name'    => 'edrive-dashboard',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
    }

    if (!get_page_by_path('edrive-update-vehicle')) {

        wp_insert_post([
            'post_title'   => 'Edrive Update Vehicle',
            'post_name'    => 'edrive-update-vehicle',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
    }


}

function gssync_frontend_assets()
{
    // if (
    //     !is_page('edrive-login') &&
    //     !is_page('edrive-dashboard')
    // ) {
    //     return;
    // }

    if (is_page('edrive-login')){
        wp_enqueue_style(
            'gssync-login',
            GSSYNC_PLUGIN_URL . 'frontend/assets/css/login.css',
            [],
            GSSYNC_VERSION
        );
    }

    if (!is_page('edrive-login')){
        wp_enqueue_style(
            'gssync-sidebar',
            GSSYNC_PLUGIN_URL . 'frontend/assets/css/sidebar.css',
            [],
            GSSYNC_VERSION
        );
    }

    wp_enqueue_style(
        'gssync-frontend',
        GSSYNC_PLUGIN_URL . 'frontend/assets/css/style.css',
        [],
        GSSYNC_VERSION
    );

    wp_enqueue_style(
        'gssync-bootstrap',
        GSSYNC_PLUGIN_URL . 'frontend/assets/css/bootstrap.css',
        [],
        GSSYNC_VERSION
    );

    wp_enqueue_script(
        'gssync-frontend',
        GSSYNC_PLUGIN_URL . 'frontend/assets/js/custom.js',
        ['jquery'],
        GSSYNC_VERSION,
        true
    );
}