<?php

if (!defined('ABSPATH')) {
    exit;
}

use RohanAdhikari\NepaliDate\NepaliDate;

add_filter('cron_schedules', function ($schedules) {

    if (!isset($schedules['monthly'])) {

        $schedules['monthly'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => 'Monthly'
        ];

    }

    return $schedules;

});


class GSSYNC_Nepali_Date_Cron
{

    public static function sync()
    {
        global $wpdb;


        $table = $wpdb->prefix . 'gssync_nepali_dates';

        $timezone = new DateTimeZone('Asia/Kathmandu');



        /*
         * Get latest date from database
         */
        $latest_date = $wpdb->get_var(
            "SELECT MAX(ad_date) FROM {$table}"
        );



        /*
         * First installation
         */
        if ($latest_date) {

            $start = new DateTime($latest_date, $timezone);
            $start->modify('+1 day');

        } else {

            $start = new DateTime('-3 years', $timezone);

        }


        $start->setTime(0, 0);



        /*
         * Maintain one year future data
         */
        $end = new DateTime('+1 year', $timezone);
        $end->setTime(0, 0);



        /*
         * Batch limit
         */
        $limit = 300;
        $count = 0;



        while ($start <= $end && $count < $limit) {


            $adDate = $start->format('Y-m-d');


            $bs = NepaliDate::fromAd(clone $start);



            /*
             * Insert only if missing
             * Requires UNIQUE KEY on ad_date
             */
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT IGNORE INTO {$table}
                    (
                        ad_date,
                        ad_year,
                        ad_month,
                        ad_day,
                        ad_month_name,
                        ad_day_name,

                        bs_date,
                        bs_year,
                        bs_month,
                        bs_day,
                        bs_month_name,
                        bs_day_name
                    )
                    VALUES
                    (
                        %s,
                        %d,
                        %d,
                        %d,
                        %s,
                        %s,

                        %s,
                        %d,
                        %d,
                        %d,
                        %s,
                        %s
                    )",

                    $adDate,

                    $start->format('Y'),
                    $start->format('n'),
                    $start->format('j'),
                    $start->format('F'),
                    $start->format('D'),


                    $bs->format('Y-m-d'),
                    $bs->format('Y'),
                    (int)$bs->format('m'),
                    (int)$bs->format('d'),
                    $bs->format('F'),
                    $bs->format('D')
                )
            );



            $start->modify('+1 day');

            $count++;

        }

    }

}