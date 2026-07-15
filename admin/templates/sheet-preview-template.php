<?php

if (!defined('ABSPATH')) {
    exit;
}

$header_index = null;

foreach ($rows as $index => $row) {

    if (
        in_array('DATE', $row, true) &&
        in_array('SOURCE', $row, true) &&
        in_array('DESTINATION', $row, true)
    ) {
        $header_index = $index;
        break;
    }
}

?>

<div class="gssync-sheet-preview">

    <?php if ($header_index !== null) : ?>

        <!-- Summary Table -->
        <div class="gssync-summary-table">

            <table class="widefat striped">

                <tbody>

                    <?php for ($i = 0; $i < $header_index; $i++) : ?>

                        <?php
                        $row = $rows[$i];

                        if (empty(array_filter($row))) {
                            continue;
                        }
                        ?>

                        <tr>

                            <?php foreach ($row as $cell) : ?>

                                <td>
                                    <?php echo esc_html($cell); ?>
                                </td>

                            <?php endforeach; ?>

                        </tr>

                    <?php endfor; ?>

                </tbody>

            </table>

        </div>

        <br><br>

        <!-- Trip Table -->
        <div class="gssync-trip-table">

            <table class="widefat striped">

                <thead>

                    <tr>

                        <?php foreach ($rows[$header_index] as $cell) : ?>

                            <th>
                                <?php echo esc_html($cell); ?>
                            </th>

                        <?php endforeach; ?>

                    </tr>

                </thead>

                <tbody>

                    <?php for ($i = $header_index + 1; $i < count($rows); $i++) : ?>

                        <?php
                        $row = $rows[$i];

                        $is_empty = true;

                        foreach ($row as $cell) {
                            if (trim($cell) !== '') {
                                $is_empty = false;
                                break;
                            }
                        }

                        if ($is_empty) {
                            continue;
                        }
                        ?>

                        <tr>

                            <?php foreach ($row as $cell) : ?>

                                <td>
                                    <?php echo esc_html($cell); ?>
                                </td>

                            <?php endforeach; ?>

                        </tr>

                    <?php endfor; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>