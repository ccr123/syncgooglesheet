<?php

$vehicle_id = isset($_GET['vehicle_id'])
    ? absint($_GET['vehicle_id'])
    : 0;

$vehicle = get_post($vehicle_id);

$vehicle_name = $vehicle
    ? $vehicle->post_title
    : 'Unknown Vehicle';

global $wpdb;

/*
|--------------------------------------------------------------------------
| Locations
|--------------------------------------------------------------------------
*/
$locations = $wpdb->get_results(
    "SELECT *
    FROM {$wpdb->prefix}gssync_locations
    ORDER BY location_name ASC"
);

/*
|--------------------------------------------------------------------------
| Expenses
|--------------------------------------------------------------------------
*/
$expenses = $wpdb->get_results(
    "SELECT *
    FROM {$wpdb->prefix}gssync_expenses
    ORDER BY expense_name ASC"
);

$primary_expenses = [];
$driver_expenses  = [];
$owner_expenses   = [];

foreach ($expenses as $expense) {

    switch ($expense->expense_tags) {

        case 'Driver':
            $driver_expenses[] = $expense;
            break;

        case 'Owner':
            $owner_expenses[] = $expense;
            break;

        default:
            $primary_expenses[] = $expense;
            break;
    }
}
// Drivers
$drivers = get_post_meta(
    $vehicle_id,
    '_gssync_drivers',
    true
);

if (!is_array($drivers)) {
    $drivers = [];
}

?>

