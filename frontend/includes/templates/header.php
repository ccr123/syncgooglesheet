<?php

if (!defined('ABSPATH')) {
    exit;
}

$site_icon = get_site_icon_url(512);

$current_user = wp_get_current_user();

$user_name = '';

if (!empty($current_user->first_name)) {

    $user_name = $current_user->first_name;

} elseif (!empty($current_user->nickname)) {

    $user_name = ucfirst($current_user->nickname);

} elseif (!empty($current_user->display_name)) {

    $user_name = $current_user->display_name;

} elseif (!empty($current_user->user_login)) {

    $user_name = $current_user->user_login;

} else {

    $user_name = $current_user->user_email;

}

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <?php wp_head(); ?>

</head>

<body <?php body_class('edrive-dashboard'); ?>>

<?php wp_body_open(); ?>

<?php if (is_page('edrive-login')) : ?>

    <header class="edrive-header">

        <div class="edrive-container">

            <a
                href="<?php echo esc_url(home_url('/')); ?>"
                class="edrive-logo"
            >

                <img
                    src="<?php echo esc_url(
                        GSSYNC_PLUGIN_URL . 'frontend/assets/images/edrive_long.png'
                    ); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
                >

            </a>

        </div>

    </header>

<?php else : ?>

    <?php

    $sidebar_file = GSSYNC_PLUGIN_DIR .
        'frontend/includes/templates/sidebar.php';

    if (file_exists($sidebar_file)) {
        require $sidebar_file;
    }

    ?>

    <header class="edrive-header">

        <div class="edrive-container d-flex align-center justify-between mr-20 mt-10">

            <div class="edrive-header-left text-cream">

                <h4 class="mb-0">
                    Welcome <?php echo esc_html($user_name); ?>
                </h4>

            </div>

            <div class="edrive-header-right d-flex align-center gap-1">

                <a
                    href="<?php echo esc_url(
                        wp_logout_url(
                            home_url('/edrive-login/')
                        )
                    ); ?>"
                    class="btn-accent"
                >
                    Logout
                </a>

            </div>

        </div>

    </header>

<?php endif; ?>