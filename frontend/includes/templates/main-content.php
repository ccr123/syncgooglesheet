<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/header.php';

$page_slug = $GLOBALS['gssync_page_slug'] ?? '';

$page_file = __DIR__ .
    '/pages/' .
    $page_slug .
    '.php';
?>

<div class="edrive-main-content mt-20 mr-20">

    <?php

    if (file_exists($page_file)) {

        require $page_file;

    } else {

        echo '<h2>Page template not found.</h2>';

    }

    ?>

</div>

<?php
require_once __DIR__ . '/footer.php';