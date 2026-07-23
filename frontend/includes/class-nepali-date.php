<?php

if (!defined('ABSPATH')) {
    exit;
}

class GSSYNC_Nepali_Date
{
    /**
     * Get full row by AD date.
     */
    public static function get_by_ad_date($ad_date)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gssync_nepali_dates';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE ad_date = %s LIMIT 1",
                $ad_date
            ),
            ARRAY_A
        );
    }

    /**
     * Get full row by BS date.
     */
    public static function get_by_bs_date($bs_date)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gssync_nepali_dates';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE bs_date = %s LIMIT 1",
                $bs_date
            ),
            ARRAY_A
        );
    }

    /**
     * AD → BS
     */
    public static function ad_to_bs($ad_date)
    {
        return self::get_by_ad_date($ad_date);
    }

    /**
     * BS → AD
     */
    public static function bs_to_ad($bs_date)
    {
        return self::get_by_bs_date($bs_date);
    }

    /**
     * Today's complete row.
     */
    public static function today()
    {
        return self::get_by_ad_date(current_time('Y-m-d'));
    }

    /**
     * Get a range of AD dates.
     */
    public static function get_range($start, $end)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'gssync_nepali_dates';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT *
                FROM {$table}
                WHERE ad_date BETWEEN %s AND %s
                ORDER BY ad_date ASC",
                $start,
                $end
            ),
            ARRAY_A
        );
    }
}