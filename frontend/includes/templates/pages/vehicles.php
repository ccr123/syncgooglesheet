<div class="gssync-card-wrapper update-entry bg-white br-12 pb-10 pl-20 pr-20">

    <section class="gssync-card">

        <h1 class="text-center pt-20 mb-20">
            Vehicles
        </h1>

        <div class="gssync-search-box mb-20">

            <input
                type="text"
                class="regular-text"
                placeholder="Search Vehicle No..."
            >

        </div>

        <?php

        $vehicles = get_posts([
            'post_type'      => 'gssync_vehicle',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        ?>

        <div class="gssync-vehicle-table-wrapper">

            <table class="gssync-vehicle-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Vehicle No
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($vehicles) : ?>

                        <?php foreach ($vehicles as $index => $vehicle) : ?>

                            <tr>

                                <td>
                                    <?php echo $index + 1; ?>
                                </td>

                                <td>
                                    <?php echo esc_html($vehicle->post_title); ?>
                                </td>

                                <td>

                                    <a
                                        href="<?php echo esc_url(
                                            home_url(
                                                '/edrive-update-vehicle/?vehicle_id=' .
                                                $vehicle->ID
                                            )
                                        ); ?>"
                                        class="btn-accent"
                                    >
                                        Edit Sheet
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>

                            <td colspan="3">
                                No vehicles found.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</div>