<div class="gssync-card-wrapper update-entry bg-white br-12 pb-10 pl-20 pr-20 mb-40">

    <section class="gssync-card">

        <h1 class="text-center pt-20 mb-20">
            Update Vehicle Entry
        </h1>

        <div class="update-entry-filters d-flex gap-2 mb-20 justify-center mt-40 flex-column">

        <div class="gssync-calendar-switch">
            <button
                type="button"
                class="gssync-calendar-btn active"
                data-calendar="ad"
            >
                AD
            </button>

            <button
                type="button"
                class="gssync-calendar-btn"
                data-calendar="bs"
            >
                BS
            </button>
        </div>
        <div class="date d-flex justify-center align-center">
            <!-- <div class="year-left d-flex align-center"><svg width="25" height="25" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="23" stroke="black" stroke-width="4"/>
                <path d="M15.5778 26.4063C14.8011 25.6208 14.8082 24.3545 15.5936 23.5779L28.3928 10.9216C29.1782 10.1449 30.4445 10.152 31.2212 10.9374C31.9978 11.7229 31.9907 12.9892 31.2053 13.7658L19.8283 25.0159L31.0783 36.3929C31.855 37.1783 31.8479 38.4446 31.0625 39.2213C30.277 39.9979 29.0107 39.9908 28.2341 39.2054L15.5778 26.4063ZM17 25L16.9888 27L16.9887 27L16.9999 25L17.0111 23L17.0112 23L17 25Z" fill="black"/>
                </svg>
            </div> -->
            <?php

                $today = current_time('Y-m-d');

                $current_date = GSSYNC_Nepali_Date::get_by_ad_date($today);

                if ($current_date) {

                    $input_value = $current_date['ad_month_name'] . ' ' . $current_date['ad_year'];

                } else {

                    $current_date = [
                        'ad_date'       => date('Y-m-d'),
                        'ad_year'       => date('Y'),
                        'ad_month'      => date('n'),
                        'ad_day'        => date('j'),
                        'ad_month_name' => date('F'),
                        'ad_day_name'   => date('D'),

                        'bs_date'       => '',
                        'bs_year'       => '',
                        'bs_month'      => '',
                        'bs_day'        => '',
                        'bs_month_name' => '',
                        'bs_day_name'   => '',
                    ];

                    $input_value = date('F Y');
                }

                ?>

                <input
                    type="text"
                    class="gssync-year-field"
                    disabled="disabled"
                    value="<?php echo esc_attr($input_value); ?>"

                    data-date-ad="<?php echo esc_attr($current_date['ad_date']); ?>"
                    data-date-bs="<?php echo esc_attr($current_date['bs_date']); ?>"

                    data-year-ad="<?php echo esc_attr($current_date['ad_year']); ?>"
                    data-year-bs="<?php echo esc_attr($current_date['bs_year']); ?>"

                    data-month-ad="<?php echo esc_attr($current_date['ad_month']); ?>"
                    data-month-bs="<?php echo esc_attr($current_date['bs_month']); ?>"

                    data-month-name-ad="<?php echo esc_attr($current_date['ad_month_name']); ?>"
                    data-month-name-bs="<?php echo esc_attr($current_date['bs_month_name']); ?>"

                    data-day-ad="<?php echo esc_attr($current_date['ad_day']); ?>"
                    data-day-bs="<?php echo esc_attr($current_date['bs_day']); ?>"

                    data-day-name-ad="<?php echo esc_attr($current_date['ad_day_name']); ?>"
                    data-day-name-bs="<?php echo esc_attr($current_date['bs_day_name']); ?>"
                >
            <!-- <div class="year-right d-flex align-center"><svg width="25" height="25" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="23" stroke="black" stroke-width="4"/>
                <path d="M15.5778 26.4063C14.8011 25.6208 14.8082 24.3545 15.5936 23.5779L28.3928 10.9216C29.1782 10.1449 30.4445 10.152 31.2212 10.9374C31.9978 11.7229 31.9907 12.9892 31.2053 13.7658L19.8283 25.0159L31.0783 36.3929C31.855 37.1783 31.8479 38.4446 31.0625 39.2213C30.277 39.9979 29.0107 39.9908 28.2341 39.2054L15.5778 26.4063ZM17 25L16.9888 27L16.9887 27L16.9999 25L17.0111 23L17.0112 23L17 25Z" fill="black"/>
                </svg>
            </div> -->
            
        </div>

        <div class="gssync-date-list owl-carousel owl-theme">

            <?php

                $today = current_time('Y-m-d');

                $dates = GSSYNC_Nepali_Date::get_range(
                    date('Y-m-d', strtotime('-4 days', strtotime($today))),
                    date('Y-m-d', strtotime('+10 days', strtotime($today)))
                );


                if (!empty($dates)) :

                foreach ($dates as $date) :

                ?>

                <div 
                 class="gssync-date-item <?php echo $date['ad_date'] === $today ? 'active today-active' : ''; ?>"

                data-date-ad="<?php echo esc_attr($date['ad_date']); ?>"
                data-date-bs="<?php echo esc_attr($date['bs_date']); ?>"

                data-year-ad="<?php echo esc_attr($date['ad_year']); ?>"
                data-year-bs="<?php echo esc_attr($date['bs_year']); ?>"
                >


                <span class="ad-value">

                    <span class="day-name">
                        <?php echo esc_html($date['ad_day_name']); ?>
                    </span>

                    <span class="day-number">
                        <?php echo esc_html($date['ad_day']); ?>
                    </span>

                </span>


                <span class="bs-value">

                    <span class="day-name">
                        <?php echo esc_html($date['bs_day_name']); ?>
                    </span>

                    <span class="day-number">
                        <?php echo esc_html($date['bs_day']); ?>
                    </span>

                </span>


                </div>


                <?php

                endforeach;

                else:

                ?>

                <div class="gssync-no-date">
                    Date data not available
                </div>

                <?php endif; ?>

        </div>

        </div>

        <div class="gssync-search-box">

            <input
                type="text"
                class="regular-text"
                placeholder="Search Vehicle No..."
            >

        </div>
        

    </section>


    <section class="gssync-card mt-30">

        <h4 class="mb-20 ml-10">
            Vehicle : <?php echo esc_html($vehicle_name); ?>
        </h4>

        <form method="post" class="gssync-vehicle-entry-form">

            <input type="hidden" name="entry_date_ad" value="">
            <input type="hidden" name="entry_date_bs" value="">

            <div class="gssync-form-row driver-row">

                <div class="d-flex align-center gap-1">

                    <label>
                        Driver:
                    </label>
                            <?php if (count($drivers) === 1) : ?>

                                <input
                                    type="hidden"
                                    name="driver_id"
                                    value="<?php echo esc_attr($drivers[0]); ?>"
                                >

                                <strong>
                                    <?php echo esc_html($drivers[0]); ?>
                                </strong>

                            <?php else : ?>
                    <select name="driver_id">

                        <option value="">
                            Select Driver
                        </option>

                        <?php foreach ($drivers as $driver) : ?>

                            <option value="<?php echo esc_attr($driver); ?>">
                                <?php echo esc_html($driver); ?>
                            </option>

                        <?php endforeach; ?>
                    <?php endif; ?>
                    </select>

                    <button
                        type="button"
                        class="btn-link"
                    >
                        + Add Driver Name
                    </button>

                </div>

            </div>

            <div class="gssync-form-row">

                <div class="d-flex align-center gap-1">
                <label>
                    Source:
                </label>
                    <select name="source">

                        <option value="">
                            Select Source
                        </option>

                        <?php foreach ($locations as $location) : ?>

                            <option value="<?php echo esc_attr($location->id); ?>">
                                <?php echo esc_html($location->location_name); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <button
                        type="button"
                        class="btn-link"
                    >
                        + Add New Source
                    </button>

                </div>

            </div>

            <div class="gssync-form-row">

                <div class="d-flex align-center gap-1">

                    <label>
                        Destination:
                    </label>
                    <select name="destination">

                        <option value="">
                            Select Destination
                        </option>

                        <?php foreach ($locations as $location) : ?>

                            <option value="<?php echo esc_attr($location->id); ?>">
                                <?php echo esc_html($location->location_name); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <button
                        type="button"
                        class="btn-link"
                    >
                        + Add New Destination
                    </button>

                </div>

            </div>

            <div class="gssync-form-row d-flex align-center gap-1">

                <label>
                    Price:
                </label>

                <input
                    type="number"
                    name="price"
                    class="regular-text"
                    placeholder="Enter Price"
                    step="0.01"
                >

                <select name="reference">
                    <option value="online">
                            Reference
                        </option>
                        <option value="online">
                            Online
                        </option>
                        <option value="offline">
                            Offline
                        </option>
                        <option value="office">
                            Office
                        </option>
                </select>
            </div>

            <div class="gssync-form-row d-flex align-center gap-1">

                

            </div>

            <div
                class="d-flex justify-right mt-30 mr-10 mb-20 action-buttons"
            >
                <button
                    type="submit"
                    class="btn-primary"
                >
                    Update
                </button>

            </div>

        </form>

    </section>

