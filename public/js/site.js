jQuery(document).ready(function ($) {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#catalog-sidebar > ul ul').hide();
    $('#catalog-sidebar .badge').on('click', function () {
        var $badge = $(this);
        var closed = $badge.siblings('ul') && !$badge.siblings('ul').is(':visible');

        if (closed) {
            $badge.siblings('ul').slideDown('normal', function () {
                $badge.children('i').removeClass('fa-plus').addClass('fa-minus');
            });
        } else {
            $badge.siblings('ul').slideUp('normal', function () {
                $badge.children('i').removeClass('fa-minus').addClass('fa-plus');
            });
        }
    });

    $('form#profiles button[type="submit"]').hide();
    $('form#profiles select').change(function () {
        if ($(this).val() == 0) {
            $('#checkout').trigger('reset');
            return;
        }

        var data = new FormData($('form#profiles')[0]);
        $.ajax({
            url: '/basket/profile',
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                $('input[name="name"]').val(data.profile.name);
                $('input[name="email"]').val(data.profile.email);
                $('input[name="phone"]').val(data.profile.phone);
                $('input[name="address"]').val(data.profile.address);
                $('textarea[name="comment"]').val(data.profile.comment);
            },
            error: function (reject) {
                alert(reject.responseJSON.error);
            }
        });
    });

    $('form.add-to-basket').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var data = new FormData($form[0]);
        console.log($form.attr('action'));
        $.ajax({
            url: $form.attr('action'),
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType: 'HTML',
            beforeSend: function () {
                var spiner = ' <span class="spinner-border spinner-border-sm"></span>';
                $form.find('button').append(spiner);
            },
            success: function (html) {
                $form.find('.spinner-border').remove();
                $('#top-basket').html(html);
            }
        });
    });

});
