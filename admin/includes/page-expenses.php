```php
<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$table_name = $wpdb->prefix . 'gssync_expenses';

/*
|--------------------------------------------------------------------------
| Add Expense
|--------------------------------------------------------------------------
*/
if (
    isset($_POST['gssync_add_expense']) &&
    !empty($_POST['expense_name'])
) {

    $wpdb->insert(
        $table_name,
        [
            'expense_name' => sanitize_text_field($_POST['expense_name']),
        ]
    );

    echo '<div class="notice notice-success"><p>Expense type added.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Update Expense
|--------------------------------------------------------------------------
*/
if (
    isset($_POST['gssync_update_expense']) &&
    !empty($_POST['expense_id'])
) {

    $wpdb->update(
        $table_name,
        [
            'expense_name' => sanitize_text_field($_POST['expense_name']),
        ],
        [
            'id' => absint($_POST['expense_id']),
        ]
    );

    echo '<div class="notice notice-success"><p>Expense type updated.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Delete Expense
|--------------------------------------------------------------------------
*/
if (
    isset($_GET['delete_expense'])
) {

    $wpdb->delete(
        $table_name,
        [
            'id' => absint($_GET['delete_expense']),
        ]
    );

    echo '<div class="notice notice-success"><p>Expense type deleted.</p></div>';
}

/*
|--------------------------------------------------------------------------
| Edit Expense
|--------------------------------------------------------------------------
*/
$edit_expense = null;

if (
    isset($_GET['edit_expense'])
) {

    $edit_expense = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d",
            absint($_GET['edit_expense'])
        )
    );
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/
$search = '';

if (!empty($_GET['expense_search'])) {
    $search = sanitize_text_field($_GET['expense_search']);
}

if (!empty($search)) {

    $expenses = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT *
             FROM {$table_name}
             WHERE expense_name LIKE %s
             ORDER BY expense_name ASC",
            '%' . $wpdb->esc_like($search) . '%'
        )
    );

} else {

    $expenses = $wpdb->get_results(
        "SELECT *
         FROM {$table_name}
         ORDER BY expense_name ASC"
    );
}

?>

<div class="wrap gssync-expenses-wrap">

    <h1 class="wp-heading-inline">
        Expense Types
    </h1>

    <div class="flex-location">

        <div class="gssync-card">

            <h2>
                <?php echo $edit_expense ? 'Edit Expense Type' : 'Add New Expense Type'; ?>
            </h2>

            <form method="post" class="gssync-inline-form">

                <?php if ($edit_expense) : ?>
                    <input
                        type="hidden"
                        name="expense_id"
                        value="<?php echo esc_attr($edit_expense->id); ?>"
                    >
                <?php endif; ?>

                <input
                    type="text"
                    name="expense_name"
                    class="regular-text"
                    placeholder="Enter expense type"
                    value="<?php echo $edit_expense ? esc_attr($edit_expense->expense_name) : ''; ?>"
                    required
                >

                <?php if ($edit_expense) : ?>

                    <input
                        type="submit"
                        name="gssync_update_expense"
                        class="button button-primary"
                        value="Update Expense"
                    >

                    <a
                        href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-expenses'); ?>"
                        class="button"
                    >
                        Cancel
                    </a>

                <?php else : ?>

                    <input
                        type="submit"
                        name="gssync_add_expense"
                        class="button button-primary"
                        value="Add Expense"
                    >

                <?php endif; ?>

            </form>

        </div>

        <div class="gssync-card">

            <h2>
                Search Expense Types
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
                    value="gssync-expenses"
                >

                <input
                    type="text"
                    name="expense_search"
                    class="regular-text"
                    placeholder="Search expense type..."
                    value="<?php echo esc_attr($search); ?>"
                >

                <input
                    type="submit"
                    class="button"
                    value="Search"
                >

                <?php if (!empty($search)) : ?>

                    <a
                        href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-expenses'); ?>"
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
            Saved Expense Types
            <span style="font-weight:400;">
                (<?php echo count($expenses); ?>)
            </span>
        </h2>

        <table class="widefat striped gssync-locations-table">

            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Expense Type</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($expenses)) : ?>

                    <?php foreach ($expenses as $expense) : ?>

                        <tr>

                            <td>
                                <?php echo esc_html($expense->id); ?>
                            </td>

                            <td>
                                <?php echo esc_html($expense->expense_name); ?>
                            </td>

                            <td>

                                <a
                                    href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-expenses&edit_expense=' . $expense->id); ?>"
                                    class="button button-small"
                                >
                                    Edit
                                </a>

                                <a
                                    href="<?php echo admin_url('edit.php?post_type=gssync_vehicle&page=gssync-expenses&delete_expense=' . $expense->id); ?>"
                                    class="button button-small"
                                    onclick="return confirm('Delete this expense type?');"
                                >
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else : ?>

                    <tr>
                        <td colspan="3">
                            No expense types found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
```
