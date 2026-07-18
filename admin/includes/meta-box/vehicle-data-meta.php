<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'add_meta_boxes',
    'gssync_vehicle_month_meta_box'
);

function gssync_vehicle_month_meta_box()
{
    add_meta_box(
        'gssync_vehicle_month',
        __('Vehicle Month Data', 'google-sheet-sync'),
        'gssync_vehicle_month_meta_box_callback',
        'gssync_vehicle',
        'normal',
        'high'
    );
}

function gssync_vehicle_month_meta_box_callback($post)
{
    ?>

    <div class="gssync-toolbar">

    <div class="gssync-toolbar-left">

        <select id="gssync-year">
            <?php for ($year = 2080; $year <= 2090; $year++) : ?>
                <option value="<?php echo esc_attr($year); ?>">
                    <?php echo esc_html($year); ?>
                </option>
            <?php endfor; ?>
        </select>

        <select id="gssync-month">
            <option value="">Month</option>
            <option value="ashar">Ashar</option>
            <option value="shrawan">Shrawan</option>
            <option value="bhadra">Bhadra</option>
            <option value="ashwin">Ashwin</option>
            <option value="kartik">Kartik</option>
            <option value="mangsir">Mangsir</option>
            <option value="poush">Poush</option>
            <option value="magh">Magh</option>
            <option value="falgun">Falgun</option>
            <option value="chaitra">Chaitra</option>
            <option value="baishakh">Baishakh</option>
            <option value="jestha">Jestha</option>
        </select>

    </div>

    <div class="gssync-toolbar-right">

        <input
            type="search"
            id="gssync-month-search"
            placeholder="Search..."
        >

    </div>

</div>

<div id="gssync-month-results"></div>

    <?php
}