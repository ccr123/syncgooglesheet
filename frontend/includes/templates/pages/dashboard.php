<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="gssync-dashboard-wrapper dashboard">

    <h1 class="text-center">
        Dashboard
    </h1>

    <p>
        Logged in user:
        <?php echo esc_html(
            wp_get_current_user()->display_name
        ); ?>
    </p>

</div>