</div>

<div class="gssync-card-wrapper update-entry bg-white br-12 pb-10 pl-20 pr-20 mb-40">
    <section class="gssync-card pt-20 pb-20">
        <div class="gssync-expense-wrapper ml-10">
            <div class="add-expense-header d-flex justify-between mb-20">
                <h4>Expenses</h4>
                <button class="add-expense-class">Add Expense</button>
            </div>

            <form method="post" class="gssync-vehicle-expense-entry-form d-flex flex-column gap-1">
                <!-- Primary -->
                <div class="gssync-expense-row gssync-primary-expense-row d-flex align-center gap-1">

                    <label>Expense:</label>

                    <select name="primary_expense[]">
                        <option value="">Select Expense</option>

                        <?php foreach ($primary_expenses as $expense) : ?>
                            <option value="<?php echo esc_attr($expense->id); ?>">
                                <?php echo esc_html($expense->expense_name); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <input
                        type="number"
                        name="primary_expense_price[]"
                        placeholder="Amount"
                    >
                </div>



                <!-- Driver -->
                <div class="gssync-expense-row gssync-driver-expense-row d-flex align-center gap-1">

                    <label>Driver:</label>

                    <select name="driver_expense[]">
                        <option value="">Select Expense</option>

                        <?php foreach ($driver_expenses as $expense) : ?>
                            <option value="<?php echo esc_attr($expense->id); ?>">
                                <?php echo esc_html($expense->expense_name); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <input
                        type="number"
                        name="driver_expense_price[]"
                        placeholder="Amount"
                    >
                </div>



                <!-- Owner -->
                <div class="gssync-expense-row gssync-owner-expense-row d-flex align-center gap-1">

                    <label>Owner:</label>

                    <select name="owner_expense[]">
                        <option value="">Select Expense</option>

                        <?php foreach ($owner_expenses as $expense) : ?>
                            <option value="<?php echo esc_attr($expense->id); ?>">
                                <?php echo esc_html($expense->expense_name); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <input
                        type="number"
                        name="owner_expense_price[]"
                        placeholder="Amount"
                    >
                </div>

                <div class="d-flex justify-between mt-30 mb-20 action-buttons">

                <button
                    type="button"
                    class="btn-secondary"
                >
                    Preview
                </button>

                <button
                    type="submit"
                    class="btn-primary"
                >
                    Update
                </button>

            </div>
            
            </form>
            </div>
    </section>
</div>