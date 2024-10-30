(function ($) {
    function url(element, options) {
        var root = element.parents('.clinked-container');
        var params = [
            'widget=' + root.data('widget')
        ];
        $.each(options, function (key, value) {
            params.push(key + '=' + value);
        });
        return root.data('ajax') + '?' + params.join('&');
    }

    $(function () {
        var container = $('.clinked-container');
        container.find('.clinked-error').hide();

        // Login form handler
        container.on('submit', '.clinked-login', function (e) {
            e.preventDefault();

            var element = $(this);
            var root = element.parents('.clinked-container');
            var errorView = root.find('.clinked-error');

            errorView.hide();

            var redirect = function () {
                // Pass event to default handler by resubmitting the form
                container.unbind('submit');
                element.submit();
            };

            $.post(url(element), element.serializeArray(), 'json')
                .success(function () {
                    redirect();
                })
                .error(function (request, textStatus, errorThrown) {
                    var json = JSON.parse(request.responseText);
                    switch (json.error) {
                        case 'missing_2fa':
                            // username and password are valid, a user will land to 2FA code input form page
                            redirect();
                            return;
                        case 'missing_2fa_sms':
                        case 'error_2fa_sms':
                        case 'invalid_2fa':
                            // TODO Handle SMS 2FA?
                            break;
                    }
                    errorView.show();
                });
        });
    });

})(jQuery);