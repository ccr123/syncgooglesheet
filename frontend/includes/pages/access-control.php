<?php

if (!defined('ABSPATH')) {
    exit;
}

function gssync_protect_dashboard()
{
    if (is_admin()) {
        return;
        }
        
    if (is_page('edrive-login')) {
        return;
    }

    if (is_page('edrive-dashboard')) {
        if (!is_user_logged_in()) {

            wp_redirect(
                site_url('/edrive-login/')
            );

            exit;
        }
    }
}


function gssync_template_loader($template)
{
    if (is_page('edrive-login')) {

        return GSSYNC_PLUGIN_DIR .
            'frontend/includes/templates/login.php';
    }

    if (!is_user_logged_in()) {

        if (
            is_page() &&
            strpos(get_post_field(
                'post_name',
                get_queried_object_id()
            ), 'edrive-') === 0
        ) {

            wp_redirect(
                home_url('/edrive-login/')
            );

            exit;
        }
    }

    if (is_page()) {

        $slug = get_post_field(
            'post_name',
            get_queried_object_id()
        );

        if (strpos($slug, 'edrive-') === 0) {

            $GLOBALS['gssync_page_slug'] = str_replace(
                'edrive-',
                '',
                $slug
            );

            return GSSYNC_PLUGIN_DIR .
                'frontend/includes/templates/main-content.php';
        }
    }

    return $template;
}

function gssync_add_page_slug_body_class($classes)
{
    if (is_page()) {

        global $post;

        if (!empty($post->post_name)) {
            $classes[] = 'page-' . sanitize_html_class(
                $post->post_name
            );
        }
    }

    return $classes;
}