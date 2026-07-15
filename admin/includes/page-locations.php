<?php

if (!defined('ABSPATH')) {
    exit;
}


global $wpdb;

$table_name = $wpdb->prefix . 'gssync_locations';

/*
|--------------------------------------------------------------------------
| Add Location
|--------------------------------------------------------------------------
*/
if (
    isset($_POST['gssync_add_location']) &&
    !empty($_POST['location_name'])
) {

    $wpdb->insert(
        $table_name,
        [
            'location_name' => sanitize_text_field($_POST['location_name']),
        ]
    );

    echo '<div class="notice notice-success"><p>Location added.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Update Location
|--------------------------------------------------------------------------
*/
if (
    isset($_POST['gssync_update_location']) &&
    !empty($_POST['location_id'])
) {

    $wpdb->update(
        $table_name,
        [
            'location_name' => sanitize_text_field($_POST['location_name']),
        ],
        [
            'id' => absint($_POST['location_id']),
        ]
    );

    echo '<div class="notice notice-success"><p>Location updated.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Delete Location
|--------------------------------------------------------------------------
*/
if (
    isset($_GET['delete_location'])
) {

    $wpdb->delete(
        $table_name,
        [
            'id' => absint($_GET['delete_location']),
        ]
    );

    echo '<div class="notice notice-success"><p>Location deleted.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Edit Location
|--------------------------------------------------------------------------
*/
$edit_location = null;

if (
    isset($_GET['edit_location'])
) {

    $edit_location = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            absint($_GET['edit_location'])
        )
    );
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
$search = '';

if (!empty($_GET['location_search'])) {
    $search = sanitize_text_field($_GET['location_search']);
}

if (!empty($search)) {

    $locations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * 
             FROM {$table_name}
             WHERE location_name LIKE %s
             ORDER BY location_name ASC",
            '%' . $wpdb->esc_like($search) . '%'
        )
    );

} else {

    $locations = $wpdb->get_results(
        "SELECT *
         FROM {$table_name}
         ORDER BY location_name ASC"
    );
}

?>

<div class="wrap gssync-locations-wrap">

    <h1 class="wp-heading-inline">
        Locations
    </h1>
    <div class="flex-location">
        <div class="gssync-card">

            <h2>
                <?php echo $edit_location ? 'Edit Location' : 'Add New Location'; ?>
            </h2>

            <form method="post" class="gssync-inline-form">

                <?php if ($edit_location) : ?>
                    <input
                        type="hidden"
                        name="location_id"
                        value="<?php echo esc_attr($edit_location->id); ?>"
                    >
                <?php endif; ?>

                <input
                    type="text"
                    name="location_name"
                    class="regular-text"
                    placeholder="Enter location name"
                    value="<?php echo $edit_location ? esc_attr($edit_location->location_name) : ''; ?>"
                    required
                >

                <?php if ($edit_location) : ?>

                    <input
                        type="submit"
                        name="gssync_update_location"
                        class="button button-primary"
                        value="Update Location"
                    >

                    <a
                        href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-locations'); ?>"
                        class="button"
                    >
                        Cancel
                    </a>

                <?php else : ?>

                    <input
                        type="submit"
                        name="gssync_add_location"
                        class="button button-primary"
                        value="Add Location"
                    >

                <?php endif; ?>

            </form>

        </div>

        
        <div class="gssync-card">

            <h2>
                Search Locations
            </h2>

            <form method="get" class="gssync-inline-form">

                <input
                    type="hidden"
                    name="post_type"
                    value="gssync_vehicle"
                >

                <input
                    type="hidden"
                    name="page"
                    value="gssync-locations"
                >

                <input
                    type="text"
                    name="location_search"
                    class="regular-text"
                    placeholder="Search location..."
                    value="<?php echo esc_attr($search); ?>"
                >

                <input
                    type="submit"
                    class="button"
                    value="Search"
                >

                <?php if (!empty($search)) : ?>

                    <a
                        href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-locations'); ?>"
                        class="button"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>

        </div>
    </div>

    <div class="gssync-card">

        <h2>
            Saved Locations
            <span style="font-weight:400;">
                (<?php echo count($locations); ?>)
            </span>
        </h2>

        <table class="widefat striped gssync-locations-table">

            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Location</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($locations)) : ?>

                    <?php foreach ($locations as $location) : ?>

                        <tr>

                            <td>
                                <?php echo esc_html($location->id); ?>
                            </td>

                            <td>
                                <?php echo esc_html($location->location_name); ?>
                            </td>

                            <td>

                                <a
                                    href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-locations&edit_location=' . $location->id); ?>"
                                    class="button button-small"
                                >
                                    Edit
                                </a>

                                <a
                                    href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-locations&delete_location=' . $location->id); ?>"
                                    class="button button-small"
                                    onclick="return confirm('Delete this location?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>
                        <td colspan="3">
                            No locations found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
