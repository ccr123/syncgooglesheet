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

?>

<div class="gssync-card-wrapper update-entry bg-white br-12 pb-10 pl-20 pr-20 mb-40">

    <section class="gssync-card">

        <h1 class="text-center pt-20 mb-20">
            Update Vehicle Entry
        </h1>

        <div class="update-entry-filters d-flex gap-2 mb-20 justify-center mt-40 flex-column">

        <div class="date d-flex justify-center align-center">
            <div class="year-left d-flex align-center"><svg width="25" height="25" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="23" stroke="black" stroke-width="4"/>
                <path d="M15.5778 26.4063C14.8011 25.6208 14.8082 24.3545 15.5936 23.5779L28.3928 10.9216C29.1782 10.1449 30.4445 10.152 31.2212 10.9374C31.9978 11.7229 31.9907 12.9892 31.2053 13.7658L19.8283 25.0159L31.0783 36.3929C31.855 37.1783 31.8479 38.4446 31.0625 39.2213C30.277 39.9979 29.0107 39.9908 28.2341 39.2054L15.5778 26.4063ZM17 25L16.9888 27L16.9887 27L16.9999 25L17.0111 23L17.0112 23L17 25Z" fill="black"/>
                </svg>
            </div>
            <input
                type="text"
                class="gssync-year-field"
                value="<?php echo date('F Y'); ?>"
                min="2000"
                max="2100"
            >
            <div class="year-right d-flex align-center"><svg width="25" height="25" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="23" stroke="black" stroke-width="4"/>
                <path d="M15.5778 26.4063C14.8011 25.6208 14.8082 24.3545 15.5936 23.5779L28.3928 10.9216C29.1782 10.1449 30.4445 10.152 31.2212 10.9374C31.9978 11.7229 31.9907 12.9892 31.2053 13.7658L19.8283 25.0159L31.0783 36.3929C31.855 37.1783 31.8479 38.4446 31.0625 39.2213C30.277 39.9979 29.0107 39.9908 28.2341 39.2054L15.5778 26.4063ZM17 25L16.9888 27L16.9887 27L16.9999 25L17.0111 23L17.0112 23L17 25Z" fill="black"/>
                </svg>
            </div>
            
        </div>

        <div class="gssync-date-list owl-carousel owl-theme">

            <?php

                $today = current_time('timestamp');

                for ($i = -4; $i <= 10; $i++) :

                    $date = strtotime("$i days", $today);

                    $classes = [];

                    if ($i === 0) {
                        $classes[] = 'active';
                        $classes[] = 'today-active';
                    }

                ?>

                    <div
                        class="gssync-date-item <?php echo esc_attr(implode(' ', $classes)); ?>"
                        data-date="<?php echo esc_attr(date('Y-m-d', $date)); ?>"
                    >

                        <span class="day-name">
                            <?php echo date('D', $date); ?>
                        </span>

                        <span class="day-number">
                            <?php echo date('d', $date); ?>
                        </span>

                    </div>

                <?php endfor; ?>

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

            <div class="gssync-form-row driver-row">

                <div class="d-flex align-center gap-1 ">
                    <label>
                        Driver:
                    </label>
                    <select name="driver_id">

                        <option value="">
                            Select Driver
                        </option>

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

            </div>

            <div class="gssync-expense-wrapper ml-10">

                <div class="gssync-expense-row d-flex align-center gap-1">

                    <label>
                        Expenses:
                    </label>

                    <select name="expense[]">

                        <option value="">
                            Select Expense
                        </option>

                        <?php foreach ($expenses as $expense) : ?>

                            <option value="<?php echo esc_attr($expense->id); ?>">
                                <?php echo esc_html($expense->expense_name); ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                    <input
                        type="number"
                        name="expense_price[]"
                        placeholder="Amount"
                    >

                </div>

                <button
                    type="button"
                    class="gssync-add-expense"
                >
                    Add Expense
                </button>
            </div>


            <div
                class="d-flex justify-between mt-30 mb-20 action-buttons"
            >

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

    </section>

</div>