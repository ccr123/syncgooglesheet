<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_user_logged_in()) {

    wp_redirect(
        site_url('/edrive-dashboard/')
    );

    exit;
}

require_once __DIR__ . '/header.php';
?>

<div class="gssync-login">

    <h1>
        Login
    </h1>

    <?php
    wp_login_form([
        'redirect' => site_url('/edrive-dashboard/')
    ]);
    ?>


    <p class="edrive-forgot-password">
        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">
            Forgot password?
        </a>
    </p>
</div>

<?php
require_once __DIR__ . '/footer.php';