jQuery(function ($) {

    $(document).on('click', '.gssync-calendar-btn', function () {

        const calendar = $(this).data('calendar');
        const $wrapper = $('.update-entry-filters');
        const $input = $('.gssync-year-field');

        $('.gssync-calendar-btn').removeClass('active');
        $(this).addClass('active');

        if (calendar === 'bs') {

            $wrapper.addClass('bs-active');

            $input.val(
                $input.data('month-name-bs') + ' ' + $input.data('year-bs')
            );

        } else {

            $wrapper.removeClass('bs-active');

            $input.val(
                $input.data('month-name-ad') + ' ' + $input.data('year-ad')
            );

        }

    });

});