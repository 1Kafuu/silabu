// jquery.form-handler.js
(function($) {
    $.fn.formHandler = function(options) {
        const defaults = {
            submitButton: null,
            rules: {},
            messages: {},
            notificationContainer: '#notification-container',
            loadingText: 'Menyimpan... <i class="fa fa-spinner fa-spin"></i>',
            onSuccess: null,
            onError: null,
            autoDismissTime: 5000
        };

        const settings = $.extend({}, defaults, options);
        
        return this.each(function() {
            const form = $(this);
            const submitButton = settings.submitButton ? 
                $(settings.submitButton) : 
                form.find('button[type="submit"]');

            // Initialize validation
            form.validate({
                rules: settings.rules,
                messages: settings.messages,
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                }
            });

            // Handle submission
            submitButton.click(function(e) {
                e.preventDefault();
                
                if (!form.valid()) return;

                const button = $(this);
                const originalText = button.html();
                
                button.html(settings.loadingText).prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        button.html(originalText).prop('disabled', false);
                        
                        if (settings.onSuccess) {
                            settings.onSuccess(response, form);
                        }
                    },
                    error: function(xhr) {
                        button.html(originalText).prop('disabled', false);
                        
                        if (settings.onError) {
                            settings.onError(xhr, form);
                        }
                    }
                });
            });
        });
    };
})(jQuery);