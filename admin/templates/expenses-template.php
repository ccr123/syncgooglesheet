<?php

if (!defined('ABSPATH')) {
    exit;
}

$header_index = null;

foreach ($rows as $index => $row) {

    if (
        in_array('SOURCE', $row, true) &&
        in_array('DESTINATION', $row, true) &&
        in_array('AMOUNT', $row, true)
    ) {
        $header_index = $index;
        break;
    }
}

if ($header_index === null) {
    echo 'Header row not found.';
    return;
}

$headers = $rows[$header_index];

$amount_index = array_search(
    'AMOUNT',
    $headers,
    true
);

$revenue_index = array_search(
    'REVENUE',
    $headers,
    true
);

$total_revenue_index = array_search(
    'TOTAL REVENUE',
    $headers,
    true
);

$driver_index = array_search(
    'DRIVER NAME',
    $headers,
    true
);

if (
    $amount_index === false ||
    $revenue_index === false
) {
    echo 'AMOUNT or REVENUE column not found.';
    return;
}
/*
|--------------------------------------------------------------------------
| Car Expenses
|--------------------------------------------------------------------------
|
| Rows after DUE and before DATE header row
|
*/

$car_expenses = [];
$total_car_expense = 0;

$skip_rows = [
    'INCOME',
    'EXPENSE',
    'REVENUE',
    'REVENUE AS HISAB',
    'RECEIVED',
    'DUE',
];

for ($i = 0; $i < $header_index; $i++) {

    $label = trim(
        $rows[$i][0] ?? ''
    );

    $amount = trim(
        $rows[$i][1] ?? ''
    );

    $remarks = trim(
        $rows[$i][2] ?? ''
    );

    if (
        empty($label) ||
        !is_numeric($amount)
    ) {
        continue;
    }

    if (
        in_array(
            strtoupper($label),
            $skip_rows,
            true
        )
    ) {
        continue;
    }

    $car_expenses[] = [
        'label'   => $label,
        'amount'  => (float) $amount,
        'remarks' => $remarks,
    ];

    $total_car_expense += (float) $amount;
}

if (!empty($car_expenses)) {

    echo '<h2>Car Expenses</h2>';

    echo '<table class="widefat striped" style="max-width:800px;margin-bottom:30px;">';

    echo '
    <thead>
        <tr>
            <th>Expense Type</th>
            <th>Amount</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($car_expenses as $expense) {

        echo '
        <tr>
            <td>' . esc_html($expense['label']) . '</td>
            <td>' . number_format($expense['amount'], 0) . '</td>
            <td>' . esc_html($expense['remarks']) . '</td>
        </tr>';
    }

    echo '
    <tr>
        <td><strong>Total Car Expense</strong></td>
        <td><strong>' . number_format($total_car_expense, 0) . '</strong></td>
        <td></td>
    </tr>';

    echo '
    </tbody>
    </table>';
}
/*
|--------------------------------------------------------------------------
| Driver Expenses
|--------------------------------------------------------------------------
*/

$expense_columns = [];

for (
    $i = $amount_index + 1;
    $i < $revenue_index;
    $i++
) {

    if (!empty($headers[$i])) {

        $expense_columns[$i] = $headers[$i];
    }
}

$driver_totals = [];

$current_driver = 'Expenses';

foreach ($rows as $row_index => $row) {

    if ($row_index <= $header_index) {
        continue;
    }

    if ($driver_index !== false) {

        $driver_name = trim(
            $row[$driver_index] ?? ''
        );

        if (!empty($driver_name)) {

            $current_driver = $driver_name;
        }
    }

    $driver_name = $current_driver;

    if (!isset($driver_totals[$driver_name])) {

        foreach ($expense_columns as $column_name) {

            $driver_totals[$driver_name][$column_name] = 0;
        }

        $driver_totals[$driver_name]['TOTAL_AMOUNT'] = 0;
        $driver_totals[$driver_name]['REVENUE'] = 0;
        $driver_totals[$driver_name]['TOTAL_REVENUE'] = 0;
        $driver_totals[$driver_name]['TOTAL_EXPENSE'] = 0;
    }

    $amount = trim(
        $row[$amount_index] ?? ''
    );

    if (
        $amount !== '' &&
        is_numeric($amount)
    ) {

        $driver_totals[$driver_name]['TOTAL_AMOUNT'] += (float) $amount;
    }

    $revenue = trim(
        $row[$revenue_index] ?? ''
    );

    if (
        $revenue !== '' &&
        is_numeric($revenue)
    ) {

        $driver_totals[$driver_name]['REVENUE'] += (float) $revenue;
    }

    if ($total_revenue_index !== false) {

        $total_revenue = trim(
            $row[$total_revenue_index] ?? ''
        );

        if (
            $total_revenue !== '' &&
            is_numeric($total_revenue)
        ) {

            $driver_totals[$driver_name]['TOTAL_REVENUE'] += (float) $total_revenue;
        }
    }

    foreach ($expense_columns as $column_index => $column_name) {

        $value = trim(
            $row[$column_index] ?? ''
        );

        if (
            $value !== '' &&
            is_numeric($value)
        ) {

            $value = (float) $value;

            $driver_totals[$driver_name][$column_name] += $value;

            $driver_totals[$driver_name]['TOTAL_EXPENSE'] += $value;
        }
    }
}

foreach ($driver_totals as $driver_name => $totals) {

    echo '<h2>' . esc_html($driver_name) . '</h2>';

    echo '<table class="widefat striped" style="max-width:600px;margin-bottom:30px;">';

    echo '
    <thead>
        <tr>
            <th>Type</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($expense_columns as $column_name) {

        if (
            empty($totals[$column_name]) ||
            $totals[$column_name] <= 0
        ) {
            continue;
        }

        echo '
        <tr>
            <td>' . esc_html($column_name) . '</td>
            <td>' . number_format($totals[$column_name], 0) . '</td>
        </tr>';
    }

    $calculated_revenue =
        $totals['TOTAL_AMOUNT']
        - $totals['TOTAL_EXPENSE'];

    echo '
    <tr>
        <td><strong>Total Amount</strong></td>
        <td><strong>' . number_format($totals['TOTAL_AMOUNT'], 0) . '</strong></td>
    </tr>

    <tr>
        <td><strong>Total Expense</strong></td>
        <td><strong>' . number_format($totals['TOTAL_EXPENSE'], 0) . '</strong></td>
    </tr>

    <tr>
        <td><strong>Calculated Revenue</strong></td>
        <td><strong>' . number_format($calculated_revenue, 0) . '</strong></td>
    </tr>

    <tr>
        <td><strong>Revenue</strong></td>
        <td><strong>' . number_format($totals['REVENUE'], 0) . '</strong></td>
    </tr>

    <tr>
        <td><strong>Total Revenue</strong></td>
        <td><strong>' . number_format($totals['TOTAL_REVENUE'], 0) . '</strong></td>
    </tr>';

    echo '
    </tbody>
    </table>';
}