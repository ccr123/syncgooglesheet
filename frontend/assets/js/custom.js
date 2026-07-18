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

    const months = [
        'January','February','March','April',
        'May','June','July','August',
        'September','October','November','December'
    ];

    let month = new Date().getMonth();
    let year = new Date().getFullYear();

    $('.year-left').on('click', function () {

        month--;

        if (month < 0) {
            month = 11;
            year--;
        }

        let currentDate = months[month] + ' ' + year;

        console.log(currentDate);

        $('.gssync-year-field').val(currentDate);

    });

    $('.year-right').on('click', function () {

        month++;

        if (month > 11) {
            month = 0;
            year++;
        }

        let currentDate = months[month] + ' ' + year;

        console.log(currentDate);
 $('.gssync-year-field').val('may');
        $('.gssync-year-field').val(currentDate);

    });

});