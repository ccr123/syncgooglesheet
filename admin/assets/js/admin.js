jQuery(function ($) {

    $('#gssync-preview-sheet').on('click', function () {
        var button = $(this);
        var spinner = button.next('.spinner');
        const spreadsheet_id = $('#gssync_spreadsheet_id').val();
        const sheet_name = $('select[name="gssync_sheet_name"]').val();

        $(spinner).addClass('is-active');

        $.ajax({
            url: gssync_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'gssync_preview_sheet',
                nonce: gssync_ajax.nonce,
                spreadsheet_id: spreadsheet_id,
                sheet_name: sheet_name
            },
            success: function (response) {

                $(spinner).removeClass('is-active');

               if ($('#gssync-sheet-preview-container').length === 0) {

                    $('#gssync_vehicle_sheet')
                        .after(
                            '<div id="gssync-sheet-preview-container" class="postbox" style="margin-top:15px;"><div class="inside"></div></div>'
                        );
                }

                $('#gssync-sheet-preview-container .inside')
                    .html(response.data);
                }
        });

    });

});

jQuery(function ($) {

    $(document).on('click', '#gssync-sync-locations', function () {

        var button = $(this);
        var spinner = button.next('.spinner');

        const spreadsheet_id = $('#gssync_spreadsheet_id').val();
        const sheet_name = $('select[name="gssync_sheet_name"]').val();

        spinner.addClass('is-active');

        $.ajax({
            url: gssync_ajax.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'gssync_sync_locations',
                spreadsheet_id: spreadsheet_id,
                sheet_name: sheet_name,
                nonce: gssync_ajax.nonce,
            },
            success: function (response) {

                spinner.removeClass('is-active');

                if (response.success) {

                    $('#gssync-sheet-location').html(
                        '<div class="notice notice-success inline"><p>' +
                        response.data +
                        '</p></div>'
                    );

                } else {

                    $('#gssync-sheet-location').html(
                        '<div class="notice notice-error inline"><p>' +
                        response.data +
                        '</p></div>'
                    );

                }
            },
            error: function () {

                spinner.removeClass('is-active');

                $('#gssync-sheet-location').html(
                    '<div class="notice notice-error inline"><p>Ajax Error</p></div>'
                );

            }
        });

    });

});

jQuery(function ($) {

    $(document).on(
        'click',
        '#gssync-generate-expenses',
        function () {

            var button = $(this);
            var spinner = button.next('.spinner');

            const spreadsheet_id = $('#gssync_spreadsheet_id').val();
            const sheet_name = $('select[name="gssync_sheet_name"]').val();

            spinner.addClass('is-active');

            $.ajax({
                url: gssync_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gssync_generate_expenses',
                    spreadsheet_id: spreadsheet_id,
                    sheet_name: sheet_name,
                    nonce: gssync_ajax.nonce
                },
                success: function (response) {

                    spinner.removeClass('is-active');

                    if (response.success) {

                        $('#gssync-expense-result').html(
                            response.data
                        );

                    } else {

                        $('#gssync-expense-result').html(
                            '<div class="notice notice-error inline"><p>' +
                            response.data +
                            '</p></div>'
                        );

                    }
                },
                error: function () {

                    spinner.removeClass('is-active');

                    $('#gssync-expense-result').html(
                        '<div class="notice notice-error inline"><p>Ajax Error</p></div>'
                    );

                }
            });

        }
    );

});

jQuery(function ($) {

    $(document).on(
        'click',
        '#gssync-sync-expense-types',
        function () {

            var button = $(this);
            var spinner = button.next('.spinner');

            const spreadsheet_id = $('#gssync_spreadsheet_id').val();
            const sheet_name = $('select[name="gssync_sheet_name"]').val();

            spinner.addClass('is-active');

            $.ajax({
                url: gssync_ajax.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gssync_sync_expenses',
                    spreadsheet_id: spreadsheet_id,
                    sheet_name: sheet_name,
                    nonce: gssync_ajax.nonce
                },
                success: function (response) {

                    spinner.removeClass('is-active');

                    if (response.success) {

                        $('#gssync-expense-types-result').html(
                            '<div class="notice notice-success inline"><p>' +
                            response.data +
                            '</p></div>'
                        );

                    } else {

                        $('#gssync-expense-types-result').html(
                            '<div class="notice notice-error inline"><p>' +
                            response.data +
                            '</p></div>'
                        );
                    }
                },
                error: function () {

                    spinner.removeClass('is-active');

                    $('#gssync-expense-types-result').html(
                        '<div class="notice notice-error inline"><p>Ajax Error</p></div>'
                    );
                }
            });

        }
    );

});