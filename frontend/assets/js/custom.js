jQuery(function ($) {

    $('#loginform input[type="text"], #loginform input[type="password"]').on(
        'focus input',
        function () {
            $(this)
                .closest('p')
                .find('label')
                .fadeOut(150);
        }
    );

    $('#loginform input[type="text"], #loginform input[type="password"]').on(
        'blur',
        function () {

            if ($(this).val().trim() === '') {

                $(this)
                    .closest('p')
                    .find('label')
                    .fadeIn(150);
            }
        }
    );

});
jQuery(function ($) {

    function getCurrentState() {

        const $input = $('.gssync-year-field');
        const isBs = $('.update-entry-filters').hasClass('bs-active');

        return {
            year: parseInt(
                isBs ? $input.data('year-bs') : $input.data('year-ad')
            ),
            month: parseInt(
                isBs ? $input.data('month-bs') : $input.data('month-ad')
            )
        };
    }

    function updateField(year, month) {

        const isBs = $('.update-entry-filters').hasClass('bs-active');
        const $input = $('.gssync-year-field');

        if (isBs) {

            $input
                .data('year-bs', year)
                .data('month-bs', month)
                .attr('data-year-bs', year)
                .attr('data-month-bs', month);

            $input.val(month + ' ' + year);

        } else {

            const months = [
                'January','February','March','April',
                'May','June','July','August',
                'September','October','November','December'
            ];

            $input
                .data('year-ad', year)
                .data('month-ad', month)
                .attr('data-year-ad', year)
                .attr('data-month-ad', month);

            $input.val(months[month - 1] + ' ' + year);

        }
    }

    $('.year-left').on('click', function () {

        let { year, month } = getCurrentState();

        month--;

        if (month < 1) {
            month = 12;
            year--;
        }

        updateField(year, month);
    });

    $('.year-right').on('click', function () {

        let { year, month } = getCurrentState();

        month++;

        if (month > 12) {
            month = 1;
            year++;
        }

        updateField(year, month);
    });

});
jQuery(function ($) {

    const activeIndex = $('.gssync-date-item.active')
        .closest('.owl-item')
        .index();

    const $owl = $('.gssync-date-list');

    $owl.owlCarousel({
        items: 6,
        margin: 10,
        nav: false,
        dots: false,
        slideBy: 1,
        responsive: {
        0: {
            items: 5
        },
        768: {
            items: 8
        },
        1200: {
            items: 12
        }
    }
    });

    $owl.trigger(
        'to.owl.carousel',
        [activeIndex, 0]
    );

    jQuery(document).on('click', '.gssync-date-item', function () {

    $('.gssync-date-item')
        .removeClass('active');

    $(this)
        .addClass('active');

});

});
