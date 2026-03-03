$(document).ready(function () {
    NProgress.configure({
        showSpinner: true,
        speed: 300,
        trickleSpeed: 150,
        parent: '#nprogress-parent'
    });

    $('#shortenForm').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        NProgress.start();

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },

            success: function (data) {
                if (data.success && data.payload && data.payload.shortUrl) {
                    $('#originalUrlResult').val(data.payload.url);
                    $('#shortUrlResult').val(data.payload.shortUrl);
                    $('#qrCodeImage').attr('src', '/api/url/qr/' + data.payload.shortHash);

                    $('.start-page-body').fadeOut(300, function () {
                        $('.result-page-body').fadeIn(300);
                    });

                    toastr.success('Сокращение выполнено');
                }
            },

            error: function (xhr) {
                var response = xhr.responseJSON || {};
                var errors = response.errors || {};

                $.each(errors, function (field, messages) {
                    if (Array.isArray(messages)) {
                        messages.forEach(function (msg) {
                            toastr.error(msg);
                        });
                    } else {
                        toastr.error(messages);
                    }
                });
            },

            complete: function () {
                NProgress.done();
            }
        });
    });
});

function goBack() {
    $('.result-page-body').fadeOut(300, function () {
        $('#urlInput').val('');
        $('.start-page-body').fadeIn(300);
    });
}

function copyField(inputId, btn) {
    var input = document.getElementById(inputId);
    input.select();
    document.execCommand('copy');
    btn.querySelector('span').textContent = '✓';
    toastr.success('URL успешно скопирован!');
    setTimeout(function () {
        btn.querySelector('span').textContent = '⧉';
    }, 2000);
}
